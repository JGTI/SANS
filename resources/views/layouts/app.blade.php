<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#09090b">
    <title>@yield('title', 'Dashboard') | {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ request()->getBasePath() }}/images/nexus-logo.png">
    <script>
        const savedTheme = localStorage.getItem('nexus-space-theme');
        document.documentElement.dataset.theme = savedTheme === 'dark' ? 'dark' : 'light';
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css">
    <link rel="stylesheet" href="{{ request()->getBasePath() }}/css/space-admin.css?v={{ filemtime(public_path('css/space-admin.css')) }}">
    @stack('styles')
</head>
<body>
@php
    $isEn = app()->getLocale() === 'en';
@endphp
<a href="#main-content" class="skip-link">{{ $isEn ? 'Skip to main content' : 'Saltar al contenido principal' }}</a>
<div class="admin-shell">
    <div class="admin-main">
        <header class="topbar">
            <a href="{{ route('home') }}" class="topbar-brand">
                <img src="{{ request()->getBasePath() }}/images/nexus-logo.png" alt="Nexus" class="nexus-logo-adaptive" style="width:22px;height:22px;object-fit:contain;">
                <span class="topbar-brand-text">
                    <strong>SPACE VISION</strong>
                    <small>SANS MONITOR · ISS-01</small>
                </span>
            </a>

            <nav class="topnav" aria-label="{{ $isEn ? 'Primary Navigation' : 'Navegación Principal' }}">
                <a class="{{ (request()->routeIs('home') || request()->routeIs('public.demo') || request()->routeIs('public.sans-monitor') || request()->routeIs('admin.sans-monitor.*')) && request('tab') !== 'astronauta' ? 'active' : '' }}" href="{{ route('home') }}">
                    <i class="fa fa-flask"></i>
                    <span>{{ $isEn ? 'Scientific Module (SANS)' : 'Módulo Científico (SANS)' }}</span>
                </a>
                <a class="{{ request()->routeIs('public.astronaut') || request('tab') === 'astronauta' ? 'active' : '' }}" href="{{ route('public.astronaut') }}">
                    <i class="fa fa-user-md"></i>
                    <span>{{ $isEn ? 'Astronaut Check' : 'Vista Astronauta' }}</span>
                </a>
                <a class="{{ request()->routeIs('public.digital-twin') || request()->routeIs('admin.digital-twin') || request()->routeIs('public.oct-analysis') || request()->routeIs('admin.oct-analysis') ? 'active' : '' }}" href="{{ route('public.digital-twin', ['mode' => 'dual']) }}">
                    <i class="fa fa-eye"></i>
                    <span>{{ $isEn ? '3D Viewer + OCT' : 'Visor 3D + OCT' }}</span>
                </a>
            </nav>

            <span class="topbar-spacer"></span>

            <div class="topbar-tools">
                <button type="button" id="btn-guided-demo" style="background:#EAFE07;color:#09090b;border:1px solid #09090b;border-radius:999px;padding:0.34rem 0.8rem;font-size:0.74rem;font-weight:800;cursor:pointer;display:inline-flex;align-items:center;gap:5px;white-space:nowrap;">
                    <i class="fa fa-play-circle"></i>
                    <span>{{ $isEn ? 'Guided Demo' : 'Demo Guiado' }}</span>
                </button>

                {{-- SELECTOR MULTILENGUAJE (INGLÉS PRINCIPAL / ESPAÑOL) --}}
                <div class="lang-switcher" role="group" aria-label="Language Selector">
                    <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}" class="{{ $isEn ? 'is-active' : '' }}">
                        <i class="fa fa-globe"></i> EN
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['lang' => 'es']) }}" class="{{ !$isEn ? 'is-active' : '' }}">
                        ES
                    </a>
                </div>

                <button class="icon-btn" id="theme-toggle" type="button" aria-label="{{ $isEn ? 'Toggle theme' : 'Cambiar tema' }}">
                    <i class="fa fa-moon-o" style="font-size:15px;"></i>
                </button>
            </div>
        </header>

        <main class="main-content" id="main-content">
            @yield('content')
        </main>

        <footer class="admin-footer" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
            <span>{{ config('app.name') }} · Nexus × NASA Space Apps Challenge · Developed by NEXUS × Visual Rehab Center for the NASA International Space Apps Challenge 2026.</span>
            <span style="display:inline-flex;align-items:center;gap:10px;">
                <img src="{{ request()->getBasePath() }}/images/nexus-logo.png" alt="Nexus" class="nexus-logo-adaptive" style="height:18px;object-fit:contain;">
                <img src="{{ request()->getBasePath() }}/images/space-apps-logo.png" alt="NASA Space Apps Challenge" style="height:18px;object-fit:contain;">
            </span>
        </footer>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.11/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>

