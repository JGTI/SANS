"""
SPACE VISION Python Scientific Engine & ML Microservice
FastAPI Entrypoint
"""
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from app.config import settings

# Routers
from app.api.v1.endpoints.health import router as health_router
from app.api.v1.endpoints.ophthalmology import router as ophthalmology_router
from app.api.v1.endpoints.simulation import router as simulation_router
from app.api.v1.endpoints.models import router as models_router
from app.api.v1.endpoints.data_quality import router as data_quality_router
from app.api.v1.endpoints.research import router as research_router
from app.api.v1.endpoints.computer_vision import router as cv_router

app = FastAPI(
    title=settings.app_name,
    version=settings.app_version,
    description="Motor de computación científica, simulación biomecánica, machine learning explicable y procesamiento oftalmológico para SPACE VISION (NASA Space Apps Challenge 2026).",
    docs_url="/docs",
    redoc_url="/redoc",
)

# CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=settings.cors_origins,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Montar Rutas Versionadas v1
app.include_router(health_router, prefix="/api/v1", tags=["Health & Diagnostics"])
app.include_router(ophthalmology_router, prefix="/api/v1/ophthalmology", tags=["Ophthalmology & Optics"])
app.include_router(simulation_router, prefix="/api/v1/simulation", tags=["Astronaut Digital Twin"])
app.include_router(models_router, prefix="/api/v1/models", tags=["Machine Learning & XAI"])
app.include_router(data_quality_router, prefix="/api/v1/data-quality", tags=["Data Quality Observatory"])
app.include_router(research_router, prefix="/api/v1/research", tags=["NASA Research Intelligence"])
app.include_router(cv_router, prefix="/api/v1/computer-vision", tags=["Computer Vision & OCT"])

@app.get("/", summary="Root status")
def root():
    return {
        "service": settings.app_name,
        "version": settings.app_version,
        "status": "OPERATIONAL",
        "documentation": "/docs",
        "api_v1": "/api/v1/health",
    }
