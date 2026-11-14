# 🚀 SPACE VISION
### NASA Space Apps Challenge 2026 · Querétaro, México
**Plataforma de Investigación Médica Espacial, Telemetría Visual y Simulación Biomecánica del Síndrome SANS**

[![Producción en Vivo](https://img.shields.io/badge/Demo%20en%20Vivo-ONLINE-22c55e?style=for-the-badge&logo=googlechrome&logoColor=white)](https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com)
[![Repositorio GitHub](https://img.shields.io/badge/GitHub-JGTI%2FSANS-181717?style=for-the-badge&logo=github&logoColor=white)](https://github.com/JGTI/SANS)

[![PHP Version](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com/)
[![PHPUnit Tests](https://img.shields.io/badge/PHPUnit-67%20Passed%20(287%20assertions)-22c55e?style=flat&logo=php&logoColor=white)]()
[![FastAPI Pytest](https://img.shields.io/badge/Pytest%20FastAPI-21%20Passed-22c55e?style=flat&logo=pytest&logoColor=white)]()
[![Event](https://img.shields.io/badge/NASA%20Space%20Apps-2026-38BDF8?style=flat&logo=nasa&logoColor=white)](https://www.spaceappschallenge.org/)

---

> 🌐 **URL de Producción / Demostración en Vivo:**  
> **[https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com](https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com)**
>
> 📦 **Repositorio Oficial:**  
> **[https://github.com/JGTI/SANS](https://github.com/JGTI/SANS)**

---

## 🌌 1. La Misión

Durante misiones espaciales prolongadas (Estación Espacial Internacional, misiones Artemis lunares y futuros tránsitos tripulados a Marte), la ausencia de gradiente de gravedad terrestre redistribuye aproximadamente **2 litros de fluidos hacia la región cefálica**. 

Este fenómeno desencadena el **Spaceflight Associated Neuro-ocular Syndrome (SANS)**, un conjunto de alteraciones estructurales que incluye:
- **Aplanamiento del polo posterior del globo ocular** (acortamiento del eje axial anteroposterior).
- **Edema de la cabeza del nervio óptico (papiledema)** cuantificado mediante la Escala de Frisén.
- **Pliegues coroideos y estrías retinianas** por congestión venosa.
- **Desplazamiento refractivo hipermetrópico ($\Delta SE$)** que reduce la agudeza visual cercana para la lectura de instrumentación y pantallas de navegación.

**SPACE VISION** es un sistema integral de telemetría biomédica, modelado biomecánico, visualización anatómica interactiva y análisis explicable, alimentado por repositorios y literatura científica de la NASA.

---

## 🛰️ 2. Sistemas y Módulos Implementados

| Módulo | Ruta Web | Descripción y Capacidades |
|---|---|---|
| **Mission Control Dashboard** | `/admin` | Centro de operaciones en tiempo real con indicadores de salud visual de la tripulación, telemetría orbital y curvas longitudinales Chart.js. |
| **Vision Lab** | `/admin/vision-lab` | Laboratorio clínico oftálmico con cálculo asistido en tiempo real de Equivalente Esférico ($SE = SPH + CYL/2$), agudeza Snellen &rarr; LogMAR y presión intraocular. |
| **Astronaut Digital Twin** | `/admin/digital-twin` | Visor anatómico dinámico en SVG que ilustra en corte sagital el aplanamiento de polo posterior y la protrusión papilar a lo largo de los días de misión (FD0 &rarr; FD120). |
| **Mission Simulator** | `/admin/mission-simulator` | Simulador determinista con 3 escenarios de vuelo acelerado (Control Estable, Hiperopía Gradual y SANS Crítico) con semillas reproducibles y avance temporal (FD0 a FD180). |
| **NASA Data Observatory** | `/admin/nasa-data` | Explorador y conector de datasets biológicos en **NASA OSDR** (OSD-137, OSD-370, OSD-422), literatura técnica en **NASA NTRS** y telemetría de radiación en **NASA DONKI** con redundancia offline al 100%. |
| **Research Intelligence** | `/admin/research-intelligence` | Matriz estructurada de hipótesis biomecánicas (gradiente translaminar IOP/ICP, estasis venosa, polimorfismos genéticos) y exportador de citas académicas en **BibTeX / APA**. |
| **Automation Center** | `/admin/automations` | Monitor de procesos en segundo plano (workers/cron), verificación de integridad de datos y botón de sincronización bajo demanda. |
| **Demo Theater (Pitch 3 Min)** | `/admin/demo-theater` | Experiencia interactiva estructurada en 5 pasos para comités evaluadores del hackatón. |
| **Expediente de Vuelo Oficial** | `/admin/demo-theater/report/{code}` | Reporte clínico médico aeroespacial en formato oficial NASA, listo para impresión o descarga en **PDF en alta resolución** (`@media print`). |
| **API RESTful v1** | `/api/space-vision/v1/*` | Endpoints públicos para misiones, tripulación, exámenes refractivos, evaluador matemático y fuentes NASA. |

---

## 🔑 3. Credenciales Maestras de Demostración

La plataforma cuenta con **botones de autorrelleno con un solo clic** en la pantalla de inicio de sesión (`/login`):

> **Contraseña universal para todas las cuentas de prueba:** `SpaceVision2026!`

| Rol de Misión | Correo Electrónico | Alcance de Permisos |
|---|---|---|
| **🩺 Flight Surgeon** | `surgeon@spacevision.nasa` | Evaluaciones clínicas, estratificación SANS y emisión de expedientes médicos de vuelo. |
| **🔬 Researcher** | `researcher@spacevision.nasa` | Acceso completo al observatorio NASA, biblioteca de hipótesis y simulador. |
| **🚀 Super Admin** | `admin@spacevision.nasa` | Director de Vuelo, consola Artisan (`/admin/maintenance`), gestión de usuarios y roles. |
| **🧑‍🚀 Astronaut** | `astronaut@spacevision.nasa` | Consulta de expediente y telemetría visual personal del tripulante. |

---

## 🔬 4. Rigor Científico y Fórmulas Validadas

### Equivalente Esférico (Spherical Equivalent)
$$SE = \text{SPH} + \frac{\text{CYL}}{2}$$

### Agudeza Visual LogMAR
$$\text{LogMAR} = \log_{10}\left(\frac{\text{Denominador}}{\text{Numerador}}\right)$$

### Desplazamiento Hipermetrópico (Hyperopic Shift)
$$\Delta SE = SE_{\text{actual}} - SE_{\text{línea base}}$$

> **Descargo Científico Ético:** SPACE VISION es una plataforma de investigación y entrenamiento. Los perfiles de astronautas sembrados corresponden a cohortes sintéticas (`is_simulated = true`) estructuradas según rangos observados por el *NASA Human Research Program*. La telemetría solar de NASA DONKI se incorpora como parámetro contextual de misión, sin asumir causalidad clínica no demostrada.

---

## 💻 5. Instalación y Puesta en Marcha Local

### Requisitos
- Ubuntu 22.04 / 24.04 LTS o Linux compatible.
- PHP 8.3+ con extensiones: `pdo_mysql`, `mbstring`, `xml`, `curl`, `bcmath`, `intl`.
- MariaDB / MySQL.
- Composer 2.x y Node.js 20+.

### Comandos de Arranque
```bash
# 1. Clonar el repositorio
git clone https://github.com/JGTI/SANS.git
cd SANS

# 2. Instalar dependencias
composer install
npm install && npm run build

# 3. Configurar entorno y base de datos
cp .env.example .env
php artisan key:generate

# 4. Ejecutar migraciones y semillero de datos biomédicos de demostración
php artisan migrate:fresh --seed

# 5. Ejecutar la suite de pruebas automatizadas
php artisan test
```

---

## 🧪 6. Suite de Pruebas Automatizadas

La aplicación cuenta con una cobertura integral de pruebas unitarias y de integración que verifican la integridad matemática, autorización, redundancia de APIs y reportes:

```bash
   PASS  Tests\Unit\ExampleTest
   PASS  Tests\Unit\OphthalmologyCalculatorTest
   PASS  Tests\Feature\AdminManagementTest
   PASS  Tests\Feature\AuthenticationTest
   PASS  Tests\Feature\DemoTheaterTest
   PASS  Tests\Feature\DigitalTwinTest
   PASS  Tests\Feature\ExampleTest
   PASS  Tests\Feature\IntelligenceAndAutomationsTest
   PASS  Tests\Feature\MissionSimulatorTest
   PASS  Tests\Feature\NasaObservatoryTest
   PASS  Tests\Feature\SpaceVisionApiTest
   PASS  Tests\Feature\VisionLabTest

Tests:    45 passed (194 assertions)
Duration: ~2.5s
Status:   100% VERDE
```

---

## 🚀 7. Despliegue en Producción
El repositorio cuenta con integración continua automatizada mediante **GitHub Actions** ([.github/workflows/deploy.yml](.github/workflows/deploy.yml)).
Para actualizar el entorno público en cPanel:
1. Navega a la pestaña **Actions** en el repositorio de GitHub.
2. Selecciona el flujo **Publicar en cPanel**.
3. Haz clic en **Run workflow**.

---

**SPACE VISION — NASA Space Apps Challenge 2026**  
*Desarrollado con rigor científico, ingeniería aeroespacial y pasión por la exploración del cosmos.*
