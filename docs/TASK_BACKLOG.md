# 📋 SPACE VISION — MASTER TASK BACKLOG

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
  - $SE = SPH + (CYL / 2)$
  - $\text{LogMAR} = \log_{10}(D / N)$
  - $\Delta SE = SE_{\text{actual}} - SE_{\text{basal}}$
  - Estratificación de riesgo explicable SANS.
- [x] Cohorte sintética realista en `OphthalmologySeeder` (progresión longitudinal de CDR Alex Mercer FD0 &rarr; FD120).
- [x] **Mission Control Dashboard** (`/admin`) con métricas vivas, telemetría y Chart.js.
- [x] **Vision Lab** (`/admin/vision-lab`, `/admin/vision-lab/create`) con cálculo reactivo.
- [x] **Exportador de Telemetría CSV** (`/admin/vision-lab/export`) con descarga en streaming.

---

## Épica 3: SANS Research Engine & Astronaut Digital Twin [✅ COMPLETADA]
- [x] **Astronaut Digital Twin** (`/admin/digital-twin`) con SVG reactivo que ilustra aplanamiento de polo posterior y edema de disco Frisén.
- [x] **Mission Simulator** (`/admin/mission-simulator`) con 3 escenarios deterministas, semilla reproducible, slider temporal FD0 &rarr; FD180 y controles Play/Pause.

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
- [x] **Unificación de Diseño Minimalista Blanco y Negro (AdminCN / Shadcn):** Migración total de todas las vistas administrativas residuales de Nexus/AdminLTE (`/admin/users`, `/admin/roles`, `/admin/maintenance`, `/admin/contacts`, `/admin/projects`) a la estética monocromática estricta en blanco y negro solicitada por el usuario.
- [x] **Corrección de Gráfica Infinita (Dashboard Bug):** Resolución del bucle de autoresize continuo de Chart.js en `/admin` y `/admin/vision-lab` encapsulando los lienzos en contenedores relativos de altura fija.
- [x] **Gemelo Digital 3D de Alto Nivel (WebGL Three.js):** Reemplazo del dibujo plano tipo Paint por un Gemelo Digital 3D interactivo con Three.js, rotación orbital 360°, corte sagital, retícula de telemetría HUD, trazado de rayos refractivos, deformación de vértices en tiempo real según el día de vuelo (FD0 &rarr; FD120) y simulación de escaneo B-Scan OCT.
- [x] **Reparación del Simulador de Misión (`/admin/mission-simulator`):** Corrección del motor de simulación reactivo (Play/Pausa, paso a paso, carga de semillas y escenarios en tiempo real).
- [x] **Resolución de Error 500 en NASA Data (`/admin/nasa-data`):** Sanitización contra estructuras tipo array en la API en vivo de NASA OSDR (campo `Mission`) tanto en `NasaDataService` como en la vista Blade, protegiendo todos los repositorios (OSDR, NTRS, DONKI, APOD, ISS).
- [x] **Garantía de Suite de Pruebas:** 48 de 48 pruebas unitarias y funcionales pasando al 100% (204 aserciones).

---

