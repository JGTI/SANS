from __future__ import annotations

import base64
import io
import time
from pathlib import Path
from typing import Any

import numpy as np
from PIL import Image, ImageDraw
from scipy import ndimage


FUNDUS_PUBLIC_DIR = Path(__file__).resolve().parents[3] / "public" / "images" / "sans" / "fundus"


def _load_fundus_image(
    image_data_url: str | None = None,
    image_filename: str | None = None,
    demo_preset: str = "edema",
) -> tuple[Image.Image, str]:
    """Load a fundus image from base64 data URL, local demo file, or preset."""
    if image_data_url and "," in image_data_url and image_data_url.startswith("data:image"):
        _, b64_part = image_data_url.split(",", 1)
        raw = base64.b64decode(b64_part)
        img = Image.open(io.BytesIO(raw)).convert("RGB")
        return img, "uploaded_base64"

    if image_filename:
        safe_name = Path(image_filename).name
        candidate = FUNDUS_PUBLIC_DIR / safe_name
        if candidate.is_file():
            return Image.open(candidate).convert("RGB"), safe_name

    preset_map = {
        "normal": "fundus-normal.jpg",
        "folds": "fundus-folds.jpg",
        "edema": "fundus-edema.png",
        "grade1": "fundus-grade1.jpg",
        "venous": "fundus-venous.jpg",
        "os_baseline": "fundus-os-baseline.jpg",
    }
    target_name = preset_map.get((demo_preset or "edema").lower(), "fundus-normal.jpg")
    candidate = FUNDUS_PUBLIC_DIR / target_name
    if not candidate.is_file():
        for fallback in ("fundus-folds.jpg", "fundus-normal.jpg", "fundus-edema.png"):
            fb_path = FUNDUS_PUBLIC_DIR / fallback
            if fb_path.is_file():
                candidate = fb_path
                target_name = fallback
                break
    return Image.open(candidate).convert("RGB"), target_name


def _encode_png_b64(img: Image.Image) -> str:
    buf = io.BytesIO()
    img.save(buf, format="PNG", optimize=True)
    return "data:image/png;base64," + base64.b64encode(buf.getvalue()).decode("ascii")


