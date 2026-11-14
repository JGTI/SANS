# 🏛️ SPACE VISION / SANS MONITOR — ARCHITECTURAL DECISION RECORDS (ADR)

## ADR-001: Preservation of Foundation Architecture
- **Date:** November 10, 2026
- **Context:** A foundation codebase built on Laravel 11 and modern frontend components was available.
- **Decision:** Reuse and adapt the core infrastructure to the **SPACE VISION** visual identity and domain architecture rather than starting from scratch.
- **Consequences:** Accelerates hackathon engineering velocity while leveraging tested framework components.

## ADR-002: Public Zero-Login Access Model for Hackathon Demonstration
- **Date:** November 14, 2026
- **Context:** Judges, ground scientists, clinical investigators, and astronauts need immediate, frictionless access to all modules during the NASA International Space Apps Challenge 2026 evaluation.
- **Decision:** Operate all demonstration, scientific, and guided check routes (`/`, `/demo`, `/sans-monitor`, `/astronaut-check`, `/digital-twin`, `/oct-analysis`, `/demo-theater`, `/vision-lab`, `/nasa-data`, `/model-lab`, `/explainability`) under a **Zero-Login Public Architecture** with an instant **`EN | ES`** bilingual selector.
- **Consequences:** Zero authentication friction for judges and reviewers while keeping clinical decision boundaries strictly enforced in domain code.

## ADR-003: Interactive Maintenance and Verification Console
- **Date:** November 10, 2026
- **Context:** System verification (`migrate`, `db:seed`, `scientific:engine status`, `scientific:engine test`, `optimize:clear`) requires a deterministic command whitelist.
- **Decision:** Maintain an explicit whitelist of pre-approved Artisan verification and maintenance commands.
- **Consequences:** Safe, repeatable environment verification.

## ADR-004: Strict Clinical Scope Separation (No Automated Fit-for-Duty Verdict)
- **Date:** November 14, 2026
- **Context:** Adoption of the canonical specification [`docs/SANS_MONITOR_SPEC.md`](SANS_MONITOR_SPEC.md).
- **Decision:** The system **never issues an automated fit-for-duty verdict** (*fit / unfit*), never diagnoses SANS autonomously, and never prescribes countermeasures. Operational record outputs are strictly limited to **Ready for review**, **Missing data**, and **Prioritize review** (`RF-10`). If a fitness decision is recorded (`RF-11`), it resides in a separate `ClinicianDecision` entity signed exclusively by an authorized flight surgeon; in its absence, the system displays **Fitness not evaluated**.
- **Consequences:** Strict compliance with aerospace medical ethics and complete separation between data completeness, ocular biomarker signals, and human clinical judgment.

## ADR-005: P0/P1/P2 Reference Implementation on Hybrid Laravel 11 + Python FastAPI Stack
- **Date:** November 14, 2026
- **Context:** Section 6 of [`docs/SANS_MONITOR_SPEC.md`](SANS_MONITOR_SPEC.md) defines a modular separation across `ui/`, `catalog/`, `domain/`, `simulation/`, `analytics/`, and `tests/`.
- **Decision:** Map that modular separation across the hybrid Laravel 11 + Python 3.14 FastAPI architecture:
  - `ui/`: High-contrast Blade views with the official SPACE VISION interactive color hierarchy (`#0042A6` Technology Blue, `#2E96F5` Electric Blue, `#07173F` Deep Space Blue, `#EAFE07` Neon Yellow), Chart.js longitudinal plots, Three.js 3D WebGL ocular twin, and ZEISS Cirrus HD-OCT console.
  - `catalog/`, `domain/`, `simulation/`, `analytics/`: Coordinated between Laravel domain services (`app/Services/`) and the Python 3.14 FastAPI scientific microservice (`scientific_engine/app/` on `:8001`).
  - `tests/`: Automated dual test suite (**78 PHPUnit tests** and **22 Pytest tests**).
- **Consequences:** Preserves all interactive 3D and scientific capabilities while aligning data contracts and longitudinal follow-up milestones (**+30, +60, and +90 days**, head-down tilt exposure, and the 6 core ocular biomarkers) with the canonical **SANS Monitor** specification.

## ADR-006: Task-Oriented Experience and Separate Research Cohorts
- **Date:** November 14, 2026
- **Context:** The scientific workstation communicates the biomarker model to ground scientists and clinical investigators, while an in-flight mission without an on-board ophthalmologist requires guided autonomous acquisition for the crew. Terrestrial studies also require distinguishing normal conditions, $-6^\circ$ head-down tilt (HDT) exposure, and cohort outcomes.
- **Decision:** Organize the product into distinct functional workflows:
  1. **Ground Scientists & Clinical Investigators Workstation (`/sans-monitor`):** Streamlined 3-step analytical wizard (6 Biomarkers Predictor, Before/During/After $+30/+60/+90\text{d}$ Timeline, and 3 Reference Scenario Models) without promotional clutter.
  2. **In-Flight Astronaut Guided Check (`/astronaut-check`):** Task-first autonomous check for crew members during flight.
  3. **3D Anatomical Viewer & OCT Console (`/digital-twin?mode=dual`):** Synchronized spatial and tomographic inspection.
- **Scientific Boundary:** A standard camera supports quality control and longitudinal comparison, but does not replace OCT or confirm papilledema or intracranial pressure on its own. $-6^\circ$ HDT exposure is an exposure variable, not an ocular biomarker.
- **Design Reference:** [`UX_RESEARCH_WORKFLOWS.md`](UX_RESEARCH_WORKFLOWS.md).

## ADR-007: Dedicated P0 Astronaut Check Separated from the Ground Scientific Workstation
- **Date:** November 14, 2026
- **Context:** The ground scientific predictor (`/sans-monitor`) is built for ground scientists, flight surgeons, and clinical investigators, whereas an astronaut performing an in-flight check needs a focused, single-task-at-a-time guided interface.
- **Decision:** Provide `/astronaut-check` as a dedicated bilingual (`EN | ES`) public flow with four destinations (`Today`, `Logbook`, `Trends`, `Profile`) and a step-by-step guided check. Meanwhile, `/sans-monitor` is explicitly designated as the **Ground Scientists & Clinical Investigators Module** (`FOR GROUND SCIENTISTS & CLINICAL INVESTIGATORS` / `MÓDULO EXCLUSIVO PARA CIENTÍFICOS E INVESTIGADORES EN TIERRA`).
- **P0 Persistence:** The prototype stores the latest guided check entry in `localStorage` for interactive demonstration without polluting the calibrated scientific database.
- **Boundaries:** On-screen tests support longitudinal monitoring; the tonometer is an external device. The application does not measure intracranial pressure, diagnose SANS, or issue fit-for-duty verdicts.
