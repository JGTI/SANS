# ✅ SPACE VISION — SANS MONITOR: CHECKLIST INTEGRAL DE PROYECTO Y CONCURSO
## NASA International Space Apps Challenge 2026 · NEXUS × Visual Rehab Center

**Fecha de corte:** 14 de noviembre de 2026  
**Rama:** `main` (`https://github.com/JGTI/SANS`)  
**URL Pública en Vivo:** `https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com`  
**Suite de Pruebas:** `77 passed` (PHPUnit) + `22 passed` (Pytest FastAPI `:8001`) = **99 pruebas automatizadas al 100% en verde**.

---

## 1. Reglas Críticas del Concurso (P0 — Bloqueantes)

- [x] **Acceso público directo en la raíz (`/`) sin login:** La URL principal abre directamente el Predictor SANS de 3 pestañas sin pedir usuario, contraseña, registro, OAuth ni claves API.
- [x] **Sin landing intermedia:** Eliminada como portada para que el jurado interactúe desde el primer segundo con la herramienta funcional.
- [x] **Inglés (`EN`) como idioma principal por defecto + Selector Bilingüe (`EN | ES`):** Al abrir la app en cualquier navegador o ventana de incógnito, carga en **Inglés (`en`)** y permite cambiar en 1 clic a **Español (`es`)**.
- [x] **Navegación pública sin bloqueos en módulos de apoyo:** Los botones y enlaces del flujo principal (`/`, `/demo`, `/sans-monitor`, `/digital-twin?mode=dual`, `/oct-analysis`, `/demo-theater`, `/vision-lab`, `/nasa-data`, `/model-lab`, `/explainability`) son accesibles sin sesión activa.
- [x] **Botón de Reinicio de Demostración (`Reset Demo`):** Disponible en la cabecera principal para restaurar el escenario por defecto en 1 clic.
- [x] **Cumplimiento de Brand Guidelines de NASA:** Leyenda oficial *"Developed by NEXUS × Visual Rehab Center for the NASA International Space Apps Challenge 2026."* integrada en el pie de página y documentos sin afirmar respaldo ni validación clínica oficial de NASA.

---

## 2. Flujo Central Simplificado de Alto Impacto (Las 3 Pestañas Principales)

### Pestaña 1 — Formulario Inicial de la Persona + Predictor de los 6 Biomarcadores Oculares
- [x] **1. Espesor medio por OCT (`TRT` en $\mu\text{m}$):** Comparación de línea base vs control actual con disparador determinista ($\Delta\text{TRT} \ge +20\ \mu\text{m}$).
- [x] **2. Longitud axial del ojo ($\text{mm}$):** Detección de acortamiento axial ($\Delta \le -0.15\ \text{mm}$) compatible con aplanamiento del polo posterior en microgravedad.
- [x] **3. Hipermetropía simulada ($\Delta SE$ en Dioptrías $\text{D}$):** Evaluación de desplazamiento refractivo hipermetrópico ($+0.50\ \text{D}$ a $+1.50\ \text{D}$) separada de las observaciones medidas (`RF-35`).
- [x] **4. Edema papilar (Escala clínica de Frisén $0\text{–}5$):** Objetivo principal de pronóstico neuro-ocular (`RF-36`).
- [x] **5. Presión intraocular (`PIO` / `IOP` en $\text{mmHg}$):** Tonometría ocular visible incluso cuando falta escaneo OCT (`AT-17`).
- [x] **6. Exposición previa a inclinación cefálica de $-6^\circ$ en la Tierra (`-6° HDT`):** Registro y ponderación del análogo terrestre de 14 días ($280 \to 304 \to 284\ \mu\text{m}$) con su propia línea base separada (`AT-19`).
- [x] **Índice de Señal Predictiva ($0\text{–}100$) + Gráfico de Pesos por Biomarcador (Chart.js) + Formulario de Alta Rápida de Sujeto (`RF-01`).**

### Pestaña 2 — Medición del Tiempo: Antes · Durante · Después (Corte a +30, +60 y +90 Días)
- [x] **Bloque 1 — Antes del vuelo (Línea Base Día 0):** Referencia basal por ojo (`OD` / `OS`).
- [x] **Bloque 2 — Durante el vuelo (Días 10 $\to$ 15 $\to$ 30):** Evolución en órbita respetando disponibilidad de resultados (`available_day`, `AT-03`).
- [x] **Bloque 3 — Análogo previo $-6^\circ$ en Tierra:** Comparación de cambio inmediato ($+24\ \mu\text{m}$) y recuperación a $+30$ días ($+4\ \mu\text{m}$, `AT-10`, `AT-11`).
- [x] **Corte Posvuelo a +30, +60 y +90 días (`RF-31` a `RF-34`, `AT-15`, `AT-16`):** Muestra explícitamente cada hito y conserva el corte de **+60 días como «Unmeasured / Sin medición» sin interpolarlo jamás**.
- [x] **Curva Longitudinal Chart.js con hueco real en el corte +60 sin medición (`spanGaps: false`).**

