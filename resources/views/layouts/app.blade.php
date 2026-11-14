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
    <aside class="sidebar" id="app-sidebar" aria-label="{{ $isEn ? 'Main navigation' : 'Navegación principal' }}">
        <a class="workspace-switcher" href="{{ route('home') }}">
            <span class="workspace-logo" aria-hidden="true" style="background:#fff;padding:4px;display:flex;align-items:center;justify-content:center;">
                <img src="{{ request()->getBasePath() }}/images/nexus-logo.png" alt="Nexus" style="width:22px;height:22px;object-fit:contain;">
            </span>
            <span class="workspace-copy">
                <span class="workspace-name">{{ config('app.name') }}</span>
                <span class="workspace-plan">SPACE VISION · NASA Space Apps</span>
            </span>
            <i class="fa fa-angle-right" style="color:var(--muted-foreground); margin-left:auto;"></i>
        </a>

        <div class="sidebar-scroll">
            <nav class="nav-section" aria-label="{{ $isEn ? 'Main SANS Flow' : 'Flujo Principal SANS' }}">
                <div class="nav-label">{{ $isEn ? 'Main Workspace' : 'Pantallas Principales' }}</div>
                <ul class="nav-list">
                    <li>
                        <a class="nav-link {{ request()->routeIs('home') || request()->routeIs('public.demo') || request()->routeIs('public.sans-monitor') || request()->routeIs('admin.sans-monitor.*') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="fa fa-heartbeat" style="width:20px; text-align:center; font-size:15px;"></i>
                            {{ $isEn ? 'SANS Monitor (3-Step Wizard)' : 'Predictor SANS (Wizard 3 Pasos)' }}
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('public.digital-twin') || request()->routeIs('admin.digital-twin') || request()->routeIs('public.oct-analysis') || request()->routeIs('admin.oct-analysis') ? 'active' : '' }}" href="{{ route('public.digital-twin', ['mode' => 'dual']) }}">
                            <i class="fa fa-eye" style="width:20px; text-align:center; font-size:15px;"></i>
                            {{ $isEn ? '3D Viewer + OCT Console' : 'Visor 3D + Consola OCT' }}
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('public.demo-theater*') || request()->routeIs('admin.demo-theater*') ? 'active' : '' }}" href="{{ route('public.demo-theater') }}">
                            <i class="fa fa-play-circle" style="width:20px; text-align:center; font-size:15px;"></i>
                            {{ $isEn ? 'Demo Pitch (3 Minutes)' : 'Demo Pitch (3 Minutos)' }}
                        </a>
                    </li>
                </ul>
            </nav>

            <details style="padding: 0 0.75rem; margin-top: 0.5rem;">
                <summary style="cursor:pointer; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted-foreground); padding:0.5rem 0.5rem; list-style:none; display:flex; align-items:center; justify-content:space-between; border-top:1px solid var(--border);">
                    <span><i class="fa fa-folder-o"></i> {{ $isEn ? 'Supporting Scientific Tools' : 'Herramientas Científicas de Apoyo' }}</span>
                    <i class="fa fa-angle-down"></i>
                </summary>

                <nav class="nav-section" aria-label="{{ $isEn ? 'Telemetry & AI' : 'Telemetría e IA' }}" style="margin-top:0.35rem;">
                    <ul class="nav-list">
                        <li>
                            <a class="nav-link {{ request()->routeIs('public.vision-lab') || request()->routeIs('admin.vision-lab.*') ? 'active' : '' }}" href="{{ route('public.vision-lab') }}">
                                <i class="fa fa-calculator" style="width:20px; text-align:center; font-size:14px;"></i>
                                {{ $isEn ? 'Optical Calculator' : 'Calculadora Óptica' }}
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('public.nasa-data') || request()->routeIs('admin.nasa-data*') ? 'active' : '' }}" href="{{ route('public.nasa-data') }}">
                                <i class="fa fa-globe" style="width:20px; text-align:center; font-size:14px;"></i>
                                {{ $isEn ? 'NASA Repositories' : 'Repositorios NASA' }}
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('public.model-lab') || request()->routeIs('admin.model-lab*') ? 'active' : '' }}" href="{{ route('public.model-lab') }}">
                                <i class="fa fa-flask" style="width:20px; text-align:center; font-size:14px;"></i>
                                Benchmark ML (RF / GBM)
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('public.explainability') || request()->routeIs('admin.explainability*') ? 'active' : '' }}" href="{{ route('public.explainability') }}">
                                <i class="fa fa-pie-chart" style="width:20px; text-align:center; font-size:14px;"></i>
                                {{ $isEn ? 'SHAP Weights (XAI)' : 'Pesos SHAP (XAI)' }}
                            </a>
                        </li>
                    </ul>
                </nav>
            </details>
        </div>

        <div class="sidebar-user">
            @php
                $currentUserName = $isEn ? 'NASA Space Apps Judge' : 'Jurado NASA Space Apps';
                $currentUserEmail = $isEn ? 'Public Mode · Zero Login' : 'Modo Público · Sin Login';
            @endphp
            <button class="user-button" id="user-menu-button" type="button" aria-expanded="false" aria-controls="user-menu">
                <span class="avatar">NS</span>
                <span class="user-copy">
                    <span class="user-name">{{ $currentUserName }}</span>
                    <span class="user-email">{{ $currentUserEmail }}</span>
                </span>
                <i class="fa fa-ellipsis-v" style="color:var(--muted-foreground); margin-left:auto;"></i>
            </button>
            <div class="user-menu" id="user-menu">
                <a href="{{ route('home', ['lang' => app()->getLocale()]) }}" style="display:block;padding:8px 12px;text-decoration:none;color:inherit;font-size:13px;">
                    <i class="fa fa-refresh" style="margin-right:8px;"></i> {{ $isEn ? 'Reset Public Demo' : 'Reiniciar Demo Pública' }}
                </a>
            </div>
        </div>
    </aside>

    <button class="sidebar-overlay" id="sidebar-overlay" type="button" aria-label="{{ $isEn ? 'Close menu' : 'Cerrar menú' }}"></button>

    <div class="admin-main">
        <header class="topbar">
            <button class="icon-btn mobile-menu" id="mobile-menu" type="button" title="{{ $isEn ? 'Toggle Navigation Drawer' : 'Abrir/Cerrar Menú Lateral' }}" aria-label="{{ $isEn ? 'Open menu' : 'Abrir menú' }}">
                <i class="fa fa-bars" style="font-size:15px;"></i>
            </button>

            <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;gap:0.5rem;text-decoration:none;color:var(--foreground);font-weight:800;font-size:0.86rem;padding-right:0.5rem;border-right:1px solid var(--border);">
                <img src="{{ request()->getBasePath() }}/images/nexus-logo.png" alt="Nexus" style="width:20px;height:20px;object-fit:contain;">
                <span>SPACE VISION</span>
            </a>

            <nav class="topnav" aria-label="{{ $isEn ? 'Primary Navigation' : 'Navegación Principal' }}">
                <a class="{{ request()->routeIs('home') || request()->routeIs('public.demo') || request()->routeIs('public.sans-monitor') || request()->routeIs('admin.sans-monitor.*') ? 'active' : '' }}" href="{{ route('home') }}">
                    <i class="fa fa-heartbeat"></i> {{ $isEn ? 'SANS Monitor (3-Step Wizard)' : 'Predictor SANS (Wizard 3 Pasos)' }}
                </a>
                <a class="{{ request()->routeIs('public.digital-twin') || request()->routeIs('admin.digital-twin') || request()->routeIs('public.oct-analysis') || request()->routeIs('admin.oct-analysis') ? 'active' : '' }}" href="{{ route('public.digital-twin', ['mode' => 'dual']) }}">
                    <i class="fa fa-eye"></i> {{ $isEn ? '3D Viewer + OCT' : 'Visor 3D + OCT' }}
                </a>
                <a class="{{ request()->routeIs('public.demo-theater*') || request()->routeIs('admin.demo-theater*') ? 'active' : '' }}" href="{{ route('public.demo-theater') }}">
                    <i class="fa fa-play-circle"></i> {{ $isEn ? 'Demo Pitch (3m)' : 'Demo Pitch (3m)' }}
                </a>

                <details style="position:relative;display:inline-block;">
                    <summary style="list-style:none;cursor:pointer;padding:6px 10px;border-radius:6px;border:1px solid var(--border);font-size:12px;font-weight:600;color:var(--muted-foreground);display:inline-flex;align-items:center;gap:5px;">
                        <i class="fa fa-flask"></i> {{ $isEn ? 'Scientific Tools' : 'Herramientas' }} <i class="fa fa-angle-down"></i>
                    </summary>
                    <div style="position:absolute;top:calc(100% + 6px);left:0;min-width:210px;background:var(--card);border:1px solid var(--border);border-radius:8px;padding:6px;box-shadow:0 10px 25px rgba(0,0,0,0.15);z-index:50;display:flex;flex-direction:column;gap:2px;">
                        <a href="{{ route('public.vision-lab') }}" style="padding:7px 10px;border-radius:5px;font-size:12px;">
                            <i class="fa fa-calculator" style="width:16px;"></i> {{ $isEn ? 'Optical Calculator' : 'Calculadora Óptica' }}
                        </a>
                        <a href="{{ route('public.nasa-data') }}" style="padding:7px 10px;border-radius:5px;font-size:12px;">
                            <i class="fa fa-globe" style="width:16px;"></i> {{ $isEn ? 'NASA Repositories' : 'Repositorios NASA' }}
                        </a>
                        <a href="{{ route('public.model-lab') }}" style="padding:7px 10px;border-radius:5px;font-size:12px;">
                            <i class="fa fa-flask" style="width:16px;"></i> Benchmark ML (RF / GBM)
                        </a>
                        <a href="{{ route('public.explainability') }}" style="padding:7px 10px;border-radius:5px;font-size:12px;">
                            <i class="fa fa-pie-chart" style="width:16px;"></i> {{ $isEn ? 'SHAP Weights (XAI)' : 'Pesos SHAP (XAI)' }}
                        </a>
                    </div>
                </details>
            </nav>

            <span class="topbar-spacer"></span>

            {{-- SELECTOR MULTILENGUAJE (INGLÉS PRINCIPAL / ESPAÑOL) --}}
            <div style="display:inline-flex;align-items:center;border:1px solid var(--border);border-radius:6px;overflow:hidden;font-size:0.76rem;font-weight:700;" role="group" aria-label="Language Selector">
                <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}"
                   style="padding:0.35rem 0.65rem;text-decoration:none;background:{{ $isEn ? 'var(--foreground)' : 'transparent' }};color:{{ $isEn ? 'var(--background)' : 'var(--foreground)' }};">
                    <i class="fa fa-globe"></i> EN
                </a>
                <a href="{{ request()->fullUrlWithQuery(['lang' => 'es']) }}"
                   style="padding:0.35rem 0.65rem;text-decoration:none;background:{{ !$isEn ? 'var(--foreground)' : 'transparent' }};color:{{ !$isEn ? 'var(--background)' : 'var(--foreground)' }};border-left:1px solid var(--border);">
                    ES
                </a>
            </div>

            <button class="icon-btn" id="theme-toggle" type="button" aria-label="{{ $isEn ? 'Toggle theme' : 'Cambiar tema' }}">
                <i class="fa fa-moon-o" style="font-size:16px;"></i>
            </button>
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
        const sidebar = document.getElementById('app-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const menuButton = document.getElementById('mobile-menu');
        const userButton = document.getElementById('user-menu-button');
        const userMenu = document.getElementById('user-menu');

        document.getElementById('theme-toggle').addEventListener('click', () => {
            const next = root.dataset.theme === 'dark' ? 'light' : 'dark';
            root.dataset.theme = next;
            localStorage.setItem('nexus-space-theme', next);
        });

        const closeSidebar = () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
        };

        menuButton?.addEventListener('click', () => {
            sidebar.classList.add('open');
            overlay.classList.add('open');
        });
        overlay.addEventListener('click', closeSidebar);

        userButton.addEventListener('click', () => {
            const open = userMenu.classList.toggle('open');
            userButton.setAttribute('aria-expanded', String(open));
        });

        document.addEventListener('click', (event) => {
            if (!event.target.closest('.sidebar-user')) {
                userMenu.classList.remove('open');
                userButton.setAttribute('aria-expanded', 'false');
            }
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
