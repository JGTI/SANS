# SPACE VISION — NASA DATA INTEGRATION & AUTONOMOUS BUILD MASTER PROMPT

> Versión 2.0 · 10 noviembre 2026 · Para ejecutar desde la raíz del repositorio NEXUS Project Factory en Ubuntu.
> **Instrucción principal:** eres el equipo de ingeniería de SPACE VISION. Claude, Codex, Antigravity o cualquier otro agente son una sola continuidad de trabajo: no roles separados. Lee el estado, ejecuta el siguiente trabajo útil, documenta y entrega una base verificable al siguiente agente.

## 0. Misión innegociable
Construir una plataforma web espectacular, funcional, demostrable y científicamente honesta para NASA Space Apps Challenge Querétaro 2026. SPACE VISION investiga cambios de refracción y agudeza visual asociados al vuelo espacial, con especial atención a SANS (Spaceflight Associated Neuro-ocular Syndrome). Integra datos reales de NASA donde tengan relevancia, una simulación inmersiva de misión y un centro de investigación. No afirmar diagnóstico, predicción clínica validada ni causalidad a partir de datos de clima espacial.

**No pidas permiso para cada paso rutinario.** Inspecciona, planea, implementa, prueba y documenta. Solicita confirmación antes de destruir datos, alterar producción, desplegar públicamente, crear cuentas externas o realizar acciones irreversibles. No inventes endpoints ni supongas que los datos existen: consulta documentación oficial, haz pruebas de conectividad y registra resultados.

## 1. Primero: inspección del repositorio
1. `pwd`, `git status --short`, `git branch --show-current`, `ls -la`, `php -v`, `composer --version`, `node -v`, `npm -v`.
2. Examina `composer.json`, `package.json`, rutas, middleware, modelos, migraciones, policies, tests, Vite, Tailwind, Blade/Livewire/Inertia/Vue/React y autenticación.
3. Conserva NEXUS Project Factory, Laravel y PHP 8.3; reutiliza autenticación y plantilla dashboard existentes. No reinstales framework ni reemplaces stack sin necesidad.
4. Detecta infraestructura de colas, scheduler, cache, DB, Docker, Redis y despliegue. Adapta instrucciones a lo encontrado.
5. Crea `docs/SPACE_VISION/STATE.md`, `DECISIONS.md`, `BACKLOG.md`, `DATA_SOURCES.md`, `API_CONTRACTS.md`, `SCIENTIFIC_LIMITATIONS.md`, `DEMO_SCRIPT.md`, `RUNBOOK.md` y `AGENT_HANDOFF.md`.
6. Si existen cambios de usuario sin commitear, respétalos. Haz cambios pequeños y pruebas antes de commits. Nunca expongas `.env` ni secretos.

## 2. Protocolo de continuidad de agentes (sin división de roles)
Todos los agentes son **el mismo desarrollador persistente**. Al comenzar cada sesión: leer `STATE.md`, `AGENT_HANDOFF.md`, `BACKLOG.md`, `DECISIONS.md`, `git log -5 --oneline` y `git status`. Continuar el objetivo prioritario, no empezar otro proyecto. Al terminar: actualizar estado, decisiones, comandos, archivos tocados, tests ejecutados, bloqueos, próximos pasos y commit SHA. Usar una sola cola de trabajo, contratos de API estables, convenciones de código, no sobrescribir trabajo ajeno. Si se trabaja simultáneamente, usar ramas/worktrees y merge consciente. No asignar módulos fijos a modelos específicos.

## 3. Mapa de fuentes NASA: prioridad científica, complementaria y narrativa
Cada conector debe tener `id`, nombre, URL oficial, licencia/atribución, autenticación, frecuencia, cobertura, formato, campos, normalización, cache TTL, estado (`verified`, `experimental`, `disabled`), última sincronización, error y modo de fallback. Implementar primero fuentes relevantes, y las restantes como módulos desacoplados y activables. No confundir **API** con repositorio, visor o portal web.

