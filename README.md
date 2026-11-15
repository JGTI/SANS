# 🚀 SPACE VISION — SANS Monitor (`Space Visual Predictor for SANS`)
### Biomarker-Driven Neuro-Ocular Surveillance & Longitudinal Timeline (+30 / +60 / +90 Days)
**Team:** NEXUS × Visual Rehab Center · **Event:** NASA International Space Apps Challenge 2026

- **Live Public Demo (Zero-Login · Primary `EN` with `EN | ES` Toggle · Default Dark Mode):** https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com
- **Official Repository:** https://github.com/JGTI/SANS
- **Canonical Specification:** [`docs/SANS_MONITOR_SPEC.md`](docs/SANS_MONITOR_SPEC.md)
- **Pitch & Submission Reference:** [`docs/PITCH_AND_SUBMISSION_FINAL.md`](docs/PITCH_AND_SUBMISSION_FINAL.md)

---

## PART I — OFFICIAL PITCH & NARRATIVE SCRIPT (ENGLISH)

### Opening Hook (30 Seconds)
> If an astronaut’s vision gets blurry mid-mission, how does the medical team know what changed?
>
> We built **SPACE VISION (`SANS Monitor`)**, a prototype that tracks changes in each eye before, during, and after spaceflight.
>
> Our demo shows three cases: one **stable**, one **with changes**, and one **with a missing scan**. A missing scan doesn’t mean nothing changed.
>
> **SPACE VISION** helps the medical team decide what to review first—without ever replacing human clinical judgment or issuing an automated fit-for-duty verdict.

### Full Presentation Script (English)
Imagine you are in space.

Thousands of kilometers away from Earth, taking part in a mission that represents the future of humanity.

But over time, something begins to change: your vision.

This is not science fiction. It is a real challenge faced by astronauts due to **Spaceflight-Associated Neuro-ocular Syndrome**, known as **SANS**.

Although we know prolonged spaceflight can cause neuro-ocular alterations, fundamental questions remain:

Why do some astronauts develop this syndrome while others do not? What physiological changes are linked to its onset? And, above all, can we identify early risk signals before their consequences manifest?

To address this challenge, we present **Space Visual Predictor for SANS (SPACE VISION)**.

Our proposal is a technological platform designed so astronauts can perform guided visual checks autonomously during their missions, without depending on the physical presence of a specialist.

Yet we want to go beyond evaluating vision.

Our project also monitors ocular biomarkers and physiological indicators potentially associated with the mechanisms that contribute to SANS.

Why does this matter?

Because the exact origin of this syndrome is still not fully understood. Connecting visual changes, ocular biomarkers, and spaceflight conditions in a single traceable record can help researchers better understand how it develops.

To achieve this, we integrate **two complementary monitoring models**:
1. **In-flight autonomous visual checks (`Astronaut Check`)**, capturing symptoms, tonometer readings, and retinal fundus images for downlink to Ground Control.
2. **Ground clinical and analog evaluation (`Scientific Module` & `Population Dashboard`)**, integrating specialized ophthalmic instruments (Cirrus SD-OCT, ETDRS 9-sector maps, axial length, refraction, Frisén grade, IOP) across normal terrestrial controls (`0°`) and **`-6°` Head-Down Tilt (HDT)** analog studies used to partially simulate physiological fluid shifts of microgravity.

What makes **SPACE VISION** innovative?

We unify per-eye (`OD` / `OS`) longitudinal tracking, **6 core ocular biomarkers**, **3 reference scenario models** (*Stable*, *Worsens*, *Unscanned*), **3D WebGL + SD-OCT visualization**, **Python Computer Vision** fundus analysis, and **Scikit-learn** exploratory modeling—allowing teams to analyze progression, flag missing scans honestly, and prioritize clinical review.

Because our goal is not merely to detect the effects of SANS. We want to help clarify its potential mechanisms and build traceable tools that allow medical teams to anticipate risk earlier.

For humanity to explore new worlds, we must first protect those who make that exploration possible.

The future of space exploration is not only about traveling farther—it is about ensuring our astronauts can continue seeing the universe we are discovering.

Thank you very much.

---

## PARTE II — GUION OFICIAL DE PRESENTACIÓN (ESPAÑOL)

### Apertura Corta (30 Segundos)
> Si la visión de un astronauta se vuelve borrosa a mitad de la misión, ¿cómo sabe el equipo médico qué cambió?
>
> Construimos **SPACE VISION (`SANS Monitor`)**, un prototipo que da seguimiento por separado a cada ojo antes, durante y después del vuelo espacial.
>
> Nuestra demostración muestra tres casos: uno **estable**, uno **con progresión/cambios** y uno **sin escaneo reciente**. Que falte un escaneo no significa que nada haya cambiado.
>
> **SPACE VISION** ayuda al equipo médico a decidir qué revisar primero, sin reemplazar el criterio clínico ni emitir dictámenes automáticos de aptitud.

