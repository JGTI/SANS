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
        width: min(760px, calc(100vw - 24px));
        background: rgba(9, 9, 11, 0.96);
        color: #ffffff;
        border: 1.5px solid #EAFE07;
        border-radius: 14px;
        padding: 0.85rem 1.05rem 0.75rem;
        box-shadow: 0 22px 60px rgba(0, 0, 0, 0.65);
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

<div id="sv-demo-player" role="region" aria-live="polite" aria-label="{{ $isEn ? 'Guided Demo Subtitles' : 'Subtítulos del Demo Guiado' }}">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:0.6rem;flex-wrap:wrap;margin-bottom:0.35rem;">
        <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
            <span id="sv-demo-badge" style="background:#EAFE07;color:#09090b;font-family:ui-monospace,monospace;font-size:0.68rem;font-weight:800;padding:0.18rem 0.55rem;border-radius:999px;">PASO 1 / 11</span>
            <strong id="sv-demo-title" style="font-size:0.9rem;color:#ffffff;"></strong>
        </div>
        <div style="display:flex;align-items:center;gap:0.35rem;">
            <button type="button" id="sv-demo-prev" title="{{ $isEn ? 'Previous' : 'Anterior' }}" style="background:rgba(255,255,255,0.12);color:#fff;border:1px solid rgba(255,255,255,0.2);border-radius:6px;padding:0.25rem 0.55rem;font-size:0.72rem;font-weight:700;cursor:pointer;"><i class="fa fa-step-backward"></i></button>
            <button type="button" id="sv-demo-pause" title="{{ $isEn ? 'Pause / Resume' : 'Pausar / Reanudar' }}" style="background:#EAFE07;color:#09090b;border:1px solid #EAFE07;border-radius:6px;padding:0.25rem 0.65rem;font-size:0.72rem;font-weight:800;cursor:pointer;"><i class="fa fa-pause"></i></button>
            <button type="button" id="sv-demo-next" title="{{ $isEn ? 'Next' : 'Siguiente' }}" style="background:rgba(255,255,255,0.12);color:#fff;border:1px solid rgba(255,255,255,0.2);border-radius:6px;padding:0.25rem 0.55rem;font-size:0.72rem;font-weight:700;cursor:pointer;"><i class="fa fa-step-forward"></i></button>
            <button type="button" id="sv-demo-stop" title="{{ $isEn ? 'Close Demo' : 'Cerrar Demo' }}" style="background:rgba(239,68,68,0.22);color:#fca5a5;border:1px solid rgba(239,68,68,0.4);border-radius:6px;padding:0.25rem 0.55rem;font-size:0.72rem;font-weight:700;cursor:pointer;"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div id="sv-demo-subtitle" style="font-size:0.84rem;line-height:1.45;color:#e4e4e7;"></div>
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
            titleEn: '1. Mission Overview & Crew Roster (Zero-Login)',
            titleEs: '1. Visión de Misión y Tripulación (Acceso Directo)',
            textEn: 'Welcome to SPACE VISION · SANS Monitor. Here flight surgeons and researchers inspect longitudinal astronaut ocular records (CDR Alex Mercer, Dr. Sara Lindqvist, Kenji Sato, etc.) without login barriers.',
            textEs: 'Bienvenido a SPACE VISION · SANS Monitor. Aquí médicos aeroespaciales e investigadores revisan expedientes oculares longitudinales de astronautas (CDR Alex Mercer, Dra. Lindqvist, Kenji Sato) sin registro previo.',
        },
        {
            page: 'home',
            selector: '[data-wizard-tab="ingreso"]',
            duration: 7000,
            titleEn: '2. Independent OD/OS Triage & 3-Stage Workflow',
            titleEs: '2. Triaje Independiente OD/OS y Flujo en 3 Fases',
            textEn: 'Each eye (OD / OS) is evaluated independently into Ready for review, Prioritize review, or Missing data—never issuing an automated fit-for-duty verdict. Use the 3 tabs: Intake & Predictor, Longitudinal Cuts, and Scenario Models.',
            textEs: 'Cada ojo (OD / OS) se evalúa por separado en Listo para revisión, Priorizar revisión o Faltan datos, sin emitir veredictos automáticos de aptitud. Las 3 pestañas guían: Ingreso, Cortes en el Tiempo y Escenarios.',
        },
        {
            page: 'home',
            selector: '#btnRunFundusScanner',
            duration: 8500,
            titleEn: '3. Live Python CV Fundus & Papilledema Scanner',
            titleEs: '3. Escáner Python CV de Fondo de Ojo y Edema Papilar',
            textEn: 'Running real computer vision in Python (Pillow + NumPy + SciPy): computes Retinal FOV mask, Optic Disc localization, Sobel rim sharpness, peripapillary halo, and choroidal folds. You can also upload a photo or use the live camera.',
            textEs: 'Ejecutando visión por computadora real en Python (Pillow + NumPy + SciPy): calcula máscara retiniana, disco óptico, nitidez Sobel del borde, halo peripapilar y pliegues coroideos. También permite subir foto o usar cámara en vivo.',
            action: () => {
                const btn = document.getElementById('btnRunFundusScanner');
                if (btn) btn.click();
            }
        },
        {
            page: 'home',
            selector: '#biomarkerImpactChart',
            duration: 7500,
            titleEn: '4. The 6 Core Ocular Biomarkers & Signal Index',
            titleEs: '4. Los 6 Biomarcadores Oculares Clave e Índice de Señal',
            textEn: 'Evaluates OCT Total Retinal Thickness (TRT), Axial Length, Hyperopic Shift, Frisén Papilledema Grade, IOP, and -6° HDT analog exposure, displaying the weighted contribution of each biomarker.',
            textEs: 'Evalúa Espesor Retiniano OCT (TRT), Longitud Axial, Cambio Hipermetrópico, Grado Frisén, PIO y exposición análoga -6° HDT, graficando el peso de cada biomarcador en el Índice de Señal.',
        },
        {
            page: 'home',
            selector: '#candidate-predict-form',
            duration: 8500,
            titleEn: '5. Future Mission Candidate Predictor (Scikit-learn)',
            titleEs: '5. Modelo Predictivo para Futuros Candidatos (Scikit-learn)',
            textEn: 'Executing the Python Scikit-learn model across 4 domains (Biometric, Ophthalmological, Neurological, and Metabolic 1-Carbon/CO₂) to estimate SANS susceptibility for future spaceflight candidates.',
            textEs: 'Ejecutando el modelo Scikit-learn en Python sobre 4 perfiles (Biométrico, Oftalmológico, Neurológico y Metabólico 1-Carbono/CO₂) para estimar susceptibilidad al SANS en futuros candidatos espaciales.',
            action: () => {
                const btn = document.getElementById('candidate-predict-btn');
                if (btn) btn.click();
            }
        },
        {
            page: 'home',
            selector: '#wizard-panel-seguimiento',
            duration: 8000,
            titleEn: '6. Longitudinal Cuts: Before · During · +30 / +60 / +90 Days',
            titleEs: '6. Cortes en el Tiempo: Antes · Durante · +30 / +60 / +90 Días',
            textEn: 'Switching to Longitudinal Cuts: tracks pre-flight baseline, in-flight shifts, and post-flight recovery milestones at +30, +60, and +90 days with historical playback.',
            textEs: 'Abriendo Cortes en el Tiempo: compara línea base pre-vuelo, cambios durante la misión y recuperación posvuelo a +30, +60 y +90 días con reproducción histórica.',
            action: () => {
                const tab = document.querySelector('[data-wizard-tab="seguimiento"]');
                if (tab) tab.click();
            }
        },
        {
            page: 'home',
            selector: '#wizard-panel-escenarios',
            duration: 8000,
            titleEn: '7. Canonical Scenarios: Stable · Worsens · Unscanned',
            titleEs: '7. Escenarios Modelos: Estable · Empeora · No se Escanea',
            textEn: 'Comparing the 3 reference archetypes: Stable, Worsens (active progression), and Unscanned—strictly enforcing that missing OCT is never assumed stable even if IOP looks normal.',
            textEs: 'Comparando los 3 escenarios canónicos: Estable, Empeora y No se Escanea, garantizando que una falta de OCT jamás se asuma como estable aunque la PIO sea normal.',
            action: () => {
                const tab = document.querySelector('[data-wizard-tab="escenarios"]');
                if (tab) tab.click();
            }
        },
        {
            page: 'home',
            selector: '#nasa-live-search-form',
            duration: 7500,
            titleEn: '8. NASA OCHMO Protocols & Live OSDR / NTRS / DONKI / ISS APIs',
            titleEs: '8. Protocolos NASA OCHMO y APIs en Vivo OSDR / NTRS / DONKI / ISS',
            textEn: 'Retrieves applicable NASA OCHMO surveillance protocols, 7 peer-reviewed references, and live space-weather/telemetry data from NASA OSDR, NTRS, DONKI, and ISS.',
            textEs: 'Consulta protocolos clínicos NASA OCHMO, 7 referencias científicas verificadas y telemetría/literatura en tiempo real desde NASA OSDR, NTRS, DONKI e ISS.',
            action: () => {
                const tab = document.querySelector('[data-wizard-tab="ingreso"]');
                if (tab) tab.click();
            }
        },
        {
            page: 'astronaut',
            selector: 'main#main-content',
            duration: 8500,
            titleEn: '9. Astronaut Check & Orbital Fundus Downlink',
            titleEs: '9. Vista Astronauta y Envío Orbital de Fondo de Ojo',
            textEn: 'In the Astronaut Check module, crew members follow a guided 8-minute orbital exam, inspect their flight logbook, and transmit OD/OS fundus captures directly to Ground Control.',
            textEs: 'En la Vista Astronauta, la tripulación sigue un chequeo guiado de 8 minutos, consulta su bitácora orbital y transmite capturas de fondo de ojo OD/OS directo a Control en Tierra.',
        },
        {
            page: 'twin',
            selector: 'main#main-content',
            duration: 8500,
            titleEn: '10. Interactive 3D WebGL Eye Digital Twin + SD-OCT',
            titleEs: '10. Gemelo Digital Ocular 3D WebGL + Capas SD-OCT',
            textEn: 'The 3D Digital Twin renders real-time posterior globe flattening, optic disc protrusion, and choroidal folds synchronized with SD-OCT retinal thickness layers.',
            textEs: 'El Gemelo Digital 3D renderiza en tiempo real el aplanamiento posterior del globo, protrusión del disco óptico y pliegues coroideos sincronizados con las capas SD-OCT.',
        },
        {
            page: 'twin',
            selector: 'main#main-content',
            duration: 7000,
            titleEn: '11. Guided Demo Complete — Ready for Interactive Exploration',
            titleEs: '11. Demo Guiado Completo — Listo para Exploración Libre',
            textEn: 'End-to-end tour complete: Python CV Fundus Analysis, 6-Biomarker & 4-Domain Scikit-learn Prediction, Longitudinal +30/+60/+90d Cuts, NASA Protocols, Astronaut Downlink, and 3D WebGL Digital Twin.',
            textEs: 'Recorrido completo finalizado: Visión por Computadora en Python, Predicción de 6 Biomarcadores y 4 Perfiles en Scikit-learn, Cortes +30/+60/+90d, Protocolos NASA, Enlace Astronauta y Gemelo 3D.',
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

    function stopDemo() {
        clearTimeout(timer);
        clearInterval(tickTimer);
        clearSpotlight();
        sessionStorage.removeItem('sv_demo_active');
        sessionStorage.removeItem('sv_demo_step');
        if (player) player.style.display = 'none';
    }

    function runStep(idx) {
        clearTimeout(timer);
        clearInterval(tickTimer);
        clearSpotlight();

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

        elapsed = 0;
        const dur = step.duration || 7500;
        if (progEl) progEl.style.width = '0%';

        tickTimer = setInterval(() => {
            if (paused) return;
            elapsed += 150;
            const pct = Math.min(100, (elapsed / dur) * 100);
            if (progEl) progEl.style.width = `${pct}%`;
            if (elapsed >= dur) {
                runStep(idx + 1);
            }
        }, 150);
    }

    launchBtn?.addEventListener('click', () => {
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
    document.getElementById('sv-demo-stop')?.addEventListener('click', stopDemo);

    if (sessionStorage.getItem('sv_demo_active') === '1') {
        const savedIdx = parseInt(sessionStorage.getItem('sv_demo_step') || '0', 10);
        setTimeout(() => runStep(savedIdx), 350);
    }
})();
</script>
@stack('scripts')
</body>
</html>