<script>
    (() => {
        const root = document.documentElement;

        document.getElementById('theme-toggle')?.addEventListener('click', () => {
            const next = root.dataset.theme === 'dark' ? 'light' : 'dark';
            root.dataset.theme = next;
            localStorage.setItem('nexus-space-theme', next);
        });

        if (root.lang === 'en') {
            const globalPhrases = {
                'Gemelo Digital Neurooftalmológico': 'Neuro-Ophthalmic 3D Digital Twin',
                'Gemelo Digital 3D': '3D Digital Twin',
                'Corte Sagital Anatómico': 'Anatomical Sagittal Cross-Section',
                'Consola Clínica ZEISS Cirrus': 'ZEISS Cirrus Clinical Console',
                'Recorrido Guiado de Demostración (3 Minutos)': 'Guided Demonstration Pitch (3 Minutes)',
                'Recorrido Guiado de Demostración': 'Guided Demonstration Pitch',
                'Abrir Predictor SANS (3 Pestañas)': 'Open SANS Predictor (3 Tabs)',
                'Ver Reporte Oficial Imprimible': 'View Official Printable Report',
                'Abrir Expediente Oficial para Impresión / PDF': 'Open Official Printable / PDF Dossier',
                'Reporte Médico Aeroespacial Oficial Listo': 'Official Aerospace Medical Dossier Ready',
                'Simulador de Misión': 'Mission Simulator',
                'Observatorio de Datos NASA': 'NASA Data Observatory',
                'Estudios de Tejidos Oculares en Microgravedad': 'Ocular Tissue Studies in Microgravity',
                'Diagnóstico de APIs & Redundancia': 'API Diagnostics & Redundancy',
                'Laboratorio Oftalmológico': 'Ophthalmology Laboratory',
                'Filtrar por Astronauta:': 'Filter by Astronaut:',
                '-- Todos los tripulantes --': '-- All Crew Members --',
                'Limpiar filtro': 'Clear filter',
                'Laboratorio de Modelos': 'Model Laboratory',
                'Exportar Benchmark (JSON)': 'Export Benchmark (JSON)',
                'Cohorte de Astronautas:': 'Astronaut Cohort:',
                'Folds de Validación (K):': 'Validation Folds (K):',
                'Semilla (Seed):': 'Random Seed:',
                'Centro de Explicabilidad': 'Explainability Center',
                'Ajuste Interactivo de Parámetros Clínicos del Astronauta': 'Interactive Astronaut Clinical Parameter Tuning',
                'Desplazamiento Hipermetrópico (D):': 'Hyperopic Shift (D):',
                'Calidad de Datos': 'Data Quality Observatory',
                'Registro de Modelos': 'Model Registry',
                'Ojo Derecho': 'Right Eye',
                'Ojo Izquierdo': 'Left Eye',
                'Línea Base': 'Baseline',
                'En Vuelo': 'In-Flight',
                'Posvuelo': 'Post-Flight',
                'Exportar CSV': 'Export CSV',
                'Imprimir Expediente': 'Print Dossier',
                'Siguiente': 'Next',
                'Anterior': 'Previous',
                'Buscar': 'Search'
            };
            const walker = document.createTreeWalker(document.getElementById('main-content'), NodeFilter.SHOW_TEXT);
            let node;
            while ((node = walker.nextNode())) {
                let text = node.nodeValue;
                if (!text || !text.trim()) continue;
                for (const [es, en] of Object.entries(globalPhrases)) {
                    if (text.includes(es)) {
                        text = text.replaceAll(es, en);
                    }
                }
                node.nodeValue = text;
            }
        }
    })();
</script>
<style>
    .sv-demo-spotlight {
        outline: 3px solid #EAFE07 !important;
        outline-offset: 4px !important;
        box-shadow: 0 0 0 6px rgba(234, 254, 7, 0.22), 0 18px 45px rgba(0, 0, 0, 0.35) !important;
        transition: outline 0.25s ease, box-shadow 0.25s ease;
        border-radius: 10px;
    }
    #sv-demo-player {
        position: fixed;
        bottom: 16px;
        left: 50%;
        transform: translateX(-50%);
        width: min(960px, calc(100vw - 24px));
        background: rgba(9, 9, 11, 0.96);
        color: #ffffff;
        border: 2px solid #EAFE07;
        border-radius: 16px;
        padding: 1.05rem 1.35rem 0.9rem;
        box-shadow: 0 24px 70px rgba(0, 0, 0, 0.72);
        z-index: 9998;
        display: none;
        backdrop-filter: blur(8px);
    }
    #sv-demo-player .sv-demo-progress-track {
        height: 3px;
        background: rgba(255, 255, 255, 0.14);
        border-radius: 999px;
        overflow: hidden;
        margin-top: 0.65rem;
    }
    #sv-demo-player .sv-demo-progress-fill {
        height: 100%;
        width: 0%;
        background: #EAFE07;
        transition: width 0.2s linear;
    }
</style>

