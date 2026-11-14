# 📊 SPACE VISION / SANS MONITOR — DEVELOPMENT STATE

**Fecha:** 14 de noviembre de 2026  
**Rama activa:** `main`  
**Especificación Canónica:** [`docs/SANS_MONITOR_SPEC.md`](SANS_MONITOR_SPEC.md)  
**URL de Producción:** `https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com`  
**Repositorio Oficial:** `https://github.com/JGTI/SANS`  

**Estado de la suite de pruebas (Baseline 100% Verde):**
- **Laravel (PHPUnit):** **75 tests aprobados (353 aserciones)**
- **Python Scientific Engine (Pytest):** **22 tests aprobados**
- **Microservicio Python FastAPI:** **ONLINE (`http://127.0.0.1:8001`)**

---

## 1. Módulos Implementados y Verificados

| Módulo | Estado | Rutas / Componentes | Pruebas |
|---|---|---|---|
| **SANS Monitor (Workspace de 5 Pestañas)** | ✅ Completo | `/admin/sans-monitor` (`SansMonitorController` & `SansMonitorEngine`): 1. Ficha y protocolos, 2. Seguimiento del ojo (+30/+60/+90), 3. Simulador, 4. Estudios y tendencias, 5. Inclinación y seguimiento | `SansMonitorAcceptanceTest` (8 tests / 66 aserciones cubriendo `AT-01` a `AT-19`) |
| **Consola ZEISS Cirrus™ HD-OCT (Macular Cube 512x128)** | ✅ Completo | `/admin/oct-analysis` y `/admin/digital-twin?mode=dual` (`DigitalTwinController`): Matriz de 9 paneles, mapas ILM-RPE, fondo LSO, B-Scans H/V, rejilla ETDRS y tabla bilateral OD/OS | `DigitalTwinTest` (4 tests) & `test_cirrus_oct_workstation_endpoint` |
| **Astronaut Digital Twin (Gemelo 3D WebGL)** | ✅ Completo | `/admin/digital-twin` (`DigitalTwinController` & Three.js con corte sagital, retícula HUD y vista dual 3D + OCT) | `DigitalTwinTest` (4 tests) |
| **Vision Lab Oftalmológico** | ✅ Completo | `/admin/vision-lab` (`VisionLabController`): Equivalente Esférico ($SE$), Snellen $\to$ LogMAR, vectores de Thibos ($M, J_0, J_{45}, B$) y exportador CSV | `VisionLabTest` (4 tests) |
| **Expedientes Individuales de Salud Visual** | ✅ Completo | `/admin/crew/{code}` (`CrewDossierController`): Ficha longitudinal por ojo (OD/OS), comparativa contra línea base y biomarcadores OCT | `SpaceVisionApiTest` / `DemoTheaterTest` |
| **Simulador Temporal SANS** | ✅ Completo | `/admin/mission-simulator` (`MissionSimulatorController`): Escenarios reproducibles con semilla, control temporal y progresión longitudinal | `MissionSimulatorTest` (3 tests) |
| **NASA Data Observatory & Catálogo** | ✅ Completo | `/admin/nasa-data` (`NasaObservatoryController` & `NasaDataService`): OSDR, NTRS, DONKI, APOD con caché local curado | `NasaObservatoryTest` (4 tests) |
| **Research Intelligence & Automatizaciones** | ✅ Completo | `/admin/research-intelligence` y `/admin/automations`: Hipótesis biomecánicas, búsqueda semántica TF-IDF y exportador BibTeX/APA | `IntelligenceAndAutomationsTest` |
| **Python Scientific Engine** | ✅ Activo | `scientific_engine/app` (FastAPI en `:8001`): NumPy 2.5, SciPy 1.18, Scikit-learn 1.9, Pandas 3.1 | `test_api_endpoints.py`, `test_machine_learning.py` (21 tests) |
| **AI Command Center** | ✅ Completo | `/admin/ai-command` (`AiCommandCenterController`): Supervisión en vivo del microservicio científico | `AdvancedAiSuiteTest` |
| **Laboratorio de Modelos (Model Lab)** | ✅ Completo | `/admin/model-lab` (`ModelLabController`): Benchmark Random Forest vs GBM vs OLS con exportación JSON | `AdvancedAiSuiteTest` |
| **AI Explainability Center (XAI)** | ✅ Completo | `/admin/explainability` (`ExplainabilityCenterController`): Atribución de características SHAP e intervalos de incertidumbre | `AdvancedAiSuiteTest` |
| **Data Quality Observatory** | ✅ Completo | `/admin/data-quality` (`DataQualityObservatoryController`): Auditoría de completitud, faltantes y rangos fisiológicos | `AdvancedAiSuiteTest` |
| **Registro de Modelos (Model Registry)** | ✅ Completo | `/admin/model-registry` (`ModelRegistryController`): Linaje de versiones y artefactos (`VALIDATED`, `CANDIDATE`, `EXPERIMENTAL`) | `AdvancedAiSuiteTest` |
| **Demo Theater & Expediente Imprimible** | ✅ Completo | `/admin/demo-theater` y `/admin/demo-theater/report/{code}` (`DemoTheaterController`): Recorrido guiado y reporte `@media print` | `DemoTheaterTest` (3 tests) |
| **Gestión de Claves de APIs & Arquitectura** | ✅ Completo | `/admin/api-keys` (`ApiKeyArchitectureController`): CRUD, ping en vivo, sync `.env` y diagrama SVG de 5 capas | `ApiKeyArchitectureTest` (8 tests) |
| **Mission Control Dashboard** | ✅ Completo | `/admin` (`MissionControlController`): Telemetría longitudinal y estado operativo de expedientes | `AuthenticationTest` |
| **Consola de Mantenimiento y Artisan** | ✅ Completo | `/admin/maintenance` (`MaintenanceController`): Ejecución controlada de comandos Artisan y motor científico | `AdminManagementTest` |

