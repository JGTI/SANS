"""
Tests para Machine Learning, Detección de Anomalías, Regresión Longitudinal y XAI
"""
import pytest
from app.machine_learning.anomaly_detection import OcularAnomalyDetector
from app.machine_learning.longitudinal_regression import fit_longitudinal_trend
from app.machine_learning.classifier_experiments import run_model_comparison_experiment
from app.machine_learning.explainability import ExplainabilityEngine
from app.data_quality.validator import audit_dataset_quality
from app.research_intelligence.semantic_search import ResearchIntelligenceEngine

def test_anomaly_detection_isolation_forest():
    detector = OcularAnomalyDetector(contamination=0.15, random_state=42)
    obs = [
        {"flight_day": 0, "hyperopic_shift": 0.0, "iop_mmhg": 15.0, "rnfl_microns": 95.0},
        {"flight_day": 30, "hyperopic_shift": 0.25, "iop_mmhg": 15.5, "rnfl_microns": 97.0},
        {"flight_day": 60, "hyperopic_shift": 0.50, "iop_mmhg": 16.0, "rnfl_microns": 102.0},
        {"flight_day": 90, "hyperopic_shift": 2.50, "iop_mmhg": 28.0, "rnfl_microns": 160.0},  # Anomalía extrema
        {"flight_day": 120, "hyperopic_shift": 0.75, "iop_mmhg": 15.8, "rnfl_microns": 106.0},
    ]
    res = detector.detect_anomalies(obs, algorithm="isolation_forest")
    assert res["total_observations"] == 5
    assert res["anomalies_detected"] >= 1
    # La observación 3 (FD90) debe marcarse o tener un score elevado
    assert any(r["is_anomaly"] for r in res["results"])

def test_longitudinal_trend_ols():
    days = [0, 30, 60, 90, 120]
    shifts = [0.0, 0.3, 0.7, 1.1, 1.5]
    res = fit_longitudinal_trend(flight_days=days, values=shifts, forecast_days=[150, 180])
    assert res["metrics"]["r_squared"] > 0.95
    assert res["metrics"]["slope_per_day"] > 0
    assert len(res["forecast_points"]) == 2

def test_classifier_benchmark():
    res = run_model_comparison_experiment(n_astronauts=25, n_splits=3, random_state=42)
    assert "logistic_regression" in res["models_evaluated"]
    assert "random_forest" in res["models_evaluated"]
    assert "gradient_boosting" in res["models_evaluated"]
    assert res["models_evaluated"]["random_forest"]["metrics"]["accuracy"] > 0.70

def test_explainability_engine():
    sample = {
        "hyperopic_shift": 1.25,
        "rnfl_delta_microns": 22.0,
        "iop_mmhg": 17.5,
        "flight_day": 90,
        "age": 45,
        "baseline_se": 0.0,
    }
    exp = ExplainabilityEngine.explain_prediction(
        sample=sample,
        prediction_label="Riesgo SANS Elevado",
        predicted_probability=0.88
    )
    assert exp["prediction"]["probability"] == 0.88
    assert len(exp["feature_attributions"]) == 6
    assert exp["primary_driving_factor"] != ""

def test_data_quality_audit():
    data = [
        {"sphere": 0.0, "cylinder": -0.5, "axis": 90, "iop_mmhg": 15.0, "rnfl_microns": 95.0, "flight_day": 0},
        {"sphere": 0.75, "cylinder": -0.5, "axis": 90, "iop_mmhg": 16.0, "rnfl_microns": 105.0, "flight_day": 30},
        {"sphere": 99.0, "cylinder": -0.5, "axis": 90, "iop_mmhg": None, "rnfl_microns": 115.0, "flight_day": 60},  # Out of range & null
    ]
    audit = audit_dataset_quality(data)
    assert audit["total_records_audited"] == 3
    assert audit["total_missing_values"] >= 1
    assert audit["total_range_violations"] >= 1

def test_research_semantic_search():
    engine = ResearchIntelligenceEngine()
    res = engine.search("hyperopic shift and optic disc edema in microgravity", top_k=2)
    assert res["results_found"] >= 1
    assert "NASA-HRP-2020-SANS" in [c["id"] for c in res["citations"]]
