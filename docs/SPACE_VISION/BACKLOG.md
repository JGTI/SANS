# 📋 SPACE VISION — MASTER TASK BACKLOG

## Épica 1: Arquitectura Base y Control de Misión [✅ COMPLETADA]
- [x] Limpieza de migraciones obsoletas de proyectos de NEXUS.
- [x] Configuración de MySQL y migraciones base.
- [x] Configuración de roles espaciales y cuentas maestras (`SpaceVision2026!`).
- [x] CRUD de usuarios y roles (`/admin/users`, `/admin/roles`).
- [x] Consola interactiva de comandos Artisan (`/admin/maintenance`).

## Épica 2: Modelado Oftalmológico y Núcleo Vision Lab [✅ COMPLETADA]
- [x] Migraciones clínicas: `astronaut_profiles`, `vision_evaluations`, `sans_observations`.
- [x] Servicio de dominio `OphthalmologyCalculator` (SE, LogMAR, Shift Hipermetrópico, Estratificación SANS).
- [x] Cohorte sintética realista en `OphthalmologySeeder` (progresión longitudinal de CDR Alex Mercer FD0 &rarr; FD120).
- [x] Mission Control Dashboard (`/admin`) con métricas vivas, telemetría y Chart.js.
- [x] Vision Lab (`/admin/vision-lab`, `/admin/vision-lab/create`) con cálculo reactivo.

## Épica 3: SANS Research Engine & Astronaut Digital Twin [✅ COMPLETADA]
- [x] Astronaut Digital Twin (`/admin/digital-twin`) con SVG reactivo que ilustra aplanamiento de polo posterior y edema de disco Frisén.
- [x] Mission Simulator (`/admin/mission-simulator`) con 3 escenarios deterministas, semilla reproducible, slider temporal FD0 &rarr; FD180 y controles Play/Pause.

## Épica 4: Observatorio de Datos NASA [✅ COMPLETADA]
- [x] Servicio `NasaDataService` con resiliencia y fallback curado en `storage/app/nasa_cache/`.
- [x] Integración de estudios biológicos de **NASA OSDR** (OSD-137, OSD-370, OSD-422, OSD-514).
- [x] Integración de reportes de evidencia de **NASA NTRS** (Stenger et al., Mader et al., Macias et al.).
- [x] Integración de telemetría de clima espacial de **NASA DONKI** con disclaimer científico.
- [x] Diagnóstico en vivo de conectividad de APIs y redundancia.

## Épica 5: Demo Theater & Pitch de Jueces [✅ COMPLETADA]
- [x] Demo Theater (`/admin/demo-theater`) con recorrido interactivo de 5 pasos en 3 minutos.
- [x] Expediente oficial de vuelo (`/admin/demo-theater/report/{code}`) imprimible en PDF con formato NASA.

## Épica 6: API RESTful Versionada [✅ COMPLETADA]
- [x] Endpoints bajo `/api/space-vision/v1/`: `missions`, `crew`, `ocular-exams`, `simulator/scenarios`, `calculator/evaluate`, `nasa/osdr`, `nasa/ntrs`, `nasa/donki`.

## Épica 7: Inteligencia Científica y Centro de Automatizaciones [PRÓXIMO ENFOQUE]
- [ ] Módulo **Research Intelligence** (`/admin/research-intelligence`): visualización de conceptos biomédicos, hipótesis estructuradas y exportador BibTeX/APA.
- [ ] Módulo **Automation Center** (`/admin/automations`): monitoreo de cron, verificación periódica de integridad de datasets y disparador manual de sincronizaciones.