### Discurso Completo (Español)
Imaginen que están en el espacio.

A miles de kilómetros de la Tierra, participando en una misión que representa el futuro de la humanidad.

Pero, con el paso del tiempo, algo comienza a cambiar: su visión.

Esto no es ciencia ficción. Es una realidad que enfrentan algunos astronautas debido al **Síndrome Neuroocular Asociado al Vuelo Espacial**, conocido como **SANS**.

Aunque sabemos que los vuelos espaciales prolongados pueden provocar alteraciones neurooculares, todavía existen preguntas fundamentales:

¿Por qué algunos astronautas desarrollan este síndrome y otros no? ¿Qué cambios fisiológicos están relacionados con su aparición? Y, sobre todo, ¿podemos identificar señales de riesgo antes de que se manifiesten sus consecuencias?

Para enfrentar este desafío, presentamos **Space Visual Predictor for SANS (SPACE VISION)**.

Nuestra propuesta consiste en una plataforma tecnológica diseñada para que los astronautas puedan realizar pruebas visuales de manera autónoma durante sus misiones espaciales, sin depender de la presencia física de un especialista.

Pero queremos ir más allá de evaluar la visión.

Nuestro proyecto también busca monitorear biomarcadores e indicadores fisiológicos potencialmente asociados con los mecanismos que contribuyen al desarrollo de SANS.

¿Por qué es importante?

Porque actualmente no se comprende por completo el origen de este síndrome. Por ello, identificar relaciones entre los cambios visuales, los biomarcadores y las condiciones del vuelo espacial podría ayudarnos a comprender mejor su desarrollo.

Para lograrlo, proponemos **dos modelos complementarios de seguimiento**:
1. **Durante el vuelo espacial (`Vista Astronauta`)**, mediante pruebas visuales autónomas, registro de síntomas, tonometría y envío de imágenes de fondo de ojo hacia Tierra.
2. **En la Tierra (`Módulo Científico` y `Dashboard Poblacional`)**, utilizando instrumentos oftalmológicos especializados (SD-OCT, mapas ETDRS de 9 sectores, longitud axial, refracción, grado Frisén, PIO) y evaluaciones comparativas en controles terrestres normales (`0°`) y bajo el modelo análogo de **inclinación a `-6°` (HDT)**, empleado para simular parcialmente los efectos fisiológicos de la microgravedad.

¿Qué hace innovador a **SPACE VISION**?

Integramos la trazabilidad longitudinal por ojo (`OD` / `OS`), **6 biomarcadores oculares clave**, **3 modelos de escenario** (*Estable*, *Empeora*, *Sin escanear*), **Gemelo Digital Ocular 3D WebGL + Cirrus SD-OCT**, análisis de fondo de ojo con **Visión por Computadora en Python** y modelado exploratorio con **Scikit-learn**, permitiendo analizar su evolución, detectar faltantes de datos con honestidad y priorizar la revisión clínica.

Porque nuestro objetivo no es solamente detectar los efectos de SANS. Queremos contribuir a comprender sus posibles mecanismos de origen y desarrollar herramientas que nos permitan anticiparnos a ellos.

Con **Space Visual Predictor for SANS** buscamos aportar una nueva perspectiva al monitoreo de la salud visual en misiones espaciales de larga duración.

Porque para que la humanidad pueda explorar nuevos mundos, primero debemos proteger a quienes hacen posible esa exploración.

El futuro de la exploración espacial no consiste únicamente en llegar más lejos, sino en garantizar que nuestros astronautas puedan seguir viendo el universo que estamos descubriendo.

Muchas gracias.

---

## PART III — TECHNICAL ARCHITECTURE & PLATFORM SUMMARY

### 1. Clinical & Operational Logic (No Automated Fit-for-Duty Verdict)
- **Independent Per-Eye Review (`OD` & `OS`):** Each eye is evaluated independently into one of three operational triage states:
  - **Ready for review** (`LISTO_PARA_REVISION`)
  - **Prioritize review** (`PRIORIZAR_REVISION`)
  - **Missing data** (`FALTAN_DATOS`)
- **Human-in-the-Loop Safeguard:** Displays `Fitness not evaluated` / `Aptitud no evaluada` until human clinical sign-off (`Flight Surgeon`). Missing scans are never treated as normal/stable.
- **Longitudinal Timeline:** Before flight (Baseline), During exposure/flight, and Post-flight controls at **`+30`**, **`+60`**, and **`+90` days**.
- **6 Core Ocular Biomarkers:**
  1. Mean OCT Total Retinal Thickness (`TRT`, µm) & `ΔTRT`
  2. Axial Length (mm) & axial shortening
  3. Hyperopic Shift (diopters, D)
  4. Frisén Papilledema Grade (0–5) & Choroidal Folds
  5. Intraocular Pressure (`IOP`, mmHg — tracked for context; explicitly shown non-predictive alone)
  6. `-6°` Head-Down Tilt (`-6° HDT`) terrestrial analog response
