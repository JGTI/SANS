# 📋 SPACE VISION / SANS MONITOR — ENGINEERING BACKLOG

> **Canonical Specification:** [`docs/SANS_MONITOR_SPEC.md`](SANS_MONITOR_SPEC.md)

---

## Completed Epics (P0 — Public Hackathon Deliverable)

- [x] **Epic 1 — Public Zero-Login Architecture & Bilingual `EN | ES` UI:**
  - Root URL (`/`) serves the 3-Tab SANS Biomarker Predictor directly in **English (`EN`)** by default with a 1-click `EN | ES` toggle and `Reset Demo` button.
- [x] **Epic 2 — 6-Biomarker Predictor & Subject Intake (`RF-01`–`RF-04`, `RF-35`, `RF-36`):**
  - Interactive evaluation of (1) Mean OCT Thickness, (2) Eye Axial Length, (3) Simulated Hyperopia, (4) Papilledema Frisén Grade, (5) Intraocular Pressure, and (6) Prior $-6^\circ$ Head-Down Tilt Exposure on Earth.
- [x] **Epic 3 — Longitudinal Timeline: Before · During · After (+30, +60, +90 Days) (`RF-10`–`RF-13`, `RF-18`–`RF-22`, `RF-31`–`RF-34`):**
  - Per-eye tracking (`OD` / `OS`), interactive Historical vs. Synthetic Simulation playback (`AT-03`, `AT-04`), and un-interpolated missing post-flight milestones (`+60d`, `AT-16`).
- [x] **Epic 4 — 3 Reference Scenario Models & Dual 3D + OCT Workstation:**
  - One-click comparison of **Stable (`CASE-STABLE-01`)**, **Worsening (`CASE-PROG-02`)**, and **Unscanned (`CASE-NOSCAN-03A`)**, plus public access to the 3D WebGL Globe & ZEISS Cirrus™ HD-OCT Console.
- [x] **Epic 5 — Curated NASA Protocols, $-6^\circ$ HDT Analog Comparator & 19 Acceptance Tests (`AT-01`–`AT-19`):**
  - Full verification across 77 PHPUnit tests and 22 Python Pytest tests.

---

## Future Research Epics (Post-Hackathon P1 / P2 Roadmap)

- [ ] **UX Research Workflow — Guided Acquisition and Four Workspaces:**
  - [x] Document the approved functional split: spaceflight, Earth study, study cohort, and results.
  - [x] Select four-destination astronaut navigation after wireframe review (`Today`, `Logbook`, `Trends`, `Profile`).
  - [x] Define the P0 autonomous minimum: calibrated screen tests plus external rebound tonometer; OCT/ultrasound remain advanced modules.
  - [x] Implement the bilingual astronaut P0 shell and four-stage guided check without changing the scientific dataset.
  - [x] Replace generic completion checks and free-entry clinical fields with OD/OS test results, paired-device imports, stage validation, and a read-only specialist baseline.
  - [ ] Define validated capture-quality thresholds and device-ingestion contracts.
  - [ ] Define the terrestrial sampling/allocation protocol and measurement schedule.
  - [ ] Select the primary outcome and statistical analysis plan.
  - [ ] Extend the canonical data contract only after the above decisions are approved.
  - [ ] Implement the remaining terrestrial, cohort, results, and specialist-review screens without mixing synthetic, terrestrial, and orbital evidence.
- [ ] **Phase P1 — Multi-Center Authorized Clinical Data Ingestion:** IRB-approved clinical cohort imports with row-level audit trails.
- [ ] **Phase P2 — Prospective External Validation of Prognostic Models:** Multi-mission external calibration before enabling clinical prognostic deployment.
