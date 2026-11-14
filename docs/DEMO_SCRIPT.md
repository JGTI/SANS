# 🎬 SPACE VISION / SANS MONITOR — GUION DE DEMOSTRACIÓN PARA EL JURADO (3 MINUTOS)

> **Objetivo del Pitch:** Mostrar un sistema limpio, enfocado y de alto impacto visual sin saturar al jurado.  
> **Flujo de 3 Pestañas:** `/admin/sans-monitor`  
> **Credencial de 1 clic en `/login`:** `surgeon@spacevision.nasa` (o `admin@spacevision.nasa`) · Contraseña: `SpaceVision2026!`

---

## Minuto 0:00 – 0:45 · El Problema y la Pestaña 1 (Formulario Inicial + Los 6 Biomarcadores)
- **Ruta:** `/admin/sans-monitor?tab=biomarcadores`
- **Qué decir:**
  > *"En vuelos prolongados hacia la Luna y Marte, la redistribución cefálica de fluidos altera la estructura del ojo (Síndrome SANS). Nuestro objetivo es nutrir un predictor basado en biomarcadores para preparar la vigilancia de cada persona sin emitir dictámenes automáticos de aptitud."*
- **Qué mostrar en pantalla:**
  1. Abrir el **Formulario Inicial** para registrar a la persona (`ID pseudónimo`, fase y duración de misión).
  2. Señalar los **6 Biomarcadores Clave** que alimentan el predictor:
     - **1. Espesor medio por OCT** ($\mu\text{m}$ — basal vs actual).
     - **2. Longitud axial del ojo** ($\text{mm}$).
     - **3. Hipermetropía simulada** ($\Delta SE$ en Dioptrías $\text{D}$).
     - **4. Edema papilar** (Escala de Frisén $0\text{–}5$).
     - **5. Presión intraocular — PIO** ($\text{mmHg}$).
     - **6. Exposición previa a inclinación cefálica de $-6^\circ$ en la Tierra** (Análogo terrestre $-6^\circ$ HDT).
  3. Hacer clic en **"Ejecutar Predictor con estos 6 Biomarcadores"** y mostrar la gráfica de atribución de impacto en vivo y la salida operativa (**«Priorizar revisión»**, **«Listo para revisión»** o **«Faltan datos»**).

---

## Minuto 0:45 – 1:45 · Pestaña 2: Medir el Tiempo — Antes · Durante · Después (+30, +60 y +90 Días)
- **Ruta:** `/admin/sans-monitor?tab=seguimiento`
- **Qué decir:**
  > *"En la segunda pestaña medimos el ojo en una sola línea de tiempo: cómo estaba Antes del vuelo, cómo evoluciona Durante la misión (días 10, 15 y 30) y hacemos un corte exacto Después del vuelo a los +30, +60 y +90 días."*
- **Qué mostrar en pantalla:**
  1. Los 3 bloques comparativos superiores: **Antes del vuelo (Línea base 280 $\mu\text{m}$)** $\to$ **Durante el vuelo ($\Delta\text{TRT} +8 \to +14 \to +26\ \mu\text{m}$)** $\to$ **Exposición previa a $-6^\circ$ en Tierra ($280 \to 304\ [+24\ \mu\text{m}] \to 284\ [+4\ \mu\text{m}]$)**.
  2. Las 3 tarjetas de **Corte Después del Vuelo (+30, +60 y +90 días)** y la gráfica longitudinal Chart.js, destacando el rigor científico: si falta el control de **+60 días**, se marca como **«Sin medición»** y **no se interpola**.

---

## Minuto 1:45 – 3:00 · Pestaña 3: Los 3 Escenarios Modelos + Visor 3D y Consola OCT
- **Ruta:** `/admin/sans-monitor?tab=escenarios` y luego clic en **"Abrir Visor 3D + OCT"** (`/admin/digital-twin?mode=dual`)
- **Qué decir:**
  > *"Finalmente, en la tercera pestaña contrastamos con un solo clic nuestros tres escenarios modelos: uno Estable, uno que Empeora y uno que No se escanea, conectados directamente con nuestro Gemelo Digital 3D y la Consola Tomográfica ZEISS Cirrus HD-OCT."*
- **Qué mostrar en pantalla:**
  1. Hacer clic sucesivamente en las 3 tarjetas:
     - **1. Escenario Estable (`CASE-STABLE-01`)** $\to$ Salida: *Listo para revisión*.
     - **2. Escenario que Empeora (`CASE-PROG-02`)** $\to$ Salida: *Priorizar revisión*.
     - **3. Escenario No se Escanea (`CASE-NOSCAN-03A`)** $\to$ Salida: *Faltan datos* (nunca se asume como estable por falta de OCT).
  2. Cerrar haciendo clic en el botón superior **"Abrir Visor 3D + OCT"** para mostrar en pantalla dividida el globo ocular 3D en WebGL junto a la estación clínica ZEISS Cirrus HD-OCT de 9 paneles.