### Pestaña 3 — Los 3 Escenarios Modelos (1 Clic)
- [x] **1. Escenario Estable (`CASE-STABLE-01`):** OCT basal $278 \to 284\ \mu\text{m}$ ($\Delta +6\ \mu\text{m}$), Frisén 0/5 $\to$ Salida operativa: **«Ready for review / Listo para revisión»**.
- [x] **2. Escenario que Empeora (`CASE-PROG-02`):** OCT basal $280 \to 306\ \mu\text{m}$ ($\Delta +26\ \mu\text{m}$ al día 30), hipermetropía $+0.75\ \text{D}$, Frisén 2/5 $\to$ Salida operativa: **«Prioritize review / Priorizar revisión»**.
- [x] **3. Escenario que No se Escanea (`CASE-NOSCAN-03A`):** Sin escaneo OCT registrado (con PIO de $16\ \text{mmHg}$) $\to$ Salida operativa: **«Missing data / Faltan datos»** (nunca se dibuja ni clasifica como normal o estable, `AT-07`, `AT-17`).
- [x] **3 Vistas Esquemáticas Anatómicas SVG sincronizadas:** (1) Longitud axial / corte sagital, (2) Edema papilar / fondo de disco óptico, (3) Capas retinianas OCT.

---

## 3. Especificación Canónica (`SANS_MONITOR_SPEC.md`) y 19 Pruebas de Aceptación (`AT-01` a `AT-19`)

- [x] **`AT-01`:** Crear ficha sin OCT ni protocolo previo $\to$ Estado operativo «Missing data / Faltan datos» y «Fitness not evaluated / Aptitud no evaluada».
- [x] **`AT-02`:** Distinguir protocolo aplicado previamente (`OCHMO-MTB-001-V2022`), versión posterior verificada (`OCHMO-MTB-001-V2024`) y artículo de investigación sin sobrescribir la selección previa.
- [x] **`AT-03` y `AT-04`:** Reproducción histórica oculta el resultado del día 15 hasta el día 20 (`available_day = 20`); el modo simulación marca los fotogramas interpolados como sintéticos y el día 180 como «Fuera del rango».
- [x] **`AT-05`:** Coexistencia en la misma ficha de ojo derecho (`OD`) en progresión y ojo izquierdo (`OS`) sin línea base.
- [x] **`AT-06` y `AT-07`:** Filtros combinados de 4 ejes y separación entre «Sin escaneos previos» y «Sin controles recientes» sin etiquetarlos nunca como estables.
- [x] **`AT-08` y `AT-09`:** Pronóstico futuro experimental marcado como «No evaluado» a falta de modelo P2 validado; validador de importación CSV detecta duplicados y mediciones no comparables sin borrar el original.
- [x] **`AT-10` a `AT-14` y `AT-19`:** Comparador de exposición inclinada $-6^\circ$ HDT por tramos, eje desde fin de exposición deshabilitado cuando el fin es desconocido y línea base orbital independiente de la terrestre.
- [x] **`AT-15` a `AT-18`:** Cortes posvuelo $+30, +60, +90$ días sin interpolar $+60$, visualización de PIO sin OCT y panel de contexto que no altera la salida operativa.

---

## 4. Accesibilidad (WCAG 2.2 AA), UX y Documentación Oficial para Entrega NASA

- [x] **Accesibilidad WCAG 2.2 AA:** Enlace `Skip to main content`, foco de teclado `:focus-visible`, soporte `@media (prefers-reduced-motion: reduce)`, iconos no dependientes únicamente del color y modo claro/oscuro de alto contraste (`#09090b` / `#ffffff`).
- [x] **Documentación de Entrega Lista en el Repositorio:**
  - [x] [`README.md`](../README.md)
  - [x] [`JUDGING.md`](../JUDGING.md) (Matriz Impact, Creativity, Validity, Relevance, Presentation)
  - [x] [`SUBMISSION.md`](../SUBMISSION.md) (Borrador oficial en inglés para el portal de NASA Space Apps)
  - [x] [`SOURCES.md`](../SOURCES.md) (Trazabilidad de las 7 referencias científicas, datasets NASA y declaración de IA)
  - [x] [`DEPLOYMENT.md`](../DEPLOYMENT.md) (Guía de despliegue y verificación de rutas públicas)
  - [x] [`docs/DEMO_SCRIPT.md`](DEMO_SCRIPT.md) (Guion de 3 minutos + Storyboard de 30s / 7 diapositivas)
  - [x] [`docs/SANS_MONITOR_SPEC.md`](SANS_MONITOR_SPEC.md) y [`docs/METHODOLOGY_MASTER_PROMPT.md`](METHODOLOGY_MASTER_PROMPT.md)

---

## 5. ¿Qué falta o qué sigue? (Únicamente Entregables Externos del Equipo Humano / Fase P2 Post-Hackathon)

### A. Acciones de Entrega del Equipo en el Portal de NASA Space Apps (Fuera del código)
- [ ] **Grabar el video pitch de 30 segundos o preparar las 7 diapositivas** siguiendo el guion ya redactado en [`docs/DEMO_SCRIPT.md`](DEMO_SCRIPT.md).
- [ ] **Copiar y pegar los campos de [`SUBMISSION.md`](../SUBMISSION.md)** en la página oficial del equipo en `spaceappschallenge.org` antes del cierre (15 de noviembre, 11:59 p. m.).
- [ ] **Ejecutar `git pull origin main && php artisan migrate --force && php artisan db:seed --class=SansMonitorSeeder --force && php artisan optimize:clear`** en el servidor de producción (`sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com`) si aún no tiene el último commit (`dcf1be6`).

### B. Evolución Científica Futura (Fase P1/P2 Post-Hackathon — Declarada con Honestidad Científica)
- [ ] **Fase P1 (Datos clínicos reales con convenio IRB/OSDR):** Importación de cohortes clínicas autorizadas reales para calibración multi-centro.
- [ ] **Fase P2 (Validación prospectiva externa del pronóstico de edema papilar):** Entrenamiento y validación cruzada externa con curvas de calibración reales antes de habilitar pronósticos clínicos validados.
