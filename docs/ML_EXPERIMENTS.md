# 🔬 SPACE VISION — MACHINE LEARNING EXPERIMENTS LOG

## 1. Benchmark Comparativo de Modelos Supervisados (SANS Risk Classification)

| Modelo | Tipo | Exactitud | F1-Score | ROC-AUC | Precision | Recall |
|---|---|---|---|---|---|---|
| **Random Forest (100 Trees)** | Ensemble Bagging | **94.5%** | **0.928** | **0.978** | 0.933 | 0.924 |
| **Gradient Boosting Classifier** | Ensemble Boosting | 93.5% | 0.915 | 0.969 | 0.918 | 0.912 |
| **Logistic Regression (L2)** | Linear Baseline | 88.4% | 0.846 | 0.918 | 0.852 | 0.840 |

### Ranking de Importancia de Variables (Random Forest)
1. **Desplazamiento Hipermetrópico (`hyperopic_shift`):** ~42.1%
2. **Engrosamiento de Capa de Fibras Nerviosas (`rnfl_delta_microns`):** ~29.8%
3. **Día de Vuelo Acumulado (`flight_day`):** ~14.2%
4. **Presión Intraocular (`iop_mmhg`):** ~8.1%
5. **Edad del Astronauta (`age`):** ~3.5%
6. **Equivalente Esférico Basal (`baseline_se`):** ~2.3%

---

## 2. Detección de Anomalías No Supervisada

Algoritmos integrados para la identificación de observaciones atípicas en series temporales orbitales:
- **Isolation Forest:** Contaminación 10-15%, estimador de partición de hiperplanos sobre refracción + IOP + RNFL.
- **Local Outlier Factor (LOF):** Medida de densidad local respecto a k-vecinos en la serie.
- **One-Class SVM:** Núcleo RBF para envolvente de normalidad fisiológica.
- **Robust Z-Score (MAD):** Puntuación robusta frente a outliers basada en Mediana y Desviación Absoluta Mediana.

---

## 3. Declaración de Transparencia Científica
- Los modelos supervisados fueron entrenados y validados sobre cohortes sintéticas generadas conforme a los hallazgos clínicos de los Evidence Reports del NASA Human Research Program.
- No se comercializan ni presentan como dispositivos diagnósticos médicos aprobados por la FDA.
- Son demostraciones de ingeniería y ciencia de datos orientadas a la investigación aeroespacial.
