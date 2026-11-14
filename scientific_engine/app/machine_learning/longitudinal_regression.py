"""
SPACE VISION Longitudinal Statistical Regression & Confidence Intervals
Ajuste polinomial, regresión estadística, intervalos de confianza del 95% y proyección de tendencias.
"""
import numpy as np
from scipy import stats
from typing import Dict, Any, List

def fit_longitudinal_trend(
    flight_days: List[int],
    values: List[float],
    forecast_days: List[int] = None,
    degree: int = 1
) -> Dict[str, Any]:
    """
    Calcula regresión estadística con intervalos de confianza del 95% (t-Student).
    """
    if len(flight_days) != len(values):
        raise ValueError("flight_days y values deben tener la misma longitud.")
    if len(flight_days) < 3:
        raise ValueError("Se requieren al menos 3 puntos temporales para modelar tendencia e intervalos.")

    x = np.array(flight_days, dtype=float)
    y = np.array(values, dtype=float)
    n = len(x)

    # Regresión lineal por SciPy
    slope, intercept, r_value, p_value, std_err = stats.linregress(x, y)
    r_squared = r_value**2

    # Residuales y error estándar del estimado
    y_pred = intercept + slope * x
    residuals = y - y_pred
    dof = n - 2
    s_err = np.sqrt(np.sum(residuals**2) / max(1, dof))

    # Valor crítico t para 95% de confianza (dos colas)
    t_val = stats.t.ppf(0.975, df=dof) if dof > 0 else 1.96

    # Calcular bandas de confianza para los puntos observados
    x_mean = np.mean(x)
    ss_x = np.sum((x - x_mean)**2) if np.sum((x - x_mean)**2) > 0 else 1.0

    fitted_curve = []
    for xi, yi, ypi in zip(x, y, y_pred):
        se_line = s_err * np.sqrt(1.0/n + ((xi - x_mean)**2) / ss_x)
        margin = t_val * se_line
        fitted_curve.append({
            "flight_day": int(xi),
            "actual_value": round(float(yi), 4),
            "fitted_value": round(float(ypi), 4),
            "ci_lower_95": round(float(ypi - margin), 4),
            "ci_upper_95": round(float(ypi + margin), 4),
            "residual": round(float(yi - ypi), 4),
        })

    # Proyecciones a futuro
    forecast = []
    if forecast_days:
        for fx in forecast_days:
            if fx not in flight_days:
                fyp = intercept + slope * fx
                se_forecast = s_err * np.sqrt(1.0 + 1.0/n + ((fx - x_mean)**2) / ss_x)
                margin_f = t_val * se_forecast
                forecast.append({
                    "flight_day": int(fx),
                    "forecast_value": round(float(fyp), 4),
                    "ci_lower_95": round(float(fyp - margin_f), 4),
                    "ci_upper_95": round(float(fyp + margin_f), 4),
                })

    # Ajuste cuadrático adicional si degree == 2
    poly_fit = None
    if degree >= 2 and n >= 4:
        poly_coeffs = np.polyfit(x, y, 2)
        poly_pred = np.polyval(poly_coeffs, x)
        poly_r2 = 1.0 - (np.sum((y - poly_pred)**2) / (np.sum((y - np.mean(y))**2) or 1.0))
        poly_fit = {
            "coefficients": [round(float(c), 6) for c in poly_coeffs],
            "r_squared": round(float(poly_r2), 4),
            "formula": f"{poly_coeffs[0]:+.4e}*FD^2 + {poly_coeffs[1]:+.4f}*FD + {poly_coeffs[2]:+.4f}",
        }

    return {
        "model_type": "Longitudinal Ordinary Least Squares (OLS) + Student-t 95% CI",
        "sample_size": n,
        "degrees_of_freedom": dof,
        "metrics": {
            "slope_per_day": round(float(slope), 6),
            "intercept": round(float(intercept), 4),
            "r_squared": round(float(r_squared), 4),
            "correlation_coefficient_r": round(float(r_value), 4),
            "p_value": float(f"{p_value:.6e}"),
            "standard_error": round(float(std_err), 6),
            "residual_standard_deviation": round(float(s_err), 4),
            "is_statistically_significant": bool(p_value < 0.05),
        },
        "polynomial_quadratic_fit": poly_fit,
        "fitted_points": fitted_curve,
        "forecast_points": forecast,
        "scientific_disclaimer": "Modelo estadístico longitudinal de tendencia para series temporales orbitales con intervalos de confianza del 95%."
    }