### A. Prioridad 0 — núcleo biomédico y evidencia
1. **NASA OSDR Biological Data API**: https://www.nasa.gov/osdr-api/ ; https://science.nasa.gov/biological-physical/data/osdr/ . Buscar datasets de ojo, retina, optic nerve, vision, spaceflight, SANS, human, analog. Indexar metadatos, organismo, tejido, ensayo, archivos y DOI. Los estudios experimentales no son cohortes clínicas de astronautas.
2. **OSDR legacy study search / metadata** (verificar versión actual): `https://osdr.nasa.gov/osdr/data/search?term=eye&size=10` y `https://osdr.nasa.gov/osdr/data/osd/meta/137`. Consultar la documentación antes de fijar rutas y esquemas; las APIs pueden evolucionar.
3. **NASA Life Sciences Data Archive / ALSDA**: acceder mediante portal y metadatos permitidos; no automatizar descarga de material restringido. Etiquetar acceso controlado.
4. **NASA Technical Reports Server (NTRS)**: OpenAPI `https://ntrs.nasa.gov/api/openapi/` ; endpoints documentados `/citations/search`, `/citations/{id}`, `/citations/{id}/downloads`. Buscar SANS, ocular, refraction, visual acuity, optic disc edema, choroidal folds. Guardar título, autores, fecha, URL, abstract, identificadores y citas. No presentar resúmenes generados como hechos sin referencia.
5. **NASA Human Research Program / Human Research Roadmap**: fuentes de evidencia, riesgos y protocolos. Cuando no haya API documentada, integrar un catálogo curado de enlaces y metadatos con atribución, no scraping agresivo.
6. **OSDR Environmental Data Application / RadLab**: investigar sus APIs oficiales a través del portal OSDR; ingestión de datos ambientales/radiación si hay cobertura y esquema verificables. No inferir dosis ocular personal a partir de una lectura ambiental genérica.

### B. Prioridad 1 — contexto de misión, clima espacial y radiación
7. **NASA DONKI**: https://api.nasa.gov/ ; https://ccmc.gsfc.nasa.gov/tools/API/ . Conectores independientes para `FLR` (solar flares), `CME`, `CMEAnalysis`, `SEP` (solar energetic particles), `GST` (geomagnetic storms), `IPS`, `MPC`, `RBE`, `HSS`, `WSAEnlilSimulations`, `notifications` según disponibilidad/documentación actual. Un panel Space Weather contextual, sin implicar asociación demostrada con SANS.
8. **CCMC Earth CME Scoreboard API**: pronósticos de llegada CME; comparar pronósticos históricos y datos. Validar Swagger antes de codificar.
9. **CCMC CAMEL API**: consultar estudios públicos y su pertinencia. Habilitar solo si existe caso de uso real.
10. **NASA POWER**: https://power.larc.nasa.gov/docs/services/api/ ; datos meteorológicos y radiación solar en la Tierra para comparar entornos y narrativa educativa, **no** dosimetría orbital ni predictor clínico SANS.
11. **NASA Earthdata CMR**: catálogo de colecciones y granules, https://cmr.earthdata.nasa.gov/search/site/docs/search/api.html ; buscar datos ambientales/radiación con utilidad concreta, manejar Earthdata Login si aplica.
12. **NASA GIBS / Worldview**: imágenes satelitales globales mediante servicios OGC para Mission Earth dashboard; uso visual contextual, no clínico.

### C. Prioridad 2 — narrativa espacial, educación y demostración
13. **NASA Image and Video Library API**: https://images.nasa.gov/docs/images.nasa.gov_api_docs.pdf ; búsqueda de imágenes/videos con metadatos, atribución y derechos verificados. Galería de misiones, entrenamiento y salud humana.
14. **NASA APOD**: https://api.nasa.gov/planetary/apod ; Hero diario / exploración, con crédito y licencia de cada imagen.
15. **NASA EPIC**: https://api.nasa.gov/EPIC/api/natural ; Earth View contextual, cache y fallback.
16. **NASA Mars Rover Photos**: https://api.nasa.gov/mars-photos/api/v1/rovers/curiosity/photos ; disponibilidad sujeta a documentación/estado actual; escenario de exploración, no datos clínicos.
17. **NASA EONET**: https://eonet.gsfc.nasa.gov/docs/v3 ; eventos naturales en Tierra, capa educativa.
18. **NASA Exoplanet Archive TAP**: https://exoplanetarchive.ipac.caltech.edu/docs/TAP/usingTAP.html ; usar TAP, no API antigua obsoleta. Módulo opcional de destinos de exploración, claramente ajeno a SANS.
19. **NASA NeoWs**: https://api.nasa.gov/neo/rest/v1/feed ; observatorio educativo, baja prioridad.
20. **NASA JPL Horizons**: https://ssd.jpl.nasa.gov/horizons/manual.html ; efemérides y trayectorias si la experiencia de simulación lo requiere. Validar uso y parámetros; no simular precisión orbital falsa.
21. **NASA Open APIs adicionales**: explorar catálogo https://api.nasa.gov/ y registrar módulos solo si aportan al producto. No implementar APIs decorativas antes de completar el núcleo científico.

