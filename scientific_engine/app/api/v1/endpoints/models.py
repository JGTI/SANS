"""
SPACE VISION Machine Learning & Statistical Endpoints
"""
from fastapi import APIRouter, Depends
from pydantic import BaseModel, Field
from typing import Optional, List, Dict, Any
from app.machine_learning.anomaly_detection import OcularAnomalyDetector
from app.machine_learning.longitudinal_regression import fit_longitudinal_trend
from app.machine_learning.classifier_experiments import run_model_comparison_experiment
from app.machine_learning.explainability import ExplainabilityEngine
from app.machine_learning.model_registry import ModelRegistry
from app.core.security import verify_internal_token

router = APIRouter(dependencies=[Depends(verify_internal_token)])

class AnomalyDetectRequest(BaseModel):
    algorithm: str = Field("isolation_forest", description="isolation_forest, local_outlier_factor, one_class_svm, robust_zscore")
    contamination: float = Field(0.10, ge=0.01, le=0.40)
    observations: List[Dict[str, Any]]

class LongitudinalTrendRequest(BaseModel):
    flight_days: List[int]
    values: List[float]
    forecast_days: Optional[List[int]] = None
    degree: int = Field(1, ge=1, le=2)

class BenchmarkRequest(BaseModel):
    n_astronauts: int = Field(60, ge=20, le=200)
    n_splits: int = Field(5, ge=2, le=10)
    random_state: int = Field(42)

class ExplainRequest(BaseModel):
    sample: Dict[str, float]
    predicted_label: str = Field("Riesgo SANS Elevado")
    probability: float = Field(0.82, ge=0.0, le=1.0)
    model_name: Optional[str] = "SpaceVision-RandomForest-v2.1"

@router.get("/registry", summary="Catálogo y linaje de modelos de IA registrados")
def get_model_registry():
    return {
        "total_models": len(ModelRegistry.list_models()),
        "models": ModelRegistry.list_models(),
    }

@router.post("/benchmark", summary="Ejecuta benchmark experimental comparativo de modelos con validación cruzada")
def run_benchmark(req: BenchmarkRequest):
    return run_model_comparison_experiment(
        n_astronauts=req.n_astronauts,
        n_splits=req.n_splits,
        random_state=req.random_state
    )

@router.post("/detect-anomalies", summary="Detecta observaciones atípicas mediante Isolation Forest, LOF o One-Class SVM")
def detect_anomalies(req: AnomalyDetectRequest):
    detector = OcularAnomalyDetector(contamination=req.contamination)
    return detector.detect_anomalies(
        observations=req.observations,
        algorithm=req.algorithm
    )

@router.post("/longitudinal-trend", summary="Ajuste de regresión estadística con intervalos de confianza del 95%")
def calculate_longitudinal_trend(req: LongitudinalTrendRequest):
    return fit_longitudinal_trend(
        flight_days=req.flight_days,
        values=req.values,
        forecast_days=req.forecast_days,
        degree=req.degree
    )

@router.post("/explain", summary="SPACE VISION AI Explainability Center: atribución de variables y justificación técnica")
def explain_inference(req: ExplainRequest):
    return ExplainabilityEngine.explain_prediction(
        sample=req.sample,
        prediction_label=req.predicted_label,
        predicted_probability=req.probability,
        model_name=req.model_name or "SpaceVision-RandomForest-v2.1"
    )
