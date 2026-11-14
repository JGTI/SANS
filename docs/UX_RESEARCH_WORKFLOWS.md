# SPACE VISION — UX and Research Cohort Workflows

**Status:** Astronaut P0 experience implemented; terrestrial, cohort, and integrated-results workspaces remain pending.  
**Date:** November 14, 2026  
**Canonical clinical requirements:** [`SANS_MONITOR_SPEC.md`](SANS_MONITOR_SPEC.md)

This document translates the SANS Monitor scientific model into experiences that can be completed by astronauts, terrestrial participants, investigators, and authorized specialists. It does not replace the canonical specification. If the documents conflict, `SANS_MONITOR_SPEC.md` prevails.

## 1. Product principle

SPACE VISION must begin with the user's task rather than with a specialist dashboard. The system guides data acquisition, verifies whether a measurement is usable, compares it with the correct personal baseline, and prepares evidence for human review.

The product does not autonomously diagnose SANS, estimate intracranial pressure from an eye image, prescribe a countermeasure, or issue a fit-for-duty decision.

## 2. Four functional workspaces

### A. Spaceflight longitudinal workflow

**Users:** astronaut during flight; authorized specialist before and after the mission.

**Stages:**

1. Specialist baseline before flight.
2. Guided autonomous checks during flight.
3. Specialist post-flight controls at the configured follow-up milestones.
4. Human review of longitudinal change, measurement quality, and missing evidence.

**Astronaut tasks:** complete a short guided check, repeat failed captures, record symptoms, and submit usable evidence.

**Permitted output:** complete measurement, repeat required, missing data, possible change from personal baseline, or prioritize specialist review.

### B. Terrestrial comparative workflow

**Users:** terrestrial participant, study operator, and authorized investigator.

**Cohorts:**

- Normal-condition reference cohort.
- Controlled head-down tilt cohort at minus six degrees.

Both cohorts use the same measurement definitions, devices, timing rules, and quality checks. The head-down tilt exposure records start, end, duration, segments, angle reference, and adherence. Its baseline remains separate from any orbital baseline.

### C. Participant and cohort workspace

**Users:** authorized study coordinator and investigator.

**Purpose:** document recruitment, eligibility, consent state, cohort assignment, measurement coverage, quality issues, and loss to follow-up.

A screen does not make a sample random. Random or stratified selection must be defined before recruitment and recorded through a reproducible sampling method. Synthetic participants remain in a separate demo dataset and never count as observed evidence.

### D. Integrated study results

**Users:** investigator, clinical reviewer, and hackathon judge in demonstration mode.

Results must be available separately for spaceflight, normal terrestrial conditions, and minus-six-degree head-down tilt. A combined view may summarize coverage, but it must preserve source type and must not treat the cohorts as physiologically equivalent.

Minimum reporting:

- Participants and evaluable eyes per cohort.
- Baseline coverage and completed visits.
- Missing, rejected, and non-comparable measurements.
- Individual change from the compatible personal baseline.
- Cohort distributions and uncertainty, not only averages.
- Observation windows and follow-up losses.
- Device, method, protocol version, and data provenance.

## 3. Shared variable model

### Identity and study design

- Pseudonymous participant ID.
- Dataset type: synthetic demo, authorized terrestrial, authorized orbital.
- Cohort and allocation method.
- Mission or exposure identifier.
- Consent and authorization status where applicable.

### Exposure

- Environment: spaceflight, terrestrial normal condition, or head-down tilt.
- Head-down tilt angle, direction, reference, start, end, duration, and segments.
- Mission phase and mission day.
- Time since exposure end.

### Ocular outcomes by eye

- OCT method and image-quality status.
- Peripapillary total retinal thickness and RNFL thickness.
- Optic-disc edema assessment and documented Frisén grade.
- Axial length.
- Spherical equivalent and hyperopic shift.
- Visual acuity in Snellen and LogMAR.
- Intraocular pressure.
- Choroidal thickness and chorioretinal folds when the modality supports them.

Prior head-down tilt exposure is an exposure history, not an ocular biomarker.

### Guided autonomous checks

- Near visual acuity.
- Distortion or Amsler-style task.
- Contrast task when the display is calibrated.
- Symptom report.
- Ocular or fundus capture only with supported optics and an explicit quality gate.

A conventional camera image can support capture quality and longitudinal comparison. It cannot replace OCT or independently confirm papilledema.

### Context and possible confounders

- Posture during measurement.
- Blood pressure with posture and method.
- Environmental carbon dioxide when available.
- Sleep, hydration, medication, and relevant symptoms when authorized.

These variables remain contextual in P0 and do not change the ocular operational state.

### Provenance and quality

- Device and software version.
- Operator or autonomous workflow.
- Observed time and result-available time.
- Eye, region, modality, unit, and protocol version.
- Quality state, repeat reason, comparability, and missing-data reason.

## 4. Analysis principles

1. Use within-person, within-eye change from a compatible baseline as the primary comparison.
2. Keep orbital and terrestrial baselines separate.
3. Compare terrestrial trajectories between the normal-condition and head-down tilt cohorts; do not rely on a simple pooled correlation.
4. For repeated measures, use a longitudinal analysis that accounts for participant and eye clustering.
5. Report uncertainty, missingness, and follow-up coverage.
6. Keep synthetic data out of observed-effect estimates.
7. Do not interpret association as causation without an appropriate controlled design.