**Política de fuentes:** toda integración requiere prueba real, test con fixture, schema versionado, cache, reintentos con backoff, rate limit, registro de errores, timestamp, enlace al origen y fallback offline. No etiquetar datos como `live` si son históricos o cacheados. No usar `DEMO_KEY` para producción. Secretos en `.env`, nunca en frontend.

## 4. Visión de producto — dashboards de impacto
1. [x] **Mission Control** (`/admin`): astronauta simulado, fase de misión, salud de conectores, alertas no diagnósticas, tarjetas de tendencias, tabla de tripulantes y gráfica longitudinal Chart.js.
2. [x] **Vision Lab** (`/admin/vision-lab`, `/admin/vision-lab/create`, `/admin/vision-lab/export`): OD/OI, esfera, cilindro, eje, AV Snellen y logMAR, equivalentes esféricos, gráficos de evolución, formulario validado y exportación de telemetría a CSV.
3. [x] **SANS Research Studio** (`/admin/crew/{code}`): catálogo de hallazgos (edema de disco, pliegues, aplanamiento, cambio hipermetrópico), matriz de evidencia, expedientes individuales por tripulante.
4. [x] **Astronaut Digital Twin** (`/admin/digital-twin`): corte sagital SVG interactivo del ojo, deformación de polo posterior y edema de papila en microgravedad (FD0 a FD120).
5. [x] **NASA Data Observatory** (`/admin/nasa-data`): explorador OSDR, NTRS y DONKI con filtros, links oficiales y redundancia offline curada al 100%.
6. [x] **Space Weather & Radiation Context** (`/admin/nasa-data?tab=donki`): eventos DONKI (CME, flares, tormentas geomagnéticas) con cláusula explícita de rigor científico sin falsa causalidad.
7. [x] **Mission Simulator** (`/admin/mission-simulator`): 3 escenarios deterministas con semilla reproducible (Estable, Hiperopía Gradual y SANS Crítico), Play/Pause, velocidad (1x, 2x, 5x) y etiqueta `SIMULATED` permanente.
8. [x] **Research Intelligence** (`/admin/research-intelligence`): matriz de hipótesis etiológicas (gradiente translaminar IOP/ICP, congestión venosa, polimorfismos genéticos), notas de investigador y generador de citas BibTeX/APA.
9. [x] **Automation Center** (`/admin/automations`): monitoreo de cron/workers, auditoría de integridad clínica y botón de sincronización de datasets bajo demanda.
10. [x] **Demo Theater** (`/admin/demo-theater`, `/admin/demo-theater/report/{code}`): recorrido guiado interactivo de 5 pasos en 3 minutos, datos locales sin dependencia de red externa y reporte oficial imprimible en PDF con formato NASA.
11. [x] **Admin / Data Governance** (`/admin/users`, `/admin/roles`, `/admin/maintenance`): control RBAC con Spatie, gestión de usuarios, roles espaciales y consola interactiva de comandos Artisan.

## 5. Modelo de datos
Crear migraciones y factories para `missions`, `crew_profiles` (pseudónimos), `ocular_examinations`, `refractions`, `visual_acuity_measurements`, `ocular_findings`, `symptom_reports`, `mission_events`, `space_weather_events`, `data_sources`, `data_sync_runs`, `research_studies`, `research_citations`, `evidence_links`, `analysis_runs`, `analysis_flags`, `simulator_scenarios`, `simulator_frames`, `generated_reports`, `audit_events`, `automation_runs`. Ajustar nombres al repositorio. Incluir `source_kind` (`NASA_PUBLIC`, `SYNTHETIC`, `USER_ENTERED`, `DERIVED`), `source_url`, `collected_at`, `ingested_at`, `schema_version`, `provenance` JSON, `confidence/quality` cuando proceda. Nunca mezclar datos sintéticos con NASA real sin etiquetas.