{{-- Modal inicial grande para activar el Demo Automático --}}
<div id="sv-demo-welcome-modal" role="dialog" aria-modal="true" style="display:none;position:fixed;inset:0;z-index:10000;background:rgba(5,5,7,0.84);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem;">
    <div style="width:100%;max-width:560px;background:#09090b;color:#ffffff;border:2px solid #EAFE07;border-radius:18px;padding:1.75rem 1.85rem;box-shadow:0 28px 80px rgba(0,0,0,0.75);text-align:center;">
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(234,254,7,0.14);color:#EAFE07;border:1px solid rgba(234,254,7,0.4);padding:0.28rem 0.85rem;border-radius:999px;font-family:ui-monospace,monospace;font-size:0.75rem;font-weight:800;margin-bottom:0.85rem;">
            <i class="fa fa-rocket"></i> SPACE VISION · SANS MONITOR
        </div>
        <h2 style="font-size:1.5rem;font-weight:900;margin:0 0 0.6rem;color:#ffffff;">
            {{ $isEn ? 'Interactive Automatic Demo' : 'Demo Automático Interactivo' }}
        </h2>
        <p style="font-size:0.98rem;line-height:1.5;color:#d4d4d8;margin:0 0 1.35rem;">
            {{ $isEn
                ? 'Sit back and watch the full 16-step automated tour with large live subtitles across Python CV, 6 Biomarkers, Scikit-learn Candidate Predictor, Astronaut View, and 3D Digital Twin + SD-OCT.'
                : 'Recorre automáticamente toda la plataforma en 16 pasos con subtítulos grandes en vivo: Escáner Python CV, 6 Biomarcadores, Predictor Scikit-learn, Vista Astronauta y Gemelo Digital 3D + SD-OCT.' }}
        </p>
        <div style="display:flex;flex-direction:column;gap:0.65rem;align-items:center;">
            <button type="button" id="sv-welcome-start-btn" style="width:100%;background:#EAFE07;color:#09090b;border:none;border-radius:10px;padding:0.95rem 1.5rem;font-size:1.08rem;font-weight:900;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:10px;box-shadow:0 10px 30px rgba(234,254,7,0.28);">
                <i class="fa fa-play-circle" style="font-size:1.25rem;"></i>
                <span>{{ $isEn ? 'ACTIVATE AUTOMATIC DEMO' : 'ACTIVAR DEMO AUTOMÁTICO' }}</span>
            </button>
            <button type="button" id="sv-welcome-skip-btn" style="background:transparent;color:#a1a1aa;border:1px solid rgba(255,255,255,0.18);border-radius:8px;padding:0.5rem 1.1rem;font-size:0.84rem;font-weight:700;cursor:pointer;">
                {{ $isEn ? 'Explore on my own' : 'Explorar por mi cuenta' }}
            </button>
        </div>
    </div>
</div>

<div id="sv-demo-player" role="region" aria-live="polite" aria-label="{{ $isEn ? 'Guided Demo Subtitles' : 'Subtítulos del Demo Guiado' }}">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:0.65rem;flex-wrap:wrap;margin-bottom:0.45rem;">
        <div style="display:flex;align-items:center;gap:0.6rem;flex-wrap:wrap;">
            <span id="sv-demo-badge" style="background:#EAFE07;color:#09090b;font-family:ui-monospace,monospace;font-size:0.78rem;font-weight:800;padding:0.22rem 0.65rem;border-radius:999px;">PASO 1 / 16</span>
            <strong id="sv-demo-title" style="font-size:1.08rem;font-weight:800;color:#ffffff;"></strong>
        </div>
        <div style="display:flex;align-items:center;gap:0.4rem;">
            <label for="sv-demo-speed" style="font-size:0.72rem;color:#a1a1aa;font-weight:700;margin:0;">{{ $isEn ? 'Speed' : 'Vel.' }}</label>
            <select id="sv-demo-speed" aria-label="{{ $isEn ? 'Demo Speed' : 'Velocidad del Demo' }}" style="background:rgba(255,255,255,0.12);color:#EAFE07;border:1px solid rgba(255,255,255,0.22);border-radius:7px;padding:0.26rem 0.45rem;font-size:0.76rem;font-weight:800;cursor:pointer;">
                <option value="0.5" style="color:#09090b;">0.5x</option>
                <option value="1" selected style="color:#09090b;">1x</option>
                <option value="1.5" style="color:#09090b;">1.5x</option>
                <option value="2" style="color:#09090b;">2x</option>
                <option value="3" style="color:#09090b;">3x</option>
            </select>
            <button type="button" id="sv-demo-prev" title="{{ $isEn ? 'Previous' : 'Anterior' }}" style="background:rgba(255,255,255,0.12);color:#fff;border:1px solid rgba(255,255,255,0.2);border-radius:7px;padding:0.32rem 0.65rem;font-size:0.78rem;font-weight:700;cursor:pointer;"><i class="fa fa-step-backward"></i></button>
            <button type="button" id="sv-demo-pause" title="{{ $isEn ? 'Pause / Resume' : 'Pausar / Reanudar' }}" style="background:#EAFE07;color:#09090b;border:1px solid #EAFE07;border-radius:7px;padding:0.32rem 0.75rem;font-size:0.78rem;font-weight:800;cursor:pointer;"><i class="fa fa-pause"></i></button>
            <button type="button" id="sv-demo-next" title="{{ $isEn ? 'Next' : 'Siguiente' }}" style="background:rgba(255,255,255,0.12);color:#fff;border:1px solid rgba(255,255,255,0.2);border-radius:7px;padding:0.32rem 0.65rem;font-size:0.78rem;font-weight:700;cursor:pointer;"><i class="fa fa-step-forward"></i></button>
            <button type="button" id="sv-demo-stop" title="{{ $isEn ? 'Close Demo' : 'Cerrar Demo' }}" style="background:rgba(239,68,68,0.22);color:#fca5a5;border:1px solid rgba(239,68,68,0.4);border-radius:7px;padding:0.32rem 0.65rem;font-size:0.78rem;font-weight:700;cursor:pointer;"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div id="sv-demo-subtitle" style="font-size:1.02rem;line-height:1.52;color:#f4f4f5;"></div>
    <div class="sv-demo-progress-track"><div id="sv-demo-progress" class="sv-demo-progress-fill"></div></div>
