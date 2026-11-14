# 📡 SPACE VISION — DATA SOURCES & NASA REPOSITORIES

## 1. NASA Open Science Data Repository (OSDR / GeneLab / ALSDA)
- **Portal Oficial:** https://osdr.nasa.gov/
- **Endpoint API:** `https://osdr.nasa.gov/osdr/data/search`
- **Ámbito:** Datasets ómicos, celulares y tisulares expuestos a microgravedad.
- **Estudios de Referencia Sincronizados:**
  - `OSD-137`: *Microgravity-Induced Ocular Changes: Retinal and Choroidal Vascular Alterations in Rodents* (ISS / RR-1).
  - `OSD-370`: *Multi-Omics Profiling of Ocular and Cephalic Tissues from Spaceflight Missions*.
  - `OSD-422`: *Optic Nerve Sheath Diameter and Retinal Nerve Fiber Layer (RNFL) Thickness Biomarkers*.
  - `OSD-514`: *Endothelial Response to Simulated Microgravity and Elevated CO2 in Human Retinal Microvessels*.

---

## 2. NASA Technical Reports Server (NTRS)
- **Portal Oficial:** https://ntrs.nasa.gov/
- **Endpoint API:** `https://ntrs.nasa.gov/api/citations/search`
- **Ámbito:** Publicaciones científicas, conferencias y reportes de evidencia del *NASA Human Research Program (HRP)*.
- **Citas Indexadas:**
  - `NTRS #20200002341`: Stenger et al. *Risk of Spaceflight-Associated Neuro-ocular Syndrome (SANS): Human Research Program Evidence Report*.
  - `NTRS #20180004910`: Mader et al. *Optic Disc Edema and Posterior Globe Flattening in Long-Duration Spaceflight Crewmembers*.
  - `NTRS #20220011504`: Macias et al. *Venous Congestion and Cephalad Fluid Redistribution as Key Biomechanical Drivers of SANS*.
  - `NTRS #20240003180`: Hargens et al. *Countermeasure Strategies for Deep Space Exploration: Lower Body Negative Pressure (LBNP)*.

---

## 3. NASA DONKI (Space Weather Database)
- **Portal Oficial:** https://ccmc.gsfc.nasa.gov/tools/API/ & https://api.nasa.gov/
- **Endpoint API:** `https://api.nasa.gov/DONKI/notifications`
- **Ámbito:** Clima espacial en órbita terrestre baja y trayectorias translunares (CME, llamaradas solares, tormentas geomagnéticas).
- **Tratamiento Científico:** Datos contextuales de misión aeroespacial. La plataforma especifica en cada vista que no se asume causalidad directa con el síndrome SANS sin dosimetría tisular específica.

---

## 4. Estrategia de Redundancia y Caché Offline
Para blindar las evaluaciones ante jueces contra caídas de red o límites de cuota (HTTP 429):
1. Las llamadas remotas tienen un timeout estricto de 3 segundos.
2. Los resultados exitosos se almacenan en caché (`Cache::remember`).
3. Ante fallos de red o tiempos de espera, el sistema conmuta automáticamente a los archivos curados en `storage/app/nasa_cache/` (`osdr_studies.json`, `ntrs_publications.json`, `donki_alerts.json`, `apod.json`).
