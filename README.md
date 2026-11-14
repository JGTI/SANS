# 🚀 SPACE VISION — SANS Monitor
### Biomarker-Driven Neuro-Ocular Surveillance & Longitudinal Timeline (+30 / +60 / +90 Days)
**Team:** NEXUS × Visual Rehab Center · **Event:** NASA International Space Apps Challenge 2026

> *"Developed by NEXUS × Visual Rehab Center for the NASA International Space Apps Challenge 2026."*  
> **Live Public Demo (Zero Login Required · Primary Language: English `EN` with `EN | ES` Toggle):**  
> `https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com`

---

## 1. Executive Summary

**SPACE VISION (SANS Monitor)** transforms fragmented aerospace ocular records into a traceable longitudinal review across **Before Flight (Baseline)**, **During Exposure/Flight (Days 10 → 15 → 30)**, and **Post-Flight Controls (+30, +60, and +90 days)**.

Built for the **NASA International Space Apps Challenge 2026**, the platform enables immediate public evaluation **without requiring any login, registration, OAuth, or API keys**:

1. **Tab 1 — Initial Subject Form & 6-Biomarker Predictor:**
   - **1. Mean OCT Thickness ($\mu\text{m}$):** Baseline vs. current Total Retinal Thickness ($\Delta\text{TRT} \ge +20\ \mu\text{m}$ progression trigger).
   - **2. Eye Axial Length ($\text{mm}$):** Axial shortening ($\Delta \le -0.15\ \text{mm}$) associated with posterior globe flattening.
   - **3. Simulated Hyperopia ($\Delta SE$ in Diopters $\text{D}$):** Hyperopic refractive shift ($+0.50\ \text{D}$ to $+1.50\ \text{D}$) kept strictly separate from measured clinical observations (`RF-35`).
   - **4. Papilledema (Frisén Grade $0\text{–}5$):** Primary neuro-ocular prognostic target (`RF-36`).
   - **5. Intraocular Pressure — IOP ($\text{mmHg}$):** Ocular tonometry displayed even when OCT scans are unavailable (`AT-17`).
   - **6. Prior $-6^\circ$ Head-Down Tilt Exposure on Earth (`-6° HDT`):** 14-day terrestrial analog ($280 \to 304 \to 284\ \mu\text{m}$) tracked on its own separate baseline (`AT-19`).
2. **Tab 2 — Longitudinal Timeline: Before · During · After (+30, +60, +90 Days):**
   - Compares pre-flight baseline, in-flight progression, $-6^\circ$ HDT recovery, and post-flight milestones at **+30, +60, and +90 days** without ever interpolating missing visits (`AT-16`).
   - Includes an interactive **Historical vs. Synthetic Simulation Playback bar** (`Day 0, 10, 15, 20, 30, 180` — `AT-03`, `AT-04`).
3. **Tab 3 — 3 Reference Scenario Models (1-Click Comparison):**
   - **1. Stable Scenario (`CASE-STABLE-01`)** $\to$ Operational State: **Ready for review**.
   - **2. Worsening Scenario (`CASE-PROG-02`)** $\to$ Operational State: **Prioritize review**.
   - **3. Unscanned Scenario (`CASE-NOSCAN-03A`)** $\to$ Operational State: **Missing data** (never assumed or drawn as stable).

---

## 2. Public Zero-Login Access & Bilingual Interface (`EN | ES`)

Any judge, researcher, or visitor can explore the entire application immediately without logging in:

- **Primary Root Entry (`/`)**: Opens the 3-tab **SANS Biomarker Predictor** in **English (`EN`)** by default, with a 1-click **`EN | ES`** language selector in the top bar and a **Reset Demo** button.
- **Direct Public Routes (No Authentication Required):**
  - `/` · `/demo` · `/sans-monitor` — Main 3-Tab SANS Predictor, NASA Protocol Drawer (`AT-02`), and NASA Judging Matrix.
  - `/digital-twin?mode=dual` — Dual 3D WebGL Anatomical Eye Viewer + ZEISS Cirrus™ HD-OCT Macular Cube 512×128 Console.
  - `/demo-theater` — 3-Minute Guided Pitch Walkthrough & Printable Aerospace Medical Report (`/demo-theater/report/ASTRO-01`).
  - `/vision-lab` — Deterministic Ophthalmology Calculator ($SE$, Snellen $\to$ LogMAR, Thibos vectors $M, J_0, J_{45}$) & CSV Export.
  - `/nasa-data` — NASA Data Observatory (OSDR, NTRS, DONKI) with local resilient cache.
  - `/model-lab` & `/explainability` — Multi-Classifier Benchmark (Random Forest, Gradient Boosting, OLS) & SHAP Feature Attribution.

---

## 3. Scientific & Ethical Integrity

1. **Implemented Deterministic Engine:** Real mathematical formulations for Spherical Equivalent ($SE = \text{Sph} + \frac{\text{Cyl}}{2}$), Snellen to LogMAR ($-\log_{10}(\text{Decimal})$), Thibos power vectors ($M, J_0, J_{45}$), and rule-based longitudinal coverage evaluation.
2. **Calibrated Synthetic & Analog Scenarios:** Pre-loaded cases (`CASE-STABLE-01`, `CASE-PROG-02`, `CASE-NOSCAN-03A`, `CASE-NORECENT-03B`, `CASE-TILT-04`) are explicitly labeled (`FICTICIO_DEMO` / `ANALOGO_TERRESTRE`) and calibrated to ranges published in NASA OCHMO and JAMA Ophthalmology literature. No private patient data is used.
3. **Non-Diagnostic Decision Support:** The software distinguishes three operational review states (**Ready for review**, **Prioritize review**, **Missing data**) and always displays **Fitness not evaluated** unless signed by an authorized clinician.

---

## 4. Technical Architecture & Verification

- **Frontend:** Laravel Blade, High-Contrast Monochrome UI (WCAG 2.2 AA compliant, `prefers-reduced-motion` support, keyboard `:focus-visible`), Chart.js, Three.js WebGL, Font Awesome 4.7.
- **Backend Application Layer:** Laravel 11 / PHP 8.3 (`SansMonitorEngine`, `OphthalmologyCalculator`, `NasaDataService`).
- **Scientific Microservice:** Python 3.14 / FastAPI (`:8001`) with NumPy, SciPy, Pandas, and Scikit-learn.

### Running Locally & Executing the Automated Test Suites
```bash
composer install
php artisan migrate --seed
php artisan scientific:engine start
php artisan test                     # 77 passed (372 assertions)
php artisan scientific:engine test   # 22 passed (pytest)
```

---

## 5. Official Documentation Index

- **[`JUDGING.md`](JUDGING.md):** Evaluation matrix mapped to NASA's 5 criteria (*Impact, Creativity, Validity, Relevance, Presentation*).
- **[`SUBMISSION.md`](SUBMISSION.md):** Official project submission summary in English.
- **[`SOURCES.md`](SOURCES.md):** Full traceability of NASA datasets, scientific literature, open-source libraries, and AI tool disclosure.
- **[`DEPLOYMENT.md`](DEPLOYMENT.md):** Deployment and public zero-login verification guide.
- **[`docs/SANS_MONITOR_SPEC.md`](docs/SANS_MONITOR_SPEC.md):** Canonical software specification (`RF-01`–`RF-37`, `AT-01`–`AT-19`).
- **[`docs/DEMO_SCRIPT.md`](docs/DEMO_SCRIPT.md):** 3-minute live demo script and 30-second / 7-slide storyboard.
- **[`docs/PROJECT_CHECKLIST.md`](docs/PROJECT_CHECKLIST.md):** Complete implementation and verification checklist.
