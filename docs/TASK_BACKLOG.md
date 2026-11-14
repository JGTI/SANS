# 📋 SPACE VISION / SANS MONITOR — MASTER TASK BACKLOG

> **Referencia normativa:** Todos los requisitos funcionales (`RF-01` a `RF-37`), no funcionales (`RNF-01` a `RNF-06`), contrato de datos y pruebas de aceptación (`AT-01` a `AT-19`) están definidos en [`docs/SANS_MONITOR_SPEC.md`](SANS_MONITOR_SPEC.md).

---

## Épica 1: Arquitectura Base y Control de Misión [✅ COMPLETADA]
- [x] Limpieza de migraciones obsoletas de proyectos de NEXUS.
- [x] Configuración de MySQL y migraciones base.
- [x] Configuración de roles espaciales y cuentas maestras (`SpaceVision2026!`).
- [x] CRUD de usuarios y roles (`/admin/users`, `/admin/roles`).
- [x] Consola interactiva de comandos Artisan (`/admin/maintenance`).

---

## Épica 2: Modelado Oftalmológico y Núcleo Vision Lab [✅ COMPLETADA]
- [x] Migraciones clínicas: `astronaut_profiles`, `vision_evaluations`, `sans_observations`.
- [x] Servicio de dominio `OphthalmologyCalculator`:
  - $SE = \text{SPH} + (\text{CYL} / 2)$
  - $\text{LogMAR} = \log_{10}(D / N)$
  - $\Delta SE = SE_{\text{actual}} - SE_{\text{basal}}$
  - Vectores de Fourier de Thibos ($M, J_0, J_{45}, B$).
- [x] Cohorte sintética realista en `OphthalmologySeeder` (progresión longitudinal de CDR Alex Mercer FD0 &rarr; FD120).
- [x] **Mission Control Dashboard** (`/admin`) con métricas vivas, telemetría y Chart.js.
- [x] **Vision Lab** (`/admin/vision-lab`, `/admin/vision-lab/create`) con cálculo reactivo.
- [x] **Exportador de Telemetría CSV** (`/admin/vision-lab/export`) con descarga en streaming.

---

## Épica 3: SANS Research Engine & Astronaut Digital Twin [✅ COMPLETADA]
- [x] **Astronaut Digital Twin** (`/admin/digital-twin`) con representación anatómica de aplanamiento de polo posterior y edema de disco Frisén.
- [x] **Mission Simulator** (`/admin/mission-simulator`) con escenarios deterministas, semilla reproducible, slider temporal y controles Play/Pause.

---

## Épica 4: Observatorio de Datos NASA [✅ COMPLETADA]
- [x] Servicio `NasaDataService` con cliente resiliente y fallback curado en `storage/app/nasa_cache/`.
- [x] Integración de estudios biológicos de **NASA OSDR** (OSD-137, OSD-370, OSD-422, OSD-514).
- [x] Integración de reportes de evidencia de **NASA NTRS** (Stenger et al., Mader et al., Macias et al.).
- [x] Integración de telemetría de clima espacial de **NASA DONKI** con disclaimer científico.
- [x] Diagnóstico en vivo de conectividad de APIs y redundancia activa.

---

## Épica 5: Demo Theater & Pitch de Jueces [✅ COMPLETADA]
- [x] **Demo Theater** (`/admin/demo-theater`) con recorrido interactivo de 5 pasos en 3 minutos.
- [x] **Expediente Oficial de Vuelo** (`/admin/demo-theater/report/{code}`) imprimible en PDF con formato NASA.

---

## Épica 6: API RESTful Versionada [✅ COMPLETADA]
- [x] Endpoints bajo `/api/space-vision/v1/`: `missions`, `crew`, `ocular-exams`, `simulator/scenarios`, `calculator/evaluate`, `nasa/osdr`, `nasa/ntrs`, `nasa/donki`.

---

