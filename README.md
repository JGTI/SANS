# 🚀 SPACE VISION — SANS Monitor
### Biomarker-Driven Neuro-Ocular Surveillance & Longitudinal Timeline (+30 / +60 / +90 Days)
**Team:** NEXUS × Visual Rehab Center · **Event:** NASA International Space Apps Challenge 2026

> *"Developed by NEXUS × Visual Rehab Center for the NASA International Space Apps Challenge 2026."*  
> **Live Public Demo (Zero Login Required · Primary Language: English `EN` with `EN | ES` Toggle):**  
> `https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com`

---

## 1. Project Overview

**SPACE VISION — SANS Monitor** is a zero-login, bilingual (`EN | ES`) neuro-ocular surveillance platform built for **Spaceflight-Associated Neuro-ocular Syndrome (SANS)** monitoring across pre-flight, in-flight microgravity exposure, and post-flight recovery (**+30, +60, and +90 days**).

The platform transforms longitudinal ocular telemetry, retinal fundus imagery, intraocular pressure (IOP), and OCT measurements into a structured clinical review dossier—distinguishing three operational review states (**Ready for review**, **Prioritize review**, **Missing data**) **without issuing an automated fit-for-duty verdict**.

---

## 2. Unified Three-Module Architecture

The application shares a unified `1600px` responsive layout, consistent top navigation, instant `EN | ES` language switching, and Light / Dark mode support with `#EAFE07` high-contrast branding accents:

| Module | Route | Purpose |
| :--- | :--- | :--- |
| **Scientific Module (SANS)** | `/` · `/sans-monitor` | 3-step clinical intake & biomarker predictor, longitudinal timeline (`Before · During · After +30/+60/+90d`), 3 reference scenario models (*Stable · Worsens · Unscanned*), Ground Fundus Python CV Scanner, and NASA scientific references. |
| **Astronaut Check** | `/astronaut-check` | ISS Orbital Flight Log for 6 crew members, 6-step guided 1-by-1 visual check wizard (Acuity, Amsler Grid, Contrast, Symptoms, IOP & Context), Mission Logbook, Biomarker Trends, Profile, and embedded 3D Orbital Eye telemetry. |
| **3D Viewer + OCT** | `/digital-twin?mode=dual` | Interactive Three.js 3D Ocular Digital Twin (with 2D Canvas fallback) synchronized with Cirrus HD-OCT macular thickness & peripapillary RNFL cross-sections and cephalic fluid-shift modeling. |

---

## 3. Core Ocular Biomarkers & Reference Scenarios

Every mission dossier evaluates **6 core ocular biomarkers** across OD (Right Eye) and OS (Left Eye):

1. **OCT Macular / Peripapillary Thickness ($\Delta\text{TRT}$)**
2. **Axial Length**
3. **Hyperopic Shift**
4. **Frisén Papilledema Grade**
5. **Intraocular Pressure (IOP)**
6. **Head-Down Tilt ($-6^\circ$ HDT) / Cephalic Fluid-Shift Exposure**

Three canonical reference models allow flight surgeons and researchers to compare incoming telemetry against known trajectories:
- **Stable (`CASE-STABLE-01`)** — Minimal $\Delta\text{TRT}$, preserved disc margins, nominal IOP.
- **Worsens (`CASE-PROG-02`)** — Progressive optic disc elevation, choroidal folds, hyperopic shift, and elevated $\Delta\text{TRT}$ requiring prioritized specialist review.
- **Unscanned (`CASE-NOSCAN-03A`)** — Missing baseline or recent OCT scan flagged transparently as **Missing data** rather than assumed normal.

---

## 4. Real Python Computer Vision Fundus & Papilledema Scanner

Embedded directly inside the **Scientific Module**, the **Ground Fundus Scanner & Papilledema Analyzer (Python CV)** processes real retinal fundus images (both orbital downlink captures and user-uploaded fundus photos) using **Pillow, NumPy, and SciPy**:

- **Sobel Disc Rim (`disc_edge_map`):** Measures optic disc margin sharpness and edge gradient energy.
- **Edema Halo Map (`edema_heatmap`):** Quantifies peripapillary nerve fiber layer swelling and halo intensity.
- **Choroidal Folds (`folds_ridge_map`):** Applies bandpass ridge filtering to detect posterior pole choroidal folds.
- **Quantitative Output & Tonometer IOP Correlation:** Reports Frisén Grade estimate, Sobel Rim Sharpness %, Peripapillary Halo %, Choroidal Fold detection, Vessel Tortuosity %, execution telemetry (resolution, pixel count, runtime in ms, optic disc coordinates), and seated/orbital Tonometer IOP correlation.

---

## 5. Crew Roster & Guided Astronaut Check

The **Astronaut Check** module models a realistic 6-astronaut ISS crew roster (`Maj. Elena Rostova`, `Dr. Marcus Thorne`, `Kenji Sato`, `CDR Alex Mercer`, `Dr. Sara Lindqvist`, `Capt. Mateo Vargas`) with persistent case switching across the wizard, logbook, trends, and 3D eye preview:
- **Today & 3D Eye:** Guided 6-step visual check wizard + interactive 3D Orbital Eye Telemetry & Fluid-Shift Monitor.
- **Mission Logbook:** Chronological flight-day records (`FD0`, `FD5`, `FD10`, `+30d`, `+90d`) with verified capture and pending OCT badges.
- **Trends & Profile:** Longitudinal IOP & OCT charts and pseudonymous mission metadata.

---

## 6. Scientific Integrity & NASA Reference Corpus

The platform enforces strict clinical transparency:
- **No Automated Fit-for-Duty Decisions:** Outputs support human specialist review only.
- **No Synthetic Missing-Data Imputation:** Unscanned OCT or IOP fields remain explicitly flagged.
- **Structured NASA & Peer-Reviewed Bibliography (`#nasa-bibliography`):** Integrates 7 canonical references including Mader et al. (2011), NASA-STD-3001 Vol. 2, Lee et al. (2020), NASA HRP SANS Evidence Report, Macias et al. (2020), Marshall-Bowman et al. (2013), and Lawley et al. (2017).

---

## 7. Local Verification (Optional Developer Reference)

Stack: **Laravel 11 (PHP 8.3)** + **Python 3.14 FastAPI Scientific Engine (`:8001`)** + **Blade / Chart.js / Three.js**.

```bash
php artisan scientific:engine status   # Verify FastAPI CV & scientific microservice (:8001)
php artisan scientific:engine test     # Python scientific engine suite (22 passed)
php artisan test                       # Laravel application & integration suite (81 passed, 446 assertions)
```
