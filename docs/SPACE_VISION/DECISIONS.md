# 🧠 SPACE VISION — ARCHITECTURAL & SCIENTIFIC DECISIONS (ADRs)

## ADR-001: Conservación de Stack Existente (NEXUS Project Factory)
- **Contexto:** El proyecto base cuenta con Laravel 11, PHP 8.3, MariaDB, Spatie Laravel-Permission y un sistema de autenticación probado con Google OAuth.
- **Decisión:** Reutilizar al 100% el stack backend y la estructura de controladores y vistas sin introducir dependencias innecesarias de microservicios ni reinstalar el framework.
- **Consecuencia:** Cero regresiones en autenticación, tiempo de arranque ultra-rápido y máxima estabilidad.

## ADR-002: Modelado Oftalmológico Científicamente Explicable
- **Contexto:** SANS (Spaceflight Associated Neuro-ocular Syndrome) es una afección compleja y multifactorial. Generar un modelo de "caja negra" con IA que pretenda diagnosticar o emitir porcentajes clínicos ficticios carece de validez científica.
- **Decisión:** Implementar un servicio de dominio (`OphthalmologyCalculator`) basado en fórmulas ópticas estándar ($SE = SPH + CYL/2$, LogMAR $= \log_{10}(D/N)$) y una heurística de estratificación explicable (Tier 1 a Tier 3) basada en la literatura médica de NASA (Stenger et al., Mader et al.).
- **Consecuencia:** Transparencia total ante los jueces, pruebas unitarias matemáticas deterministas y ausencia de afirmaciones clínicas no comprobadas.

## ADR-003: Resiliencia de APIs NASA Mediante Fallback Local Curado
- **Contexto:** Las APIs públicas de NASA (OSDR, NTRS, DONKI) pueden presentar latencia de red, tiempo de inactividad o límites de cuota (HTTP 429) durante la evaluación en vivo.
- **Decisión:** Implementar en `NasaDataService` una capa de caché con degradación elegante. Si la llamada remota excede 3 segundos o falla, el sistema conmuta sin fricción a datasets curados en `storage/app/nasa_cache/`.
- **Consecuencia:** Uptime del 100% en la demostración para jueces sin fallos de conectividad.

## ADR-004: Separación Estricta entre Clima Espacial y SANS
- **Contexto:** Aunque la radiación cósmica es un factor de riesgo en misiones espaciales (cataratas por radiación), la etiología primaria documentada de SANS radica en el desplazamiento cefálico de fluidos por microgravedad y congestión venosa retrobulbar.
- **Decisión:** Tratar los datos de NASA DONKI exclusivamente como telemetría ambiental de contexto aeroespacial, incorporando una cláusula explícita de rigor científico que rechaza la correlación causal simplista.
- **Consecuencia:** Responsabilidad y rigor ético-médico alineados con las normas del NASA Space Apps Challenge.
