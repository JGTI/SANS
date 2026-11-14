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
