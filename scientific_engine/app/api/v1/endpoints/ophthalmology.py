"""
SPACE VISION Ophthalmology Scientific Endpoints
"""
from fastapi import APIRouter, HTTPException, Depends
from pydantic import BaseModel, Field
from typing import Optional, Dict, Any, List
from app.ophthalmology.refraction import (
    spherical_equivalent,
    snellen_to_logmar,
    thibos_power_vectors,
    longitudinal_shift,
    validate_refraction_ranges
)
from app.ophthalmology.sans_risk import calculate_sans_risk
from app.core.security import verify_internal_token

router = APIRouter(dependencies=[Depends(verify_internal_token)])

class RefractionEvalRequest(BaseModel):
    sphere: float = Field(..., description="Esfera en dioptrías (SPH)")
    cylinder: float = Field(..., description="Cilindro en dioptrías (CYL)")
    axis: float = Field(..., ge=0.0, le=180.0, description="Eje astigmático en grados (0-180)")
    snellen_numerator: int = Field(20, description="Numerador Snellen")
    snellen_denominator: int = Field(20, description="Denominador Snellen")
    baseline_sphere: Optional[float] = None
    baseline_cylinder: Optional[float] = None
    frisen_grade: int = Field(0, ge=0, le=5, description="Grado en escala de Frisén (0 a 5)")
    rnfl_thickness: float = Field(95.0, description="Grosor RNFL actual en micrómetros")
    baseline_rnfl: Optional[float] = 95.0
    has_globe_flattening: bool = False
    has_choroidal_folds: bool = False
    has_cotton_wool_spots: bool = False
    flight_day: int = Field(0, ge=0)

class RangeValidationRequest(BaseModel):
    sphere: float
    cylinder: float
    axis: float
    iop: float = 15.0
    rnfl_thickness: float = 95.0

@router.post("/evaluate", summary="Evaluación oftalmológica integral determinista")
def evaluate_refraction(req: RefractionEvalRequest):
    se = spherical_equivalent(req.sphere, req.cylinder)
    logmar = snellen_to_logmar(req.snellen_numerator, req.snellen_denominator)
    vectors = thibos_power_vectors(req.sphere, req.cylinder, req.axis)
    
    # Delta longitudinal si se provee valor basal
    shift_info = None
    if req.baseline_sphere is not None and req.baseline_cylinder is not None:
        base_se = spherical_equivalent(req.baseline_sphere, req.baseline_cylinder)
        shift_info = longitudinal_shift(base_se, se)
        shift_val = shift_info["delta_spherical_equivalent"]
    else:
        shift_val = 0.0

    rnfl_delta = req.rnfl_thickness - (req.baseline_rnfl or 95.0)

    # Riesgo SANS
    risk = calculate_sans_risk(
        hyperopic_shift=shift_val,
        frisen_grade=req.frisen_grade,
        rnfl_delta_microns=rnfl_delta,
        has_globe_flattening=req.has_globe_flattening,
        has_choroidal_folds=req.has_choroidal_folds,
        has_cotton_wool_spots=req.has_cotton_wool_spots,
        flight_days=req.flight_day
    )

    return {
        "spherical_equivalent": se,
        "logmar": logmar,
        "snellen": f"{req.snellen_numerator}/{req.snellen_denominator}",
        "thibos_vectors": vectors,
        "longitudinal_shift": shift_info,
        "rnfl_delta_microns": round(rnfl_delta, 1),
        "sans_risk": risk,
    }

@router.post("/validate", summary="Validación de consistencia y rangos fisiológicos")
def validate_ranges(req: RangeValidationRequest):
    return validate_refraction_ranges(
        sphere=req.sphere,
        cylinder=req.cylinder,
        axis=req.axis,
        iop=req.iop,
        rnfl_thickness=req.rnfl_thickness
    )


