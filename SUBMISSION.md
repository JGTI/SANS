# 🚀 SPACE VISION (SANS MONITOR) — OFFICIAL PROJECT SUBMISSION DRAFT
## NASA International Space Apps Challenge 2026 (English & Español)

> *Developed by NEXUS × Visual Rehab Center for the NASA International Space Apps Challenge 2026.*

---

## 1. Project Identity (English — International Judging Ready)

- **Project Name:** SPACE VISION — SANS Monitor
- **Team Name:** NEXUS × Visual Rehab Center (Querétaro, México)
- **Live Public Demo URL (No Login Required):** https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com/demo
- **Landing Page URL:** https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com
- **Public GitHub Repository:** https://github.com/JGTI/SANS

### Executive Summary (Short Description)
**SPACE VISION (SANS Monitor)** turns fragmented ocular exams into a traceable longitudinal review across spaceflight and terrestrial head-down tilt ($-6^\circ$) exposures. Using an interactive **6-Biomarker Predictor** (mean OCT retinal thickness, axial length, simulated hyperopia, Frisén papilledema grade, intraocular pressure, and prior $-6^\circ$ head-down tilt exposure on Earth), a **Before · During · After (+30, +60, +90 days) Timeline**, and **3 Reference Scenario Models (Stable, Worsens, Unscanned)**, the platform helps medical and research teams prepare ocular surveillance and prioritize reviews **without issuing automated fitness-for-duty verdicts**.

### Problem Description
In microgravity (ISS, Artemis lunar missions, and future Mars transit), cephalad fluid redistribution triggers **Spaceflight Associated Neuro-ocular Syndrome (SANS)**—characterized by optic disc edema, posterior globe flattening, choroidal folds, and hyperopic refractive shifts ($\Delta SE \ge +0.50\ \text{D}$). Clinical teams must compare baseline, in-flight (Days 10, 15, 30), and post-flight recovery controls (+30, +60, +90 days) per eye while distinguishing true stability from missing or non-comparable scans.

### Proposed Solution & How It Works
SPACE VISION organizes ocular surveillance into **three streamlined, zero-login public tabs** (`/demo` or `/sans-monitor`):
1. **Tab 1 — Initial Subject Form & 6-Biomarker Predictor:** Captures pseudonymous subject metadata and evaluates six core ocular biomarkers:
   - Mean OCT Peripapillary Thickness ($\text{TRT}$ in $\mu\text{m}$ — baseline vs. current).
   - Ocular Axial Length ($\text{mm}$).
   - Simulated Hyperopia ($\Delta SE$ in Diopters $\text{D}$).
   - Optic Disc Edema / Papilledema (Frisén Scale $0\text{–}5$).
   - Intraocular Pressure — IOP ($\text{mmHg}$).
   - Prior $-6^\circ$ Head-Down Tilt Exposure on Earth (terrestrial analog history).
2. **Tab 2 — Longitudinal Timeline: Before · During · After (+30, +60, +90 Days):** Tracks eye-specific changes before exposure, during flight (Days 10 $\to$ 15 $\to$ 30), and across post-flight recovery cuts at **+30, +60, and +90 days**, strictly respecting data availability dates (`available_at`) and never interpolating missing visits (e.g., a missing `+60` visit is explicitly shown as *"Sin medición"*).
3. **Tab 3 — Three Reference Scenario Models (Stable · Worsens · Unscanned):** 1-click interactive comparison across `CASE-STABLE-01` (*Ready for review*), `CASE-PROG-02` (*Prioritize review*, $\Delta\text{TRT} +8 \to +14 \to +26\ \mu\text{m}$), and `CASE-NOSCAN-03A` (*Missing data*, IOP recorded without OCT, never misclassified as stable), paired with synchronized Sagittal, Fundus, and OCT Layer vector schematics plus a 3D WebGL Digital Twin and ZEISS Cirrus™ HD-OCT workstation.

### NASA Data & Scientific Resources Used
- **NASA OCHMO Technical Brief:** *Spaceflight Associated Neuro-ocular Syndrome (SANS)* (`ochmo-mtb-001-sans.pdf`).
- **NASA Human Research Program (HRP):** *Risk Research Plan 2026* (`hrp-rap-for-web-62026-hhc.pdf`) & **NASA Life Sciences Portal (NLSP)**.
- **NASA Open Science Data Repository (OSDR):** `OSD-137`, `OSD-370`, `OSD-422`, `OSD-514`.
- **NASA Technical Reports Server (NTRS) & Peer-Reviewed Literature:** Macias et al. (*JAMA Ophthalmol* 2020), Laurie et al. (*JAMA Ophthalmol* 2019), Carter et al. (*JAMA Ophthalmol* 2025), and Specialist Consensus (*Eye* 2026).
- **NASA DONKI:** Space weather context telemetry with explicit non-causality scientific disclaimers.

### Methodology, Results & Limitations
- **Implemented & Verified:** 13 canonical data contract tables, deterministic ophthalmic math engine, curated protocol catalog with version lineage, 3-tab public workspace, and **97 automated tests passing (75 PHPUnit / 353 assertions + 22 Python Pytest)**.
- **Scientific Limitations:** Public demo cases use calibrated synthetic data (`FICTICIO_DEMO`); the app prepares surveillance and flags missing data or progression signals, but **does not diagnose SANS, prescribe countermeasures, or certify flight fitness**.

### Declaration of AI & Software Tools Used
- **Stack:** Laravel 11 (PHP 8.3), Python 3.14 (FastAPI, NumPy 2.5, SciPy 1.18, Scikit-learn 1.9, Pandas 3.1), MariaDB, Chart.js, Three.js WebGL, Font Awesome 4.7.
- **AI Engineering Assistants:** Used as pair-programming and documentation copilots under human engineering and clinical domain supervision; all mathematical formulas, rules, and 97 automated tests are deterministic and verified in code.