## 6. Cálculos y seguridad científica
- Equivalente esférico: `SE = sphere + cylinder/2`, dioptrías.
- Refracción por ojo y convención de cilindro documentada. Validar eje 0–180, unidades, precisión, valores nulos y cambios de convención.
- Agudeza visual: almacenar Snellen como numerador/denominador y convertir a logMAR `log10(denominator/numerator)` cuando sea válido; no usar cadenas sin validar ni mezclar con ETDRS sin transformación explícita.
- Delta longitudinal por ojo, baseline, intervalos, pendientes, incertidumbre, missingness.
- Flags configurables **de investigación**, nunca diagnóstico automático de SANS. Si se usa un umbral como +0.75 D, documentar fuente, contexto y límites; no equiparar con enfermedad.
- Sin datos clínicos suficientes, no construir clasificador de SANS ni mostrar porcentajes de riesgo personalizados. Un `research pattern score` puede ser ilustrativo, pero debe etiquetarse como heurística no validada y explicar cada componente; preferir banderas interpretables.
- Radiación/clima espacial: contexto de misión; no declarar causalidad con cambios refractivos.
- Privacidad: datos ficticios por defecto; cualquier dato real requiere consentimiento, minimización, control de acceso, auditoría y política legal aplicable.

## 7. Arquitectura preferida
Laravel existente + PHP 8.3 + PostgreSQL o DB ya presente + Redis/queues si existen + Scheduler + Vite. Reutilizar UI NEXUS; si es Inertia/React/Vue/Livewire, seguir patrón actual. Tailwind, componentes reutilizables, ECharts/Plotly/Chart.js según stack. Python FastAPI solo para procesos científicos que realmente lo requieran, como análisis de imágenes o ML futuro; no crear microservicios por moda. Adaptadores NASA en `app/Integrations/Nasa/*`, servicios de dominio en `app/Domain/SpaceVision/*`, jobs en `app/Jobs/*`, tests de integración con HTTP fake y fixtures. Interfaces: `DataProvider`, `SyncResult`, `Normalizer`, `ProvenanceRecord`.

## 8. Automatizaciones
- Sincronización NTRS/OSDR diaria con ETag/cache cuando sea compatible.
- DONKI cada 30–60 min según límites y necesidad; registrar timestamp de origen.
- Reindexación bibliográfica nocturna; deduplicación por DOI/identificador.
- Detección de nuevas publicaciones y datasets relevantes.
- Chequeo de salud de APIs y aviso cuando falle un proveedor.
- Validación de calidad de datos al ingresar: unidades, duplicados, rango, missingness.
- Recalcular series y banderas cuando entra una evaluación.
- Generar reportes de misión en PDF/CSV con procedencia y disclaimer.
- Backup y limpieza de cachés con retención configurable.
- Capturas/screenshots de demo y test end-to-end antes de presentación.
- Toda automatización: idempotente, observable, reintentable, con timeout, límites y posibilidad de desactivar.

## 9. Experiencia visual
Diseño oscuro premium, azul profundo, cyan y acentos ámbar para eventos; tipografía legible, excelente contraste, accesibilidad WCAG, responsive. Dashboard cinematográfico **sin sacrificar precisión científica**. Estados loading/empty/error/offline reales. Skeletons, animaciones sutiles, gráficos con unidades, leyendas, ejes y etiquetas de fuente. Evitar saturación de widgets irrelevantes.

## 10. Contratos y pruebas
[x] Crear API versionada `/api/space-vision/v1/` para `missions`, `crew`, `ocular-exams`, `simulator/scenarios`, `calculator/evaluate`, `nasa/osdr`, `nasa/ntrs`, `nasa/donki`.
[x] Pruebas automatizadas pasando al 100% (46 tests / 197 assertions).
[x] Tests de conversión Snellen a LogMAR, Equivalente Esférico y aislamiento de roles Spatie.

## 11. Plan de ejecución autónoma por etapas
- [x] **Etapa 0 — Reconocimiento (1–2 h):** inventario NEXUS, decisiones, entorno, riesgos, backlog.
- [x] **Etapa 1 — Vertical slice (3–5 h):** login existente con autorrelleno → Mission Control → astronauta simulado → registro OD/OI → cálculo SE/LogMAR → gráfico longitudinal → reporte de misión.
- [x] **Etapa 2 — NASA real (3–6 h):** OSDR metadata + NTRS search + DONKI, 3 conectores verificados y probados con cache y fallback local en `storage/app/nasa_cache/`.
- [x] **Etapa 3 — Simulación (3–5 h):** escenarios reproducibles (Estable, Hiperopía Gradual y SANS Crítico), línea temporal FD0 a FD180, banderas explicables y Mission Simulator.
- [x] **Etapa 4 — Wow factor (3–5 h):** Astronaut Digital Twin en corte sagital SVG, Demo Theater en 5 pasos, NASA Data Observatory, Research Intelligence con BibTeX y expedientes individuales de tripulación.
- [x] **Etapa 5 — Endurecimiento (2–4 h):** 46 tests verdes, accesibilidad, responsive, seeds biomédicos, modo offline blindado, pitch de 3 minutos, documentación y checklist de envío (`docs/HACKATHON_SUBMISSION.md`).
- [x] **Etapa 6 — Extensiones:** Exportador de telemetría a CSV (`/admin/vision-lab/export`), Centro de Automatizaciones (`/admin/automations`), APOD hero y portada aeroespacial pública.

