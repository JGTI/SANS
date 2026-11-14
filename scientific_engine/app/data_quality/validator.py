"""
SPACE VISION Data Quality Observatory
Auditoría de integridad, detección de valores faltantes, anomalías y rangos fisiológicos.
"""
import numpy as np
import pandas as pd
from typing import Dict, Any, List

PHYSIOLOGICAL_BOUNDS = {
    "sphere": {"min": -25.0, "max": 25.0, "unit": "D"},
    "cylinder": {"min": -12.0, "max": 0.0, "unit": "D"},
    "axis": {"min": 0.0, "max": 180.0, "unit": "deg"},
    "iop_mmhg": {"min": 5.0, "max": 50.0, "unit": "mmHg"},
    "rnfl_microns": {"min": 45.0, "max": 240.0, "unit": "µm"},
    "flight_day": {"min": 0, "max": 1200, "unit": "days"},
}

def audit_dataset_quality(records: List[Dict[str, Any]]) -> Dict[str, Any]:
    """
    Ejecuta un diagnóstico exhaustivo de calidad de datos sobre una lista de registros biomédicos.
    """
    if not records:
        return {
            "error": "Dataset vacío. No se proporcionaron registros para auditoría.",
            "quality_score": 0.0,
            "total_records": 0,
        }

    df = pd.DataFrame(records)
    total_rows = len(df)
    total_cells = total_rows * len(df.columns)

    # 1. Análisis de valores faltantes (Missing Values)
    missing_by_column = df.isnull().sum().to_dict()
    total_missing = sum(missing_by_column.values())
    completeness_rate = round(1.0 - (total_missing / max(1, total_cells)), 4)

    # 2. Análisis de rangos fisiológicos y violaciones
    range_violations = {}
    total_violations = 0
    column_stats = {}

    for col, bounds in PHYSIOLOGICAL_BOUNDS.items():
        if col in df.columns:
            series = pd.to_numeric(df[col], errors="coerce").dropna()
            if not series.empty:
                under_min = int((series < bounds["min"]).sum())
                over_max = int((series > bounds["max"]).sum())
                col_violations = under_min + over_max
                total_violations += col_violations

                range_violations[col] = {
                    "violations_count": col_violations,
                    "below_min": under_min,
                    "above_max": over_max,
                    "expected_range": f"{bounds['min']} a {bounds['max']} {bounds['unit']}",
                    "observed_min": round(float(series.min()), 2),
                    "observed_max": round(float(series.max()), 2),
                }

                column_stats[col] = {
                    "mean": round(float(series.mean()), 3),
                    "std": round(float(series.std()), 3),
                    "median": round(float(series.median()), 3),
                    "q25": round(float(series.quantile(0.25)), 3),
                    "q75": round(float(series.quantile(0.75)), 3),
                    "valid_samples": int(len(series)),
                }

    # 3. Puntuación ponderada de calidad de datos (0 a 100)
    # 50% completitud + 50% conformidad con rangos
    conformity_rate = max(0.0, 1.0 - (total_violations / max(1, total_rows * len(PHYSIOLOGICAL_BOUNDS))))
    quality_score = round((0.50 * completeness_rate + 0.50 * conformity_rate) * 100.0, 1)

    # Nivel de Integridad
    if quality_score >= 90.0:
        health_tier = "EXCELENTE"
        health_color = "#22c55e"
    elif quality_score >= 75.0:
        health_tier = "ACEPTABLE"
        health_color = "#eab308"
    else:
        health_tier = "DEGRADADO / REQUIERE LIMPIEZA"
        health_color = "#ef4444"

    return {
        "observatory": "SPACE VISION Data Quality Observatory",
        "total_records_audited": total_rows,
        "features_audited": list(df.columns),
        "overall_quality_score": quality_score,
        "health_tier": health_tier,
        "health_color": health_color,
        "completeness_percentage": round(completeness_rate * 100.0, 1),
        "total_missing_values": int(total_missing),
        "missing_by_feature": {k: int(v) for k, v in missing_by_column.items()},
        "total_range_violations": int(total_violations),
        "range_violations": range_violations,
        "feature_distributions": column_stats,
        "provenance_notes": "Validación de esquema y límites biológicos según directivas de la Sociedad Médica Aeroespacial.",
    }
