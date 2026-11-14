# 🛰️ SPACE VISION — API INTEGRATIONS & NASA DATA REPOSITORIES

## 1. Filosofía de Conectividad y Resiliencia
En demostraciones en vivo de hackatón ante comités de evaluación y jueces, la dependencia exclusiva de servicios de red externos representa un riesgo crítico de latencia o rate-limiting (HTTP 429). SPACE VISION implementa una **arquitectura híbrida con degradación elegante y caché en cascada**:
1. Conexión HTTP asíncrona con timeout corto (3 segundos).
2. Si la API oficial responde satisfactoriamente, almacena en caché en memoria/disco.
3. Si la red externa no responde o está saturada, el sistema activa automáticamente el **respaldo local curado** en `storage/app/nasa_cache/`, garantizando 100% de disponibilidad sin interrupciones.

---

## 2. Repositorios de NASA Integrados

### A. NASA Open Science Data Repository (OSDR / GeneLab / ALSDA)
- **Endpoint oficial:** `https://osdr.nasa.gov/osdr/data/search`
- **Propósito:** Búsqueda e inspección de datasets de transcriptómica, proteómica e histología de tejidos expuestos a microgravedad (retina, nervio óptico, coroides, endotelio ocular).
- **Estudios curados clave:**
  - `OSD-137`: *Microgravity-Induced Ocular Changes: Retinal and Choroidal Vascular Alterations in Rodents* (ISS / RR-1).
  - `OSD-370`: *Multi-Omics Profiling of Ocular and Cephalic Tissues from Spaceflight Missions* (STS-135 / ISS).
  - `OSD-422`: *Optic Nerve Sheath Diameter and Retinal Nerve Fiber Layer (RNFL) Thickness Biomarkers*.
  - `OSD-514`: *Endothelial Response to Simulated Microgravity and Elevated CO2 in Human Retinal Microvessels*.

### B. NASA Technical Reports Server (NTRS)
- **Endpoint oficial:** `https://ntrs.nasa.gov/api/citations/search`
- **Propósito:** Indexación de reportes técnicos de evidencia y publicaciones científicas del NASA Human Research Program (HRP).
- **Citas indexadas clave:**
  - `NTRS #20200002341`: Stenger et al. *Risk of Spaceflight-Associated Neuro-ocular Syndrome (SANS): Human Research Program Evidence Report*.
  - `NTRS #20180004910`: Mader et al. *Optic Disc Edema and Posterior Globe Flattening in Long-Duration Spaceflight Crewmembers*.
  - `NTRS #20220011504`: Macias et al. *Venous Congestion and Cephalad Fluid Redistribution as Key Biomechanical Drivers of SANS*.
  - `NTRS #20240003180`: Hargens et al. *Countermeasure Strategies for Deep Space Exploration: Lower Body Negative Pressure (LBNP)*.

### C. NASA DONKI (Space Weather Database Of Notifications, Knowledge, Information)
- **Endpoint oficial:** `https://api.nasa.gov/DONKI/notifications`
- **Propósito:** Telemetría ambiental orbital (eyecciones de masa coronal CME, llamaradas solares y tormentas geomagnéticas).
- **Rigor ético y científico:** Acompañado obligatoriamente en toda la interfaz por la cláusula de descargo científico:
  > *"Telemetría ambiental de contexto aeroespacial. La radiación ionizante espacial constituye un riesgo biomédico sistémico (cataratas por radiación, daño oxidativo endotelial), pero no se asume una correlación causal directa con el aplanamiento de globo ni con el síndrome SANS sin dosimetría tisular específica."*

### D. NASA APOD (Astronomy Picture of the Day)
- **Endpoint oficial:** `https://api.nasa.gov/planetary/apod`
- **Propósito:** Enriquecimiento visual astronómico del encabezado del observatorio.