class BiomarkerPredictorRequest(BaseModel):
    case_id: str = Field("CASE-PROG-02", description="ID pseudónimo de la persona")
    scenario_model: str = Field("EMPEORA", description="ESTABLE | EMPEORA | NO_ESCANEA")
    oct_trt_baseline_um: Optional[float] = Field(280.0, description="1. Espesor medio OCT basal (µm)")
    oct_trt_current_um: Optional[float] = Field(306.0, description="1. Espesor medio OCT actual (µm)")
    axial_length_mm: Optional[float] = Field(23.82, description="2. Longitud axial del ojo (mm)")
    axial_baseline_mm: float = Field(24.10, description="Longitud axial basal (mm)")
    simulated_hyperopia_d: float = Field(0.75, description="3. Hipermetropía simulada (ΔSE en D)")
    papilledema_frisen: Optional[int] = Field(2, ge=0, le=5, description="4. Edema papilar (Escala Frisén 0-5)")
    iop_mmhg: float = Field(18.5, description="5. Presión intraocular (mmHg)")
    prior_hdt6_exposure: bool = Field(True, description="6. Exposición previa a inclinación cefálica de -6° en la Tierra")


@router.post("/biomarker-predictor", summary="Predictor SANS basado en los 6 biomarcadores oculares, corte +30/+60/+90 y 3 escenarios")
def evaluate_biomarker_predictor(req: BiomarkerPredictorRequest):
    if req.scenario_model == "NO_ESCANEA" or req.oct_trt_current_um is None or req.oct_trt_baseline_um is None:
        return {
            "case_id": req.case_id,
            "scenario_model": "NO_ESCANEA",
            "operational_output": "Faltan datos",
            "fitness_verdict": "Aptitud no evaluada",
            "delta_trt_um": None,
            "delta_axial_mm": None,
            "biomarkers": {
                "oct_thickness_um": None,
                "axial_length_mm": req.axial_length_mm,
                "simulated_hyperopia_d": req.simulated_hyperopia_d,
                "papilledema_frisen": req.papilledema_frisen,
                "iop_mmhg": req.iop_mmhg,
                "prior_hdt6_exposure": req.prior_hdt6_exposure,
            },
            "timeline_cuts": {
                "before_flight_um": None,
                "during_flight_days_10_15_30_um": [None, None, None],
                "after_flight_cuts_30_60_90_um": {"day_30": None, "day_60": "Sin medición", "day_90": None},
            },
        }

    delta_trt = round(req.oct_trt_current_um - req.oct_trt_baseline_um, 1)
    delta_axial = round((req.axial_length_mm or req.axial_baseline_mm) - req.axial_baseline_mm, 2)

    is_progressing = (
        req.scenario_model == "EMPEORA"
        or delta_trt >= 20.0
        or (req.papilledema_frisen is not None and req.papilledema_frisen >= 1)
    )

    operational_output = "Priorizar revisión" if is_progressing else "Listo para revisión"

    return {
        "case_id": req.case_id,
        "scenario_model": "EMPEORA" if is_progressing else "ESTABLE",
        "operational_output": operational_output,
        "fitness_verdict": "Aptitud no evaluada",
        "delta_trt_um": delta_trt,
        "delta_axial_mm": delta_axial,
        "biomarkers": {
            "oct_thickness_um": req.oct_trt_current_um,
            "axial_length_mm": req.axial_length_mm,
            "simulated_hyperopia_d": req.simulated_hyperopia_d,
            "papilledema_frisen": req.papilledema_frisen,
            "iop_mmhg": req.iop_mmhg,
            "prior_hdt6_exposure": req.prior_hdt6_exposure,
        },
        "timeline_cuts": {
            "before_flight_um": req.oct_trt_baseline_um,
            "during_flight_days_10_15_30_um": [
                round(req.oct_trt_baseline_um + delta_trt * 0.31, 1),
                round(req.oct_trt_baseline_um + delta_trt * 0.54, 1),
                req.oct_trt_current_um,
            ],
            "after_flight_cuts_30_60_90_um": {
                "day_30": round(req.oct_trt_baseline_um + delta_trt * 0.58, 1),
                "day_60": "Sin medición",
                "day_90": round(req.oct_trt_baseline_um + delta_trt * 0.19, 1),
            },
        },
    }
