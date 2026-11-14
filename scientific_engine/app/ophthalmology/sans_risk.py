"""
SPACE VISION SANS Clinical Risk Stratification
Estratificación explicable de riesgo de Síndrome Neuroocular Asociado al Vuelo Espacial.
Basado en criterios del NASA Human Research Program (HRP) y Escala de Frisén.
"""
from typing import Dict, Any, List

def calculate_sans_risk(
    hyperopic_shift: float,
    frisen_grade: int,
    rnfl_delta_microns: float = 0.0,
    has_globe_flattening: bool = False,
    has_choroidal_folds: bool = False,
    has_cotton_wool_spots: bool = False,
    flight_days: int = 0
) -> Dict[str, Any]:
    """
    Estratifica el riesgo de SANS de forma multivariable, transparente y explicable.
    
    Puntaje ponderado (0 a 100):
    - Desplazamiento hipermetrópico (hasta 35 pts):
      > +1.50 D: 35 pts, +1.00 D: 25 pts, +0.50 D: 15 pts
    - Edema de papila / Frisén (hasta 30 pts):
      Grado 0: 0 pts, Grado 1: 10 pts, Grado 2: 20 pts, Grado >=3: 30 pts
    - Incremento de RNFL OCT (hasta 15 pts):
      > +30 µm: 15 pts, +15 a +30 µm: 10 pts, +5 a +14 µm: 5 pts
    - Aplanamiento posterior del globo (10 pts)
    - Pliegues coroideos (5 pts)
    - Manchas algodonosas (5 pts)
    """
    score = 0.0
    evidence: List[str] = []
    contributions: Dict[str, float] = {}
    
    # 1. Componente Refractivo
    shift = max(0.0, float(hyperopic_shift))
    if shift >= 1.50:
        c_shift = 35.0
        evidence.append(f"Hipermetropización severa ({shift:+.2f} D). Indicador mayor de aplanamiento axial.")
    elif shift >= 1.00:
        c_shift = 25.0
        evidence.append(f"Hipermetropización moderada ({shift:+.2f} D).")
    elif shift >= 0.50:
        c_shift = 15.0
        evidence.append(f"Hipermetropización leve en umbral NASA ({shift:+.2f} D).")
    elif shift > 0.25:
        c_shift = 5.0
        evidence.append(f"Desplazamiento hipermetrópico incipiente ({shift:+.2f} D).")
    else:
        c_shift = 0.0
        evidence.append("Refracción esférica estable respecto al valor basal pre-vuelo.")
    score += c_shift
    contributions["hyperopic_shift"] = c_shift

    # 2. Edema de Disco Óptico (Escala de Frisén)
    fg = int(frisen_grade)
    if fg >= 3:
        c_frisen = 30.0
        evidence.append(f"Edema de disco óptico avanzado (Frisén Grado {fg}). Obscurecimiento vascular severo.")
    elif fg == 2:
        c_frisen = 20.0
        evidence.append("Edema de disco moderado (Frisén Grado 2). Elevación circular completa del borde.")
    elif fg == 1:
        c_frisen = 10.0
        evidence.append("Edema de disco óptico incipiente (Frisén Grado 1). Borramiento del polo nasal.")
    else:
        c_frisen = 0.0
        evidence.append("Borde de disco óptico nítido (Frisén Grado 0 - Normal).")
    score += c_frisen
    contributions["frisen_papilledema"] = c_frisen

    # 3. Engrosamiento de Capa de Fibras Nerviosas (RNFL en OCT)
    r_delta = float(rnfl_delta_microns)
    if r_delta >= 30.0:
        c_rnfl = 15.0
        evidence.append(f"Engrosamiento severo de RNFL (+{r_delta:.1f} µm en OCT).")
    elif r_delta >= 15.0:
        c_rnfl = 10.0
        evidence.append(f"Engrosamiento significativo de RNFL (+{r_delta:.1f} µm en OCT).")
    elif r_delta >= 5.0:
        c_rnfl = 5.0
        evidence.append(f"Variación leve en grosor de RNFL (+{r_delta:.1f} µm).")
    else:
        c_rnfl = 0.0
    score += c_rnfl
    contributions["rnfl_thickening"] = c_rnfl

    # 4. Signos Estructurales
    c_flattening = 10.0 if has_globe_flattening else 0.0
    if has_globe_flattening:
        evidence.append("Aplanamiento posterior del globo ocular confirmado por ecografía/OCT.")
    score += c_flattening
    contributions["posterior_globe_flattening"] = c_flattening

    c_folds = 5.0 if has_choroidal_folds else 0.0
    if has_choroidal_folds:
        evidence.append("Pliegues coroideos visibles en angiografía o fondo de ojo.")
    score += c_folds
    contributions["choroidal_folds"] = c_folds

    c_cotton = 5.0 if has_cotton_wool_spots else 0.0
    if has_cotton_wool_spots:
        evidence.append("Manchas algodonosas (microinfartos de axones en retina).")
    score += c_cotton
    contributions["cotton_wool_spots"] = c_cotton

    # Normalización del Score a escala 0.0 - 1.0
    normalized_score = round(min(1.0, score / 100.0), 3)

    # Nivel de Riesgo
    if normalized_score >= 0.70:
        tier = "CRÍTICO"
        color = "#ef4444"
        recommendation = "Protocolo de contramedida inmediata: presurización con chasis de presión negativa inferior (LBNP), ajuste ergonómico de pantallas y consulta neurooftalmológica urgente."
    elif normalized_score >= 0.45:
        tier = "ELEVADO"
        color = "#f97316"
        recommendation = "Intensificar monitoreo visual cada 7 días; prescribir lentes ajustables de astronauta (Space Specs) para compensar la hipermetropía."
    elif normalized_score >= 0.20:
        tier = "MODERADO"
        color = "#eab308"
        recommendation = "Monitoreo longitudinal quincenal de agudeza visual y grosor RNFL en OCT orbital."
    else:
        tier = "BAJO"
        color = "#22c55e"
        recommendation = "Mantener protocolo estándar de telemedicina visual orbital mensual."

    return {
        "risk_score": normalized_score,
        "risk_percentage": round(normalized_score * 100.0, 1),
        "risk_tier": tier,
        "badge_color": color,
        "recommendation": recommendation,
        "evidence_rationale": evidence,
        "factor_contributions": contributions,
        "flight_days": flight_days,
        "scientific_disclaimer": "Estratificación de riesgo probabilística algorítmica con propósitos de investigación aeroespacial. No constituye diagnóstico médico clínico por sí sola."
    }
