# 🤝 SPACE VISION — AGENT HANDOFF PROTOCOL

> **Regla de Continuidad:** Cualquier agente de IA (Claude, Codex, Antigravity) o desarrollador humano que retome este proyecto forma parte de la **misma continuidad técnica**. No fragmentar ni empezar desde cero.

---

## 1. Verificación Inicial Rápida
Antes de realizar cualquier cambio, ejecuta:
```bash
git status
php artisan test
```
Ambos comandos deben reportar el árbol limpio y los 42 tests en verde.

---

## 2. Puntos de Entrada Clave del Código
- **Rutas Principales:**
  - `routes/web.php`: Rutas del panel `/admin` protegidas por middleware `auth`.
  - `routes/api.php`: Endpoints públicos/versionados bajo `/api/space-vision/v1/`.
- **Servicios Científicos de Dominio:**
  - `app/Services/OphthalmologyCalculator.php`: Algoritmos certificados de SE ($SE = SPH + CYL/2$), LogMAR ($\log_{10}(D/N)$), $\Delta SE$ y estratificación explicable de SANS.
  - `app/Services/NasaDataService.php`: Cliente HTTP resiliente para NASA OSDR, NTRS, DONKI y APOD, respaldado por datos locales certificados en `storage/app/nasa_cache/`.
- **Controladores del Panel:**
  - `app/Http/Controllers/Admin/MissionControlController.php`
  - `app/Http/Controllers/Admin/VisionLabController.php`
  - `app/Http/Controllers/Admin/DigitalTwinController.php`
  - `app/Http/Controllers/Admin/MissionSimulatorController.php`
  - `app/Http/Controllers/Admin/NasaObservatoryController.php`
  - `app/Http/Controllers/Admin/DemoTheaterController.php`

---

## 3. Próximos Pasos Sugeridos
1. **Despliegue a Producción:**
   - La base local está sincronizada en GitHub (`origin/main`).
   - El workflow `.github/workflows/deploy.yml` sube automáticamente los cambios vía FTP/cPanel al activarse desde GitHub Actions (`workflow_dispatch`).
2. **Presentación en Vivo (Pitch del Hackatón):**
   - Utilizar la ruta `/admin/demo-theater` para el recorrido de 3 minutos guiado.
   - Utilizar `/admin/demo-theater/report/ASTRO-01` para mostrar el expediente clínico impreso en PDF.
