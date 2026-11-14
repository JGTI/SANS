# 🏗️ SPACE VISION — SYSTEM ARCHITECTURE
## Ecosistema Científico Integrado: Laravel 11 + Python FastAPI

```text
               +---------------------------------------------------+
               |             SPACE VISION FRONTEND                 |
               | (Blade, AdminCN / Shadcn Minimalist Black & White)|
               +---------------------------------------------------+
                                         |
                                         v
               +---------------------------------------------------+
               |             NEXUS / LARAVEL 11 (PHP 8.3)          |
               | - Autenticación y Autorización (Spatie RBAC)      |
               | - Orquestación de Misión y Auditoría              |
               | - Persistencia Relacional y Transacciones         |
               | - Artisan CLI (`php artisan scientific:engine`)   |
               +---------------------------------------------------+
                         |                           |
                         | (MySQL / MariaDB)         | (Redis / Queues)
                         v                           v
               +-------------------+       +-----------------------+
               |  Primary Database |       | In-Memory Cache/Queue |
               +-------------------+       +-----------------------+
                         |
                         | HTTP Internal API (Bearer Auth / Port 8001)
                         v
               +---------------------------------------------------+
               |       PYTHON SCIENTIFIC & ML ENGINE (FastAPI)     |
               |                                                   |
               |  +---------------------------------------------+  |
               |  | 1. Computación Científica & Óptica          |  |
               |  |    NumPy, SciPy, Vectores Thibos, LogMAR    |  |
               |  +---------------------------------------------+  |
               |  | 2. Machine Learning Experimental            |  |
               |  |    Scikit-learn, Random Forest, GBM, LOF    |  |
               |  |    Isolation Forest, One-Class SVM          |  |
               |  +---------------------------------------------+  |
               |  | 3. Explainable AI (XAI)                    |  |
               |  |    Pesos SHAP, Descomposición de Factores   |  |
               |  +---------------------------------------------+  |
               |  | 4. Simulación de Gemelos Digitales          |  |
               |  |    Cohortes Sintéticas, Semillas, Esc. A-E  |  |
               |  +---------------------------------------------+  |
               |  | 5. Research Intelligence                    |  |
               |  |    Búsqueda Semántica TF-IDF / RAG NASA     |  |
               |  +---------------------------------------------+  |
               |  | 6. Computer Vision & OCT Preprocessor       |  |
               |  |    Perfil Peripapilar TSNIT RNFL Spectralis |  |
               |  +---------------------------------------------+  |
               +---------------------------------------------------+
```

---

## Contrato de Comunicación y Seguridad

1. **Protocolo:** RESTful JSON sobre HTTP interno (`127.0.0.1:8001` o red privada Docker `http://scientific-engine:8001`).
2. **Autenticación:** Cabecera `Authorization: Bearer <SCIENTIFIC_ENGINE_TOKEN>`.
3. **Resiliencia & Fallback:**
   - Si el microservicio Python está activo, Laravel consume la telemetría calculada por NumPy/SciPy/Scikit-learn.
   - Si el microservicio entra en mantenimiento o está offline, `ScientificEngineService` conmuta automáticamente al motor de cálculo nativo de PHP (`OphthalmologyCalculator`), marcando `engine: 'php_fallback'` sin interrumpir la experiencia de usuario.