## 12. Definition of Done
- [x] Instalación documentada en Ubuntu y comando reproducible para arrancar (`php artisan migrate:fresh --seed`).
- [x] Login NEXUS funcional con chips de autorrelleno para jueces; sin duplicación de auth.
- [x] 3 escenarios de misión sintéticos reproducibles y etiquetados en `/admin/mission-simulator`.
- [x] Registro refractivo y AV con validaciones y cálculo certificado ($SE = SPH + CYL/2$, $\text{LogMAR} = \log_{10}(D/N)$).
- [x] 3 integraciones NASA reales con pruebas y URLs oficiales, con fallback local garantizado.
- [x] Gráficas y fuentes con unidades (dioptrías, mmHg, $\mu\text{m}$), fechas y procedencia.
- [x] Reporte exportable en PDF/imprimible, exportador CSV, Demo Theater offline y guion de 3 minutos.
- [x] Tests verdes (46 tests / 197 aserciones), sin secretos en git, sin endpoints falsos, sin diagnóstico clínico no validado.
- [x] README oficial de NASA Space Apps 2026, decisiones ADR, handoff y pasos de despliegue completos.

## 13. Guion de demo de 3 minutos
0:00–0:20: problema SANS y necesidad de seguimiento.
0:20–0:45: Mission Control con astronauta y contexto de misión.
0:45–1:20: Vision Lab, introducir cambio refractivo y observar tendencia.
1:20–1:50: Mission Simulator, evolución acelerada y bandera explicable.
1:50–2:20: NASA Data Observatory, mostrar estudio OSDR/NTRS real y procedencia.
2:20–2:45: Space Weather contextual y reporte reproducible.
2:45–3:00: impacto, límites científicos y hoja de ruta de validación.

## 14. Verificación del reto oficial
Antes de afirmar elegibilidad, revisar desafíos 2026 en https://www.spaceappschallenge.org/2026/ y elegir uno cuya descripción permita SPACE VISION. Si ninguno encaja, documentar pivot viable; no inventar que NASA pidió específicamente una herramienta SANS. La entrega y requisitos oficiales tienen prioridad sobre este documento.

## 15. Prompt operativo de arranque (copiar al agente)
> Eres la continuidad de ingeniería de SPACE VISION. Lee por completo `SPACE_VISION_NASA_API_MEGA_PROMPT.md`, inspecciona el repositorio NEXUS real y crea/actualiza `docs/SPACE_VISION/STATE.md` y `AGENT_HANDOFF.md`. No te dividas por nombres de agentes. Implementa la siguiente unidad vertical de mayor valor, no una maqueta. Reutiliza Laravel/PHP 8.3 y autenticación/dashboard existentes. Prioriza Vision Lab + Mission Control + datos NASA OSDR/NTRS/DONKI con procedencia verificable, simulador y demo offline. Ejecuta pruebas y build, registra evidencias y deja instrucciones exactas para que el siguiente agente continúe sin perder contexto. Trabaja autónomamente en acciones reversibles y pide confirmación para operaciones destructivas o despliegues externos. No inventes APIs, resultados clínicos, métricas de predicción ni datos de astronautas.

## 16. Referencias oficiales de partida (verificadas en consulta documental, no en ejecución del entorno)
- OSDR API: https://www.nasa.gov/osdr-api/
- OSDR data portal: https://science.nasa.gov/biological-physical/data/osdr/
- OSDR legacy API: https://api.nasa.gov/
- NTRS OpenAPI: https://ntrs.nasa.gov/api/openapi/
- CCMC APIs: https://ccmc.gsfc.nasa.gov/tools/API/
- NASA Open APIs: https://api.nasa.gov/
- POWER: https://power.larc.nasa.gov/docs/services/api/
- Exoplanet TAP: https://exoplanetarchive.ipac.caltech.edu/docs/TAP/usingTAP.html
- NASA Space Apps 2026: https://www.spaceappschallenge.org/2026/

**Comienza ahora.** Tu primera respuesta debe resumir lo que encontraste realmente en NEXUS, tu primer vertical slice y los comandos que ejecutaste. Después, implementa.
