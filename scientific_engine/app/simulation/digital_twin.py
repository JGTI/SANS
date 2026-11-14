"""
SPACE VISION Longitudinal Astronaut Digital Twin Simulator
Generador de cohortes sintéticas reproducibles con semillas matemáticas.
Modela la biomecánica ocular y evolución temporal bajo microgravedad sostenida.
"""
import numpy as np
from typing import Dict, Any, List, Optional
from app.ophthalmology.refraction import spherical_equivalent, thibos_power_vectors, snellen_to_logmar
from app.ophthalmology.sans_risk import calculate_sans_risk

SCENARIOS = {
    "A": {
        "name": "Escenario A: Estabilidad Visual",
        "description": "Fluctuaciones normofisiológicas menores. Sin desarrollo de síndrome SANS significativo.",
        "max_hyperopic_shift": 0.25,
        "max_frisen": 0,
        "max_rnfl_delta": 4.0,
        "has_flattening": False,
        "has_folds": False,
        "has_cotton": False,
    },
    "B": {
        "name": "Escenario B: Desplazamiento Refractivo Progresivo",
        "description": "Evolución clásica de SANS con hipermetropización progresiva a partir del día 30 por aplanamiento posterior del globo.",
        "max_hyperopic_shift": 1.75,
        "max_frisen": 2,
        "max_rnfl_delta": 28.0,
        "has_flattening": True,
        "has_folds": False,
        "has_cotton": False,
    },
    "C": {
        "name": "Escenario C: Cambio Refractivo Transitorio",
        "description": "Respuesta aguda inicial en las primeras 6 semanas con estabilización biomecánica posterior.",
        "max_hyperopic_shift": 0.75,
        "max_frisen": 1,
        "max_rnfl_delta": 12.0,
        "has_flattening": False,
        "has_folds": False,
        "has_cotton": False,
    },
    "D": {
        "name": "Escenario D: Cambios Visuales Persistentes",
        "description": "Hipermetropía sostenida y aplanamiento que persisten incluso tras retorno simulado a 1G.",
        "max_hyperopic_shift": 1.50,
        "max_frisen": 1,
        "max_rnfl_delta": 18.0,
        "has_flattening": True,
        "has_folds": True,
        "has_cotton": False,
    },
    "E": {
        "name": "Escenario E: Combinación Funcional y Estructural Severa",
        "description": "Presentación neuroftalmológica completa: hipermetropía pronunciada, edema Frisén Grado 2/3, pliegues coroideos y aplanamiento marcado.",
        "max_hyperopic_shift": 2.25,
        "max_frisen": 3,
        "max_rnfl_delta": 45.0,
        "has_flattening": True,
        "has_folds": True,
        "has_cotton": True,
    },
}

