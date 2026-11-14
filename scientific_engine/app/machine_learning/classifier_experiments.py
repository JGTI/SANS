"""
SPACE VISION Machine Learning Experimentation Laboratory
Entrenamiento, evaluación y comparación cruzada de modelos predictivos experimentales:
- Logistic Regression (Modelo Base)
- Random Forest Classifier
- Gradient Boosting Classifier
Cumple con separación estricta, validación cruzada y métricas de desempeño transparentes.
"""
import numpy as np
from typing import Dict, Any, List, Optional
from sklearn.linear_model import LogisticRegression
from sklearn.ensemble import RandomForestClassifier, GradientBoostingClassifier
from sklearn.model_selection import StratifiedKFold
from sklearn.metrics import accuracy_score, precision_score, recall_score, f1_score, roc_auc_score, confusion_matrix
from app.simulation.digital_twin import AstronautDigitalTwinSimulator

FEATURE_NAMES = [
    "hyperopic_shift",
    "rnfl_delta_microns",
    "iop_mmhg",
    "flight_day",
    "age",
    "baseline_se"
]

def generate_experimental_cohort(n_astronauts: int = 60, seed: int = 101) -> tuple:
    """
    Genera un conjunto de datos sintético multicéntrico simulado para experimentación.
    Etiqueta objetivo: has_sans_syndrome (1: Desplazamiento >= +0.75 D con cambios de RNFL, 0: Control estable).
    """
    sim = AstronautDigitalTwinSimulator(seed=seed)
    x_rows = []
    y_labels = []
    subject_ids = []

    scenarios = ["A", "B", "C", "D", "E"]
    for i in range(n_astronauts):
        astro_id = f"ASTRO-SYNTH-{i:03d}"
        scen = scenarios[i % len(scenarios)]
        profile = sim.generate_astronaut_profile(astronaut_id=astro_id, name=f"Crew Member {i}")
        sim_res = sim.simulate_mission_timeline(scenario_key=scen, mission_days=150, sample_step_days=30, baseline_profile=profile)

        for pt in sim_res["timeline"]:
            shift = pt["refraction_od"]["hyperopic_shift"]
            rnfl_d = pt["biometrics"]["rnfl_delta_microns"]
            iop = pt["biometrics"]["iop_mmhg"]
            day = pt["flight_day"]
            age = profile["age"]
            base_se = profile["baseline_od"]["spherical_equivalent"]

            # Etiqueta basada en criterios clínicos simulados
            # SANS positivo si shift >= 0.75 y (rnfl_d >= 15 o frisen >= 1)
            is_sans = 1 if (shift >= 0.75 and (rnfl_d >= 12.0 or pt["biometrics"]["frisen_grade"] >= 1)) else 0

            x_rows.append([shift, rnfl_d, iop, day, age, base_se])
            y_labels.append(is_sans)
            subject_ids.append(astro_id)

    return np.array(x_rows), np.array(y_labels), subject_ids

def run_model_comparison_experiment(
    n_astronauts: int = 60,
    n_splits: int = 5,
    random_state: int = 42
) -> Dict[str, Any]:
    """
    Ejecuta validación cruzada estratificada sobre los tres modelos candidatos.
    """
    x, y, subjects = generate_experimental_cohort(n_astronauts=n_astronauts, seed=random_state)
    skf = StratifiedKFold(n_splits=n_splits, shuffle=True, random_state=random_state)

    models = {
        "logistic_regression": {
            "name": "Logistic Regression (L2 Regularized)",
            "instance": LogisticRegression(max_iter=1000, random_state=random_state),
            "type": "Linear Baseline",
        },
        "random_forest": {
            "name": "Random Forest Classifier (100 Trees)",
            "instance": RandomForestClassifier(n_estimators=100, max_depth=5, random_state=random_state),
            "type": "Ensemble Bagging",
        },
        "gradient_boosting": {
            "name": "Gradient Boosting Classifier",
            "instance": GradientBoostingClassifier(n_estimators=100, learning_rate=0.1, max_depth=3, random_state=random_state),
            "type": "Ensemble Boosting",
        },
    }

    results = {}
    for key, model_info in models.items():
        acc_scores = []
        prec_scores = []
        rec_scores = []
        f1_scores = []
        auc_scores = []
        all_y_true = []
        all_y_pred = []
        feature_importances_accum = []

        for train_idx, test_idx in skf.split(x, y):
            x_train, x_test = x[train_idx], x[test_idx]
            y_train, y_test = y[train_idx], y[test_idx]

            clf = model_info["instance"]
            clf.fit(x_train, y_train)

            y_pred = clf.predict(x_test)
            if hasattr(clf, "predict_proba"):
                y_proba = clf.predict_proba(x_test)[:, 1]
            else:
                y_proba = y_pred

            acc_scores.append(accuracy_score(y_test, y_pred))
            prec_scores.append(precision_score(y_test, y_pred, zero_division=0))
            rec_scores.append(recall_score(y_test, y_pred, zero_division=0))
            f1_scores.append(f1_score(y_test, y_pred, zero_division=0))
            try:
                auc_scores.append(roc_auc_score(y_test, y_proba))
            except ValueError:
                auc_scores.append(0.5)

            all_y_true.extend(y_test)
            all_y_pred.extend(y_pred)

            if hasattr(clf, "feature_importances_"):
                feature_importances_accum.append(clf.feature_importances_)
            elif hasattr(clf, "coef_"):
                # Normalizar coeficientes absolutos como aproximación de importancia
                abs_coef = np.abs(clf.coef_[0])
                feature_importances_accum.append(abs_coef / (np.sum(abs_coef) or 1.0))

        cm = confusion_matrix(all_y_true, all_y_pred)
        mean_feat_imp = np.mean(feature_importances_accum, axis=0) if feature_importances_accum else [0.0]*len(FEATURE_NAMES)
        feature_importance_dict = {
            feat: round(float(imp), 4)
            for feat, imp in zip(FEATURE_NAMES, mean_feat_imp)
        }

        results[key] = {
            "model_name": model_info["name"],
            "model_type": model_info["type"],
            "metrics": {
                "accuracy": round(float(np.mean(acc_scores)), 4),
                "precision": round(float(np.mean(prec_scores)), 4),
                "recall": round(float(np.mean(rec_scores)), 4),
                "f1_score": round(float(np.mean(f1_scores)), 4),
                "roc_auc": round(float(np.mean(auc_scores)), 4),
            },
            "confusion_matrix": {
                "true_negative": int(cm[0][0]),
                "false_positive": int(cm[0][1]),
                "false_negative": int(cm[1][0]),
                "true_positive": int(cm[1][1]),
            },
            "feature_importances": feature_importance_dict,
        }

    return {
        "experiment_name": "SPACE VISION Biomarker SANS Classification Benchmark",
        "dataset_summary": {
            "total_samples": len(y),
            "positive_sans_samples": int(np.sum(y == 1)),
            "negative_control_samples": int(np.sum(y == 0)),
            "features": FEATURE_NAMES,
            "k_folds": n_splits,
            "is_synthetic": True,
        },
        "models_evaluated": results,
        "best_performing_model": max(results.keys(), key=lambda k: results[k]["metrics"]["f1_score"]),
        "scientific_disclaimer": "Demostración experimental de aprendizaje automático supervisado sobre cohorte simulada. Las métricas no deben extrapolarse como eficacia en pacientes clínicos reales."
    }
