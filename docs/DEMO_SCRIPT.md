# 🎬 SPACE VISION (SANS MONITOR) — OFFICIAL PITCH & DEMO SCRIPT
## NASA International Space Apps Challenge 2026 · NEXUS × Visual Rehab Center

> **Public Demo URL (Zero Login Required · Primary Language: English `EN` with `EN | ES` Toggle):**  
> `https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com`

---

## PART A — 30-SECOND VIDEO / 7-SLIDE STORYBOARD (INTERNATIONAL JUDGING)

| Slide / Time | Section | Visual on Screen | Spoken Script (English) |
|---|---|---|---|
| **Slide 1 (0:00–0:04)** | **1. The Problem** | Root URL (`/`) showing the SANS Biomarker Predictor header and `CASE-PROG-02` | *"During prolonged spaceflight, up to 70% of astronauts develop Spaceflight Associated Neuro-ocular Syndrome (SANS)—optic disc edema, axial shortening, and hyperopic shifts—while clinical records remain fragmented across mission phases."* |
| **Slide 2 (0:04–0:09)** | **2. SPACE VISION** | Subject selector (`CASE-STABLE-01`, `CASE-PROG-02`, `CASE-NOSCAN-03A`) and `EN | ES` toggle | *"We built SPACE VISION — SANS Monitor: an open, zero-login clinical decision-support workstation that unifies ocular records per eye across Before, During, and Post-Flight controls (+30, +60, and +90 days)."* |
| **Slide 3 (0:09–0:15)** | **3. Live Demo (Tab 1: 6 Biomarkers)** | Tab 1: Modifying the 6 biomarkers and clicking **Run Predictor** | *"In Tab 1, anyone can enter a subject's data and evaluate six key biomarkers: Mean OCT thickness, Axial length, Simulated hyperopia, Papilledema Frisén grade, Intraocular pressure, and prior -6° head-down tilt exposure on Earth."* |
| **Slide 4 (0:15–0:20)** | **4. Innovation (Tab 2: +30/+60/+90d)** | Tab 2: Timeline chart showing the missing +60d visit un-interpolated and the AT-03/AT-04 playback bar | *"In Tab 2, we track Before, During (Days 10, 15, 30), and Post-Flight cuts (+30, +60, +90 days)—preserving missing visits like +60d without false interpolation and separating -6° Earth analogs from orbital baselines."* |
| **Slide 5 (0:20–0:24)** | **5. 3 Scenario Models & 3D+OCT** | Tab 3: Clicking **1. Stable**, **2. Worsening**, and **3. Unscanned**, plus dual 3D WebGL + OCT view | *"In Tab 3, judges compare three reference scenarios in one click: Stable, Worsening, and Unscanned—where missing OCT scans are never assumed normal."* |
| **Slide 6 (0:24–0:27)** | **6. NASA Relevance** | Opening the **NASA Protocols & 7 Scientific References (`AT-02`)** drawer | *"Every rule is grounded in NASA OCHMO-MTB-001, NASA-STD-3001, SLS-SPEC-159, OSDR, and NTRS literature, distinguishing verified protocol versions from research articles."* |
| **Slide 7 (0:27–0:30)** | **7. Results & Ethical Impact** | Header badges: **Prioritize review** & **Fitness not evaluated** | *"Backed by 99 automated tests in Laravel and Python, SPACE VISION prepares longitudinal medical surveillance without issuing automated fit-for-duty verdicts."* |

---

## PART B — 3-MINUTE LIVE WALKTHROUGH FOR JUDGES (NO LOGIN REQUIRED)

1. **Open the Root URL (`/`) — Zero Login & Bilingual `EN | ES` (0:00 – 0:30):**
   - Open `/` in any browser or incognito window. It loads directly in **English (`EN`)** with no login required, featuring a 1-click **`EN | ES`** toggle and a **Reset Demo** button.
2. **Tab 1 — Feed the 6 Ocular Biomarkers (0:30 – 1:15):**
   - Inspect **1. Mean OCT Thickness** ($280 \to 306\ \mu\text{m}$, $\Delta +26\ \mu\text{m}$), **2. Eye Axial Length** ($23.82\ \text{mm}$), **3. Simulated Hyperopia** ($+0.75\ \text{D}$), **4. Papilledema Frisén Scale** (Grade 2/5), **5. IOP** ($18.5\ \text{mmHg}$), and **6. Prior $-6^\circ$ Head-Down Tilt on Earth**.
   - Click **Run Predictor with these 6 Biomarkers** to compute the explainable Signal Index and feature weight chart.
3. **Tab 2 — Longitudinal Timeline: Before · During · After (+30, +60, +90 Days) (1:15 – 2:00):**
   - Switch to **Tab 2** to compare Before Flight ($280\ \mu\text{m}$), During Flight ($+8 \to +14 \to +26\ \mu\text{m}$), and Prior $-6^\circ$ Tilt on Earth ($280 \to 304 \to 284\ \mu\text{m}$).
   - Verify the **+30, +60, and +90 days post-flight cards**, noting that the missing **+60 days** cut is explicitly marked **Unmeasured** and never interpolated.
4. **Tab 3 — 3 Reference Scenario Models & Dual 3D + OCT Workstation (2:00 – 3:00):**
   - Switch to **Tab 3** and click **1. Stable Scenario (`CASE-STABLE-01`)**, **2. Worsening Scenario (`CASE-PROG-02`)**, and **3. Unscanned Scenario (`CASE-NOSCAN-03A`)**.
   - Click **Open 3D Viewer + OCT** (`/digital-twin?mode=dual`) to explore the interactive 3D WebGL anatomical globe alongside the 9-panel ZEISS Cirrus™ HD-OCT console.
