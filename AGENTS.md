# 🚀 SPACE VISION — CONTINUIDAD DE AGENTES DE INGENIERÍA

> **Regla de Oro:** Claude, Codex, Antigravity o cualquier otro asistente/agente son **distintas interfaces de una misma continuidad de desarrollo**.
> Antes de escribir código, lee este archivo y los documentos en `docs/`. Al terminar, actualiza el estado y deja instrucciones claras para el siguiente agente.

---

## 1. Misión del Proyecto
- **Proyecto:** SPACE VISION
- **Evento:** NASA Space Apps Challenge 2026 — Querétaro, México
- **Desafío:** Investigación médica espacial, neurooftalmología y telemetría de salud visual en microgravedad con enfoque en el síndrome **SANS** (*Spaceflight Associated Neuro-ocular Syndrome*).
- **Parámetros clave:** Refracción ocular (esfera, cilindro, eje, equivalente esférico) y Agudeza Visual (Snellen, LogMAR), correlacionados con tiempo en órbita y telemetría ambiental espacial de NASA.

---

## 2. Stack y Entorno
- **Sistema Operativo:** Ubuntu Linux (x86_64)
- **Framework:** Laravel 11 / PHP 8.3 + Python 3.14 (FastAPI en `:8001`)
- **Base de Datos:** MySQL / MariaDB (`nexus_spaceappschallenge`) + Docker Compose
- **Computación Científica:** NumPy 2.5, SciPy 1.18, Scikit-learn 1.9, Pandas 3.1
- **Autenticación:** Híbrida (Credenciales locales + Google OAuth Socialite) + Internal Bearer Token
- **Roles y Permisos:** Spatie Laravel-Permission
- **Frontend:** Blade, AdminCN / Shadcn Minimalist Black & White, Chart.js, Three.js 3D WebGL

---

## 3. Credenciales Maestras de Demostración
Contraseña universal para usuarios de prueba/demo: `SpaceVision2026!`

| Rol | Correo | Propósito / Permisos |
|---|---|---|
| **super-admin** | `admin@spacevision.nasa` | Director de Vuelo / Control total, migraciones y comandos |
| **admin** | `operator@spacevision.nasa` | Operador de misión / Gestión de usuarios y telemetría |
| **flight-surgeon** | `surgeon@spacevision.nasa` | Médico de vuelo / Evaluaciones oftalmológicas y SANS |
| **researcher** | `researcher@spacevision.nasa` | Investigador biomédico / Datos NASA y simulación |
| **astronaut** | `astronaut@spacevision.nasa` | Tripulante simulado / Consulta de su expediente visual |

---

## 4. Rutas Críticas Implementadas
- `/login`: Inicio de sesión (con credenciales locales y botón Google OAuth).
- `/admin`: Mission Control Dashboard aeroespacial de SPACE VISION.
- `/admin/ai-command`: AI Command Center (monitoreo en vivo del microservicio FastAPI).
- `/admin/model-lab`: Laboratorio experimental de modelos (Random Forest vs GBM vs OLS).
- `/admin/explainability`: AI Explainability Center (atribución de variables y pesos SHAP).
- `/admin/data-quality`: Data Quality Observatory (auditoría de completitud y límites).
- `/admin/model-registry`: Catálogo y linaje de versiones de modelos de IA.
- `/admin/digital-twin`: Gemelo Digital 3D WebGL (Three.js con corte sagital y OCT).
- `/admin/mission-simulator`: Simulador de escenarios de microgravedad FD0 &rarr; FD180.
- `/admin/nasa-data`: Observatorio de datos oficiales NASA (OSDR, NTRS, DONKI, APOD).
- `/admin/demo-theater`: Recorrido guiado de 5 pasos para jueces con reporte imprimible.
- `/admin/users`: CRUD completo de usuarios con diseño Shadcn monocromático.
- `/admin/roles`: Gestión de roles y permisos de acceso.
- `/admin/api-keys`: Gestión de claves de APIs (altas, bajas, cambios, test de ping) y Diagrama de Arquitectura del sistema.
- `/admin/maintenance`: Consola interactiva de comandos Artisan y migraciones.

---

## 5. Instrucciones para el Siguiente Agente
1. Lee `docs/DEVELOPMENT_STATE.md`, `docs/ARCHITECTURE.md`, `docs/ML_EXPERIMENTS.md` y `docs/TASK_BACKLOG.md`.
2. Para verificar el microservicio científico Python:
   - `php artisan scientific:engine status` (debe reportar ONLINE en `:8001`).
   - `php artisan scientific:engine test` (debe pasar los 20 tests de pytest).
3. Para verificar la suite completa de Laravel:
   - `php artisan test` (debe pasar los 56 tests de PHPUnit).
3. El siguiente paso prioritario del backlog es:
   - Crear las migraciones y modelos para los módulos oftalmológicos centrales:
     - `astronaut_profiles` (perfiles de astronautas / tripulación)
     - `vision_evaluations` (evaluaciones de refracción OD/OS y agudeza visual Snellen/LogMAR)
     - `sans_observations` (registro de signos estructurales: edema de disco, pliegues coroideos)
4. Recuerda mantener la honestidad científica: distinguir claramente datos simulados de datos clínicos reales.
