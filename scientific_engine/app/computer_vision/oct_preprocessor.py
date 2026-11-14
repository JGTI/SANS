"""
SPACE VISION Computer Vision & OCT Preprocessor Module
Infraestructura de ingestión, validación de dimensiones y perfil de grosor de capas retinianas (RNFL / OCT).
Diseñado para análisis de B-Scans tomográficos y fotografías de fondo de ojo de misión espacial.
"""
from typing import Dict, Any, List, Optional
import numpy as np

class OctImagePreprocessor:
    """
    Preprocesador y validador de imágenes oftalmológicas OCT y fondo de ojo.
    """
    SUPPORTED_MODALITIES = ["OCT_B_SCAN", "FUNDUS_PHOTOGRAPHY", "AUTORREFRACTOMETRY_SCAN"]

    @classmethod
    def validate_image_metadata(
        cls,
        width: int,
        height: int,
        channels: int = 1,
        bit_depth: int = 8,
        modality: str = "OCT_B_SCAN"
    ) -> Dict[str, Any]:
        """
        Valida que la imagen cumpla con los estándares biomédicos de resolución y contraste.
        """
        errors = []
        warnings = []

        if modality not in cls.SUPPORTED_MODALITIES:
            errors.append(f"Modalidad no soportada: {modality}. Modalidades admitidas: {cls.SUPPORTED_MODALITIES}")

        if width < 256 or height < 256:
            errors.append(f"Resolución insuficiente ({width}x{height}). Se requiere un mínimo de 256x256 píxeles.")
        elif width < 512 or height < 512:
            warnings.append(f"Resolución subóptima ({width}x{height}) para segmentación sub-micrométrica de retina.")

        if bit_depth not in [8, 12, 16]:
            warnings.append(f"Profundidad de color atípica ({bit_depth} bits). Los OCT estándar usan 8 o 16 bits.")

        return {
            "is_valid": len(errors) == 0,
            "modality": modality,
            "resolution": f"{width}x{height}",
            "aspect_ratio": round(width / max(1, height), 2),
            "bit_depth": bit_depth,
            "errors": errors,
            "warnings": warnings,
        }

    @classmethod
    def extract_simulated_rnfl_profile(
        cls,
        base_thickness_microns: float = 95.0,
        edema_delta_microns: float = 0.0,
        sectors_count: int = 12
    ) -> Dict[str, Any]:
        """
        Genera perfil de grosor circunferencial peripapilar (TSNIT: Temporal, Superior, Nasal, Inferior, Temporal).
        Emula la salida de tomografía Spectralis / Cirrus OCT.
        """
        # Distribución normal de doble joroba (Double-Hump) característica del RNFL normal (Superior e Inferior más gruesos)
        angles = np.linspace(0, 360, sectors_count, endpoint=False)
        profile_sectors = []

        # El edema por SANS (Frisén 1+) suele manifestarse predominantemente en polo nasal y superior
        for ang in angles:
            rad = np.radians(ang)
            # Doble joroba fisiológica: picos a 90° (superior) y 270° (inferior)
            physiological_peak = 35.0 * np.cos(2 * rad - np.pi/2)**2
            # Distribución asimétrica del edema de SANS
            sans_edema_component = edema_delta_microns * (0.6 + 0.4 * np.sin(rad))
            
            thickness = round(float(base_thickness_microns + physiological_peak + sans_edema_component), 1)

            # Clasificación percentil según base normativa (verde: normal > 5%, amarillo: borderline 1-5%, rojo: <1% o >99% edema)
            if thickness > 135.0:
                flag = "EDEMA_SEVERO_RED"
            elif thickness > 115.0:
                flag = "BORDERLINE_ELEVADO_YELLOW"
            elif thickness < 65.0:
                flag = "ATROFIA_RED"
            else:
                flag = "DENTRO_LIMITES_NORMALES_GREEN"

            sector_label = (
                "Temporal" if (337.5 <= ang or ang < 22.5) else
                "Temporal-Superior" if (22.5 <= ang < 67.5) else
                "Superior" if (67.5 <= ang < 112.5) else
                "Nasal-Superior" if (112.5 <= ang < 157.5) else
                "Nasal" if (157.5 <= ang < 202.5) else
                "Nasal-Inferior" if (202.5 <= ang < 247.5) else
                "Inferior" if (247.5 <= ang < 292.5) else
                "Temporal-Inferior"
            )

            profile_sectors.append({
                "angle_degrees": int(ang),
                "sector": sector_label,
                "thickness_microns": thickness,
                "normative_status": flag,
            })

        mean_thickness = round(float(np.mean([s["thickness_microns"] for s in profile_sectors])), 1)

        return {
            "modality": "Peripapillary RNFL TSNIT Profile",
            "sectors_count": sectors_count,
            "average_thickness_microns": mean_thickness,
            "baseline_reference_microns": base_thickness_microns,
            "net_edema_increase": round(mean_thickness - base_thickness_microns, 1),
            "sectors": profile_sectors,
            "disclaimer": "Perfil tomográfico estructurado para análisis biomecánico experimental. Requiere confirmación por médico oftalmólogo de vuelo.",
        }

    @classmethod
    def generate_cirrus_oct_workstation_data(
        cls,
        astronaut_code: str = "ASTRO-01",
        astronaut_name: str = "CDR Alex Mercer",
        flight_day: int = 45,
        scenario: str = "B"
    ) -> Dict[str, Any]:
        """
        Emula la estación de trabajo ZEISS Cirrus™ HD-OCT (Spectral Domain) Macular Cube 512x128.
        Produce análisis comparativo bilateral OD vs OS con rejilla ETDRS de 9 sectores,
        mapas de espesor ILM-RPE y perfiles de B-Scan tomográficos horizontales y verticales.
        """
        # Progresión fisiológica del edema por microgravedad y síndrome SANS según día de misión y escenario
        edema_factor = 0.0
        if scenario in ["B", "D", "E"]:
            # El edema neuroocular en microgravedad se incrementa progresivamente con el tiempo en órbita
            edema_factor = min(1.0, max(0.0, (flight_day - 15) / 105.0))
        elif scenario == "C":
            # Escenario transitorio con recuperación
            edema_factor = 0.5 * np.exp(-((flight_day - 60) ** 2) / (2 * 25 ** 2))

        # En SANS suele haber asimetría interocular (frecuentemente OD presenta mayor compromiso que OS o viceversa)
        od_edema_um = 65.0 * edema_factor if scenario in ["B", "D", "E"] else (15.0 * edema_factor)
        os_edema_um = 40.0 * edema_factor if scenario in ["B", "D", "E"] else (10.0 * edema_factor)

        # 1. Parámetros Cuantitativos de la Tabla Central (Norma: Central ~250-260 µm, Cubo ~9.9-10.2 mm³)
        od_central_subfield = round(252.0 + od_edema_um, 1)
        os_central_subfield = round(248.0 + os_edema_um, 1)
        od_volume_cube = round(10.1 + (od_edema_um * 0.012), 2)
        os_volume_cube = round(10.0 + (os_edema_um * 0.009), 2)
        od_avg_thickness = round(280.0 + (od_edema_um * 0.8), 1)
        os_avg_thickness = round(276.0 + (os_edema_um * 0.6), 1)

        # 2. Generación de los 9 Sectores ETDRS de Espesor Macular (Círculos concéntricos 1mm, 3mm, 6mm)
        # Valores de referencia de la base de datos normativa Cirrus (Green 5-95%, Yellow 95-99%, Red >99%)
        def calculate_etdrs_grid(base_central: float, edema: float, is_od: bool) -> Dict[str, Any]:
            # Sectores estándar ETDRS
            sectors = {
                "central": round(base_central, 1),
                "inner_superior": round(320.0 + edema * 0.9, 1),
                "inner_nasal": round(325.0 + edema * 1.1, 1), # Polo nasal suele congestionarse más en SANS
                "inner_inferior": round(318.0 + edema * 0.85, 1),
                "inner_temporal": round(310.0 + edema * 0.75, 1),
                "outer_superior": round(280.0 + edema * 0.7, 1),
                "outer_nasal": round(295.0 + edema * 0.95, 1),
                "outer_inferior": round(272.0 + edema * 0.65, 1),
                "outer_temporal": round(265.0 + edema * 0.55, 1),
            }
            # Clasificación de percentiles normativos
            status = {}
            for k, val in sectors.items():
                if val >= 340.0:
                    status[k] = "OUTSIDE_NORMAL_RED" # > 99% percentil (Edema patológico)
                elif val >= 315.0:
                    status[k] = "BORDERLINE_YELLOW" # 95% - 99%
                elif val < 210.0:
                    status[k] = "ATROPHY_RED" # < 1%
                else:
                    status[k] = "NORMAL_GREEN" # 5% - 95% normal
            return {"values": sectors, "normative_status": status}

        etdrs_od = calculate_etdrs_grid(od_central_subfield, od_edema_um, is_od=True)
        etdrs_os = calculate_etdrs_grid(os_central_subfield, os_edema_um, is_od=False)

        # 3. Datos de Perfiles B-Scan Tomográficos (Capas ILM, RNFL y RPE)
        # 100 puntos muestreados horizontalmente para renderizar las curvas tomográficas
        x_points = np.linspace(-3.0, 3.0, 100) # En milímetros (-3mm a +3mm foveal)

        def generate_bscan_curves(edema: float, folds_active: bool) -> Dict[str, List[float]]:
            # Depresión foveal gaussiana normal (profundidad de ~120 µm en retina central)
            foveal_pit = -110.0 * np.exp(-(x_points ** 2) / (2 * (0.45 ** 2)))
            
            # En edema severo por SANS, la fóvea pierde profundidad (aplanamiento foveal)
            foveal_attenuation = max(0.2, 1.0 - (edema / 90.0))
            attenuated_pit = foveal_pit * foveal_attenuation

            # Pliegues coroideos (Choroidal Folds) ondulan la retina si están activos
            folds_undulation = 12.0 * np.sin(2 * np.pi * x_points * 2.2) if folds_active else np.zeros_like(x_points)

            # Capa 1: ILM (Internal Limiting Membrane - superficie vítreo-retiniana)
            ilm_y = 280.0 + (edema * 0.9) + attenuated_pit + folds_undulation
            
            # Capa 2: RNFL / IPL (Capa de fibras nerviosas / plexiforme interna)
            rnfl_y = ilm_y - (45.0 + edema * 0.25)
            
            # Capa 3: RPE (Retinal Pigment Epithelium / Membrana de Bruch)
            rpe_y = np.full_like(x_points, 50.0) + folds_undulation * 0.7

            return {
                "x_mm": [round(float(v), 2) for v in x_points],
                "ilm_layer": [round(float(v), 1) for v in ilm_y],
                "rnfl_layer": [round(float(v), 1) for v in rnfl_y],
                "rpe_layer": [round(float(v), 1) for v in rpe_y],
            }

        has_folds = (scenario in ["D", "E"] and flight_day >= 60)
        bscan_horizontal_od = generate_bscan_curves(od_edema_um, has_folds)
        bscan_vertical_od = generate_bscan_curves(od_edema_um * 0.9, has_folds)
        bscan_horizontal_os = generate_bscan_curves(os_edema_um, False)
        bscan_vertical_os = generate_bscan_curves(os_edema_um * 0.85, False)

        return {
            "workstation_model": "ZEISS Cirrus™ HD-OCT (Spectral Domain Technology)",
            "protocol": "Macular Cube 512x128",
            "exam_date": f"2026-11-10 FD{flight_day}",
            "patient": {
                "code": astronaut_code,
                "name": astronaut_name,
                "flight_day": flight_day,
                "scenario": scenario,
            },
            "signal_strength": {
                "od": "9/10",
                "os": "10/10",
            },
            "summary_table": {
                "central_subfield_um": {"od": od_central_subfield, "os": os_central_subfield},
                "volume_cube_mm3": {"od": od_volume_cube, "os": os_volume_cube},
                "avg_thickness_cube_um": {"od": od_avg_thickness, "os": os_avg_thickness},
            },
            "etdrs_grid": {
                "od": etdrs_od,
                "os": etdrs_os,
            },
            "b_scans": {
                "od": {
                    "horizontal": bscan_horizontal_od,
                    "vertical": bscan_vertical_od,
                },
                "os": {
                    "horizontal": bscan_horizontal_os,
                    "vertical": bscan_vertical_os,
                },
            },
            "biomarkers": {
                "optic_disc_elevation_um": round(od_edema_um * 1.6, 1),
                "frisen_estimated_grade": 2 if od_edema_um > 50 else (1 if od_edema_um > 25 else 0),
                "choroidal_folds_present": has_folds,
                "foveal_pit_flattening_pct": round(float(min(90.0, (od_edema_um / 80.0) * 100)), 1),
            },
            "scientific_disclaimer": "Simulación tomográfica de alta fidelidad para modelado del síndrome SANS en microgravedad. Basado en bases normativas biomédicas de Spectral Domain OCT.",
        }