</div>

<script>
(function () {
    const isEn = @json($isEn);
    const routes = {
        home: @json(route('home')),
        astronaut: @json(route('public.astronaut')),
        twin: @json(route('public.digital-twin', ['mode' => 'dual'])),
    };

    const steps = [
        {
            page: 'home',
            selector: '#sans-monitor-workspace',
            duration: 7500,
            titleEn: '1. Mission Overview, Bilingual EN/ES & Crew Roster (Zero-Login)',
            titleEs: '1. Visión de Misión, Bilingüe EN/ES y Tripulación (Acceso Directo)',
            textEn: 'Welcome to SPACE VISION · SANS Monitor (NEXUS × Visual Rehab Center). No login required, instant EN|ES switching and Day/Night mode. Inspect 6 longitudinal astronaut dossiers (CDR Alex Mercer, Dr. Lindqvist, Kenji Sato, etc.).',
            textEs: 'Bienvenido a SPACE VISION · SANS Monitor (NEXUS × Visual Rehab Center). Acceso público sin login, cambio EN|ES y modo Día/Noche. Aquí seleccionas entre 6 expedientes longitudinales de astronautas (CDR Alex Mercer, Dra. Lindqvist, Kenji Sato).',
        },
        {
            page: 'home',
            selector: '[data-wizard-tab="ingreso"]',
            duration: 7500,
            titleEn: '2. Independent OD/OS Triage & 3-Stage Clinical Workflow',
            titleEs: '2. Triaje Independiente OD/OS y Flujo Clínico en 3 Fases',
            textEn: 'Right Eye (OD) and Left Eye (OS) are evaluated independently into Ready for review, Prioritize review, or Missing data—never issuing an automated fit-for-duty verdict. The 3 tabs organize Intake, Time Cuts, and Reference Scenarios.',
            textEs: 'Ojo Derecho (OD) y Ojo Izquierdo (OS) se evalúan por separado en Listo para revisión, Priorizar revisión o Faltan datos, sin emitir veredictos automáticos de aptitud. Las 3 pestañas guían: Ingreso, Cortes en el Tiempo y Escenarios.',
        },
        {
            page: 'home',
            selector: '#btnRunFundusScanner',
            duration: 7500,
            titleEn: '3. Ground Fundus Scanner (Python CV)',
            titleEs: '3. Escáner de Fondo de Ojo (Python CV)',
            textEn: 'Running real Computer Vision in Python (Pillow + NumPy + SciPy): computes Retinal FOV mask, Optic Disc localization, Sobel rim sharpness, peripapillary halo, and choroidal folds.',
            textEs: 'Ejecutando Visión por Computadora real en Python (Pillow + NumPy + SciPy): calcula máscara retiniana, disco óptico, nitidez Sobel del borde, halo peripapilar y pliegues coroideos.',
            action: () => {
                const btn = document.getElementById('btnRunFundusScanner');
                if (btn) btn.click();
            }
        },
        {
            page: 'home',
            selector: '#cvCameraBox > div',
            duration: 5500,
            titleEn: '4. Live Camera App Modal (Reticle, Flip Camera & Flash)',
            titleEs: '4. App de Cámara en Vivo (Retícula, Voltear Cámara y Flash)',
            textEn: 'Opening the built-in Camera App modal automatically: includes an optic disc alignment reticle, Flip Camera for mobile devices, Flash toggle, and one-tap Capture & Scan in Python.',
            textEs: 'Abriendo automáticamente la App de Cámara integrada: incluye retícula circular de alineación ocular, Voltear Cámara para celulares, Flash y disparo directo hacia Python CV.',
            action: () => {
                const openCam = document.getElementById('btnOpenCvCamera');
                if (openCam) openCam.click();
            },
            cleanup: () => {
                const closeCam = document.getElementById('btnCloseCvCamera');
                if (closeCam) closeCam.click();
            }
        },
        {
            page: 'home',
            selector: '#biomarkerImpactChart',
            duration: 7500,
            titleEn: '4. The 6 Core Ocular Biomarkers & Signal Index',
            titleEs: '4. Los 6 Biomarcadores Oculares Clave e Índice de Señal',
            textEn: 'Interactive inputs for OCT Total Retinal Thickness (TRT), Axial Length, Hyperopic Shift, Frisén Papilledema Grade, IOP, and -6° HDT analog exposure. The bar chart quantifies each biomarker’s weight in the Signal Index.',
            textEs: 'Campos editables para Espesor Retiniano OCT (TRT), Longitud Axial, Cambio Hipermetrópico, Grado Frisén, PIO y exposición análoga -6° HDT. La gráfica muestra el peso exacto de cada biomarcador en el Índice de Señal.',
        },
        {
            page: 'home',
            selector: '#candidate-predict-form',
            duration: 8500,
            titleEn: '5. Future Mission Candidate Predictor (Python Scikit-learn)',
            titleEs: '5. Simulador Predictivo para Futuros Candidatos (Python Scikit-learn)',
            textEn: 'Executing the Python Scikit-learn model across 4 domains—Biometric, Ophthalmological, Neurological, and Metabolic (1-Carbon / Vit B / CO₂)—to estimate SANS susceptibility probability and recommended monitoring cadence for future astronauts.',
            textEs: 'Ejecutando el modelo Scikit-learn en Python sobre 4 perfiles —Biométrico, Oftalmológico, Neurológico y Metabólico (1-Carbono / Vit B / CO₂)— para estimar la susceptibilidad al SANS y la cadencia de monitoreo en futuros candidatos.',
            action: () => {
                const btn = document.getElementById('candidate-predict-btn');
                if (btn) btn.click();
            }
        },
        {
            page: 'home',
            selector: 'details.collapsible-card',
            duration: 6500,
            titleEn: '6. New Subject / Dossier Registration Form',
            titleEs: '6. Registro de Nueva Persona / Expediente Clínico',
            textEn: 'Flight surgeons can register new subjects or mission dossiers at any time with mission phase, planned duration, prior flights, and initial review notes, immediately feeding the longitudinal monitor.',
            textEs: 'Permite registrar nuevas personas o expedientes en cualquier fase (Pre-vuelo, En vuelo, Posvuelo), duración planeada, vuelos previos y motivo inicial para integrarlos de inmediato al monitor.',
            action: () => {
                const det = document.querySelector('details.collapsible-card');
                if (det) det.open = true;
            }
        },
        {
            page: 'home',
            selector: '#wizard-panel-seguimiento',
            duration: 8000,
            titleEn: '7. Longitudinal Cuts: Before · During · +30 / +60 / +90 Days',
            titleEs: '7. Cortes en el Tiempo: Antes · Durante · +30 / +60 / +90 Días',
            textEn: 'Switching to Longitudinal Cuts: compares pre-flight baseline, in-flight shifts, -6° HDT ground analogs, and post-flight recovery milestones at +30, +60, and +90 days with interactive historical playback.',
            textEs: 'Abriendo Cortes en el Tiempo: compara línea base pre-vuelo, evolución en vuelo, análogos -6° HDT y recuperación posvuelo a +30, +60 y +90 días con reproducción histórica por día.',
            action: () => {
                const tab = document.querySelector('[data-wizard-tab="seguimiento"]');
                if (tab) tab.click();
            }
        },
        {
            page: 'home',
            selector: '#wizard-panel-escenarios',
            duration: 8000,
            titleEn: '8. Canonical Scenarios: Stable · Worsens · Unscanned',
            titleEs: '8. Escenarios Modelos: Estable · Empeora · No se Escanea',
            textEn: 'Comparing the 3 reference archetypes: Stable, Worsens (active progression), and Unscanned—strictly enforcing that missing OCT is never assumed stable even if intraocular pressure (IOP) looks normal.',
            textEs: 'Comparando los 3 escenarios canónicos: Estable, Empeora y No se Escanea, garantizando que la ausencia de OCT nunca se asuma como estable aunque la presión intraocular (PIO) sea normal.',
            action: () => {
                const tab = document.querySelector('[data-wizard-tab="escenarios"]');
                if (tab) tab.click();
            }
        },
        {
            page: 'home',
            selector: '#wizard-panel-poblacion',
            duration: 8500,
            titleEn: '9. Integrated Population Dashboard & Predictive Conclusion',
            titleEs: '9. Dashboard Integral de Poblaciones y Conclusión Predictiva',
            textEn: 'Synthesizing all 3 populations (Orbital Astronauts, Terrestrial Controls 0°, and -6° HDT Analog): confirms ΔTRT OCT + -6° HDT + short axial length show the highest predictive value, whereas IOP alone is not predictive.',
            textEs: 'Sintetiza las 3 poblaciones (Astronautas, Terrestres 0° y Terrestres -6° HDT): concluye que ΔTRT OCT + respuesta -6° HDT + longitud axial corta poseen el mayor valor predictivo, mientras la PIO sola no es predictiva.',
            action: () => {
                const tab = document.querySelector('[data-wizard-tab="poblacion"]');
                if (tab) tab.click();
            }
        },
        {
            page: 'home',
            selector: '#nasa-live-search-form',
            duration: 7500,
            titleEn: '9. NASA OCHMO Protocols & Live OSDR / NTRS / DONKI / ISS APIs',
            titleEs: '9. Protocolos NASA OCHMO y APIs en Vivo OSDR / NTRS / DONKI / ISS',
            textEn: 'Retrieves applicable NASA OCHMO clinical protocols (preserving prior versions alongside newer verified ones), 7 scientific references, and live NASA OSDR, NTRS, DONKI, and ISS telemetry.',
            textEs: 'Consulta protocolos clínicos NASA OCHMO (sin sobrescribir versiones previas), 7 referencias científicas verificadas y búsqueda en vivo contra NASA OSDR, NTRS, DONKI e ISS.',
            action: () => {
                const tab = document.querySelector('[data-wizard-tab="ingreso"]');
                if (tab) tab.click();
            }
        },
        {
            page: 'astronaut',
            selector: 'main#main-content',
            duration: 7500,
            titleEn: '10. Astronaut View: Live ISS Telemetry & Daily Orbital Check',
            titleEs: '10. Vista Astronauta: Telemetría ISS en Vivo y Chequeo Orbital',
            textEn: 'Inside Astronaut Check, crew members view live ISS altitude, velocity, 0.00 G microgravity, DONKI space-weather alerts, and the 3D cephalic fluid-shift monitor before starting their 8-minute eye exam.',
            textEs: 'En la Vista Astronauta, la tripulación ve altitud y velocidad de la ISS, microgravedad 0.00 G, alertas DONKI y el monitor 3D de desplazamiento cefálico antes de iniciar su chequeo ocular de 8 minutos.',
        },
        {
            page: 'astronaut',
            selector: '[data-step="0"]',
            duration: 7500,
            titleEn: '11. How the Astronaut Feels Today ("¿Cómo te sientes?")',
            titleEs: '11. Estado Diario del Astronauta: "¿Cómo te sientes hoy?"',
            textEn: 'Opening today’s guided check: automatically selects symptoms (e.g. mild blurred near vision) so Ground Control correlates subjective symptoms with objective biomarkers.',
            textEs: 'Iniciando la revisión de hoy: el asistente selecciona síntomas (ej. visión borrosa cercana) para que Control en Tierra correlacione cómo se siente el astronauta con sus biomarcadores.',
            action: () => {
                const startBtn = document.querySelector('[data-start-check]');
                if (startBtn) startBtn.click();
                setTimeout(() => {
                    const sym = document.querySelector('input[name="symptoms"]:not([value="none"])') || document.querySelector('input[name="symptoms"]');
                    if (sym && !sym.checked) {
                        sym.checked = true;
                        sym.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }, 250);
            }
        },
        {
            page: 'astronaut',
            selector: '[data-step="1"]',
            duration: 8000,
            titleEn: '12. 6-Step Visual Exam: Acuity, Amsler Grid & Contrast Sensitivity',
            titleEs: '12. Examen Visual: Agudeza, Rejilla de Amsler y Contraste',
            textEn: 'Advancing automatically to Step 2 of the wizard: completes the 6 interactive visual checks (Tumbling-E acuity, Amsler grid distortion, and contrast sensitivity) in orbit.',
            textEs: 'Avanzando automáticamente al Paso 2 del wizard: registra las 6 pruebas visuales interactivas (Agudeza Visual, Rejilla de Amsler y Sensibilidad al Contraste) en órbita.',
            action: () => {
                const startBtn = document.querySelector('[data-start-check]');
                const flow = document.querySelector('[data-check-flow]');
                if (startBtn && flow && flow.hidden) startBtn.click();
                const sym = document.querySelector('input[name="symptoms"]');
                if (sym && !document.querySelector('input[name="symptoms"]:checked')) {
                    sym.checked = true;
                    sym.dispatchEvent(new Event('change', { bubbles: true }));
                }
                const nextBtn = document.querySelector('[data-next-step]');
                if (nextBtn) nextBtn.click();
                setTimeout(() => {
                    document.querySelectorAll('[data-test-answer]').forEach(sel => {
                        if (!sel.value && sel.options.length > 1) {
                            sel.selectedIndex = 1;
                            sel.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    });
                }, 300);
            }
        },
        {
            page: 'astronaut',
            selector: '[data-fundus-panel]',
            duration: 8500,
            titleEn: '13. Bring the Eye Closer: Orbital Fundus Camera & Tonometer Downlink',
            titleEs: '13. Acerca el Ojo: Cámara Orbital de Fondo de Ojo OD/OS y Tonómetro',
            textEn: 'Advancing to Step 3: synchronizes the orbital tonometer & ocular telemetry devices, lets the astronaut bring their eye close to the fundus lens, and transmits OD/OS captures to Ground Control.',
            textEs: 'Avanzando al Paso 3: sincroniza el tonómetro y dispositivos orbitales, permite acercar el ojo al equipo de fondo de ojo OD/OS y transmite las capturas directo al Escáner Python CV en Tierra.',
            action: () => {
                document.querySelectorAll('[data-test-answer]').forEach(sel => {
                    if (!sel.value && sel.options.length > 1) {
                        sel.selectedIndex = 1;
                        sel.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
                const nextBtn = document.querySelector('[data-next-step]');
                if (nextBtn) nextBtn.click();
                setTimeout(() => {
                    document.querySelectorAll('[data-capture-device]').forEach(btn => btn.click());
                }, 350);
            }
        },
        {
            page: 'astronaut',
            selector: '[data-step="3"]',
            duration: 7500,
            titleEn: '14. Physiological Context (Sleep, Hydration, Exercise & Posture)',
            titleEs: '14. Contexto Fisiológico (Sueño, Hidratación, Ejercicio y Postura)',
            textEn: 'Advancing to Step 4 of the wizard: records sleep hours, hydration, recent exercise, and measurement posture before sealing and transmitting the check to Ground Control.',
            textEs: 'Avanzando al Paso 4 del wizard: registra horas de sueño, hidratación, ejercicio reciente y postura de medición antes de sellar y enviar la revisión a Control en Tierra.',
            action: () => {
                document.querySelectorAll('[data-device]').forEach(dev => dev.classList.add('is-complete'));
                const nextBtn = document.querySelector('[data-next-step]');
                if (nextBtn) nextBtn.click();
            }
        },
        {
            page: 'astronaut',
            selector: '[data-crew-tab="log"]',
            duration: 7500,
            titleEn: '15. Flight Logbook, Longitudinal Trends & Read-Only Pre-Flight Profile',
            titleEs: '15. Bitácora Espacial, Tendencias y Perfil Basal Protegido',
            textEn: 'Closing the wizard into the Crew Tabs: Astronauts review their Flight Logbook, symptom/IOP Trends, and their read-only pre-flight baseline profile.',
            textEs: 'Cerrando el wizard hacia las pestañas de tripulación: el astronauta consulta su Bitácora de Vuelo, Tendencias y su Perfil Basal Pre-vuelo protegido como solo lectura.',
            action: () => {
                const logTab = document.querySelector('[data-crew-tab="log"]');
                if (logTab) logTab.click();
            }
        },
        {
            page: 'twin',
            selector: '#panel-3d-layout',
            duration: 8500,
            titleEn: '15. Interactive 3D WebGL Eye Digital Twin (Posterior Flattening & Folds)',
            titleEs: '15. Gemelo Digital Ocular 3D WebGL (Aplanamiento y Pliegues)',
            textEn: 'In the 3D Viewer + OCT module, inspect the interactive 3D WebGL ocular globe showing posterior globe flattening, optic disc protrusion, and choroidal folds—complete with orbital presets and real-time deformation.',
            textEs: 'En el Visor 3D + OCT interactúas con el Gemelo Digital Ocular en WebGL 3D: muestra aplanamiento posterior del globo, protrusión del disco óptico y pliegues coroideos con vistas predefinidas y deformación en vivo.',
        },
        {
            page: 'twin',
            selector: '#panel-cirrus-oct',
            duration: 9000,
            titleEn: '16. Cirrus SD-OCT Workstation & ETDRS 9-Sector Map (Final Summary)',
            titleEs: '16. Estación Cirrus SD-OCT y Mapa ETDRS de 9 Sectores (Cierre)',
            textEn: 'Below is the synchronized SD-OCT B-Scan workstation for OD & OS, ETDRS 9-sector retinal thickness maps, Frisén grade, and choroidal fold status. End-to-end SPACE VISION automatic tour complete!',
            textEs: 'Abajo se muestra la estación SD-OCT B-Scan sincronizada para OD y OS, mapas ETDRS de 9 sectores, grado Frisén y estado de pliegues coroideos. ¡Recorrido automático completo de SPACE VISION finalizado!',
            action: () => {
                const etdrs = document.getElementById('etdrsSvgOd');
                if (etdrs) setTimeout(() => etdrs.scrollIntoView({ behavior: 'smooth', block: 'center' }), 3200);
            }
        }
    ];

    const player = document.getElementById('sv-demo-player');
    const badgeEl = document.getElementById('sv-demo-badge');
    const titleEl = document.getElementById('sv-demo-title');
    const subEl = document.getElementById('sv-demo-subtitle');
    const progEl = document.getElementById('sv-demo-progress');
    const pauseBtn = document.getElementById('sv-demo-pause');
    const launchBtn = document.getElementById('btn-guided-demo');

    let timer = null;
    let tickTimer = null;
    let currentSpotlight = null;
    let lastCleanup = null;
    let paused = false;
    let elapsed = 0;

    function currentPageKey() {
        const p = window.location.pathname;
        if (p.includes('astronaut-check')) return 'astronaut';
        if (p.includes('digital-twin') || p.includes('oct-analysis')) return 'twin';
        return 'home';
    }

    function clearSpotlight() {
        if (currentSpotlight) {
            currentSpotlight.classList.remove('sv-demo-spotlight');
            currentSpotlight = null;
        }
    }

    function stopDemo(dismiss = true) {
        clearTimeout(timer);
        clearInterval(tickTimer);
        clearSpotlight();
        if (typeof lastCleanup === 'function') {
            try { lastCleanup(); } catch (_) {}
            lastCleanup = null;
        }
        sessionStorage.removeItem('sv_demo_active');
        sessionStorage.removeItem('sv_demo_step');
        if (dismiss) sessionStorage.setItem('sv_demo_v2_dismissed', '1');
        if (player) player.style.display = 'none';
    }

    function runStep(idx) {
        clearTimeout(timer);
        clearInterval(tickTimer);
        clearSpotlight();
        if (typeof lastCleanup === 'function') {
            try { lastCleanup(); } catch (_) {}
            lastCleanup = null;
        }

        if (idx < 0) idx = 0;
        if (idx >= steps.length) {
            stopDemo();
            return;
        }

        const step = steps[idx];
        sessionStorage.setItem('sv_demo_active', '1');
        sessionStorage.setItem('sv_demo_step', String(idx));

        if (step.page !== currentPageKey()) {
            const target = routes[step.page];
            if (target) {
                window.location.href = target;
                return;
            }
        }

        if (player) player.style.display = 'block';
        if (badgeEl) badgeEl.textContent = `${isEn ? 'STEP' : 'PASO'} ${idx + 1} / ${steps.length}`;
        if (titleEl) titleEl.textContent = isEn ? step.titleEn : step.titleEs;
        if (subEl) subEl.textContent = isEn ? step.textEn : step.textEs;

        lastCleanup = typeof step.cleanup === 'function' ? step.cleanup : null;
        if (typeof step.action === 'function') {
            try { step.action(); } catch (_) {}
        }

        setTimeout(() => {
            const el = document.querySelector(step.selector);
            if (el) {
                currentSpotlight = el;
                if (step.selector !== '#sans-monitor-workspace' && step.selector !== 'main#main-content') {
                    el.classList.add('sv-demo-spotlight');
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        }, 120);

        const speedSel = document.getElementById('sv-demo-speed');
        let demoSpeed = parseFloat(sessionStorage.getItem('sv_demo_speed') || '1') || 1;
        if (speedSel && speedSel.value !== String(demoSpeed)) {
            speedSel.value = String(demoSpeed);
        }

        elapsed = 0;
        const dur = step.duration || 7500;
        if (progEl) progEl.style.width = '0%';

        tickTimer = setInterval(() => {
            if (paused) return;
            const curSpeed = parseFloat(sessionStorage.getItem('sv_demo_speed') || '1') || 1;
            elapsed += 150 * curSpeed;
            const pct = Math.min(100, (elapsed / dur) * 100);
            if (progEl) progEl.style.width = `${pct}%`;
            if (elapsed >= dur) {
                runStep(idx + 1);
            }
        }, 150);
    }

    const speedSelectEl = document.getElementById('sv-demo-speed');
    if (speedSelectEl) {
        const savedSpeed = sessionStorage.getItem('sv_demo_speed');
        if (savedSpeed) speedSelectEl.value = savedSpeed;
        speedSelectEl.addEventListener('change', () => {
            sessionStorage.setItem('sv_demo_speed', speedSelectEl.value || '1');
        });
    }

    launchBtn?.addEventListener('click', () => {
        sessionStorage.removeItem('sv_demo_v2_dismissed');
        if (welcomeModal) welcomeModal.style.display = 'none';
        clearInterval(welcomeCountdownTimer);
        paused = false;
        if (pauseBtn) pauseBtn.innerHTML = '<i class="fa fa-pause"></i>';
        runStep(0);
    });
    document.getElementById('sv-demo-prev')?.addEventListener('click', () => {
        const cur = parseInt(sessionStorage.getItem('sv_demo_step') || '0', 10);
        runStep(Math.max(0, cur - 1));
    });
    document.getElementById('sv-demo-next')?.addEventListener('click', () => {
        const cur = parseInt(sessionStorage.getItem('sv_demo_step') || '0', 10);
        runStep(cur + 1);
    });
    pauseBtn?.addEventListener('click', () => {
        paused = !paused;
        pauseBtn.innerHTML = paused ? '<i class="fa fa-play"></i>' : '<i class="fa fa-pause"></i>';
    });
    document.getElementById('sv-demo-stop')?.addEventListener('click', () => stopDemo(true));

    const welcomeModal = document.getElementById('sv-demo-welcome-modal');
    const welcomeStartBtn = document.getElementById('sv-welcome-start-btn');
    let welcomeCountdownTimer = null;

    function startDemoFromWelcome() {
        clearInterval(welcomeCountdownTimer);
        if (welcomeModal) welcomeModal.style.display = 'none';
        sessionStorage.removeItem('sv_demo_v2_dismissed');
        paused = false;
        if (pauseBtn) pauseBtn.innerHTML = '<i class="fa fa-pause"></i>';
        runStep(0);
    }

    welcomeStartBtn?.addEventListener('click', startDemoFromWelcome);
    document.getElementById('sv-welcome-skip-btn')?.addEventListener('click', () => {
        clearInterval(welcomeCountdownTimer);
        if (welcomeModal) welcomeModal.style.display = 'none';
        sessionStorage.setItem('sv_demo_v2_dismissed', '1');
    });

    const forceDemoLink = window.location.pathname.endsWith('/demo') || new URLSearchParams(window.location.search).get('demo') === '1';
    if (forceDemoLink) {
        sessionStorage.removeItem('sv_demo_v2_dismissed');
        setTimeout(() => runStep(0), 120);
    } else if (sessionStorage.getItem('sv_demo_active') === '1') {
        const savedIdx = parseInt(sessionStorage.getItem('sv_demo_step') || '0', 10);
        setTimeout(() => runStep(savedIdx), 120);
    } else if (sessionStorage.getItem('sv_demo_v2_dismissed') !== '1') {
        if (welcomeModal) {
            welcomeModal.style.display = 'flex';
            let sec = 1;
            const labelSpan = welcomeStartBtn?.querySelector('span');
            const baseLabel = isEn ? 'ACTIVATE AUTOMATIC DEMO' : 'ACTIVAR DEMO AUTOMÁTICO';
            if (labelSpan) labelSpan.textContent = `${baseLabel} (${sec}s)`;
            welcomeCountdownTimer = setInterval(() => {
                sec--;
                if (sec <= 0) {
                    startDemoFromWelcome();
                } else if (labelSpan) {
                    labelSpan.textContent = `${baseLabel} (${sec}s)`;
                }
            }, 1000);
        } else {
            setTimeout(() => runStep(0), 150);
        }
    }
})();
</script>
@stack('scripts')
</body>
</html>
