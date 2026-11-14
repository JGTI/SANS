"""
SPACE VISION Machine Learning - Anomaly Detection
Algoritmos de detección de anomalías y observaciones atípicas en telemetría oftalmológica:
- Isolation Forest
- Local Outlier Factor (LOF)
- One-Class SVM
- Robust Z-Score (Median Absolute Deviation)
"""
import numpy as np
from typing import Dict, Any, List
from sklearn.ensemble import IsolationForest
from sklearn.neighbors import LocalOutlierFactor
from sklearn.svm import OneClassSVM

class OcularAnomalyDetector:
    """
    Detector de anomalías en series de refracción y biometría ocular.
    Nota científica: Una anomalía indica desviación estadística del patrón esperado,
    no constituye automáticamente diagnóstico de patología.
    """
    FEATURE_NAMES = ["hyperopic_shift", "iop_mmhg", "rnfl_microns", "flight_day"]

    def __init__(self, contamination: float = 0.1, random_state: int = 42):
        self.contamination = contamination
        self.random_state = random_state

    def detect_anomalies(
        self,
        observations: List[Dict[str, Any]],
        algorithm: str = "isolation_forest"
    ) -> Dict[str, Any]:
        """
        Ejecuta detección de anomalías sobre un conjunto de observaciones.
        Cada observación debe contener: hyperopic_shift, iop_mmhg, rnfl_microns, flight_day
        """
        if len(observations) < 4:
            return {
                "error": "Se requieren al menos 4 observaciones para calibrar modelos de anomalías.",
                "total_samples": len(observations),
                "anomalies_detected": 0,
                "results": []
            }

        x_mat = []
        for obs in observations:
            shift = float(obs.get("hyperopic_shift", 0.0))
            iop = float(obs.get("iop_mmhg", 15.0))
            rnfl = float(obs.get("rnfl_microns", 95.0))
            day = float(obs.get("flight_day", 0.0))
            x_mat.append([shift, iop, rnfl, day])
        x = np.array(x_mat)

        algo_key = algorithm.lower().strip()
        predictions = []
        scores = []

        if algo_key == "one_class_svm":
            model = OneClassSVM(nu=self.contamination, kernel="rbf", gamma="scale")
            preds = model.fit_predict(x)
            # sklearn devuelve -1 para anomalía, 1 para normal
            dec = model.decision_function(x)
            predictions = [bool(p == -1) for p in preds]
            scores = [round(float(s), 4) for s in dec]
            algo_name = "One-Class Support Vector Machine"

        elif algo_key == "local_outlier_factor":
            n_neighbors = min(len(observations) - 1, 5)
            model = LocalOutlierFactor(n_neighbors=n_neighbors, contamination=self.contamination)
            preds = model.fit_predict(x)
            dec = model.negative_outlier_factor_
            predictions = [bool(p == -1) for p in preds]
            scores = [round(float(s), 4) for s in dec]
            algo_name = "Local Outlier Factor (LOF)"

        elif algo_key == "robust_zscore":
            # Mediana y MAD (Median Absolute Deviation)
            medians = np.median(x, axis=0)
            mad = np.median(np.abs(x - medians), axis=0)
            mad[mad == 0] = 1e-6
            z_scores = 0.6745 * np.abs(x - medians) / mad
            max_z = np.max(z_scores, axis=1)
            threshold = 2.5
            predictions = [bool(z > threshold) for z in max_z]
            scores = [round(float(z), 4) for z in max_z]
            algo_name = "Robust Z-Score (Median Absolute Deviation)"

        else:  # Default: Isolation Forest
            model = IsolationForest(
                contamination=self.contamination,
                random_state=self.random_state,
                n_estimators=100
            )
            preds = model.fit_predict(x)
            dec = model.score_samples(x)
            predictions = [bool(p == -1) for p in preds]
            scores = [round(float(s), 4) for s in dec]
            algo_name = "Isolation Forest"

        annotated_results = []
        anomaly_count = 0
        for i, obs in enumerate(observations):
            is_anom = predictions[i]
            if is_anom:
                anomaly_count += 1
            annotated_results.append({
                "flight_day": obs.get("flight_day", i),
                "is_anomaly": is_anom,
                "anomaly_score": scores[i],
                "values": {
                    "hyperopic_shift": float(obs.get("hyperopic_shift", 0.0)),
                    "iop_mmhg": float(obs.get("iop_mmhg", 15.0)),
                    "rnfl_microns": float(obs.get("rnfl_microns", 95.0)),
                }
            })

        return {
            "algorithm": algo_name,
            "algorithm_code": algo_key,
            "features_used": self.FEATURE_NAMES,
            "total_observations": len(observations),
            "anomalies_detected": anomaly_count,
            "contamination_rate": self.contamination,
            "results": annotated_results,
            "scientific_disclaimer": "Las anomalías detectadas señalan fluctuaciones matemáticas inusuales en la serie. No deben interpretarse como diagnóstico médico directo."
        }
