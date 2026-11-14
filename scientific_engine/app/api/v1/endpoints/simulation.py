"""
SPACE VISION Simulation Endpoints
"""
from fastapi import APIRouter, Depends
from pydantic import BaseModel, Field
from typing import Optional
from app.simulation.digital_twin import AstronautDigitalTwinSimulator, SCENARIOS
from app.core.security import verify_internal_token

router = APIRouter(dependencies=[Depends(verify_internal_token)])

class SimulationRequest(BaseModel):
    scenario_key: str = Field("B", description="Clave de escenario (A, B, C, D o E)")
    seed: int = Field(42, description="Semilla determinista reproducible")
    mission_days: int = Field(180, ge=30, le=720, description="Duración total de la misión en días")
    step_days: int = Field(15, ge=5, le=60, description="Paso temporal entre evaluaciones")
    astronaut_id: Optional[str] = "ASTRO-CDR-ALEX"
    astronaut_name: Optional[str] = "CDR Alex Mercer"

@router.get("/scenarios", summary="Lista de escenarios de simulación disponibles")
def list_scenarios():
    return {
        "total_scenarios": len(SCENARIOS),
        "scenarios": SCENARIOS,
    }

@router.post("/digital-twin", summary="Genera la evolución temporal del gemelo digital de astronauta")
def run_digital_twin_simulation(req: SimulationRequest):
    sim = AstronautDigitalTwinSimulator(seed=req.seed)
    profile = sim.generate_astronaut_profile(
        astronaut_id=req.astronaut_id or "ASTRO-01",
        name=req.astronaut_name or "Simulated Astronaut"
    )
    result = sim.simulate_mission_timeline(
        scenario_key=req.scenario_key,
        mission_days=req.mission_days,
        sample_step_days=req.step_days,
        baseline_profile=profile
    )
    return result
