# 🏛️ SPACE VISION / SANS MONITOR — ARCHITECTURAL DECISION RECORDS (ADR)

## ADR-001: Conservación de NEXUS Project Factory
- **Fecha:** 10 de noviembre de 2026
- **Contexto:** Existía una base de código previa construida con Laravel 11, AdminLTE y Tailwind.
- **Decisión:** Reutilizar la base existente (autenticación, layouts y estructura) en lugar de rehacer el proyecto desde cero, adaptándola a la identidad visual espacial de **SPACE VISION**.
- **Consecuencias:** Acelera el desarrollo del hackatón y aprovecha componentes maduros y probados.

## ADR-002: Modelo de Roles y Permisos para el Entorno Espacial y SANS Monitor
- **Fecha:** 10 de noviembre de 2026 (Actualizado 14 de noviembre de 2026)
- **Contexto:** Se requieren perfiles diferenciados para demostración, curación de protocolos, manejo de datos, investigación y revisión médica autorizada.
- **Decisión:** Configurar Spatie Permission con 5 roles especializados mapeados a la especificación **SANS Monitor**:
  - `super-admin` / Presentador de Demo (`admin@spacevision.nasa`)
  - `admin` / Responsable de Datos y Curador de Protocolos (`operator@spacevision.nasa`)
  - `flight-surgeon` / Especialista Médico Autorizado (`surgeon@spacevision.nasa`)
  - `researcher` / Investigador y Analista (`researcher@spacevision.nasa`)
  - `astronaut` / Tripulante Simulado (`astronaut@spacevision.nasa`)
- **Consecuencias:** Control de acceso granular que separa la carga de datos, la curación de fuentes y la revisión clínica (P1), permitiendo modo de demostración ágil en P0.

## ADR-003: Consola Interactiva de Mantenimiento y Comandos
- **Fecha:** 10 de noviembre de 2026
- **Contexto:** Los administradores necesitan aplicar migraciones, seeders y verificar el motor científico sin depender de SSH en vivo durante la presentación.
- **Decisión:** Crear `/admin/maintenance` con una lista blanca estricta de comandos Artisan pre-aprobados (`migrate`, `db:seed`, `scientific:engine status`, `scientific:engine test`, `optimize:clear`, etc.).
- **Seguridad:** Protegido por middleware `auth` y permisos de `super-admin`.

## ADR-004: Separación Estricta de Alcance Clínico (No Dictamen Automático de Aptitud)
- **Fecha:** 14 de noviembre de 2026
- **Contexto:** Adopción de la especificación canónica [`docs/SANS_MONITOR_SPEC.md`](SANS_MONITOR_SPEC.md).
- **Decisión:** El sistema **nunca emite un veredicto automático de aptitud** («apto / no apto»), ni diagnostica SANS, ni prescribe contramedidas. Las salidas operativas del expediente se limitan a **«Listo para revisión»**, **«Faltan datos»** y **«Priorizar revisión»** (RF-10). Si se registra un dictamen de aptitud (RF-11), reside en una entidad separada (`ClinicianDecision`) firmada exclusivamente por el especialista autorizado; en su ausencia se muestra **«Aptitud no evaluada»**.
- **Consecuencias:** Cumplimiento estricto del rigor ético aeroespacial y separación total entre completitud de datos, señales oculares y decisión médica humana.

## ADR-005: Implementación de Referencia P0/P1/P2 sobre Stack Híbrido Laravel 11 + Python FastAPI
- **Fecha:** 14 de noviembre de 2026
- **Contexto:** La especificación [`docs/SANS_MONITOR_SPEC.md`](SANS_MONITOR_SPEC.md) sugiere en su Sección 6 una arquitectura de referencia P0 en Python + Dash + Plotly con separación en `ui/`, `catalog/`, `domain/`, `simulation/`, `analytics/` y `tests/`.
- **Decisión:** Conservar y mapear esa separación modular sobre la infraestructura híbrida de nivel profesional ya operativa en el repositorio:
  - `ui/`: Vistas Blade monocromáticas AdminCN/Shadcn (`#09090b` / `#ffffff`), gráficas Chart.js de alto contraste, gemelo 3D WebGL (Three.js) y consola clínica ZEISS Cirrus HD-OCT.
  - `catalog/`, `domain/`, `simulation/`, `analytics/`: Implementados de forma coordinada entre servicios de dominio en Laravel (`app/Services/`) y el microservicio científico Python 3.14 FastAPI (`scientific_engine/app/` en `:8001`).
  - `tests/`: Suite dual con **67 tests en PHPUnit** y **21 tests en Pytest** (100% en verde).
- **Consecuencias:** Se preservan todas las capacidades avanzadas ya construidas sin romper ninguna funcionalidad existente, alineando el contrato de datos y los flujos de seguimiento (+30, +60, +90 días, exposición inclinada e indicadores oculares) con la especificación oficial de **SANS Monitor**.