- **3 Canonical Reference Scenario Models:** *Stable* (`ESTABLE`), *Worsens* (`EMPEORA`), and *Unscanned* (`SIN_ESCANEAR`).

### 2. Platform Modules (Zero-Login Public Access)
| Module | Route / Tab | Key Capabilities |
|---|---|---|
| **Modules Hub** | `/?tab=hub` | Central user-friendly launcher guiding judges and clinicians across all modules + Guided Tour launch. |
| **Scientific Module (SANS)** | `/` | 6 biomarker inputs, Signal Index chart, Scikit-learn candidate susceptibility simulator, Python CV fundus scanner + Demo Fundus Camera modal, longitudinal cuts (`+30/+60/+90d`), 3 scenario models, NASA protocols & bibliography. |
| **Astronaut Check** | `/astronaut` | Task-first orbital crew wizard: near-vision/symptom check, simulated tonometer & vitals, fundus downlink to Ground Control, flight logbook, and read-only pre-flight baseline. |
| **3D Viewer + OCT** | `/digital-twin?mode=dual` | Interactive Three.js WebGL 3D ocular globe (posterior globe flattening, optic disc protrusion, choroidal folds) + Cirrus HD-OCT B-Scan & ETDRS 9-sector deviation maps with animated sample playback. |
| **Population Dashboard** | `/?tab=poblacion` | Cross-cohort comparison across Orbital Astronauts, Terrestrial Controls (`0°`), and `-6° HDT` Analog. |
| **Our Team** | `/?tab=team` | Multidisciplinary team roster (`EN \| ES`) with university affiliations, NASA Space Apps handles, and project links. |
| **Guided Tour** | Topbar `Guided Demo` | Automated walkthrough (`0.5x` to `6x`, default `2x`) covering all modules, demo eye-alignment camera sequence, 3D/OCT playback, Population Dashboard, and Team. |

### 3. Technology Stack & NASA Data Integration
- **Web Application:** Laravel 11 / PHP 8.3, Blade (High-Contrast Monochrome + Space Blue/Neon WCAG 2.2 AA, Default Dark Mode), Chart.js, Three.js WebGL, Font Awesome 4.7.
- **Scientific Microservice:** Python 3.14 FastAPI (`127.0.0.1:8001`) with NumPy 2.5, SciPy 1.18, Scikit-learn 1.9, Pandas 3.1, and Pillow (retinal FOV masking, optic disc localization, Sobel rim sharpness, peripapillary halo, and choroidal fold scoring).
- **NASA Open Science Integrations:** Live OSDR datasets, NTRS technical reports, DONKI space weather context, and ISS orbital telemetry.

---

## PART IV — MULTIDISCIPLINARY PROJECT TEAM (NASA SPACE APPS CHALLENGE 2026)

| Member | Role | University / Affiliation | NASA Space Apps | Contact / Website |
|---|---|---|---|---|
| **Dra. Gloria Margarita Pérez Vallejo** | Clinical Optometry & Visual Rehabilitation (Specialist in Clinical Optometry, Vision Therapy & Strabismus; Federal Prof. License No. 13911122; 8th Semester General Medicine Student) | Universidad Autónoma de Querétaro (UAQ) · Universidad Autónoma de Durango (UAD) | `@gloriapervall` | https://visualrehabcenter.com/ · `gloriavallejo@visualrehabcenter.com` |
| **Lic. Victoria Isabel Pérez Vallejo** | International Relations | Universidad Iberoamericana (IBERO) | `@victoriaisabelpervall` | `victoriavallejo@visualrehabcenter.com` |
| **Lic. Mónica Belem Pérez Vallejo** | Attorney at Law (LL.B.) | Universidad Autónoma de Querétaro (UAQ) | `@monicapervall` | `monicavallejo@nexusdigitaldb.com` |
| **Ing. José Guadalupe García Espinosa** | Computer Systems Engineer | Universidad Politécnica de Querétaro (UPQ) | `@gitti` | https://nexusdigitaldb.com/ · `github.jgge@gmail.com` · `josegarcia@nexusdigitaldb.com` |
| **Dr. Alam Tinajero Palacios** | Ph.D. in Legal Sciences | Universidad Autónoma de Querétaro (UAQ) | `@alamtpalacios` | `alampalacios@nexusdititaldb.com` |
| **Miguel Ángel García Espinosa** | Software Engineering | Universidad Politécnica de Santa Rosa Jáuregui (UPSRJ) | `@gitmiguelti` | `miguelgarcia@nexusdigitaldb.com` |

---

## PART V — AUTOMATED VERIFICATION BASELINE

```bash
php artisan scientific:engine status   # ONLINE at http://127.0.0.1:8001 (Python 3.14.4)
php artisan scientific:engine test     # 22 passed (Pytest)
php artisan test                       # 82 passed (470 assertions, PHPUnit)
```