## Épica 7: Inteligencia Científica y Centro de Automatizaciones [✅ COMPLETADA]
- [x] **Research Intelligence** (`/admin/research-intelligence`): visualización de conceptos biomédicos, hipótesis estructuradas y exportador BibTeX/APA.
- [x] **Automation Center** (`/admin/automations`): monitoreo de cron, verificación periódica de integridad de datasets y disparador manual de sincronizaciones.

---

## Épica 8: Expedientes Individuales de Salud Visual [✅ COMPLETADA]
- [x] **Expediente de Tripulante** (`/admin/crew/{code}`): ficha clínica completa con comparativa bilateral OD vs OS, telemetría basal vs actual y biomarcadores OCT.

---

## Épica 9: Landing Page Aeroespacial & Auto-fill de Jueces [✅ COMPLETADA]
- [x] Portada pública (`/`) con diseño espacial y accesos rápidos a Mission Control y Demo Theater.
- [x] Pantalla de login (`/login`) con botones de autorrelleno con un solo clic para los roles de evaluación (`surgeon`, `researcher`, `admin`).
- [x] Ficha oficial de entrega al hackatón (`docs/HACKATHON_SUBMISSION.md`).

---

## Épica 10: Refinamiento de Estabilidad, Calidad Visual y Alta Fidelidad 3D [✅ COMPLETADA]
- [x] **Unificación de Diseño Minimalista Blanco y Negro (AdminCN / Shadcn):** Migración de todas las vistas administrativas a la estética monocromática estricta en blanco y negro (`#09090b` / `#ffffff`) con iconografía exclusiva Font Awesome 4.7.
- [x] **Corrección de Gráfica Infinita (Dashboard Bug):** Contenedores relativos de altura fija y alto contraste dinámico claro/oscuro en Chart.js.
- [x] **Gemelo Digital 3D de Alto Nivel (WebGL Three.js):** Rotación orbital 360°, corte sagital, retícula HUD, trazado de rayos refractivos y deformación según el día de vuelo.
- [x] **Reparación del Simulador de Misión (`/admin/mission-simulator`)** y sanitización de estructuras en **NASA Data (`/admin/nasa-data`)**.

---

## Épica 11: Advanced AI & Machine Learning Scientific Engine [✅ COMPLETADA]
- [x] **Motor Científico en Python & FastAPI (`scientific_engine/app`):** Microservicio en Python 3.14 con FastAPI, NumPy, SciPy, Scikit-learn y Pandas en `:8001`.
- [x] **Núcleo Oftalmológico Matemático Puro:** Equivalente Esférico, LogMAR, vectores de potencia de Thibos ($M, J_0, J_{45}, B$), delta longitudinal y límites biológicos.
- [x] **Laboratorio de Machine Learning Supervisado (`/admin/model-lab`)** y **Detección de Anomalías No Supervisada**.
- [x] **AI Explainability Center (`/admin/explainability`)**, **Data Quality Observatory (`/admin/data-quality`)** y **Model Registry (`/admin/model-registry`)**.
- [x] **Comando Artisan:** `php artisan scientific:engine {status|start|stop|test}`.

---

## Épica 12: Gestión de Claves de APIs & Diagrama de Arquitectura [✅ COMPLETADA]
- [x] **Migración & Modelo `system_api_keys`** y Seeder maestro de integración.
- [x] **Pantalla de Altas, Bajas y Cambios (`/admin/api-keys`)** con ping en vivo (`/admin/api-keys/{id}/ping`) y diagrama interactivo SVG de 5 capas.

---

## Épica 13: Consola Clínica ZEISS Cirrus™ HD-OCT y Modo Dual 3D + OCT [✅ COMPLETADA]
- [x] **Consola ZEISS Cirrus™ HD-OCT (Macular Cube 512x128)** en `/admin/oct-analysis`: matriz hospitalaria de 9 paneles, mapas ILM-RPE en pseudocolor (100 a 500 $\mu\text{m}$), fondo infrarrojo LSO, cortes B-Scan horizontal y vertical con segmentación ILM / RNFL-IPL / RPE-Bruch, rejillas ETDRS de 9 sectores y tabla cuantitativa ($\Delta\text{OD-OS}$).
- [x] **Vista Dual Simultánea (3D + OCT)** en `/admin/digital-twin?mode=dual`.
- [x] **Baseline Verificado:** **67 tests en Laravel (287 aserciones)** y **21 tests en Python Pytest** pasando al 100%.

