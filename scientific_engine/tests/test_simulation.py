"""
Tests para el simulador de gemelos digitales de astronautas
"""
import pytest
from app.simulation.digital_twin import AstronautDigitalTwinSimulator, SCENARIOS

def test_digital_twin_reproducibility():
    sim1 = AstronautDigitalTwinSimulator(seed=123)
    p1 = sim1.generate_astronaut_profile()
    t1 = sim1.simulate_mission_timeline(scenario_key="B", mission_days=60, baseline_profile=p1)

    sim2 = AstronautDigitalTwinSimulator(seed=123)
    p2 = sim2.generate_astronaut_profile()
    t2 = sim2.simulate_mission_timeline(scenario_key="B", mission_days=60, baseline_profile=p2)

    assert p1["baseline_od"]["sphere"] == p2["baseline_od"]["sphere"]
    assert len(t1["timeline"]) == len(t2["timeline"])
    assert t1["timeline"][-1]["refraction_od"]["hyperopic_shift"] == t2["timeline"][-1]["refraction_od"]["hyperopic_shift"]

def test_all_scenarios_execute():
    sim = AstronautDigitalTwinSimulator(seed=99)
    for key in ["A", "B", "C", "D", "E"]:
        res = sim.simulate_mission_timeline(scenario_key=key, mission_days=90, sample_step_days=30)
        assert res["scenario"]["key"] == key
        assert len(res["timeline"]) > 0
        assert res["metadata"]["synthetic_cohort"] is True