## Épica 11: Advanced AI & Machine Learning Scientific Engine [✅ COMPLETADA]
- [x] **Motor Científico en Python & FastAPI (`scientific_engine/app`):** Microservicio en Python 3.14 con FastAPI, NumPy, SciPy, Scikit-learn y Pandas, ejecutando en puerto `:8001` con autenticación Bearer y contratos versionados.
- [x] **Núcleo Oftalmológico Matemático Puro:** Cálculo determinista de Equivalente Esférico, LogMAR, vectores de potencia de Thibos ($M, J_0, J_{45}, B$), delta longitudinal y límites biológicos.
- [x] **Simulador de Gemelos Digitales de Astronautas:** Generación determinista con semillas de los Escenarios A, B, C, D y E en microgravedad con shift de fluidos cefálicos.
- [x] **Laboratorio de Machine Learning Supervisado:** Benchmark de validación cruzada estratificada entre Random Forest (100 árboles), Gradient Boosting y Logistic Regression con matrices de confusión y rankings de variables.
- [x] **Detección de Anomalías No Supervisada:** Isolation Forest, Local Outlier Factor (LOF), One-Class SVM y Robust Z-Score sobre series temporales oftalmológicas orbitales.
- [x] **SPACE VISION — AI Explainability Center (XAI):** Atribución de características locales/globales con pesos SHAP-compatibles, dirección de impacto, intervalos de incertidumbre del 95% y descripciones biomecánicas sin presunciones de causalidad no demostradas.
- [x] **Data Quality Observatory:** Auditoría automática de completitud de datasets, detección de valores faltantes y conformidad con rangos fisiológicos.
- [x] **Model Registry & Linaje:** Inventario formal de modelos registrados (`VALIDATED`, `CANDIDATE`, `EXPERIMENTAL`), metadatos de autor, versiones y artefactos.
- [x] **Research Intelligence & Semantic Search:** Motor de búsqueda semántica RAG con TF-IDF y similitud de coseno sobre corpus verificado de NASA NTRS y OSDR sin inventar citas.
- [x] **Preprocesador de Imágenes OCT / Fondo de Ojo:** Segmentación simulada y perfil circunferencial peripapilar TSNIT RNFL Spectralis.
- [x] **Integración Resiliente en Laravel:** `ScientificEngineService` con conmutación transparente a cálculo local PHP si el microservicio entra en mantenimiento.
- [x] **Comando Artisan y Consola de Misión:** `php artisan scientific:engine {status|start|stop|test}` integrado en `/admin/maintenance`.
- [x] **5 Vistas Nuevas en Frontend Monocromático:** `/admin/ai-command`, `/admin/model-lab`, `/admin/explainability`, `/admin/data-quality`, `/admin/model-registry`.
- [x] **Suite de Pruebas Automatizadas Verificadas:**
  - 20 pruebas en Python (`pytest`) pasando al 100%.
  - 56 pruebas en Laravel (`php artisan test`) pasando al 100% (235 aserciones).

---

## Épica 12: Gestión de Claves de APIs & Diagrama de Arquitectura [✅ COMPLETADA]
- [x] **Migración & Modelo `system_api_keys`:** Persistencia relacional de credenciales con enmascaramiento seguro, categorización, URLs de endpoints y trazabilidad de pruebas.
- [x] **Seeder Maestro de Claves de Integración:** Configuración predeterminada para `NASA_API_KEY`, `SCIENTIFIC_ENGINE_TOKEN`, `SCIENTIFIC_ENGINE_URL`, `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY` y `GEMINI_API_KEY`.
- [x] **Pantalla de Altas, Bajas y Cambios (`/admin/api-keys`):**
  - Registro de nuevas claves de API mediante modal interactivo.
  - Edición en caliente de valores, estados (`ACTIVE`, `OPTIONAL`, `DISABLED`) y descripciones.
  - Eliminación segura con protección de claves estructurales de la misión.
  - Conmutador para ocultar/mostrar valores secretos con icono de ojo.
  - Prueba de conectividad en vivo con latencia en milisegundos (`/admin/api-keys/{id}/ping`).
- [x] **Diagrama de Arquitectura de Alto Nivel:**
  - Diagrama visual interactivo en SVG monocromático de alta resolución al pie de la pantalla.
  - Mapeo completo de las 5 capas del sistema: Estación de Tierra (Frontend Three.js / Chart.js), Orquestador Laravel 11, Enlace Interno Bearer, Motor Científico Python FastAPI y Servicios Externos de NASA y Google Cloud.
- [x] **Suite de Pruebas Feature (`ApiKeyArchitectureTest`):** 7 pruebas automatizadas pasando al 100%. Totalizando 63 pruebas en Laravel y 20 en Python.
