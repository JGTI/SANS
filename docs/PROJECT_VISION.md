# 🪐 SPACE VISION / SANS MONITOR — PROJECT VISION & SCIENTIFIC SCOPE

> **Documento Rector de Requerimientos:** Consulta [`docs/SANS_MONITOR_SPEC.md`](SANS_MONITOR_SPEC.md) para la especificación completa de software, flujo general, contrato mínimo de datos (RF-01 a RF-37, RNF-01 a RNF-06) y pruebas de aceptación de extremo a extremo (`AT-01` a `AT-19`).

---

## 1. Declaración de Misión y Objetivo
**SPACE VISION — SANS Monitor** reúne las revisiones oculares de una persona en una sola línea de tiempo trazable para misiones espaciales de larga duración (ISS, misiones lunares Artemis y tránsito a Marte) y estudios análogos terrestres (reposo en cama con inclinación cefálica a $-6^\circ$).

**Objetivo central:** Convertir un expediente ocular en una revisión trazable: recuperar qué protocolo y estudios se usaron antes, encontrar referencias o seguimientos pertinentes, mostrar qué ha cambiado por ojo (antes, durante la exposición/vuelo y en controles posteriores **+30, +60 y +90 días**) y estudiar si hay señales útiles para anticipar cambios futuros.

La aplicación ayuda al equipo médico a distinguir tres situaciones operativas:
1. **Hay datos y el ojo se mantiene estable** («Listo para revisión» / trayectoria estable).
2. **Hay mediciones que muestran empeoramiento** («Priorizar revisión» / en progresión).
3. **Faltan estudios o línea base para decidir** («Faltan datos» / seguimiento insuficiente).

> **Principio rector:** El propósito del sistema es **facilitar la revisión y preparar la vigilancia**, **no determinar automáticamente quién es apto para volar**.

---

## 2. Entregas por Fase (P0 · P1 · P2)
- **P0 (Demo Funcional — Hackatón):** Fichas pseudónimas y escenarios ficticios reproducibles (estable, en progresión, sin escaneos previos / sin control reciente, asimetría interocular), simulador temporal (días 10 → 15 → 30 y rangos 0–30, 0–90, 0–150) y seguimiento posvuelo/posexposición (+30, +60, +90 días).
- **P1 (Entorno de Investigación Autorizado):** Datos autorizados con procedencia, día real de toma (`observed_at`) vs. día de disponibilidad (`available_at`), curación de catálogo de protocolos verificados y registro del dictamen humano exclusivo del especialista autorizado.
- **P2 (Pronóstico Experimental Validado):** Separado estrictamente de observaciones y simulaciones; emitido solo para desenlace, población y horizonte evaluados con incertidumbre y versión de modelo, mostrando «No estimable» fuera de su alcance validado.

---

## 3. Indicadores Oculares Centrales y Capa de Contexto

### A. Mediciones por Ojo (OD / OS)
| Código | Medida | Unidad / Valor | Modalidad y Regla |
|---|---|---|---|
| `PERIPAPILLARY_TRT` | Grosor retiniano peripapilar (TRT) | $\mu\text{m}$ | OCT. Marcador primario propuesto ($\Delta\text{TRT} \ge 55\ \mu\text{m}$; señal temprana $\ge 20\ \mu\text{m}$). |
| `FRISEN_GRADE` | Grado de edema del disco óptico | Escala 0 a 5 | Fondo de ojo / evaluación clínica. Objetivo principal de investigación. |
| `RNFL` | Capa de fibras nerviosas de la retina | $\mu\text{m}$ | OCT (perfil TSNIT y cuadrantes). |
| `CHOROID_THICKNESS` / `CHORIORETINAL_FOLD` | Grosor coroideo y pliegues coriorretinianos | $\mu\text{m}$ / Categoría y región | OCT / LSO. Los pliegues se comparan como ausencia, presencia o cambio de región, no como resta numérica. |
| `AXIAL_LENGTH` | Longitud axial anteroposterior | $\text{mm}$ | Biometría óptica (vista sagital). |
| `POSTERIOR_FLATTENING` | Aplanamiento del polo posterior | Presente / Ausente / Indeterminado / No evaluado | Imagen apropiada (RM / ecografía / OCT). No se deduce automáticamente de la longitud axial. |
| `SPHERICAL_EQ` | Equivalente esférico y cambio ($\Delta SE$) | Dioptrías ($\text{D}$) | Refracción ($SE = \text{SPH} + \text{CYL}/2$, vectores de Thibos $M, J_0, J_{45}, B$ y agudeza Snellen $\to$ LogMAR). |
| `IOP` | Presión intraocular (PIO) | $\text{mmHg}$ | Tonometría fechada con método. |

### B. Cinco Dominios de Contexto (Sección Plegable Secundaria)
Se registran como información de apoyo fechada sin alterar el estado ocular ni generar un puntaje general en P0:
1. **Neurológico:** Síntomas registrados (ej. cefalea) sin estimar presión intracraneal a partir de síntomas, PIO u OCT.
2. **Metabólico / Nutricional:** Suplementación o laboratorio disponible sin solicitar paneles nuevos.
3. **Cardiovascular:** Presión arterial y postura de registro sin confundir presiones arterial, venosa, intracraneal e intraocular.
4. **Ambiental:** $\text{CO}_2$ ambiental y clima espacial (NASA DONKI) con ventana temporal sin tratar coincidencia temporal como causalidad.
5. **Conductual:** Horas de sueño o actividad sin usarlas para clasificar aptitud.

---

## 4. Referencias Científicas Precargadas y Documentadas
- **NASA OCHMO:** [Guía técnica sobre el riesgo SANS (`ochmo-mtb-001-sans.pdf`)](https://www.nasa.gov/wp-content/uploads/2024/02/ochmo-mtb-001-sans.pdf) — Referencia técnica, no autorización clínica automática.
- **Consenso de Especialistas SANS (2026):** [doi:10.1038/s41433-026-04651-6](https://doi.org/10.1038/s41433-026-04651-6) — Separación de hallazgos, umbrales y calendarios de investigación respecto a hitos de demo.
- **Macias et al. (2020):** [doi:10.1001/jamaophthalmol.2020.0673](https://doi.org/10.1001/jamaophthalmol.2020.0673) — Cambios oculares estructurales en órbita vs. antes/después y recuperación.
- **Carter et al. (2025):** [doi:10.1001/jamaophthalmol.2025.4635](https://doi.org/10.1001/jamaophthalmol.2025.4635) — Progresión del edema del disco óptico (referencia para días 30 y 150, no predictor de aptitud).
- **Laurie et al. (2019):** [doi:10.1001/jamaophthalmol.2019.5261](https://doi.org/10.1001/jamaophthalmol.2019.5261) — Comparación ocular entre vuelo espacial y análogo terrestre de reposo inclinado ($-6^\circ$ HDT), fundamento para mantener ambas cronologías y líneas base separadas.
- **NASA Human Research Program (HRP 2026):** [Plan de investigación de riesgos (`hrp-rap-for-web-62026-hhc.pdf`)](https://www.nasa.gov/wp-content/uploads/2026/08/hrp-rap-for-web-62026-hhc.pdf) — Fundamenta tratar los factores fisiológicos y ambientales como preguntas abiertas de investigación.
- **NASA Life Sciences Portal (NLSP):** [https://www.nasa.gov/hrp/nlsp/](https://www.nasa.gov/hrp/nlsp/) — Portal para consulta y solicitud de datos humanos autorizados.
