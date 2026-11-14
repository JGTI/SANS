# 📖 SPACE VISION — OPERATIONAL RUNBOOK

## 1. Requisitos del Sistema
- **Sistema Operativo:** Ubuntu 22.04 / 24.04 LTS (x86_64)
- **PHP:** 8.3 (extensiones: `pdo_mysql`, `mbstring`, `xml`, `curl`, `bcmath`, `intl`)
- **Base de Datos:** MySQL / MariaDB (`nexus_spaceappschallenge`)
- **Servidor Web:** Apache / Nginx / `php artisan serve`
- **Node.js:** v20 / v22 (para compilación de activos Vite si se modifica frontend)

---

## 2. Puesta en Marcha Rápida (Entorno Local)

```bash
# 1. Clonar o acceder al directorio del proyecto
cd ~/Documents/dev/code/personal-projects/spaceappschallenge

# 2. Configurar entorno (.env)
# Verificar credenciales de BD y APP_URL

# 3. Ejecutar migraciones y semillero de datos espaciales
php artisan migrate:fresh --seed

# 4. Ejecutar la suite de pruebas automatizadas
php artisan test
```

---

## 3. Acceso a las Interfaces de SPACE VISION
- **Inicio de Sesión:** `http://localhost/login` o `https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com/login`
  - Usuario: `surgeon@spacevision.nasa`
  - Contraseña: `SpaceVision2026!`
- **Mission Control Dashboard:** `/admin`
- **Vision Lab (Laboratorio Oftalmológico):** `/admin/vision-lab`
- **Gemelo Digital (Astronaut Digital Twin):** `/admin/digital-twin`
- **Simulador de Misiones (Mission Simulator):** `/admin/mission-simulator`
- **NASA Data Observatory:** `/admin/nasa-data`
- **Demo Theater (Pitch para Jueces):** `/admin/demo-theater`
- **Expediente de Vuelo Oficial Imprimible:** `/admin/demo-theater/report/ASTRO-01`
- **Consola de Mantenimiento y Artisan:** `/admin/maintenance`

---

## 4. Despliegue a Producción (cPanel / Servidor Remoto)
El repositorio cuenta con integración continua configurada en `.github/workflows/deploy.yml`:
1. Hacer push a `origin/main`:
   ```bash
   git push origin main
   ```
2. En GitHub, acceder a la pestaña **Actions** &rarr; Seleccionar **Publicar en cPanel** &rarr; Hacer clic en **Run workflow**.
3. El pipeline compila los activos y sincroniza el código vía FTP de forma segura y atómica.
