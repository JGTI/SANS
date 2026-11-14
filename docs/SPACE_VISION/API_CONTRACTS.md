# 🔌 SPACE VISION — API CONTRACTS (VERSION 1)

Prefijo base: `/api/space-vision/v1/`

---

## 1. Misiones Espaciales
- **GET** `/api/space-vision/v1/missions`
- **Respuesta:**
```json
{
  "status": "success",
  "version": "v1",
  "data": [
    {
      "mission": "Artemis III",
      "agency": "NASA",
      "crew_count": 1,
      "type": "Lunar Exploration",
      "environment": "Microgravity / Spaceflight",
      "is_simulated": true
    }
  ]
}
```

---

## 2. Tripulación y Expedientes
- **GET** `/api/space-vision/v1/crew`
- **Parámetros Opcionales:** `code`, `agency`
- **Respuesta:**
```json
{
  "status": "success",
  "count": 3,
  "data": [
    {
      "code": "ASTRO-01",
      "name": "CDR Alex Mercer",
      "agency": "NASA",
      "mission": "Artemis III",
      "days_in_space": 120,
      "baseline_refraction": {
        "od_se": 0.00,
        "os_se": 0.00,
        "od_logmar": 0.000,
        "os_logmar": 0.000
      }
    }
  ]
}
```

---

## 3. Evaluaciones Longitudinales de Refracción
- **GET** `/api/space-vision/v1/ocular-exams`
- **Parámetros Opcionales:** `astronaut_code`, `eye` (OD/OS), `max_day`

---

## 4. Escenarios de Simulación
- **GET** `/api/space-vision/v1/simulator/scenarios`
- **Escenarios Retornados:**
  - `scenario-1-stable`: Control estable normofisiológico.
  - `scenario-2-gradual`: Hiperopía progresiva moderada (+0.75 D).
  - `scenario-3-sans-critical`: Cuadro crítico SANS (+1.75 D, Frisén 2+, pliegues coroideos).

---

## 5. Calculador Oftálmico Espacial
- **POST** `/api/space-vision/v1/calculator/evaluate`
- **Cuerpo (JSON):**
```json
{
  "sphere": 1.50,
  "cylinder": -0.50,
  "axis": 90,
  "snellen_fraction": "20/25",
  "baseline_se": 0.00,
  "frisen_grade": 2,
  "globe_flattening": true,
  "choroidal_folds": true
}
```
- **Respuesta:**
```json
{
  "status": "success",
  "results": {
    "spherical_equivalent": 1.25,
    "logmar": 0.097,
    "hyperopic_shift": 1.25,
    "sans_risk": {
      "tier": "severe",
      "score": 85,
      "factors": [...],
      "recommendation": "..."
    }
  }
}
```

---

## 6. Endpoints de Repositorios NASA
- **GET** `/api/space-vision/v1/nasa/osdr` (parámetro opcional: `query`)
- **GET** `/api/space-vision/v1/nasa/ntrs` (parámetro opcional: `query`)
- **GET** `/api/space-vision/v1/nasa/donki`
