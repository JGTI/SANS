# 🔬 SPACE VISION — SCIENTIFIC VALIDATION & SANS TAXONOMY

## 1. Definición del Síndrome SANS
El **Spaceflight Associated Neuro-ocular Syndrome (SANS)** es un espectro de cambios estructurales y refractivos documentados en astronautas expuestos a microgravedad durante misiones de larga duración (típicamente >30 días en la Estación Espacial Internacional).

### Hallazgos Frecuentes en Literatura de NASA:
1. **Desplazamiento refractivo hipermetrópico:** Aumento progresivo de la hipermetropía (necesidad de gafas de lectura o aumento de graduación positiva).
2. **Edema de papila (disco óptico):** Grados Frisen 1-3 documentados mediante oftalmoscopia y OCT (*Optical Coherence Tomography*).
3. **Pliegues coroideos y retinianos:** Estrías onduladas en coroides causadas por cambios de presión translaminares.
4. **Aplanamiento posterior del globo ocular:** Reducción de la longitud axial relativa del globo ocular visualizada por ultrasonido y resonancia magnética.

---

## 2. Fórmulas de Conversión Implementadas

### Equivalente Esférico (Spherical Equivalent)
$$SE = SPH + \frac{CYL}{2}$$
*Criterio de alerta temprana:* Una variación $\Delta SE \ge +0.50\text{ D}$ con respecto al valor basal pre-vuelo sugiere un desplazamiento hipermetrópico significativo.

### Conversión Snellen a LogMAR
$$\text{LogMAR} = \log_{10}\left(\frac{\text{Denominador}}{\text{Numerador}}\right)$$

| Snellen | Decimal | LogMAR | Estado Clínico |
|---|---|---|---|
| **20/20** | 1.00 | 0.00 | Visión estándar de vuelo |
| **20/25** | 0.80 | +0.10 | Discreta alteración |
| **20/40** | 0.50 | +0.30 | Límite para tareas operacionales sin corrección |
| **20/100** | 0.20 | +0.70 | Alteración visual moderada |
| **20/200** | 0.10 | +1.00 | Alteración severa |

---

## 3. Límites Éticos y Explicabilidad
- **Explicabilidad algorítmica:** Cada alerta generada por SPACE VISION describe los valores numéricos y la desviación respecto a la línea base del astronauta, sin usar "cajas negras" opacas.
- **Transparencia:** Todos los datos provienen de fuentes oficiales de NASA o de modelos de simulación basados en parámetros documentados por el Human Research Program (HRP).