---

## Épica 14: Especificación Canónica SANS Monitor — Trazabilidad Longitudinal, Inclinación y Seguimiento +30/+60/+90 [📌 DEFINIDA EN `docs/SANS_MONITOR_SPEC.md`]
- [x] **Documentación Canónica Adoptada:** Incorporación íntegra de [`docs/SANS_MONITOR_SPEC.md`](SANS_MONITOR_SPEC.md) como especificación oficial del proyecto.
- [ ] **A. Formulario inicial y alimentación de datos (`RF-01` a `RF-04`):**
  - Ficha con ID pseudónimo/ficticio, distinción explícita «desconocido» vs «no aplica», apertura sin OCT previa (`AT-01`).
  - Separación entre fecha de observación (`observed_at` / `mission_day`) y fecha de disponibilidad (`available_at` / `available_day`) (`AT-04`).
  - Importación CSV con detección de duplicados y OCT no comparables sin sobrescribir el dato original (`AT-09`).
- [ ] **B. Catálogo curado y búsqueda de protocolos (`RF-05` a `RF-09`):**
  - Precarga de las 7 referencias normativas y científicas (NASA OCHMO, Consenso 2026, Macias 2020, Carter 2025, NLSP, Laurie 2019, NASA HRP 2026).
  - Separación entre protocolo previamente aplicado, referencias de seguimiento posterior y versiones verificadas (`AT-02`).
- [ ] **C. Revisión de candidatos sin dictamen automático (`RF-10` a `RF-12`):**
  - Salidas operativas estrictas: «Listo para revisión», «Faltan datos», «Priorizar revisión» y estado «Aptitud no evaluada» salvo registro humano en `ClinicianDecision` (`AT-05`, `AT-08`).
- [ ] **D. Dashboard de expedientes, estudios y tendencias (`RF-13` a `RF-17`):**
  - Cuatro ejes de filtro combinables: conjunto de datos (ficticio, análogo terrestre, orbital autorizado), evolución observada, cobertura (sin línea base vs sin escaneos previos vs sin control reciente) y motor de evaluación (`AT-06`, `AT-07`).
- [ ] **E. Simulador temporal sincronizado (`RF-18` a `RF-22`):**
  - Hitos de demo **10 → 15 → 30** y rangos **0–30**, **0–90**, **0–150** días, con separación entre reproducción histórica y simulación ilustrativa (`AT-03`, `AT-17`).
- [ ] **G. Pestaña «Inclinación y seguimiento» (`RF-26` a `RF-30`):**
  - `ExposureSession` y `ExposureSegment` (ángulo en grados, dirección cabeza abajo/arriba/horizontal, tramos múltiples sin promediar, fin de exposición como día 0 de recuperación) y clasificación por hallazgo (cambio observado, retorno al rango basal con tolerancia documentada, cambio aún observado o seguimiento insuficiente) (`AT-10` a `AT-14`, `AT-19`).
- [ ] **H. Seguimiento cronológico y acciones de revisión hasta +90 días (`RF-31` a `RF-34`):**
  - Tarjetas de hitos **+30, +60 y +90 días** tras aterrizaje o fin de exposición, sin interpolar visitas faltantes (`AT-15`, `AT-16`).
- [ ] **I. Indicadores oculares y capa plegable de contexto (`RF-35` a `RF-37`):**
  - Panel secundario con los 5 dominios (Neurológico, Metabólico, Cardiovascular, Ambiental, Conductual) que no altera el estado ocular ni la acción sugerida en P0 (`AT-18`).
