"""
SPACE VISION Health & Diagnostics Endpoint
"""
from fastapi import APIRouter
import platform
import sys
import numpy as np
import scipy
import sklearn
import pandas as pd
from app.config import settings

router = APIRouter()

@router.get("/health", summary="Estado del motor científico y versiones de librerías")
def get_health_status():
    return {
        "status": "ONLINE",
        "service": settings.app_name,
        "version": settings.app_version,
        "environment": settings.app_env,
        "system": {
            "os": platform.system(),
            "architecture": platform.machine(),
            "python_version": sys.version.split()[0],
        },
        "scientific_stack": {
            "numpy": np.__version__,
            "scipy": scipy.__version__,
            "scikit_learn": sklearn.__version__,
            "pandas": pd.__version__,
        },
        "modules_active": [
            "Ophthalmology Core (Thibos & LogMAR)",
            "SANS Clinical Risk Stratification",
            "Longitudinal Digital Twin Simulator (Scenarios A-E)",
            "Machine Learning Anomaly Detection (Isolation Forest, LOF, One-Class SVM)",
            "Longitudinal Trend Regression & 95% CI",
            "Supervised Classifier Benchmark (RF, GBM, Logistic)",
            "AI Explainability Center (Feature Attributions)",
            "Data Quality Observatory (Completeness & Ranges)",
            "NASA Research Intelligence (Semantic TF-IDF RAG)",
            "Computer Vision & OCT TSNIT Preprocessor",
        ],
    }
