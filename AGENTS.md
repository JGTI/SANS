# 🚀 SPACE VISION / SANS MONITOR — CONTINUIDAD DE AGENTES DE INGENIERÍA

> **Regla de Oro:** Claude, Codex, Antigravity o cualquier otro asistente/agente son **distintas interfaces de una misma continuidad de desarrollo**.
> Antes de escribir código, lee este archivo, la especificación canónica **[`docs/SANS_MONITOR_SPEC.md`](docs/SANS_MONITOR_SPEC.md)** y los documentos en `docs/`. Al terminar, actualiza el estado y deja instrucciones claras para el siguiente agente.

---

## 1. Misión del Proyecto y Especificación Canónica
- **Proyecto:** SPACE VISION — **SANS Monitor**
- **Evento:** NASA Space Apps Challenge 2026 — Querétaro, México
- **URL Producción:** `https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com`
- **Repositorio Oficial:** `https://github.com/JGTI/SANS`
- **Especificación Rectora:** **[`docs/SANS_MONITOR_SPEC.md`](docs/SANS_MONITOR_SPEC.md)** (prevalece sobre cualquier documento previo).
- **Objetivo:** Convertir un expediente ocular en una revisión longitudinal trazable (antes, durante exposición/vuelo y controles posteriores **+30, +60 y +90 días**), recuperar protocolos aplicados, buscar referencias pertinentes y distinguir tres salidas operativas (**«Listo para revisión»**, **«Priorizar revisión»**, **«Faltan datos»**) **sin emitir un veredicto automático de aptitud**.

---

## 2. Stack y Entorno
- **Sistema Operativo:** Ubuntu Linux (x86_64)
- **Framework:** Laravel 11 / PHP 8.3 + Python 3.14 (FastAPI en `:8001`)
- **Base de Datos:** MySQL / MariaDB (`nexus_spaceappschallenge`)
- **Computación Científica:** NumPy 2.5, SciPy 1.18, Scikit-learn 1.9, Pandas 3.1
- **Autenticación:** Híbrida (Credenciales locales + Google OAuth Socialite) + Internal Bearer Token
- **Roles y Permisos:** Spatie Laravel-Permission
- **Frontend:** Blade, AdminCN / Shadcn Minimalist Black & White (`#09090b` / `#ffffff`), Chart.js (alto contraste claro/oscuro), Three.js 3D WebGL, **exclusivamente Font Awesome 4.7 (`<i class="fa fa-*"></i>`)** en las vistas.

---

## 3. Credenciales Maestras de Demostración
Contraseña universal para usuarios de prueba/demo: `SpaceVision2026!`

| Rol | Correo | Propósito / Responsabilidad en SANS Monitor |
|---|---|---|
| **super-admin** | `admin@spacevision.nasa` | Presentador de demo / Director de Vuelo / Control total, migraciones y comandos |
| **admin** | `operator@spacevision.nasa` | Responsable de datos y Curador de protocolos / Calidad y telemetría |
| **flight-surgeon** | `surgeon@spacevision.nasa` | Especialista médico autorizado / Revisión de expediente y dictamen humano |
| **researcher** | `researcher@spacevision.nasa` | Investigador / Analista / Cohortes, inclinación y comparación de modelos |
| **astronaut** | `astronaut@spacevision.nasa` | Tripulante simulado / Consulta de su expediente visual |

---

## 4. Rutas Críticas Implementadas
- `/login`: Inicio de sesión (con credenciales locales, botones de 1 clic y Google OAuth).
- `/admin`: Mission Control Dashboard aeroespacial de SPACE VISION.
- `/admin/crew/{code}`: Expediente longitudinal de salud visual por ojo (OD/OS).
- `/admin/vision-lab`: Vision Lab oftalmológico ($SE$, Snellen $\to$ LogMAR, vectores de Thibos, exportador CSV).
- `/admin/oct-analysis`: Consola clínica ZEISS Cirrus™ HD-OCT (Macular Cube 512x128 de 9 paneles).
- `/admin/digital-twin`: Gemelo Digital 3D WebGL (Three.js con corte sagital, OCT y modo dual `?mode=dual`).
- `/admin/mission-simulator`: Simulador temporal de escenarios (días 10 → 15 → 30 y rangos 0–30, 0–90, 0–150).
- `/admin/nasa-data`: Catálogo curado y observatorio de datos oficiales NASA (OSDR, NTRS, DONKI, APOD).
- `/admin/research-intelligence` y `/admin/automations`: Inteligencia de investigación, citas BibTeX/APA y automatizaciones.
- `/admin/ai-command`: AI Command Center (monitoreo en vivo del microservicio FastAPI).
- `/admin/model-lab`: Laboratorio experimental de modelos (Random Forest vs GBM vs OLS).
- `/admin/explainability`: AI Explainability Center (atribución de variables y pesos SHAP).
- `/admin/data-quality`: Data Quality Observatory (auditoría de completitud y límites).
- `/admin/model-registry`: Catálogo y linaje de versiones de modelos de IA.
- `/admin/demo-theater` y `/admin/demo-theater/report/{code}`: Recorrido guiado y expediente oficial imprimible.
- `/admin/users` y `/admin/roles`: CRUD de usuarios y gestión RBAC.
- `/admin/api-keys`: Gestión de claves de APIs (CRUD, ping, sync `.env`) y Diagrama de Arquitectura de 5 capas.
- `/admin/maintenance`: Consola interactiva de comandos Artisan y migraciones.

---

## 5. Instrucciones y Baseline para el Siguiente Agente
1. Lee [`docs/SANS_MONITOR_SPEC.md`](docs/SANS_MONITOR_SPEC.md), [`docs/DEVELOPMENT_STATE.md`](docs/DEVELOPMENT_STATE.md), [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md), [`docs/DECISIONS.md`](docs/DECISIONS.md) y [`docs/TASK_BACKLOG.md`](docs/TASK_BACKLOG.md).
2. Cualquier cambio debe mantener el 100% de las pruebas en verde:
   - `php artisan scientific:engine status` (debe reportar ONLINE en `:8001`).
   - `php artisan scientific:engine test` (debe aprobar los **21 tests** de Pytest).
   - `php artisan test` (debe aprobar los **67 tests / 287 aserciones** de PHPUnit).
3. El siguiente paso de ingeniería es implementar progresivamente los ítems de la **Épica 14** en [`docs/TASK_BACKLOG.md`](docs/TASK_BACKLOG.md) siguiendo el contrato de datos y las 19 pruebas de aceptación (`AT-01` a `AT-19`) de [`docs/SANS_MONITOR_SPEC.md`](docs/SANS_MONITOR_SPEC.md).
4. Mantén siempre la honestidad científica: distinguir observaciones reales, análogos terrestres ($-6^\circ$ HDT), simulaciones sintéticas (`is_simulated = true`) y pronósticos experimentales, sin emitir nunca un dictamen automático de aptitud.
