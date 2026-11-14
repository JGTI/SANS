# 🪐 SPACE VISION — PROJECT VISION & SCIENTIFIC SCOPE

## 1. Declaración de Misión
**SPACE VISION** es una plataforma de investigación oftalmológica espacial, telemetría y gemelo digital diseñada para monitorear, modelar y analizar la evolución de la salud visual de los astronautas durante misiones espaciales de larga duración (ISS, Artemis, Gateway, Marte).

El núcleo del sistema aborda el **Spaceflight Associated Neuro-ocular Syndrome (SANS)**, caracterizado por:
- Edema del disco óptico (papiledema inducido por microgravedad).
- Aplanamiento posterior del globo ocular (posterior globe flattening).
- Pliegues coroideos y retinianos.
- Desplazamiento hipermetrópico refractivo (hiperopización).
- Manchas algodonosas y alteraciones en agudeza visual.

---

## 2. Parámetros Clínicos Centrales

### A. Refracción Ocular (Refraction)
Registrada por ojo (**OD** - Oculus Dexter / Ojo Derecho; **OS** - Oculus Sinister / Ojo Izquierdo):
- **Esfera (Sphere - SPH):** Dioptrías (+ / -). Mide hipermetropía o miopía. En SANS se observa con frecuencia un corrimiento hipermetrópico progresivo.
- **Cilindro (Cylinder - CYL):** Dioptrías para astigmatismo.
- **Eje (Axis):** Grados (1° a 180°).
- **Equivalente Esférico (Spherical Equivalent - SE):**
  $$\text{SE} = \text{Esfera} + \frac{\text{Cilindro}}{2}$$

### B. Agudeza Visual (Visual Acuity)
- Registrada en notación Snellen (e.g. 20/20, 20/40, 20/100).
- Convertida internamente a escala estándar científica **LogMAR** (*Logarithm of the Minimum Angle of Resolution*):
  $$\text{LogMAR} = \log_{10}\left(\frac{\text{Denominador Snellen}}{\text{Numerador Snellen}}\right)$$
  - 20/20 = LogMAR 0.00
  - 20/40 = LogMAR +0.30
  - 20/200 = LogMAR +1.00

---

## 3. Principio de Rigor Científico y Responsabilidad
1. **No diagnóstico clínico autónomo:** El software no emite diagnósticos médicos vinculantes ni prescribe tratamientos. Proporciona herramientas de telemetría y análisis longitudinal explicable para Directores Médicos de Vuelo (*Flight Surgeons*).
2. **Distinción de datos:** Los datos sintéticos o generados por simulaciones se etiquetan explícitamente como `[SIMULATED]`.
3. **Correlación no implica causalidad:** Las variaciones ambientales (como radiación solar o clima espacial de NASA DONKI) se presentan como contexto operacional de misión, sin afirmar causalidad patológica directa sin respaldo de estudios con revisión por pares.