def analyze_fundus_image(
    *,
    case_id: str,
    eye: str,
    iop_mmhg: float,
    demo_preset: str = "edema",
    image_filename: str | None = None,
    image_data_url: str | None = None,
) -> dict[str, Any]:
    """Run real NumPy/SciPy Computer Vision analysis on a retinal fundus photograph."""
    t0 = time.perf_counter()
    orig_img, source_label = _load_fundus_image(
        image_data_url=image_data_url,
        image_filename=image_filename,
        demo_preset=demo_preset,
    )
    orig_w, orig_h = orig_img.size

    # Standardize working resolution for consistent spatial kernel scales
    work_size = (384, 384)
    img = orig_img.resize(work_size, Image.Resampling.BILINEAR)
    arr = np.asarray(img, dtype=np.float32) / 255.0  # (H, W, 3)

    r, g, b = arr[:, :, 0], arr[:, :, 1], arr[:, :, 2]
    lum = 0.299 * r + 0.587 * g + 0.114 * b

    # 1. Segment circular fundus field of view
    retina_mask = ndimage.binary_fill_holes(lum > 0.06)
    retina_mask = ndimage.binary_erosion(retina_mask, iterations=4)
    if retina_mask.sum() < 500:
        retina_mask = np.ones_like(lum, dtype=bool)

    yy, xx = np.indices(lum.shape)
    cy_fov = float(np.mean(yy[retina_mask]))
    cx_fov = float(np.mean(xx[retina_mask]))
    fov_radius = float(np.sqrt(retina_mask.sum() / np.pi))

    # 2. Optic Disc Localization via Gaussian-smoothed brightness + yellow-orange salience
    disc_salience = (0.55 * r + 0.45 * g) * retina_mask
    smoothed = ndimage.gaussian_filter(disc_salience, sigma=14.0)
    # Restrict search within inner 75% of FOV to avoid edge artifacts
    inner_mask = ((yy - cy_fov) ** 2 + (xx - cx_fov) ** 2) <= (0.72 * fov_radius) ** 2
    search_map = np.where(inner_mask & retina_mask, smoothed, -1.0)
    disc_y, disc_x = np.unravel_index(int(np.argmax(search_map)), search_map.shape)
    disc_r = max(22.0, fov_radius * 0.17)

    dist_from_disc = np.sqrt((yy - disc_y) ** 2 + (xx - disc_x) ** 2)
    rim_ring = (dist_from_disc >= 0.65 * disc_r) & (dist_from_disc <= 1.35 * disc_r) & retina_mask
    halo_ring = (dist_from_disc >= 1.05 * disc_r) & (dist_from_disc <= 2.35 * disc_r) & retina_mask
    periphery_ring = (dist_from_disc >= 2.6 * disc_r) & (dist_from_disc <= 4.2 * disc_r) & retina_mask

    # 3. Sobel Rim Gradient -> Disc Margin Sharpness
    gx = ndimage.sobel(lum, axis=1)
    gy = ndimage.sobel(lum, axis=0)
    grad_mag = np.hypot(gx, gy)
    rim_grad = float(np.percentile(grad_mag[rim_ring], 85)) if rim_ring.any() else 0.15
    bg_grad = float(np.median(grad_mag[retina_mask])) + 1e-4
    sharpness_raw = rim_grad / (bg_grad * 7.5 + 0.18)
    disc_margin_sharpness = round(float(np.clip(sharpness_raw, 0.18, 0.96)), 2)

    # 4. Peripapillary Edema Halo Index (elevated pale/yellow scatter around disc)
    halo_lum = float(np.mean(lum[halo_ring])) if halo_ring.any() else 0.3
    periph_lum = float(np.mean(lum[periphery_ring])) if periphery_ring.any() else 0.25
    halo_excess = (halo_lum - periph_lum) / (periph_lum + 0.12)
    peripapillary_halo = round(float(np.clip(0.22 + halo_excess * 0.95, 0.08, 0.94)), 2)

    # 5. Choroidal Folds Anisotropy (horizontal/oblique high-pass striae in papillomacular zone)
    # Green channel maximizes retinal vessel & choroidal fold contrast
    g_hp = g - ndimage.gaussian_filter(g, sigma=4.5)
    gy_hp = np.abs(ndimage.sobel(g_hp, axis=0))
    gx_hp = np.abs(ndimage.sobel(g_hp, axis=1))
    macular_zone = (dist_from_disc >= 1.4 * disc_r) & (dist_from_disc <= 3.6 * disc_r) & retina_mask
    if macular_zone.any():
        vert_energy = float(np.mean(gy_hp[macular_zone]))
        horiz_energy = float(np.mean(gx_hp[macular_zone])) + 1e-5
        anisotropy = abs(vert_energy - horiz_energy) / (vert_energy + horiz_energy + 1e-5)
        ridge_energy = float(np.percentile(np.abs(g_hp[macular_zone]), 90))
        folds_score = round(float(np.clip(anisotropy * 1.8 + ridge_energy * 2.2, 0.05, 0.95)), 2)
    else:
        folds_score = 0.12

    # 6. Vessel Tortuosity / Venous Engorgement Proxy
    lap_g = np.abs(ndimage.laplace(ndimage.gaussian_filter(g, sigma=1.2)))
    vessel_tortuosity = round(
        float(np.clip(np.percentile(lap_g[retina_mask], 92) * 3.8, 0.10, 0.92)),
        2,
    )

    # 7. Frisén Grade Estimate from CV metrics
    edema_composite = (1.0 - disc_margin_sharpness) * 0.45 + peripapillary_halo * 0.40 + folds_score * 0.15
    if edema_composite < 0.38:
        frisen_grade = 0
    elif edema_composite < 0.48:
        frisen_grade = 1
    elif edema_composite < 0.60:
        frisen_grade = 2
    else:
        frisen_grade = 3

    choroidal_folds = bool(folds_score >= 0.25 or frisen_grade >= 2)
    priority = "READY_FOR_REVIEW" if frisen_grade == 0 and not choroidal_folds else "PRIORITIZE_REVIEW"

    # 8. Generate 3 real Python CV diagnostic overlay images (Base64 PNG)
    # 8a. Optic Disc Localization + Sobel Rim Contour Overlay
    edge_norm = np.clip(grad_mag / (np.percentile(grad_mag[retina_mask], 96) + 1e-5), 0.0, 1.0)
    overlay_edge = arr.copy()
    edge_mask = (edge_norm > 0.35) & retina_mask
    overlay_edge[edge_mask, 0] = 0.12
    overlay_edge[edge_mask, 1] = np.clip(0.65 + 0.35 * edge_norm[edge_mask], 0, 1)
    overlay_edge[edge_mask, 2] = 0.98
    edge_pil = Image.fromarray((overlay_edge * 255).astype(np.uint8))
    draw = ImageDraw.Draw(edge_pil)
    draw.ellipse(
        [disc_x - disc_r, disc_y - disc_r, disc_x + disc_r, disc_y + disc_r],
        outline=(56, 189, 248),
        width=2,
    )
    draw.ellipse(
        [disc_x - 2.1 * disc_r, disc_y - 2.1 * disc_r, disc_x + 2.1 * disc_r, disc_y + 2.1 * disc_r],
        outline=(250, 204, 21),
        width=1,
    )
    draw.line([(disc_x - 10, disc_y), (disc_x + 10, disc_y)], fill=(56, 189, 248), width=2)
    draw.line([(disc_x, disc_y - 10), (disc_x, disc_y + 10)], fill=(56, 189, 248), width=2)

    # 8b. Peripapillary Edema Elevation Heatmap
    elev = ndimage.gaussian_filter(lum * retina_mask, sigma=6.0)
    elev_norm = np.clip((elev - np.percentile(elev[retina_mask], 20)) / (np.ptp(elev[retina_mask]) + 1e-5), 0.0, 1.0)
    # Inferno-like clinical palette: dark -> violet -> orange -> bright yellow
    hm_r = np.clip(1.6 * elev_norm, 0, 1)
    hm_g = np.clip(1.8 * (elev_norm - 0.35), 0, 1)
    hm_b = np.clip(0.9 * np.sin(np.pi * elev_norm), 0, 1)
    heatmap_rgb = np.stack([hm_r, hm_g, hm_b], axis=-1)
    blended_hm = np.where(retina_mask[:, :, None], 0.35 * arr + 0.65 * heatmap_rgb, 0.0)
    heatmap_pil = Image.fromarray((np.clip(blended_hm, 0, 1) * 255).astype(np.uint8))

    # 8c. Choroidal Folds & Vascular Ridge Map
    ridge_vis = np.clip(np.abs(g_hp) * 6.5, 0.0, 1.0) * retina_mask
    ridge_rgb = np.stack(
        [
            0.18 * arr[:, :, 0] + 0.15 * ridge_vis,
            0.18 * arr[:, :, 1] + 0.92 * ridge_vis,
            0.22 * arr[:, :, 2] + 0.55 * ridge_vis,
        ],
        axis=-1,
    )
    ridge_pil = Image.fromarray((np.clip(ridge_rgb, 0, 1) * 255).astype(np.uint8))

    if frisen_grade == 0:
        finding_en = (
            f"Sharp optic disc margins (Sobel rim sharpness {int(disc_margin_sharpness * 100)}%, "
            f"Frisén Grade 0). Nominal peripapillary profile."
        )
        finding_es = (
            f"Bordes papilares nítidos (nitidez Sobel {int(disc_margin_sharpness * 100)}%, "
            f"Grado Frisén 0). Perfil peripapilar nominal."
        )
    elif frisen_grade == 1:
        finding_en = (
            f"Mild optic disc margin blurring (Frisén Grade 1, sharpness {int(disc_margin_sharpness * 100)}%) "
            f"with early peripapillary halo ({int(peripapillary_halo * 100)}%)."
        )
        finding_es = (
            f"Borramiento leve del borde papilar (Grado Frisén 1, nitidez {int(disc_margin_sharpness * 100)}%) "
            f"con halo peripapilar inicial ({int(peripapillary_halo * 100)}%)."
        )
    else:
        finding_en = (
            f"Optic disc elevation & peripapillary edema halo ({int(peripapillary_halo * 100)}%, "
            f"Frisén Grade {frisen_grade}) with {'choroidal folds' if choroidal_folds else 'venous engorgement'}."
        )
        finding_es = (
            f"Elevación del disco óptico y halo de edema peripapilar ({int(peripapillary_halo * 100)}%, "
            f"Grado Frisén {frisen_grade}) con {'pliegues coroideos' if choroidal_folds else 'ingurgitación venosa'}."
        )

    iop_status_en = (
        "Within nominal seated/orbital band (10–21 mmHg)"
        if 10.0 <= iop_mmhg <= 21.0
        else "Outside nominal band — correlate with ground specialist"
    )
    iop_status_es = (
        "Dentro de rango nominal (10–21 mmHg)"
        if 10.0 <= iop_mmhg <= 21.0
        else "Fuera de rango nominal — correlacionar con especialista en Tierra"
    )

    elapsed_ms = round((time.perf_counter() - t0) * 1000, 1)

    return {
        "engine": "SPACE VISION Python CV Fundus Analyzer v2.0 (Pillow + NumPy + SciPy)",
        "python_executed": True,
        "case_id": case_id,
        "eye": eye.upper(),
        "source_image": source_label,
        "image_resolution": f"{orig_w}x{orig_h} -> {work_size[0]}x{work_size[1]}",
        "pixels_analyzed": int(retina_mask.sum()),
        "processing_ms": elapsed_ms,
        "disc_center_px": [int(disc_x), int(disc_y)],
        "cv_pipeline": [
            "Pillow RGB Decode & FOV Mask",
            "SciPy Gaussian Optic Disc Localization",
            "Sobel Rim Gradient Sharpness",
            "Peripapillary Halo Ring Photometry",
            "Bandpass Choroidal Folds Anisotropy",
        ],
        "iop_mmhg": round(iop_mmhg, 1),
        "iop_interpretation": {"en": iop_status_en, "es": iop_status_es},
        "frisen_grade_estimate": frisen_grade,
        "disc_margin_sharpness_index": disc_margin_sharpness,
        "peripapillary_edema_halo": peripapillary_halo,
        "choroidal_folds_detected": choroidal_folds,
        "choroidal_folds_anisotropy": folds_score,
        "vessel_tortuosity_index": vessel_tortuosity,
        "operational_flag": priority,
        "cv_overlays": {
            "disc_edge_map": _encode_png_b64(edge_pil),
            "edema_heatmap": _encode_png_b64(heatmap_pil),
            "folds_ridge_map": _encode_png_b64(ridge_pil),
        },
        "clinical_interpretation": {"en": finding_en, "es": finding_es},
        "safety_notice": {
            "en": "Automated CV screening support only; specialist verification required. Does not issue fit-for-duty verdicts.",
            "es": "Apoyo automatizado de tamizaje por visión computacional; requiere verificación del especialista. No emite dictamen de aptitud.",
        },
    }
