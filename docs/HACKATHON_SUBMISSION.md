# 🚀 SPACE VISION / SANS MONITOR — NASA SPACE APPS CHALLENGE 2026 SUBMISSION SHEET

> **Uso:** Utiliza este documento para copiar y pegar directamente la información en el formulario oficial de entrega del **NASA Space Apps Challenge 2026** y como guía de presentación ante el jurado.
> **Especificación de Software:** [`docs/SANS_MONITOR_SPEC.md`](SANS_MONITOR_SPEC.md)

---

## 1. Datos Básicos del Proyecto
- **Título del Proyecto:** SPACE VISION — SANS Monitor
- **Subtítulo / Tagline:** Revisión Longitudinal Trazable de Expedientes Oculares, Seguimiento de Protocolos y Simulación Biomecánica del Síndrome SANS en Microgravedad y Análogos Terrestres.
- **Sede Local:** Querétaro, México.
- **Repositorio de Código Abierto (GitHub):** https://github.com/JGTI/SANS
- **Demostración en Vivo (Producción):** https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com

---

## 2. Resumen Ejecutivo (Short Description)
**SANS Monitor (SPACE VISION)** reúne las revisiones oculares de una persona en una sola línea de tiempo trazable antes, durante y después de un vuelo espacial o una exposición análoga terrestre (reposo inclinado a $-6^\circ$). 

Su objetivo es convertir un expediente ocular en una revisión trazable: recuperar qué protocolo y estudios se usaron antes, encontrar referencias o seguimientos pertinentes, mostrar qué ha cambiado por ojo (OD/OS) hasta los controles **+30, +60 y +90 días** posteriores, y ayudar al equipo médico a distinguir tres situaciones operativas (**«Listo para revisión»**, **«Priorizar revisión»** o **«Faltan datos»**) **sin emitir un veredicto automático de aptitud**.

---

## 3. ¿Cómo Aborda el Desafío?
1. **Línea de Tiempo Unificada por Ojo y Fase (`RF-01` a `RF-04`, `RF-31` a `RF-34`):** Distingue pre-vuelo/preexposición, controles en vuelo/exposición (días 10 → 15 → 30), fin (+0) y controles posvuelo/posexposición (+30, +60 y +90 días), separando fecha de toma (`observed_at`) de fecha de disponibilidad (`available_at`) y manteniendo independientes las cronologías de vuelo orbital y análogo terrestre.
2. **Catálogo Curado y Trazabilidad de Protocolos (`RF-05` a `RF-09`):** Distingue entre el protocolo previamente aplicado al expediente, referencias pertinentes para el seguimiento posterior y versiones verificadas, separando protocolos operativos, consensos, artículos de investigación y escenarios de demo.
3. **Consola Clínica ZEISS Cirrus™ HD-OCT y Gemelo Digital 3D (`RF-18` a `RF-22`):** Tres vistas anatómicas sincronizadas (sagital del globo, fondo de ojo con disco óptico y corte de capas OCT) con separación estricta entre **reproducción histórica** y **simulación ilustrativa**.
4. **Comparativa de Inclinación y Recuperación (`RF-26` a `RF-30`):** Evalúa exposiciones inclinadas por tramos (ángulo, dirección, duración real derivada de inicio a fin) usando el fin de exposición como día 0 de recuperación.
5. **Vision Lab y Motor Científico Híbrido (`RF-35` a `RF-37`):** Registro separado de TRT peripapilar ($\Delta\text{TRT} \ge 55\ \mu\text{m}$ primario; $\ge 20\ \mu\text{m}$ señal temprana), grado Frisén, RNFL, grosor y pliegues coroideos, longitud axial, aplanamiento posterior, refracción ($SE$, vectores de Thibos, LogMAR) y PIO, acompañado de una **capa plegable de contexto** en 5 dominios (neurológico, metabólico, cardiovascular, ambiental y conductual) que apoya la investigación sin sesgar la evaluación ocular.
6. **Alcance Preventivo y Ético (`RF-10` a `RF-12`):** El sistema detecta faltantes y prioriza revisiones, pero nunca certifica aptitud ni diagnostica SANS de forma automática.

---

## 4. Referencias y Repositorios Documentados
- **NASA OCHMO:** [Guía técnica sobre el riesgo SANS (`ochmo-mtb-001-sans.pdf`)](https://www.nasa.gov/wp-content/uploads/2024/02/ochmo-mtb-001-sans.pdf)
- **Consenso de Especialistas sobre Diagnóstico y Manejo de SANS (2026):** [doi:10.1038/s41433-026-04651-6](https://doi.org/10.1038/s41433-026-04651-6)
- **Macias et al. (2020):** [Cambios oculares estructurales y recuperación (`doi:10.1001/jamaophthalmol.2020.0673`)](https://doi.org/10.1001/jamaophthalmol.2020.0673)
- **Carter et al. (2025):** [Progresión del edema del disco óptico en días 30 y 150 (`doi:10.1001/jamaophthalmol.2025.4635`)](https://doi.org/10.1001/jamaophthalmol.2025.4635)
- **Laurie et al. (2019):** [Comparación ocular entre vuelo espacial y reposo inclinado (`doi:10.1001/jamaophthalmol.2019.5261`)](https://doi.org/10.1001/jamaophthalmol.2019.5261)
- **NASA Human Research Program (2026):** [Plan de investigación de riesgos (`hrp-rap-for-web-62026-hhc.pdf`)](https://www.nasa.gov/wp-content/uploads/2026/08/hrp-rap-for-web-62026-hhc.pdf)
- **NASA Life Sciences Portal (NLSP) & OSDR / NTRS / DONKI:** [https://www.nasa.gov/hrp/nlsp/](https://www.nasa.gov/hrp/nlsp/) (`OSD-137`, `OSD-370`, `OSD-422`, `OSD-514`).

---

## 5. Credenciales Maestras para los Jueces
> **Contraseña universal:** `SpaceVision2026!`
- **Especialista Médico Autorizado (Flight Surgeon):** `surgeon@spacevision.nasa`
- **Investigador / Analista Biomédico (Researcher):** `researcher@spacevision.nasa`
- **Responsable de Datos / Curador (Operator Admin):** `operator@spacevision.nasa`
- **Director de Vuelo / Presentador de Demo (Super Admin):** `admin@spacevision.nasa`
- **Tripulante Simulado (Astronaut):** `astronaut@spacevision.nasa`

*(La pantalla `/login` cuenta además con botones de autorrelleno con un solo clic).*

---

## 6. Stack Tecnológico y Suite de Pruebas
- **Frontend:** Blade Minimalista Monocromático AdminCN/Shadcn (`#09090b` / `#ffffff`), Three.js 3D WebGL, Consola ZEISS Cirrus HD-OCT, Chart.js, Font Awesome 4.7.
- **Backend Orquestador:** PHP 8.3 / Laravel 11 + MariaDB + Spatie RBAC + REST API v1.
- **Motor Científico:** Python 3.14 + FastAPI (`:8001`) + NumPy 2.5 + SciPy 1.18 + Scikit-learn 1.9 + Pandas 3.1.
- **Pruebas Automatizadas (100% Verde):**
  - **PHPUnit (Laravel):** **67 tests aprobados (287 aserciones)**.
  - **Pytest (FastAPI):** **21 tests aprobados**.
- **CI/CD:** GitHub Actions ([`.github/workflows/deploy.yml`](../.github/workflows/deploy.yml)).
