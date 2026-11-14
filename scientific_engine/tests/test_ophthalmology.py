"""
Tests para el módulo científico de oftalmología y óptica
"""
import pytest
from app.ophthalmology.refraction import (
    spherical_equivalent,
    snellen_to_logmar,
    thibos_power_vectors,
    longitudinal_shift,
    validate_refraction_ranges
)
from app.ophthalmology.sans_risk import calculate_sans_risk

def test_spherical_equivalent():
    # SE = SPH + CYL/2
    assert spherical_equivalent(-2.0, -1.0) == -2.50
    assert spherical_equivalent(1.5, -0.5) == 1.25
    assert spherical_equivalent(0.0, 0.0) == 0.0

def test_snellen_to_logmar():
    # 20/20 -> 0.0
    assert snellen_to_logmar(20, 20) == 0.0
    # 20/40 -> log10(2) approx 0.301
    assert abs(snellen_to_logmar(20, 40) - 0.301) < 0.005
    # 20/200 -> log10(10) = 1.0
    assert snellen_to_logmar(20, 200) == 1.0

def test_thibos_vectors():
    vec = thibos_power_vectors(sphere=1.0, cylinder=-1.0, axis_degrees=90.0)
    assert "m_spherical_power" in vec
    assert "j0_orthogonal" in vec
    assert "j45_oblique" in vec
    assert vec["m_spherical_power"] == 0.5

def test_longitudinal_shift():
    shift = longitudinal_shift(baseline_se=0.0, current_se=1.25)
    assert shift["delta_spherical_equivalent"] == 1.25
    assert shift["is_hyperopic_shift"] is True
    assert shift["clinically_significant"] is True

def test_sans_risk_stratification():
    # Sin hallazgos
    low = calculate_sans_risk(hyperopic_shift=0.1, frisen_grade=0)
    assert low["risk_tier"] == "BAJO"
    assert low["risk_score"] < 0.20

    # Hallazgos moderados
    mod = calculate_sans_risk(hyperopic_shift=0.8, frisen_grade=1, rnfl_delta_microns=16.0)
    assert mod["risk_tier"] in ["MODERADO", "ELEVADO"]

    # Hallazgos severos
    crit = calculate_sans_risk(
        hyperopic_shift=1.75,
        frisen_grade=3,
        rnfl_delta_microns=38.0,
        has_globe_flattening=True,
        has_choroidal_folds=True
    )
    assert crit["risk_tier"] == "CRÍTICO"
    assert crit["risk_score"] >= 0.70