---

## 2. Especificación Canónica SANS Monitor (P0 · P1 · P2)

El documento [`docs/SANS_MONITOR_SPEC.md`](SANS_MONITOR_SPEC.md) define el alcance funcional definitivo:
- ** Cinco pestañas / flujos centrales (P0):**
  1. **Ficha y protocolos** (`RF-01` a `RF-09`): Ficha pseudónima, antecedentes por ojo, catálogo curado de protocolos con separación entre protocolo previamente aplicado, seguimiento pertinente y versiones verificadas.
  2. **Seguimiento del ojo (+30, +60, +90 días)** (`RF-10` a `RF-13`, `RF-31` a `RF-36`): Línea de tiempo por ojo (Antes, Durante días 10→15→30, Fin +0, y controles posteriores +30, +60 y +90 días), tres salidas operativas («Listo para revisión», «Faltan datos», «Priorizar revisión») sin dictamen automático de aptitud, y panel plegable con los 5 dominios de contexto.
  3. **Simulador temporal del ojo** (`RF-18` a `RF-22`): Tres vistas esquemáticas sincronizadas (sagital del globo, fondo de ojo con disco óptico, corte de capas tipo OCT), hitos 10 → 15 → 30 y rangos 0–30, 0–90, 0–150 días, separando reproducción histórica (`available_at`) de simulación ilustrativa.
  4. **Estudios y tendencias** (`RF-14` a `RF-17`, `RF-23` a `RF-25`, `RF-30`, `RF-37`): Filtros combinables por conjunto de datos (ficticio, análogo terrestre, orbital autorizado), evolución observada, cobertura y motor de evaluación.
  5. **Inclinación y seguimiento** (`RF-26` a `RF-29`): Registro y comparación de exposiciones inclinadas (ej. $-6^\circ$ cabeza abajo) por tramos, tomando el fin de la exposición como día 0 de recuperación e identificando cambio observado, retorno al rango basal, cambio aún observado o seguimiento insuficiente.

---

## 3. Credenciales Maestras de Demostración
Contraseña universal para cuentas de prueba/demo: `SpaceVision2026!`
- `admin@spacevision.nasa` (`super-admin` — Director de Vuelo / Presentador de Demo)
- `operator@spacevision.nasa` (`admin` — Responsable de Datos / Curador de Protocolos)
- `surgeon@spacevision.nasa` (`flight-surgeon` — Especialista Médico Autorizado)
- `researcher@spacevision.nasa` (`researcher` — Investigador / Analista)
- `astronaut@spacevision.nasa` (`astronaut` — Tripulante Simulado)

---

## 4. Instrucciones de Ejecución del Entorno Completo
```bash
# 1. Verificar o iniciar el motor científico Python (FastAPI en :8001)
php artisan scientific:engine status
php artisan scientific:engine start

# 2. Ejecutar la suite completa de pruebas científicas en Python (21 tests)
php artisan scientific:engine test

# 3. Ejecutar la suite de pruebas de Laravel (67 tests / 287 aserciones)
php artisan test
```