## 5. Proposed navigation

The four workspaces may appear as four primary destinations or as three primary tabs with participant management nested under the research workspace. The final choice requires a wireframe and usability review.

Recommended task labels:

1. `My Eye Check` — guided participant or astronaut workflow.
2. `Earth Study` — normal-condition and head-down tilt sessions.
3. `Study Cohort` — participants, allocation, coverage, and quality.
4. `Results` — separate and integrated analyses.

Specialist review remains a role-specific view of the same records rather than a competing source of truth.

## 6. Acceptance criteria before implementation

- Each workspace has one primary user and one primary task.
- Every measurement identifies who captured it, when, with which method, and whether it is comparable.
- A failed quality gate leads to a repeat or an explicit missing state, never a normal value.
- The interface distinguishes observed, simulated, and experimental predicted data.
- The interface never labels an unscanned participant as stable.
- Spaceflight, terrestrial normal, and head-down tilt cohorts can be filtered independently.
- Integrated results expose denominators and missing data.
- Specialist validation before and after flight remains distinguishable from autonomous in-flight acquisition.

## 7. Open decisions

- Whether the public navigation uses three or four primary tabs.
- Which autonomous visual tests are feasible with the available hardware.
- Whether fundus capture uses a validated adapter, a dedicated camera, or remains a future integration.
- Sampling and allocation protocol for the terrestrial study.
- Exact timing and duration of the terrestrial normal-condition and head-down tilt measurements.
- Primary study outcome and statistical analysis plan.

## 8. Implemented astronaut experience (P0)

The public route `/astronaut-check` now provides a task-first, bilingual astronaut workflow while preserving the existing scientific predictor as a separate workspace.

Primary destinations:

- `Today`: one prominent action to begin the scheduled eye check, the last accepted result, and setup guidance.
- `Logbook`: chronological personal entries with quality and repeat states.
- `Trends`: plain-language within-person comparison with the personal baseline, separated by eye and bounded by scientific-honesty language.
- `Profile`: pseudonymous general information plus progressive disclosure for ocular, physiological, and environmental context.

The guided check presents one task at a time:

1. Symptoms.
2. Visual acuity, Amsler-style distortion, and contrast checks.
3. Rebound-tonometer IOP readings by eye, blood pressure, and heart rate.
4. Sleep, hydration, exercise, posture, and bounded relevant-change reporting.

Interaction rules implemented after usability review:

- The astronaut must select at least one symptom state; `No symptoms` is mutually exclusive with symptom choices.
- Visual checks record constrained results for both OD and OS. They cannot be completed by clicking a generic check mark.
- The acuity, Amsler, and contrast modules show a test stimulus and require an outcome for each eye before advancing.
- IOP, blood pressure, and heart rate are read-only values imported from paired demo devices; the astronaut does not type clinical readings.
- The pre-flight profile is read-only during flight because it is established and verified by a specialist.
- Context uses bounded choices instead of open numerical fields. An `Other` state requests ground follow-up without inviting unstructured clinical interpretation.
- Each incomplete stage explains exactly what remains before the user can continue.

P0 demonstration entries persist only in browser local storage. They are not presented as authorized orbital evidence and do not modify the canonical scientific dataset. Server-side persistence, device ingestion, identity, audit history, and clinical review remain separate future contracts.

Visual direction: the astronaut experience reuses the same white, deep-blue, and electric-blue system as the canonical SANS Predictor and 3D/OCT Viewer. It keeps one dominant action per view, minimal card use, and responsive touch targets while deliberately avoiding numbered dashboard tiles, decorative circles, gradients, and diagnostic AI language.

## 9. Canonical color template

This palette is the single source of truth for the astronaut experience. Do not introduce gradients, decorative glow colors, or new accent colors without recording a design decision.

| Token | Hex | Purpose |
| --- | --- | --- |
| `crew-canvas` | `#FFFFFF` | Page background and empty space |
| `crew-surface` | `#FFFFFF` | Primary content surface |
| `crew-ink` | `#0A0A0A` | Headings and primary text |
| `crew-muted` | `#52525B` | Supporting text and metadata |
| `crew-primary` | `#0042A6` | The single primary action and selected state |
| `crew-primary-dark` | `#07173F` | Hover state and highest-emphasis blue text |
| `crew-electric` | `#2E96F5` | Secondary eye series and controlled highlight |
| `crew-sky` | `#F4F4F5` | Navigation background and neutral grouping |
| `crew-border` | `#E4E4E7` | Dividers and component boundaries |
| `crew-soft` | `#EFF6FF` | Low-emphasis blue background |
| `crew-positive` | `#16A34A` | Accepted or completed state only |
| `crew-attention` | `#F59E0B` | Attention and preparation notices only |

Rules:

- Use solid fills only; gradients are not part of the decorative visual language. A functional scientific stimulus such as the Amsler grid may use repeated CSS lines to render the test itself.
- Reserve NASA blue for the primary action or current selection, never for decoration.
- Reserve amber for actionable attention, not routine information.
- Prefer straight rules, spacing, and subtle rounded rectangles over decorative circles.
- Each screen has one visually dominant action.
- Any medical or quality state must also include text or an icon; color alone is insufficient.

