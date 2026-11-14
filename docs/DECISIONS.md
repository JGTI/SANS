# 🏛️ SPACE VISION — ARCHITECTURAL DECISION RECORDS (ADR)

## ADR-001: Conservación de NEXUS Project Factory
- **Fecha:** 10 de noviembre de 2026
- **Contexto:** Existía una base de código previa construida con Laravel 11, AdminLTE y Tailwind.
- **Decisión:** Reutilizar la base existente (autenticación, layouts y estructura) en lugar de rehacer el proyecto desde cero, adaptándola a la identidad visual espacial de **SPACE VISION**.
- **Consecuencias:** Acelera el desarrollo del hackatón y aprovecha componentes maduros y probados.

## ADR-002: Modelo de Roles y Permisos para el Entorno Espacial
- **Fecha:** 10 de noviembre de 2026
- **Contexto:** Se requerían perfiles diferenciados para la demostración ante los jueces.
- **Decisión:** Configurar Spatie Permission con 5 roles especializados: `super-admin`, `admin`, `flight-surgeon`, `researcher` y `astronaut`.
- **Consecuencias:** Permite demostrar control de acceso granular según el rol del usuario en la misión.

## ADR-003: Consola Interactiva de Mantenimiento y Comandos
- **Fecha:** 10 de noviembre de 2026
- **Contexto:** Los administradores necesitan poder aplicar migraciones, seeders y limpiar caché sin tener que acceder por SSH o cPanel en vivo durante la presentación.
- **Decisión:** Crear `/admin/maintenance` con una lista blanca estricta de comandos Artisan pre-aprobados (`migrate`, `db:seed`, `optimize:clear`, `about`, etc.) ejecutados de forma controlada vía AJAX y desplegando la salida en un visor tipo terminal.
- **Seguridad:** Los comandos están limitados a una lista cerrada y protegidos por el middleware `auth` y permisos de `super-admin`.
