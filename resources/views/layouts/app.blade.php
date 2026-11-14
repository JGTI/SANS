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
                <div class="nav-label">{{ $isEn ? 'Main Hackathon Flow' : 'Flujo Principal Hackathon' }}</div>
                <ul class="nav-list">
                    <li>
                        <a class="nav-link {{ (request()->routeIs('home') || request()->routeIs('public.*') || request()->routeIs('admin.sans-monitor.*')) && !in_array(request('tab'), ['seguimiento', 'simulador', 'escenarios']) ? 'active' : '' }}" href="{{ route('admin.sans-monitor.index', ['tab' => 'biomarcadores']) }}">
                            <i class="fa fa-Sliders fa-heartbeat" style="width:20px; text-align:center; font-size:15px;"></i>
                            {{ $isEn ? '1. Predictor & Biomarkers' : '1. Predictor y Biomarcadores' }}
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ (request()->routeIs('home') || request()->routeIs('public.*') || request()->routeIs('admin.sans-monitor.*')) && request('tab') === 'seguimiento' ? 'active' : '' }}" href="{{ route('admin.sans-monitor.index', ['tab' => 'seguimiento']) }}">
                            <i class="fa fa-calendar-check-o" style="width:20px; text-align:center; font-size:15px;"></i>
                            {{ $isEn ? '2. Timeline (+30 / +60 / +90 d)' : '2. Tiempo (+30 / +60 / +90 d)' }}
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ (request()->routeIs('home') || request()->routeIs('public.*') || request()->routeIs('admin.sans-monitor.*')) && in_array(request('tab'), ['escenarios', 'simulador']) ? 'active' : '' }}" href="{{ route('admin.sans-monitor.index', ['tab' => 'escenarios']) }}">
                            <i class="fa fa-cubes" style="width:20px; text-align:center; font-size:15px;"></i>
                            {{ $isEn ? '3. Scenarios (Stable/Worsens)' : '3. Escenarios (Estable/Empeora)' }}
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.digital-twin') || request()->routeIs('admin.oct-analysis') ? 'active' : '' }}" href="{{ route('admin.digital-twin', ['mode' => 'dual']) }}">
                            <i class="fa fa-eye" style="width:20px; text-align:center; font-size:15px;"></i>
                            {{ $isEn ? '3D Viewer + OCT Console' : 'Visor 3D + Consola OCT' }}
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.demo-theater*') ? 'active' : '' }}" href="{{ route('admin.demo-theater') }}">
                            <i class="fa fa-play-circle" style="width:20px; text-align:center; font-size:15px;"></i>
                            {{ $isEn ? 'Demo Pitch (3 Minutes)' : 'Demo Pitch (3 Minutos)' }}
                        </a>
                    </li>
                </ul>
            </nav>

            <details style="padding: 0 0.75rem; margin-top: 0.5rem;">
                <summary style="cursor:pointer; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted-foreground); padding:0.5rem 0.5rem; list-style:none; display:flex; align-items:center; justify-content:space-between; border-top:1px solid var(--border);">
                    <span><i class="fa fa-folder-o"></i> {{ $isEn ? 'Support & Admin Modules' : 'Módulos de Apoyo y Admin' }}</span>
                    <i class="fa fa-angle-down"></i>
                </summary>

                <nav class="nav-section" aria-label="{{ $isEn ? 'Telemetry & AI' : 'Telemetría e IA' }}" style="margin-top:0.35rem;">
                    <ul class="nav-list">
                        <li>
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fa fa-tachometer" style="width:20px; text-align:center; font-size:14px;"></i>
                                Mission Control
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('admin.vision-lab.*') ? 'active' : '' }}" href="{{ route('admin.vision-lab.index') }}">
                                <i class="fa fa-calculator" style="width:20px; text-align:center; font-size:14px;"></i>
                                {{ $isEn ? 'Optical Calculator' : 'Calculadora Óptica' }}
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('admin.nasa-data*') ? 'active' : '' }}" href="{{ route('admin.nasa-data') }}">
                                <i class="fa fa-globe" style="width:20px; text-align:center; font-size:14px;"></i>
                                {{ $isEn ? 'NASA Repositories' : 'Repositorios NASA' }}
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('admin.model-lab*') ? 'active' : '' }}" href="{{ route('admin.model-lab') }}">
                                <i class="fa fa-flask" style="width:20px; text-align:center; font-size:14px;"></i>
                                Benchmark ML (RF / GBM)
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('admin.explainability*') ? 'active' : '' }}" href="{{ route('admin.explainability') }}">
                                <i class="fa fa-pie-chart" style="width:20px; text-align:center; font-size:14px;"></i>
                                {{ $isEn ? 'SHAP Weights (XAI)' : 'Pesos SHAP (XAI)' }}
                            </a>
                        </li>
                        @can('users.manage')
                        <li>
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                <i class="fa fa-users" style="width:20px; text-align:center; font-size:14px;"></i>
                                {{ $isEn ? 'Users' : 'Usuarios' }}
                            </a>
                        </li>
                        @endcan
                        @role('super-admin')
                        <li>
                            <a class="nav-link {{ request()->routeIs('admin.api-keys*') ? 'active' : '' }}" href="{{ route('admin.api-keys.index') }}">
                                <i class="fa fa-key" style="width:20px; text-align:center; font-size:14px;"></i>
                                {{ $isEn ? 'API Keys' : 'Claves de APIs' }}
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('admin.maintenance*') ? 'active' : '' }}" href="{{ route('admin.maintenance') }}">
                                <i class="fa fa-wrench" style="width:20px; text-align:center; font-size:14px;"></i>
                                {{ $isEn ? 'Artisan Console' : 'Consola Artisan' }}
                            </a>
                        </li>
                        @endrole
                    </ul>
                </nav>
            </details>
        </div>

        <div class="sidebar-user">
            @php
                $currentUserName = auth()->user()?->name ?? 'Public Demo Judge';
                $currentUserEmail = auth()->user()?->email ?? ($isEn ? 'Public Mode · No Login Required' : 'Modo Público · Sin Login');
            @endphp
            <button class="user-button" id="user-menu-button" type="button" aria-expanded="false" aria-controls="user-menu">
                <span class="avatar">{{ strtoupper(mb_substr($currentUserName, 0, 2)) }}</span>
                <span class="user-copy">
                    <span class="user-name">{{ $currentUserName }}</span>
                    <span class="user-email">{{ $currentUserEmail }}</span>
                </span>
                <i class="fa fa-ellipsis-v" style="color:var(--muted-foreground); margin-left:auto;"></i>
            </button>
            <div class="user-menu" id="user-menu">
                @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"><i class="fa fa-sign-out" style="margin-right:8px;"></i> {{ $isEn ? 'Sign out' : 'Cerrar sesión' }}</button>
                </form>
                @else
                <a href="{{ route('login') }}" style="display:block;padding:8px 12px;text-decoration:none;color:inherit;font-size:13px;">
                    <i class="fa fa-sign-in" style="margin-right:8px;"></i> {{ $isEn ? 'Staff / Admin Login (Optional)' : 'Acceso Staff / Admin (Opcional)' }}
                </a>
                @endauth
            </div>
        </div>
    </aside>

    <button class="sidebar-overlay" id="sidebar-overlay" type="button" aria-label="{{ $isEn ? 'Close menu' : 'Cerrar menú' }}"></button>

    <div class="admin-main">
        <header class="topbar">
            <button class="icon-btn mobile-menu" id="mobile-menu" type="button" aria-label="{{ $isEn ? 'Open menu' : 'Abrir menú' }}">
                <i class="fa fa-bars" style="font-size:16px;"></i>
            </button>
            <nav class="topnav" aria-label="{{ $isEn ? 'Main sections' : 'Secciones del dashboard' }}">
                <a class="active" href="{{ route('home') }}">{{ $isEn ? 'SANS Predictor' : 'Predictor SANS' }}</a>
                <a href="{{ route('admin.sans-monitor.index', ['tab' => 'seguimiento']) }}">{{ $isEn ? 'Timeline (+30/+60/+90d)' : 'Tiempo (+30/+60/+90d)' }}</a>
                <a href="{{ route('admin.sans-monitor.index', ['tab' => 'escenarios']) }}">{{ $isEn ? '3 Scenarios' : '3 Escenarios' }}</a>
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
                'Gemelo Digital 3D': '3D Digital Twin',
                'Consola Clínica ZEISS Cirrus': 'ZEISS Cirrus Clinical Console',
                'Simulador de Misión': 'Mission Simulator',
                'Observatorio de Datos NASA': 'NASA Data Observatory',
                'Laboratorio de Modelos': 'Model Laboratory',
                'Centro de Explicabilidad': 'Explainability Center',
                'Calidad de Datos': 'Data Quality Observatory',
                'Registro de Modelos': 'Model Registry',
                'Ojo Derecho': 'Right Eye',
                'Ojo Izquierdo': 'Left Eye',
                'Línea Base': 'Baseline',
                'En Vuelo': 'In-Flight',
                'Posvuelo': 'Post-Flight',
                'Exportar CSV': 'Export CSV',
                'Imprimir Expediente': 'Print Dossier'
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
