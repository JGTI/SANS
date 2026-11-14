# 🌌 SPACE VISION (SANS MONITOR) — PROJECT VISION & SCIENTIFIC SCOPE
## NASA International Space Apps Challenge 2026 · NEXUS × Visual Rehab Center

---

## 1. Purpose
**SPACE VISION — SANS Monitor** structures longitudinal ocular reviews for spaceflight and terrestrial microgravity analogs ($-6^\circ$ head-down tilt). It retrieves previously applied clinical protocols, surfaces verified protocol updates and peer-reviewed NASA literature, tracks per-eye changes (`OD` / `OS`) across **Before Flight**, **During Flight (Days 10, 15, 30)**, and **Post-Flight Controls (+30, +60, +90 days)**, and evaluates **6 core ocular biomarkers** to help medical teams prepare surveillance **without issuing an automated fit-for-duty verdict**.

---

## 2. Three-Tab Core Architecture
1. **Tab 1 — Initial Subject Form & 6-Biomarker Predictor:**
   - Evaluates **Mean OCT Thickness ($\mu\text{m}$)**, **Eye Axial Length ($\text{mm}$)**, **Simulated Hyperopia ($\Delta SE$ in $\text{D}$)**, **Papilledema (Frisén Grade $0\text{–}5$)**, **Intraocular Pressure ($\text{mmHg}$)**, and **Prior $-6^\circ$ Head-Down Tilt on Earth**.
2. **Tab 2 — Longitudinal Timeline (Before · During · After +30, +60, +90 Days):**
   - Separates historical observation availability (`available_day`) from synthetic scenario interpolation and never interpolates missing post-flight visits (`+60d`).
3. **Tab 3 — 3 Reference Scenario Models:**
   - **1. Stable (`CASE-STABLE-01`)** $\to$ *Ready for review*.
   - **2. Worsening (`CASE-PROG-02`)** $\to$ *Prioritize review*.
   - **3. Unscanned (`CASE-NOSCAN-03A`)** $\to$ *Missing data*.

---

## 3. Delivery Phases (`P0` $\to$ `P1` $\to$ `P2`)
- **P0 (Implemented Public Demo):** Zero-login bilingual (`EN | ES`) workstation with calibrated synthetic and $-6^\circ$ HDT analog scenarios, deterministic ophthalmic engine, and 99 automated tests.
- **P1 (Authorized Research Environment):** Curated multi-center clinical imports with row-level provenance and human clinical sign-off.
- **P2 (Prospective Validated Prognosis):** External prospective validation of papilledema prognostic models prior to clinical deployment.
