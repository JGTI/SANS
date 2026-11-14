"""
Tests de integración para los endpoints de FastAPI
"""
import pytest
from fastapi.testclient import TestClient
from app.main import app
from app.config import settings

client = TestClient(app)
AUTH_HEADERS = {"Authorization": f"Bearer {settings.secret_token}"}

def test_root_and_health():
    res = client.get("/")
    assert res.status_code == 200
    assert res.json()["status"] == "OPERATIONAL"

    health = client.get("/api/v1/health")
    assert health.status_code == 200
    data = health.json()
    assert data["status"] == "ONLINE"
    assert "numpy" in data["scientific_stack"]

def test_security_bearer_enforcement():
    # Sin cabecera debe responder 401
    unauth = client.post("/api/v1/ophthalmology/evaluate", json={
        "sphere": 1.0, "cylinder": -0.5, "axis": 90,
        "snellen_numerator": 20, "snellen_denominator": 20
    })
    assert unauth.status_code == 401

    # Token incorrecto debe responder 403
    bad = client.post("/api/v1/ophthalmology/evaluate", json={
        "sphere": 1.0, "cylinder": -0.5, "axis": 90,
        "snellen_numerator": 20, "snellen_denominator": 20
    }, headers={"Authorization": "Bearer wrong-token"})
    assert bad.status_code == 403

def test_ophthalmology_evaluate_endpoint():
    res = client.post("/api/v1/ophthalmology/evaluate", json={
        "sphere": 1.50,
        "cylinder": -0.50,
        "axis": 90.0,
        "snellen_numerator": 20,
        "snellen_denominator": 25,
        "baseline_sphere": 0.0,
        "baseline_cylinder": -0.50,
        "frisen_grade": 1,
        "rnfl_thickness": 110.0,
        "has_globe_flattening": True,
        "flight_day": 60,
    }, headers=AUTH_HEADERS)
    assert res.status_code == 200
    body = res.json()
    assert body["spherical_equivalent"] == 1.25
    assert body["sans_risk"]["risk_tier"] in ["MODERADO", "ELEVADO", "CRÍTICO"]

def test_digital_twin_endpoint():
    res = client.post("/api/v1/simulation/digital-twin", json={
        "scenario_key": "B",
        "seed": 42,
        "mission_days": 60,
        "step_days": 15
    }, headers=AUTH_HEADERS)
    assert res.status_code == 200
    body = res.json()
    assert body["scenario"]["key"] == "B"
    assert len(body["timeline"]) == 5

def test_model_registry_endpoint():
    res = client.get("/api/v1/models/registry", headers=AUTH_HEADERS)
    assert res.status_code == 200
    assert res.json()["total_models"] >= 4

def test_explainability_endpoint():
    res = client.post("/api/v1/models/explain", json={
        "sample": {
            "hyperopic_shift": 1.25,
            "rnfl_delta_microns": 20.0,
            "iop_mmhg": 18.0,
            "flight_day": 60,
            "age": 44,
            "baseline_se": 0.0
        },
        "predicted_label": "Riesgo SANS Elevado",
        "probability": 0.85
    }, headers=AUTH_HEADERS)
    assert res.status_code == 200
    assert "feature_attributions" in res.json()

def test_research_search_endpoint():
    res = client.get("/api/v1/research/semantic-search?q=SANS+evidence+report&limit=2", headers=AUTH_HEADERS)
    assert res.status_code == 200
    assert res.json()["results_found"] >= 1

def test_cirrus_oct_workstation_endpoint():
    res = client.post("/api/v1/computer-vision/cirrus-workstation", json={
        "astronaut_code": "ASTRO-01",
        "astronaut_name": "CDR Alex Mercer",
        "flight_day": 45,
        "scenario": "B"
    }, headers=AUTH_HEADERS)
    assert res.status_code == 200
    data = res.json()
    assert data["protocol"] == "Macular Cube 512x128"
    assert "summary_table" in data
    assert "etdrs_grid" in data
    assert "b_scans" in data
    assert "od" in data["etdrs_grid"]
    assert "os" in data["etdrs_grid"]
