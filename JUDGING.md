# 🏆 SPACE VISION (SANS MONITOR) — NASA SPACE APPS 2026 JUDGING MATRIX

> *Developed by NEXUS × Visual Rehab Center for the NASA International Space Apps Challenge 2026.*  
> Official Reference: [NASA Space Apps Judging & Awards Guide](https://www.spaceappschallenge.org/resources/judging-awards-guide/)

This document maps **SPACE VISION — SANS Monitor** against the five official NASA International Space Apps Challenge evaluation criteria: **Impact, Creativity, Validity, Relevance, and Presentation**.

---

## Quick Verification for Judges (Zero Login Required)
- **Live Production URL:** [https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com](https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com)
- **Direct Public Demo (No Login / Works in Incognito):** `/demo` or `/sans-monitor` or `/admin/sans-monitor`
- **Automated Test Verification:** **75 PHPUnit tests (353 assertions) + 22 Python Pytest tests = 97 automated tests passing (100% green).**

---

## 1. IMPACT (Impacto)

| Evaluation Aspect | Evidence & Implementation in SPACE VISION | Location in App / Repo | Backing Sources |
|---|---|---|---|
| **Problem Addressed** | During long-duration spaceflight (ISS, Artemis lunar missions, Mars transit), cephalad fluid shifts induce **Spaceflight Associated Neuro-ocular Syndrome (SANS)**: optic disc edema, globe flattening, choroidal folds, and hyperopic shifts ($\Delta SE \ge +0.50\ \text{D}$). Clinical follow-up is often fragmented across pre-flight, in-flight, and post-flight visits. | `/demo` (Tab 1 & Tab 2), [`docs/SANS_MONITOR_SPEC.md`](docs/SANS_MONITOR_SPEC.md) | NASA OCHMO Technical Brief (`ochmo-mtb-001-sans.pdf`); Macias et al., *JAMA Ophthalmol* (2020) |
| **Who It Affects & Why It Matters** | Affects astronauts on long-duration orbital/deep-space missions and terrestrial analog participants ($-6^\circ$ Head-Down Tilt bed rest). Unchecked neuro-ocular progression threatens near visual acuity during critical mission tasks and requires structured post-flight surveillance at **+30, +60, and +90 days**. | `/demo?tab=seguimiento` | Laurie et al. (2019); Carter et al. (2025) |
| **Demonstrated vs. Potential Impact** | **Demonstrated (P0):** Unifies ocular records per eye (OD/OS) into a single traceable timeline, flags missing baselines or unverified scans without falsely labeling them as "stable", and separates terrestrial analog baselines from orbital baselines. **Potential (P1/P2):** Standardized longitudinal dataset preparation for multi-mission cohort research and validated prognostic modeling. | [`app/Services/SansMonitor/SansMonitorEngine.php`](app/Services/SansMonitor/SansMonitorEngine.php) | NASA HRP Risk Research Plan (2026) |

---

## 2. CREATIVITY (Creatividad)

| Evaluation Aspect | Evidence & Implementation in SPACE VISION | Location in App / Repo |
|---|---|---|
| **Originality of Approach** | Instead of a black-box classifier that claims automatic "fit/unfit for flight" verdicts, SPACE VISION introduces a **6-Biomarker Explainable Predictor + 3-Phase Temporal Cut (+30/+60/+90 days) + 3 Reference Scenario Models (Stable, Worsens, Unscanned)**. | `/demo?tab=biomarcadores` & `/demo?tab=escenarios` |
| **Hybrid Scientific Architecture** | Combines a resilient web orchestrator (Laravel 11 / PHP 8.3) with a dedicated **Python 3.14 FastAPI Scientific Engine (`:8001`)** running NumPy 2.5, SciPy 1.18, and Scikit-learn 1.9, plus synchronized vector schematics (Sagittal Globe, Optic Disc Fundus, OCT Layers), a 3D WebGL Digital Twin, and a 9-panel ZEISS Cirrus™ HD-OCT clinical workstation replica. | [`scientific_engine/app/`](scientific_engine/app/), `/admin/digital-twin?mode=dual` |
| **Epistemic Honesty in UI** | Explicitly differentiates between **Historical Playback** (where a Day 15 scan whose result becomes available on Day 20 remains hidden at Day 15) and **Illustrative Simulation** (where interpolated frames are badged and post-range days show *"Out of range"* rather than extrapolating). | [`SansMonitorAcceptanceTest.php`](tests/Feature/SansMonitorAcceptanceTest.php) (`AT-03`, `AT-04`, `AT-16`) |

---

## 3. VALIDITY (Validez Científica y Técnica)

| Evaluation Aspect | Evidence & Implementation in SPACE VISION | Verification |
|---|---|---|
| **Deterministic Ophthalmic Math** | Spherical Equivalent ($SE = \text{SPH} + \text{CYL}/2$), Snellen to LogMAR ($\log_{10}(D/N)$), Thibos Fourier power vectors ($M, J_0, J_{45}, B$), and peripapillary Total Retinal Thickness ($\Delta\text{TRT}$) against eye-specific compatible baselines. | `OphthalmologyCalculatorTest.php` & `test_ophthalmology.py` |
| **Strict Distinction of Output States** | Emits three operational review states (**"Listo para revisión"**, **"Priorizar revisión"**, **"Faltan datos"**) and keeps fitness status as **"Aptitud no evaluada"** unless a human specialist signs a separate `ClinicianDecision` record (`RF-10`, `RF-11`). | `SansMonitorAcceptanceTest.php` (`AT-01`, `AT-05`, `AT-08`) |
| **Separation of Analog ($-6^\circ$ HDT) vs. Spaceflight** | Terrestrial head-down tilt exposures (`ExposureSession` / `ExposureSegment`) store multi-angle segments without hidden averaging, use exposure end as Day 0 for recovery ($280 \to 304\ [+24\ \mu\text{m}] \to 284\ [+4\ \mu\text{m}]$ at +30d), and never mix analog baselines with orbital flight baselines. | `SansMonitorAcceptanceTest.php` (`AT-10` to `AT-14`, `AT-19`) |
| **Documented Limitations** | Synthetic demo cohorts (`source_type = FICTICIO_DEMO`) are clearly labeled; missing `+60` post-flight visits are never interpolated from `+30` and `+90`; contextual domains (neurological, metabolic, cardiovascular, environmental $\text{CO}_2$, behavioral) are displayed as secondary research context without altering the ocular assessment. | [`docs/SANS_MONITOR_SPEC.md`](docs/SANS_MONITOR_SPEC.md) |

---

## 4. RELEVANCE (Relevancia para el Reto NASA)

**Traceability Chain:**  
`NASA Human Research Program SANS Risk` $\to$ `Longitudinal Ocular Biomarker Surveillance & Post-Flight Recovery (+30/+60/+90d)` $\to$ `Curated NASA OCHMO / HRP / OSDR / NTRS Resources` $\to$ `SPACE VISION 3-Tab Predictor & Timeline` $\to$ `Verifiable Review Preparation`.

| Official Resource / Dataset | Type | Official URL / Identifier | How It Is Integrated in SPACE VISION |
|---|---|---|---|
| **NASA OCHMO SANS Technical Brief** | Operational Technical Reference | https://www.nasa.gov/wp-content/uploads/2024/02/ochmo-mtb-001-sans.pdf | Preloaded in `sans_protocol_versions` (`OCHMO-MTB-001-V2022` & `V2024`) to demonstrate version lineage and pre/in/post-flight surveillance rules. |
| **NASA Human Research Program (HRP) Risk Plan 2026** | Research Program Plan | https://www.nasa.gov/wp-content/uploads/2026/08/hrp-rap-for-web-62026-hhc.pdf | Governs the 5 secondary context domains (`sans_context_records`) as open research questions rather than proven causes. |
| **NASA Life Sciences Portal (NLSP) & OSDR** | Biological Datasets | https://www.nasa.gov/hrp/nlsp/ · `OSD-137`, `OSD-370`, `OSD-422`, `OSD-514` | Integrated via `NasaDataService` with offline cache fallback in `storage/app/nasa_cache/` and protocol catalog links. |
| **Peer-Reviewed NASA JSC / SANS Literature** | Scientific Publications | Macias et al. (`10.1001/jamaophthalmol.2020.0673`), Laurie et al. (`10.1001/jamaophthalmol.2019.5261`), Carter et al. (`10.1001/jamaophthalmol.2025.4635`), Specialist Consensus (`10.1038/s41433-026-04651-6`) | Defines the $\Delta\text{TRT} \ge 20\ \mu\text{m}$ early signal, $\ge 55\ \mu\text{m}$ primary marker, Day 30 / Day 150 checkpoints, and $-6^\circ$ HDT analog separation. |

---

## 5. PRESENTATION (Presentación y Accesibilidad)

1. **Zero-Friction Public Access:** Judges open `/` and click **"Explore the Demo · Launch SPACE VISION (No Login Required)"** to immediately test the 3 core tabs in `/demo` or `/sans-monitor`.
2. **Streamlined 3-Tab Cognitive Architecture:**
   - **Tab 1 — Initial Form & 6 Biomarkers Predictor:** OCT mean thickness, axial length, simulated hyperopia, papilledema (Frisén), IOP, and prior $-6^\circ$ head-down tilt exposure on Earth.
   - **Tab 2 — Timeline Cut (Before · During · After +30, +60, +90 Days):** Clear longitudinal progression and post-flight recovery cuts with Chart.js high-contrast visualization.
   - **Tab 3 — 3 Reference Scenario Models (Stable · Worsens · Unscanned):** 1-click comparison with synchronized anatomical SVG schematics (Sagittal, Fundus, OCT layers).
3. **Accessibility (WCAG 2.2 AA Principles):** High-contrast monochrome palette (`#09090b` / `#ffffff`), keyboard-operable selectors and tabs, explicit text + unit + status labels (never relying on color alone).
