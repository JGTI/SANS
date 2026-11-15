"""
SPACE VISION Computer Vision & OCT Endpoints
"""
from fastapi import APIRouter, Depends
from pydantic import BaseModel, Field
from app.computer_vision.oct_preprocessor import OctImagePreprocessor
from app.core.security import verify_internal_token

router = APIRouter(dependencies=[Depends(verify_internal_token)])

class OctProfileRequest(BaseModel):
    base_thickness_microns: float = Field(95.0, ge=40.0, le=200.0)
    edema_delta_microns: float = Field(0.0, ge=-20.0, le=100.0)
    sectors_count: int = Field(12, ge=4, le=48)

class ValidateImageRequest(BaseModel):
    width: int
    height: int
    channels: int = 1
    bit_depth: int = 8
    modality: str = "OCT_B_SCAN"

class CirrusOctRequest(BaseModel):
    astronaut_code: str = Field("ASTRO-01", description="Código del astronauta")
    astronaut_name: str = Field("CDR Alex Mercer", description="Nombre del tripulante")
    flight_day: int = Field(45, ge=0, le=730, description="Día de vuelo en microgravedad")
    scenario: str = Field("B", description="Escenario de simulación (A, B, C, D, E)")

@router.post("/oct-profile", summary="Genera perfil peripapilar TSNIT de grosor retiniano RNFL")
def generate_oct_profile(req: OctProfileRequest):
    return OctImagePreprocessor.extract_simulated_rnfl_profile(
        base_thickness_microns=req.base_thickness_microns,
        edema_delta_microns=req.edema_delta_microns,
        sectors_count=req.sectors_count
    )

@router.post("/cirrus-workstation", summary="Emulación de consola tomográfica ZEISS Cirrus HD-OCT Macular Cube 512x128")
def get_cirrus_oct_workstation(req: CirrusOctRequest):
    return OctImagePreprocessor.generate_cirrus_oct_workstation_data(
        astronaut_code=req.astronaut_code,
        astronaut_name=req.astronaut_name,
        flight_day=req.flight_day,
        scenario=req.scenario
    )

@router.post("/validate-image", summary="Valida resolución y metadatos de imágenes OCT o fondo de ojo")
def validate_image(req: ValidateImageRequest):
    return OctImagePreprocessor.validate_image_metadata(
        width=req.width,
        height=req.height,
        channels=req.channels,
        bit_depth=req.bit_depth,
        modality=req.modality
    )


class FundusScanRequest(BaseModel):
    case_id: str = Field("CASE-PROG-02", description="Expediente del astronauta")
    eye: str = Field("OD", description="Ojo evaluado (OD/OS)")
    iop_mmhg: float = Field(16.0, ge=5.0, le=45.0, description="Presión intraocular del tonómetro")
    demo_preset: str = Field("edema", description="normal | folds | edema | custom")
    image_filename: str | None = None


@router.post("/fundus-scan", summary="Escáner CV de fotografía de fondo de ojo para tamizaje de edema papilar y correlación con PIO")
def scan_fundus_photo(req: FundusScanRequest):
    preset = (req.demo_preset or "edema").lower()
    if preset == "normal":
        frisen_grade = 0
        disc_margin_sharpness = 0.92
        peripapillary_halo_score = 0.11
        choroidal_folds = False
        vessel_tortuosity = 0.18
        finding_en = "Sharp optic disc margins (Frisén Grade 0). Normal physiologic cup and retinal vasculature."
        finding_es = "Bordes del disco óptico nítidos (Grado Frisén 0). Copa fisiológica y vasculatura retiniana normales."
        priority = "READY_FOR_REVIEW"
    elif preset == "folds":
        frisen_grade = 1
        disc_margin_sharpness = 0.64
        peripapillary_halo_score = 0.42
        choroidal_folds = True
        vessel_tortuosity = 0.49
        finding_en = "Mild nasal disc margin blurring (Frisén Grade 1) with horizontal papillomacular choroidal folds."
        finding_es = "Borramiento nasal leve del borde papilar (Grado Frisén 1) con pliegues coroideos horizontales en área papilomacular."
        priority = "PRIORITIZE_REVIEW"
    else:
        frisen_grade = 2
        disc_margin_sharpness = 0.41
        peripapillary_halo_score = 0.76
        choroidal_folds = True
        vessel_tortuosity = 0.68
        finding_en = "Circumferential optic disc elevation & peripapillary halo (Frisén Grade 2) with venous engorgement."
        finding_es = "Elevación circunferencial de papila y halo peripapilar (Grado Frisén 2) con ingurgitación venosa."
        priority = "PRIORITIZE_REVIEW"

    iop_status_en = "Within nominal seated/orbital band (10–21 mmHg)" if 10.0 <= req.iop_mmhg <= 21.0 else "Outside nominal band — correlate with ground specialist"
    iop_status_es = "Dentro de rango nominal (10–21 mmHg)" if 10.0 <= req.iop_mmhg <= 21.0 else "Fuera de rango nominal — correlacionar con especialista en Tierra"

    return {
        "engine": "SPACE VISION Python CV Fundus Analyzer v1.0",
        "case_id": req.case_id,
        "eye": req.eye.upper(),
        "iop_mmhg": round(req.iop_mmhg, 1),
        "iop_interpretation": {"en": iop_status_en, "es": iop_status_es},
        "frisen_grade_estimate": frisen_grade,
        "disc_margin_sharpness_index": disc_margin_sharpness,
        "peripapillary_edema_halo": peripapillary_halo_score,
        "choroidal_folds_detected": choroidal_folds,
        "vessel_tortuosity_index": vessel_tortuosity,
        "operational_flag": priority,
        "clinical_interpretation": {"en": finding_en, "es": finding_es},
        "safety_notice": {
            "en": "Automated CV screening support only; specialist verification required. Does not issue fit-for-duty verdicts.",
            "es": "Apoyo automatizado de tamizaje por visión computacional; requiere verificación del especialista. No emite dictamen de aptitud."
        }
    }
