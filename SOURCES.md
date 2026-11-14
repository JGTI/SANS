# 📚 SPACE VISION (SANS MONITOR) — SCIENTIFIC, DATA & TECHNICAL SOURCES

> *Developed by NEXUS × Visual Rehab Center for the NASA International Space Apps Challenge 2026.*

This document provides full transparency regarding all scientific publications, NASA datasets, software libraries, visual assets, and AI engineering tools used in **SPACE VISION — SANS Monitor**.

---

## 1. Normative & Peer-Reviewed Scientific References (Preloaded in Protocol Catalog)

1. **NASA Office of the Chief Health and Medical Officer (OCHMO):**
   - *Title:* Spaceflight Associated Neuro-ocular Syndrome (SANS) Medical Technical Brief (`OCHMO-MTB-001`).
   - *URL:* https://www.nasa.gov/wp-content/uploads/2024/02/ochmo-mtb-001-sans.pdf
   - *Role in Project:* Technical reference for pre-, in-, and post-flight surveillance milestones (`OCHMO-MTB-001-V2022` and `V2024`). Not an authorization for automated clinical verdicts.
2. **Specialist Consensus on SANS Diagnosis and Management (2026):**
   - *DOI:* https://doi.org/10.1038/s41433-026-04651-6
   - *Role in Project:* Basis for separating clinical findings, research thresholds ($\Delta\text{TRT} \ge 20\ \mu\text{m}$ early signal; $\ge 55\ \mu\text{m}$ primary marker), and demo milestones.
3. **Macias et al. (2020) — Structural Ocular Changes and Recovery:**
   - *DOI:* https://doi.org/10.1001/jamaophthalmol.2020.0673
   - *Role in Project:* Documents differences between in-flight orbital measurements and pre/post-flight recovery trajectories.
4. **Carter et al. (2025) — Optic Disc Edema Progression Study:**
   - *DOI:* https://doi.org/10.1001/jamaophthalmol.2025.4635
   - *Role in Project:* Reference for Day 30 and Day 150 ocular progression analysis (not an individual fitness predictor).
5. **Laurie et al. (2019) — Spaceflight vs. Terrestrial Head-Down Tilt Bed Rest Analog ($-6^\circ$ HDT):**
   - *DOI:* https://doi.org/10.1001/jamaophthalmol.2019.5261
   - *Role in Project:* Scientific basis for keeping terrestrial $-6^\circ$ head-down tilt exposures (`ExposureSession`) and orbital missions (`Mission`) on separate baselines and timelines (`AT-19`).
6. **NASA Human Research Program (HRP) Risk Research Plan (2026):**
   - *URL:* https://www.nasa.gov/wp-content/uploads/2026/08/hrp-rap-for-web-62026-hhc.pdf
   - *Role in Project:* Supports treating physiological and environmental covariates (neurological, metabolic, cardiovascular, ambient $\text{CO}_2$, behavioral) as open research questions in a secondary context layer (`sans_context_records`).
7. **NASA Life Sciences Portal (NLSP):**
   - *URL:* https://www.nasa.gov/hrp/nlsp/
   - *Role in Project:* Official gateway for requesting authorized human spaceflight life-sciences data (P1/P2).

---

## 2. NASA Open Repositories & APIs Integrated

| Repository / API | Identifiers / Endpoints | Integration Method & Offline Resilience |
|---|---|---|
| **NASA OSDR (Open Science Data Repository)** | `OSD-137`, `OSD-370`, `OSD-422`, `OSD-514` | `App\Services\NasaDataService` + curated offline fallback in `storage/app/nasa_cache/` so the demo runs 100% offline (`RNF-05`). |
| **NASA NTRS (Technical Reports Server)** | `20200002341`, `20180004910`, `20220011504` | Indexed in `NasaDataService` and TF-IDF semantic search in `scientific_engine/app/research_intelligence/`. |
| **NASA DONKI (Space Weather Database)** | CME / Solar Flare / GST endpoints | Contextual environmental telemetry with explicit non-causality medical disclaimer. |
| **NASA APOD** | Astronomy Picture of the Day | Optional visual telemetry enrichment with local cache fallback. |

---

## 3. Open-Source Software, Frameworks & Libraries

- **Backend & Web Framework:** Laravel 11 (PHP 8.3), Spatie Laravel-Permission (MIT License).
- **Scientific Computing Engine:** Python 3.14, FastAPI, Pydantic, Uvicorn, NumPy 2.5, SciPy 1.18, Scikit-learn 1.9, Pandas 3.1 (BSD / MIT Licenses).
- **Frontend & Visualization:** Blade templates, Chart.js 4 (MIT), Three.js WebGL (MIT), Font Awesome 4.7 (SIL OFL / MIT).
- **Testing Suites:** PHPUnit 11 (75 tests / 353 assertions) and Pytest 9 (22 tests).

---

## 4. AI Assistance & Authorship Transparency

- AI coding assistants (Claude / Gemini / Antigravity / Codex) were used for code generation, refactoring, and documentation structuring under direct human architecture and clinical domain specification by **NEXUS × Visual Rehab Center**.
- Every ophthalmic equation, timeline rule, and acceptance test (`AT-01` through `AT-19`) is deterministically coded and verified by automated unit and feature tests.
