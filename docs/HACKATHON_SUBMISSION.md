# 🚀 SPACE VISION — NASA SPACE APPS CHALLENGE 2026 SUBMISSION SHEET

> **Uso:** Utiliza este documento para copiar y pegar directamente la información en el formulario oficial de entrega en la plataforma del **NASA Space Apps Challenge 2026**.

---

## 1. Datos Básicos del Proyecto
- **Título del Proyecto:** SPACE VISION
- **Subtítulo / Tagline:** Plataforma de Telemetría Neurooftalmológica, Gemelo Digital y Simulación de Salud Visual en Microgravedad para el Síndrome SANS.
- **Sede Local:** Querétaro, México.
- **Repositorio de Código Abierto (GitHub):** https://github.com/JGTI/SANS
- **Demostración en Vivo (Producción):** https://sans-visualrehabcenter-spaceappschallenge.nexusdigitaldb.com

---

## 2. Resumen Ejecutivo (Short Description)
Durante los vuelos espaciales prolongados hacia la Luna y Marte, la redistribución cefálica de fluidos eleva la presión venosa retrobulbar, provocando el síndrome **SANS** (*Spaceflight Associated Neuro-ocular Syndrome*): edema de papila, pliegues coroideos y un aplanamiento posterior del globo ocular que induce desplazamientos hipermetrópicos (pérdida de visión cercana). 

**SPACE VISION** combina ingeniería de software biomédica, modelado biomecánico en tiempo real, integración directa con repositorios biológicos de NASA (OSDR) y literatura del *Human Research Program* (NTRS), para permitir a médicos de vuelo e investigadores monitorear la salud visual de la tripulación, simular la evolución en microgravedad y emitir expedientes clínicos de misión en formato oficial NASA.

---

## 3. ¿Cómo Aborda el Desafío?
1. **Laboratorio Oftalmológico Digital (Vision Lab):** Registro riguroso de refracción por ojo (esfera, cilindro, eje), cálculo matemático de Equivalente Esférico ($SE = SPH + CYL/2$), agudeza Snellen convertida a LogMAR y presión intraocular.
2. **Gemelo Digital del Ojo (Astronaut Digital Twin):** Visor sagital interactivo en SVG dinámico que ilustra cómo la cabeza del nervio óptico protruye según la escala de Frisén (Grados 0 a 5) y el polo posterior se aplana a medida que transcurren los días de misión (FD0 a FD120).
3. **Simulador Determinista de Misiones:** Permite evaluar escenarios de vuelo acelerado (Control Estable, Hiperopía Gradual y SANS Crítico) mediante semillas matemáticas reproducibles para probar contramedidas biomecánicas (LBNP / Charly suit).
4. **Estratificación Explicable de Riesgo:** Algoritmo auditable sin "cajas negras" que clasifica la severidad clínica en Tiers 1, 2 o 3 con recomendaciones terapéuticas específicas.
5. **Expediente Médico Oficial Imprimible:** Generador de reportes de vuelo médico aeroespaciales listos para impresión o exportación a PDF de alta resolución.
6. **API RESTful v1 Abierta:** Endpoints versionados bajo `/api/space-vision/v1/` para interoperabilidad con sistemas de aviónica y telemetría de misiones.

---

## 4. Datos y Repositorios de NASA Utilizados
- **NASA Open Science Data Repository (OSDR):**
  - `OSD-137`: *Microgravity-Induced Ocular Changes: Retinal and Choroidal Vascular Alterations in Rodents* (ISS / RR-1).
  - `OSD-370`: *Multi-Omics Profiling of Ocular and Cephalic Tissues from Spaceflight Missions*.
  - `OSD-422`: *Optic Nerve Sheath Diameter and Retinal Nerve Fiber Layer (RNFL) Thickness Biomarkers*.
  - `OSD-514`: *Endothelial Response to Simulated Microgravity and Elevated CO2 in Human Retinal Microvessels*.
- **NASA Technical Reports Server (NTRS):**
  - `NTRS #20200002341`: Stenger et al. *Risk of Spaceflight-Associated Neuro-ocular Syndrome (SANS): Human Research Program Evidence Report*.
  - `NTRS #20180004910`: Mader et al. *Optic Disc Edema and Posterior Globe Flattening in Long-Duration Spaceflight Crewmembers*.
  - `NTRS #20220011504`: Macias et al. *Venous Congestion and Cephalad Fluid Redistribution as Key Biomechanical Drivers of SANS*.
- **NASA DONKI (Space Weather Database):**
  - Telemetría ambiental de radiación solar (CME, Solar Flares, Tormentas Geomagnéticas) contextual para la misión.
- **NASA APOD:** Imagen astronómica diaria para enriquecimiento visual aeroespacial.

---

## 5. Credenciales Maestras para los Jueces
> **Contraseña universal:** `SpaceVision2026!`
- **🩺 Médico de Vuelo (Flight Surgeon):** `surgeon@spacevision.nasa`
- **🔬 Investigador Biomédico (Researcher):** `researcher@spacevision.nasa`
- **🚀 Director de Vuelo (Super Admin):** `admin@spacevision.nasa`
- **🧑‍🚀 Tripulante Simulado (Astronaut):** `astronaut@spacevision.nasa`

*(La pantalla `/login` cuenta además con botones de autorrelleno con un solo clic).*

---

## 6. Stack Tecnológico
- **Backend:** PHP 8.3 / Laravel 11 (Arquitectura modular, controladores desacoplados, servicios de dominio).
- **Base de Datos:** MySQL / MariaDB (Esquema relacional con llaves foráneas, índices de vuelo e integridad referencial).
- **Control de Acceso:** Spatie Laravel-Permission con roles y políticas RBAC.
- **Autenticación:** Híbrida (Credenciales locales cifradas + Google OAuth Socialite).
- **Visualización Frontend:** Blade, CSS Aeroespacial Oscuro, SVG interactivo, Chart.js dinámico, AdminLTE 2 / DataTables.
- **Pruebas Automatizadas:** PHPUnit con 46 tests pasando al 100% (197 aserciones).
- **CI/CD:** GitHub Actions con despliegue automatizado hacia servidor cPanel.

---

## 7. Responsabilidad Ética y Científica
SPACE VISION no realiza diagnósticos clínicos autónomos ni emite predicciones pseudocientíficas. Los perfiles clínicos de tripulantes sembrados se identifican expresamente como cohortes simuladas (`is_simulated = true`) basadas en rangos publicados por NASA HRP. Los datos solares de NASA DONKI se presentan con descargo explícito de no-causalidad médica directa sin dosimetría tisular específica.
