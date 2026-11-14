# 🚀 SPACE VISION / SANS MONITOR — ENGINEERING ARCHITECTURE & GUIDELINES

## 1. Project Mission & Canonical Specification
- **Project:** SPACE VISION — **SANS Monitor** (`NEXUS × Visual Rehab Center`)
- **Event:** NASA International Space Apps Challenge 2026
- **Production URL:** `https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com`
- **Official Repository:** `https://github.com/JGTI/SANS`
- **Canonical Specification:** **[`docs/SANS_MONITOR_SPEC.md`](docs/SANS_MONITOR_SPEC.md)**
- **Core Objective:** Convert an ocular record into a traceable longitudinal review (Before flight, During exposure/flight, and Post-flight controls at **+30, +60, and +90 days**), evaluate the **6 core ocular biomarkers**, compare the **3 reference scenario models** (*Stable, Worsens, Unscanned*), retrieve applicable NASA protocols, and distinguish three operational review states (**Ready for review**, **Prioritize review**, **Missing data**) **without issuing an automated fit-for-duty verdict**.

---

## 2. Public Zero-Login Architecture & Bilingual Standard (`EN | ES`)
- **Zero-Login Public Access:** The root URL (`/`) and all demonstration modules (`/demo`, `/sans-monitor`, `/digital-twin?mode=dual`, `/oct-analysis`, `/demo-theater`, `/vision-lab`, `/nasa-data`, `/model-lab`, `/explainability`) operate publicly without requiring user authentication, registration, OAuth, or API keys.
- **Primary Language:** **English (`EN`)** is the default language across the platform and documentation, with an instant **`EN | ES`** bilingual selector in the top navigation bar (`?lang=en` / `?lang=es`).

---

## 3. Technology Stack
- **Operating System:** Linux (x86_64)
- **Web Framework:** Laravel 11 / PHP 8.3 + Python 3.14 Scientific Microservice (FastAPI on `:8001`)
- **Scientific Computing:** NumPy 2.5, SciPy 1.18, Scikit-learn 1.9, Pandas 3.1
- **Frontend & Accessibility:** Blade, High-Contrast Monochrome Design (`#09090b` / `#ffffff`, WCAG 2.2 AA), Chart.js, Three.js 3D WebGL, Font Awesome 4.7 (`<i class="fa fa-*"></i>`).

---

## 4. Verification Baseline
Every engineering change must keep 100% of the automated test suites passing:
- `php artisan scientific:engine status` (ONLINE at `:8001`)
- `php artisan scientific:engine test` (**22 passed** in Pytest)
- `php artisan test` (**77 passed / 372 assertions** in PHPUnit)
