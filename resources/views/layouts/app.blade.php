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
                <img src="{{ request()->getBasePath() }}/images/nexus-logo.png" alt="Nexus" style="width:22px;height:22px;object-fit:contain;">
                <span class="topbar-brand-text">
                    <strong>SPACE VISION</strong>
                    <small>SANS MONITOR · ISS-01</small>
                </span>
            </a>

            <nav class="topnav" aria-label="{{ $isEn ? 'Primary Navigation' : 'Navegación Principal' }}">
                <a class="{{ (request()->routeIs('home') || request()->routeIs('public.demo') || request()->routeIs('public.sans-monitor') || request()->routeIs('admin.sans-monitor.*')) && request('tab') !== 'astronauta' ? 'active' : '' }}" href="{{ route('home') }}">
                    <span class="topnav-idx">01</span>
                    <i class="fa fa-flask"></i>
                    <span>{{ $isEn ? 'Scientific Module (SANS)' : 'Módulo Científico (SANS)' }}</span>
                </a>
                <a class="{{ request()->routeIs('public.astronaut') || request('tab') === 'astronauta' ? 'active' : '' }}" href="{{ route('public.astronaut') }}">
                    <span class="topnav-idx">02</span>
                    <i class="fa fa-user-md"></i>
                    <span>{{ $isEn ? 'Astronaut Check' : 'Vista Astronauta' }}</span>
                </a>
                <a class="{{ request()->routeIs('public.digital-twin') || request()->routeIs('admin.digital-twin') || request()->routeIs('public.oct-analysis') || request()->routeIs('admin.oct-analysis') ? 'active' : '' }}" href="{{ route('public.digital-twin', ['mode' => 'dual']) }}">
                    <span class="topnav-idx">03</span>
                    <i class="fa fa-eye"></i>
                    <span>{{ $isEn ? '3D Viewer + OCT' : 'Visor 3D + OCT' }}</span>
                </a>
            </nav>

            <span class="topbar-spacer"></span>

            <div class="topbar-tools">
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
                <img src="{{ request()->getBasePath() }}/images/nexus-logo.png" alt="Nexus" style="height:18px;object-fit:contain;">
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
@stack('scripts')
</body>
</html>