class AstronautDigitalTwinSimulator:
    """
    Simulador determinista de gemelos digitales de astronautas en microgravedad.
    """
    def __init__(self, seed: Optional[int] = 42):
        self.seed = seed
        self.rng = np.random.default_rng(seed)

    def generate_astronaut_profile(self, astronaut_id: str = "ASTRO-SYNTH-01", name: str = "Alex Mercer") -> Dict[str, Any]:
        """
        Genera perfil basal fisiológico antes del vuelo.
        """
        age = int(self.rng.integers(35, 56))
        gender = self.rng.choice(["Male", "Female"])
        baseline_od_sph = round(float(self.rng.normal(-0.5, 0.75)), 2)
        baseline_od_cyl = round(float(self.rng.choice([0.0, -0.25, -0.5, -0.75])), 2)
        baseline_od_axis = int(self.rng.integers(0, 180))
        baseline_os_sph = round(baseline_od_sph + self.rng.normal(0, 0.25), 2)
        baseline_os_cyl = round(baseline_od_cyl, 2)
        baseline_os_axis = int(self.rng.integers(0, 180))
        
        return {
            "astronaut_id": astronaut_id,
            "name": name,
            "age": age,
            "gender": gender,
            "mission": "Artemis III Lunar / ISS Simulated Cohort",
            "baseline_od": {
                "sphere": baseline_od_sph,
                "cylinder": baseline_od_cyl,
                "axis": baseline_od_axis,
                "spherical_equivalent": spherical_equivalent(baseline_od_sph, baseline_od_cyl),
                "snellen": "20/20",
                "logmar": 0.0,
                "iop_mmhg": round(float(self.rng.uniform(13.5, 16.5)), 1),
                "rnfl_microns": round(float(self.rng.uniform(92.0, 98.0)), 1),
            },
            "baseline_os": {
                "sphere": baseline_os_sph,
                "cylinder": baseline_os_cyl,
                "axis": baseline_os_axis,
                "spherical_equivalent": spherical_equivalent(baseline_os_sph, baseline_os_cyl),
                "snellen": "20/20",
                "logmar": 0.0,
                "iop_mmhg": round(float(self.rng.uniform(13.5, 16.5)), 1),
                "rnfl_microns": round(float(self.rng.uniform(92.0, 98.0)), 1),
            },
            "metadata": {
                "seed": self.seed,
                "synthetic": True,
                "model": "SpaceVision-DigitalTwin-v1.0"
            }
        }

    def simulate_mission_timeline(
        self,
        scenario_key: str = "B",
        mission_days: int = 180,
        sample_step_days: int = 15,
        baseline_profile: Optional[Dict[str, Any]] = None
    ) -> Dict[str, Any]:
        """
        Ejecuta la evolución temporal del astronauta a través de la misión espacial.
        """
        scenario = SCENARIOS.get(scenario_key.upper(), SCENARIOS["B"])
        if baseline_profile is None:
            baseline_profile = self.generate_astronaut_profile()
            
        base_se_od = baseline_profile["baseline_od"]["spherical_equivalent"]
        base_rnfl_od = baseline_profile["baseline_od"]["rnfl_microns"]
        base_iop_od = baseline_profile["baseline_od"]["iop_mmhg"]
        
        timeline_points = []
        days_array = list(range(0, mission_days + 1, sample_step_days))
        
        for day in days_array:
            t = day / float(max(1, mission_days))
            # Dinámica sigmoid de progresión biomecánica
            # La redistribución cefálica de fluidos se acelera entre FD15 y FD60
            progress_factor = 1.0 / (1.0 + np.exp(-10.0 * (t - 0.25)))
            
            # Cálculo de hipermetropía según escenario
            if scenario_key.upper() == "C":
                # Transitorio: pico en FD45 y luego meseta o leve descenso
                peak_factor = np.exp(-0.5 * ((day - 45) / 30.0)**2)
                cur_shift = float(scenario["max_hyperopic_shift"] * peak_factor)
            else:
                cur_shift = float(scenario["max_hyperopic_shift"] * progress_factor)
            
            # Adición de ruido microfisiológico determinista
            noise_shift = float(self.rng.normal(0, 0.04))
            total_shift = max(0.0, round(cur_shift + noise_shift, 2))
            
            # Variación de RNFL (edema de papila)
            rnfl_delta = round(float(scenario["max_rnfl_delta"] * progress_factor + self.rng.normal(0, 0.5)), 1)
            current_rnfl = round(base_rnfl_od + rnfl_delta, 1)
            
            # Escala de Frisén
            if total_shift > 1.8 or rnfl_delta > 35:
                frisen = min(scenario["max_frisen"], 3)
            elif total_shift > 1.0 or rnfl_delta > 20:
                frisen = min(scenario["max_frisen"], 2)
            elif total_shift > 0.40 or rnfl_delta > 8:
                frisen = min(scenario["max_frisen"], 1)
            else:
                frisen = 0
                
            # Presión Intraocular con elevación cefálica inicial (+2.5 a +4 mmHg)
            iop_shift = 3.2 * np.exp(-day / 40.0) + (1.2 if total_shift > 0.5 else 0.0)
            current_iop = round(base_iop_od + iop_shift + float(self.rng.normal(0, 0.3)), 1)
            
            # Esfera actual
            current_sph = round(baseline_profile["baseline_od"]["sphere"] + total_shift, 2)
            current_cyl = baseline_profile["baseline_od"]["cylinder"]
            current_axis = baseline_profile["baseline_od"]["axis"]
            current_se = spherical_equivalent(current_sph, current_cyl)
            
            # Agudeza visual (disminuye en visión cercana o si hipermetropía no compensada)
            if total_shift >= 1.50:
                snellen_denom = 30
            elif total_shift >= 0.75:
                snellen_denom = 25
            else:
                snellen_denom = 20
            logmar_val = snellen_to_logmar(20, snellen_denom)
            
            # Signos estructurales presentes a partir de umbrales
            flattening_active = scenario["has_flattening"] and (total_shift >= 0.75 or day >= 45)
            folds_active = scenario["has_folds"] and (total_shift >= 1.25 or day >= 60)
            cotton_active = scenario["has_cotton"] and (total_shift >= 1.75 and day >= 75)
            
            # Evaluación del riesgo algorítmico SANS
            risk_eval = calculate_sans_risk(
                hyperopic_shift=total_shift,
                frisen_grade=frisen,
                rnfl_delta_microns=rnfl_delta,
                has_globe_flattening=flattening_active,
                has_choroidal_folds=folds_active,
                has_cotton_wool_spots=cotton_active,
                flight_days=day
            )
            
            vectors = thibos_power_vectors(current_sph, current_cyl, current_axis)
            
            point = {
                "flight_day": day,
                "flight_day_label": f"FD{day}",
                "refraction_od": {
                    "sphere": current_sph,
                    "cylinder": current_cyl,
                    "axis": current_axis,
                    "spherical_equivalent": current_se,
                    "hyperopic_shift": total_shift,
                    "snellen": f"20/{snellen_denom}",
                    "logmar": logmar_val,
                    "vectors": vectors,
                },
                "biometrics": {
                    "iop_mmhg": current_iop,
                    "rnfl_microns": current_rnfl,
                    "rnfl_delta_microns": rnfl_delta,
                    "frisen_grade": frisen,
                    "globe_flattening": flattening_active,
                    "choroidal_folds": folds_active,
                    "cotton_wool_spots": cotton_active,
                },
                "sans_risk": risk_eval,
            }
            timeline_points.append(point)
            
        return {
            "scenario": {
                "key": scenario_key.upper(),
                "name": scenario["name"],
                "description": scenario["description"],
            },
            "astronaut": baseline_profile,
            "mission_duration_days": mission_days,
            "total_evaluations": len(timeline_points),
            "timeline": timeline_points,
            "metadata": {
                "seed": self.seed,
                "synthetic_cohort": True,
                "scientific_disclaimer": "Demostración matemática simulada. No representa datos clínicos reales de pacientes humanos."
            }
        }
