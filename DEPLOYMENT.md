# 🚀 SPACE VISION (SANS MONITOR) — DEPLOYMENT & PUBLIC ACCESS VERIFICATION GUIDE

> *Developed by NEXUS × Visual Rehab Center for the NASA International Space Apps Challenge 2026.*

---

## 1. Public Production Environment
- **Landing Page:** https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com
- **Direct Public Demo (No Login Required):** https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com/demo
- **Alternative Public Demo Route:** https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com/sans-monitor

---

## 2. Local Setup & Execution

```bash
# 1. Install PHP & JS dependencies
composer install
npm install && npm run build

# 2. Configure environment and run migrations + canonical SANS Monitor seeder
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force

# 3. Verify or start the Python FastAPI Scientific Engine (:8001)
php artisan scientific:engine status
php artisan scientific:engine start
```

---

## 3. Automated Verification (97 Tests — 100% Green)

```bash
# Run Laravel PHPUnit suite (75 tests + public access verification)
php artisan test

# Run Python FastAPI Pytest suite (22 tests)
php artisan scientific:engine test
```

---

## 4. Checklist for Verifying Zero-Login Public Access (Incognito Mode)

Open a fresh **Incognito / Private browser window** (no cookies, no active session):

1. Visit `/` $\to$ Confirm the page loads with **"Explore the Demo · Launch SPACE VISION (No Login Required)"**.
2. Visit `/demo` or `/sans-monitor` or `/admin/sans-monitor` $\to$ Confirm **HTTP 200 OK** with **zero redirect to `/login`**.
3. Test **Tab 1 (`?tab=biomarcadores`)** $\to$ Modify any of the 6 biomarkers (OCT thickness, axial length, simulated hyperopia, Frisén grade, IOP, $-6^\circ$ exposure) and click **"Ejecutar Predictor con estos 6 Biomarcadores"**. Confirm the Signal Index and Chart.js bar chart update immediately without login.
4. Test **Tab 2 (`?tab=seguimiento`)** $\to$ Confirm the **Before · During · After (+30, +60, +90 days)** timeline cards and Chart.js curve render, showing `+60` as *"Sin medición"* without interpolation.
5. Test **Tab 3 (`?tab=escenarios`)** $\to$ Click between **1. Escenario Estable (`CASE-STABLE-01`)**, **2. Escenario que Empeora (`CASE-PROG-02`)**, and **3. Escenario No se Escanea (`CASE-NOSCAN-03A`)** and confirm the 3 anatomical SVG schematics update immediately.
6. Confirm the footer displays:  
   `"Developed by NEXUS × Visual Rehab Center for the NASA International Space Apps Challenge 2026."`
