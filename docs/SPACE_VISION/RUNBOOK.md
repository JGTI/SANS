# 🛠️ SPACE VISION — OPERATIONS & VERIFICATION RUNBOOK

## 1. Local & Production Setup (Zero Login Required)
```bash
composer install
php artisan migrate --seed
php artisan scientific:engine start
php artisan optimize:clear
```

## 2. Public Zero-Login Routes
- `/` · `/demo` · `/sans-monitor` — 3-Tab SANS Biomarker Predictor (English default `EN | ES`).
- `/digital-twin?mode=dual` — Dual 3D WebGL Anatomical Viewer + ZEISS Cirrus™ HD-OCT Console.
- `/demo-theater` — 3-Minute Guided Pitch Walkthrough & Printable Dossier.
- `/vision-lab` — Ophthalmology Calculator ($SE$, LogMAR, Thibos vectors) & CSV Export.
- `/nasa-data` — NASA OSDR, NTRS & DONKI Observatory.

## 3. Automated Test Suites
```bash
php artisan test                     # 77 passed (372 assertions)
php artisan scientific:engine test   # 22 passed (pytest)
```
