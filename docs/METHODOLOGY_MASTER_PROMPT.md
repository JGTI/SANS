# 🧭 MASTER PROMPT — SPACE VISION (CORAZÓN METODOLÓGICO DEL CONCURSO)
## NASA International Space Apps Challenge 2026 · NEXUS × Visual Rehab Center

> **Estatus:** Directiva Metodológica Oficial adoptada en el repositorio.  
> **Documento Complementario de Especificación Funcional:** [`docs/SANS_MONITOR_SPEC.md`](SANS_MONITOR_SPEC.md)  
> **Entregables Oficiales Relacionados:** [`../JUDGING.md`](../JUDGING.md), [`../SUBMISSION.md`](../SUBMISSION.md), [`../SOURCES.md`](../SOURCES.md), [`../DEPLOYMENT.md`](../DEPLOYMENT.md), [`DEMO_SCRIPT.md`](DEMO_SCRIPT.md).

---

## 1. Equipo Multidisciplinario y Propósito
SPACE VISION es desarrollado por **NEXUS × Visual Rehab Center** para el **NASA International Space Apps Challenge 2026** (14 y 15 de noviembre de 2026).

Integra criterios de:
- Principal Software Engineer & Senior Full-Stack Architect
- Senior UX/UI Product Designer & Accessibility Engineer (WCAG 2.2 AA)
- Scientific Research & Data Engineer
- Quality Assurance Engineer & Technical Product Manager

**Objetivo:** Entregar una solución tecnológica real, accesible **sin requerir login para la demostración pública**, científicamente defendible, técnicamente verificable y preparada para evaluación por un jurado nacional e internacional.

---

## 2. Guías Oficiales de NASA Space Apps Consideradas
1. **Brand Guidelines:** https://www.spaceappschallenge.org/brand/
   - Leyenda oficial adoptada en la aplicación y documentación:  
     *"Developed by NEXUS × Visual Rehab Center for the NASA International Space Apps Challenge 2026."*
   - SPACE VISION mantiene su propia identidad sin afirmar respaldo, patrocinio ni validación clínica oficial por parte de NASA.
2. **Project Submission Guide:** https://www.spaceappschallenge.org/resources/project-submission-guide/#navigating-the-hackathon-project-submission
3. **Judging & Awards Guide:** https://www.spaceappschallenge.org/resources/judging-awards-guide/
   - Cinco criterios centrales: **Impact, Creativity, Validity, Relevance, Presentation** (documentados punto por punto en [`../JUDGING.md`](../JUDGING.md)).
4. **Portal 2026 & Legal:** https://www.spaceappschallenge.org/2026/ y https://www.spaceappschallenge.org/legal/

---

## 3. Regla Absoluta P0: Demostración Pública Sin Login

Cualquier juez, investigador, mentor o visitante puede abrir la URL pública y ejecutar la demostración principal de **SPACE VISION — SANS Monitor** **sin iniciar sesión, sin registrarse, sin OAuth y sin configurar claves API**:

- **Recorrido directo sin bloqueos:**
  1. Portada pública (`/`) con botón principal **"Explore the Demo · Launch SPACE VISION (No Login Required)"**.
  2. Rutas públicas directas de demostración interactiva:
     - `/demo`
     - `/sans-monitor`
     - `/admin/sans-monitor`
  3. Dentro de la demostración pública, el visitante puede:
     - Nutrir y ejecutar el **Predictor de los 6 Biomarcadores Oculares** (*1. Espesor medio OCT, 2. Longitud axial, 3. Hipermetropía simulada, 4. Edema papilar Frisén, 5. Presión intraocular PIO, 6. Exposición previa a inclinación cefálica de $-6^\circ$ en la Tierra*).
     - Explorar la **Línea de Tiempo Longitudinal: Antes · Durante (Días 10, 15, 30) · Después (+30, +60 y +90 días)**.
     - Alternar entre los **3 Escenarios Modelos** (*1. Estable, 2. Empeora, 3. No se escanea*).
     - Registrar nuevas fichas ficticias y consultar el catálogo curado de referencias científicas de NASA.
- Los módulos administrativos internos (`/admin/users`, `/admin/maintenance`, `/admin/api-keys` y firma de dictamen médico autorizado `RF-11`) conservan su protección de seguridad RBAC sin interferir jamás con el recorrido público del jurado.

---

## 4. Transparencia Científica y Separación de Alcance
1. **Funcionalidad implementada:** Trazabilidad longitudinal por ojo (OD/OS), cálculo oftalmológico determinista ($SE$, LogMAR, vectores de Thibos), catálogo curado de protocolos, comparador de exposiciones inclinadas ($-6^\circ$ HDT) y seguimiento a +30, +60 y +90 días.
2. **Prototipo experimental / Simulación:** Los escenarios pre-cargados (`CASE-STABLE-01`, `CASE-PROG-02`, `CASE-NOSCAN-03A`, `CASE-NORECENT-03B`, `CASE-TILT-04`) utilizan datos sintéticos calibrados conforme a rangos publicados en literatura de NASA y JAMA Ophthalmology, etiquetados explícitamente como ficticios/simulados.
3. **No diagnóstico autónomo:** La aplicación distingue tres estados operativos de revisión (**«Listo para revisión»**, **«Priorizar revisión»**, **«Faltan datos»**) y muestra **«Aptitud no evaluada»** salvo decisión firmada por especialista médico autorizado.
