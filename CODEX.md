# CODEX.md

## Proyecto
Nombre: web-nexus-digital-db

Ruta local:
$ProjectPath

Repositorio:
$RepoFullName

Base de datos local:
$DatabaseName

ENV local:
$ProjectPath\.env

## Regla crítica de secretos
- El `.env` local vive en la raíz del proyecto y debe permanecer ignorado por Git.
- No imprimir contraseñas, tokens o secrets en logs, commits o documentación.
- No guardar FTP_PASSWORD ni credenciales en archivos versionados.
- Los secretos de deploy pertenecen a GitHub Actions Secrets.

## Stack obligatorio
- Laravel 12.
- PHP 8.3+.
- MySQL / MariaDB.
- AdminLTE **2** (Nexus Classic). No migrar a AdminLTE 3 salvo instrucción explícita.
- Bootstrap 3 para el layout administrativo clásico.
- DataTables + Yajra.
- SweetAlert2.
- Select2.
- Chart.js.
- Flatpickr.
- Font Awesome.
- Laravel Sanctum.
- Spatie Permission.
- Spatie Activity Log.
- Laravel Excel.
- DomPDF.
- Intervention Image.
- Queues Laravel disponibles. Horizon solo se instala automáticamente en entornos Linux/Unix compatibles con pcntl + posix.
- Debugbar solo para desarrollo.

## Convenciones
- Controllers delgados.
- Form Requests para validaciones.
- Services para lógica de negocio compleja.
- Policies / Spatie Permission para autorización.
- Migraciones para cambios de esquema.
- Eloquent por defecto; SQL directo solo cuando exista una razón técnica.
- Registrar en Activity Log las operaciones administrativas relevantes.
- DataTables para listados administrativos grandes.
- Excel para importaciones/exportaciones.
- DomPDF para documentos PDF.
- No cambiar dependencias mayores sin revisar compatibilidad.

## Storage
Local:
storage/app/public

Local usa php artisan storage:link cuando Windows lo permite.

Producción cPanel:
PUBLIC_STORAGE_PATH puede apuntar a una carpeta física como /home/USUARIO/public_html/storage.

El código de la aplicación debe seguir usando:
Storage::disk('public')

No programar rutas distintas para local y producción si el disk puede resolverlo.

## API
outes/api.php está habilitado y Sanctum está instalado.

## Seguridad HTTP
App\Http\Middleware\SecurityHeaders está activo para rutas web.

No bloquear micrófono globalmente: futuros proyectos Nexus/JARVIS pueden requerir voz.

## Convención cPanel
El proyecto está preparado para hosting donde el **root completo del Laravel se publica en public_html**.

Archivos críticos:
- /index.php = front controller de producción en raíz.
- /.htaccess = enrutamiento + protección de archivos internos.
- /public/index.php se mantiene para compatibilidad Laravel/local.
- No mover assets de /public salvo que el hosting cambie de estrategia.
- No eliminar las reglas que bloquean pp, config, database, outes, storage, endor y archivos secretos.

## SEO
- /robots.txt se genera dinámicamente.
- /sitemap.xml se genera dinámicamente.
- config/seo.php contiene el starter del sitemap.
- Entornos no-production bloquean indexación completa.
- Admin/API/auth/maintenance reciben X-Robots-Tag: noindex.
- Al crear una página pública importante, evaluar canonical, title, description y añadirla al sitemap.
- obots.txt NO es un mecanismo de seguridad.

## Mantenimiento cPanel
Panel:
/api/_nexus/maintenance

El panel:
- Es stateless y se carga por rutas API para funcionar incluso antes de crear tablas de session/cache.
- Está protegido por NEXUS_MAINTENANCE_KEY.
- Requiere NEXUS_MAINTENANCE_ENABLED=true.
- Solo ejecuta una whitelist definida en NexusMaintenanceController.
- Nunca debe modificarse para aceptar comandos Artisan arbitrarios, shell, exec, system o parámetros libres.

## Deploy
Workflow:
.github/workflows/deploy.yml

El deploy a producción es manual mediante workflow_dispatch salvo que el usuario decida cambiarlo.

Secrets esperados:
- FTP_HOST
- FTP_USER
- FTP_PASSWORD

Variables esperadas:
- FTP_PROTOCOL
- FTP_PORT
- CPANEL_DIR

Nunca escribir esos secrets directamente en deploy.yml.

## Antes de modificar
1. Revisar rutas.
2. Revisar modelos y relaciones.
3. Revisar migraciones existentes.
4. Revisar roles/permisos.
5. Revisar CODEX.md.
6. No romper compatibilidad con cPanel compartido.

## Después de modificar
Ejecutar cuando corresponda:

```
php artisan optimize:clear
php artisan test
php artisan route:list
```

Si cambió el esquema:
```
php artisan migrate
```

## Git
No versionar:
- .env
- .env.*
- dumps SQL
- backups
- claves privadas
- tokens
- credenciales
