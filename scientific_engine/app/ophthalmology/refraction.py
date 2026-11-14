"""
SPACE VISION Ophthalmology Scientific Module
Matemática óptica pura, descomposición vectorial de Thibos y agudeza visual.
Sin aproximaciones superficiales de IA para operaciones deterministas exactas.
"""
import math
from typing import Dict, Any, List

def spherical_equivalent(sphere: float, cylinder: float) -> float:
    """
    Calcula el Equivalente Esférico (SE):
    SE = SPH + (CYL / 2)
    """
    return round(float(sphere) + (float(cylinder) / 2.0), 4)

def snellen_to_logmar(numerator: int, denominator: int) -> float:
    """
    Convierte notación Snellen a LogMAR:
    LogMAR = log10(denominator / numerator)
    Ejemplo: 20/20 = log10(1) = 0.000 (normal)
             20/40 = log10(2) = 0.301
    """
    if numerator <= 0 or denominator <= 0:
        raise ValueError("Los valores de Snellen deben ser mayores a cero.")
    return round(math.log10(float(denominator) / float(numerator)), 4)

def thibos_power_vectors(sphere: float, cylinder: float, axis_degrees: float) -> Dict[str, float]:
    """
    Descomposición vectorial de refracción de Thibos & Horner (1997):
    M = SPH + (CYL / 2)                (Poder esférico neto)
    J0 = - (CYL / 2) * cos(2 * alpha)   (Astigmatismo cruzado Jackson a 90°/180°)
    J45 = - (CYL / 2) * sin(2 * alpha)  (Astigmatismo cruzado Jackson oblicuo a 45°/135°)
    B = sqrt(M^2 + J0^2 + J45^2)        (Vector de desenfoque total)
    """
    alpha = math.radians(float(axis_degrees))
    half_cyl = float(cylinder) / 2.0
    
    m = float(sphere) + half_cyl
    j0 = - half_cyl * math.cos(2.0 * alpha)
    j45 = - half_cyl * math.sin(2.0 * alpha)
    b_blur = math.sqrt(m**2 + j0**2 + j45**2)
    
    return {
        "m_spherical_power": round(m, 4),
        "j0_orthogonal": round(j0, 4),
        "j45_oblique": round(j45, 4),
        "overall_blur_vector": round(b_blur, 4),
        "cylinder": round(float(cylinder), 4),
        "axis_degrees": round(float(axis_degrees), 2),
    }

def longitudinal_shift(baseline_se: float, current_se: float) -> Dict[str, Any]:
    """
    Calcula el desplazamiento refractivo longitudinal (Hyperopic Shift):
    Delta SE = SE_actual - SE_basal
    Valores positivos indican desplazamiento hipermetrópico característico de SANS.
    """
    delta = round(float(current_se) - float(baseline_se), 4)
    is_hyperopic = delta > 0.0
    is_clinically_significant = delta >= 0.50  # Umbral estándar NASA SANS (+0.50 D)
    
    return {
        "delta_spherical_equivalent": delta,
        "is_hyperopic_shift": is_hyperopic,
        "clinically_significant": is_clinically_significant,
        "classification": (
            "Hipermetropización Severa (>= +1.50 D)" if delta >= 1.50 else
            "Hipermetropización Moderada (+0.75 a +1.49 D)" if delta >= 0.75 else
            "Hipermetropización Leve (+0.50 a +0.74 D)" if delta >= 0.50 else
            "Variación Fisiológica Estable (-0.49 a +0.49 D)" if delta >= -0.49 else
            "Miopización / Desplazamiento Negativo (< -0.50 D)"
        )
    }

def validate_refraction_ranges(
    sphere: float,
    cylinder: float,
    axis: float,
    iop: float = 15.0,
    rnfl_thickness: float = 95.0
) -> Dict[str, Any]:
    """
    Validación de rangos biológicamente factibles y detección de anomalías de entrada.
    """
    errors: List[str] = []
    warnings: List[str] = []
    
    if not (-25.0 <= sphere <= 25.0):
        errors.append(f"Esfera fuera de rango clínico factible (-25.0 D a +25.0 D): {sphere}")
    if not (-12.0 <= cylinder <= 0.0):
        # Convención oftalmológica en cilindro negativo
        if 0.0 < cylinder <= 12.0:
            warnings.append(f"Cilindro expresado en signo positivo (+{cylinder} D). Se recomienda transposición a cilindro negativo.")
        else:
            errors.append(f"Cilindro fuera de rango clínico factible: {cylinder}")
    if not (0.0 <= axis <= 180.0):
        errors.append(f"Eje astigmático fuera de rango angular (0° a 180°): {axis}")
    if not (5.0 <= iop <= 60.0):
        warnings.append(f"Presión intraocular (IOP) atípica ({iop} mmHg). Rango fisiológico esperado 10-21 mmHg.")
    if not (40.0 <= rnfl_thickness <= 250.0):
        warnings.append(f"Grosor de capa de fibras nerviosas retinianas (RNFL) atípico ({rnfl_thickness} µm). Rango normal 75-115 µm.")
        
    return {
        "is_valid": len(errors) == 0,
        "errors": errors,
        "warnings": warnings,
    }
