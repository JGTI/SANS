<!DOCTYPE html>
<html lang="es">
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
<div class="admin-shell">
    <aside class="sidebar" id="app-sidebar" aria-label="Navegación principal">
        <a class="workspace-switcher" href="{{ route('admin.dashboard') }}">
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
            <nav class="nav-section" aria-label="Flujo Principal SANS">
                <div class="nav-label">Flujo Principal Hackathon</div>
                <ul class="nav-list">
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.sans-monitor.*') && !in_array(request('tab'), ['seguimiento', 'simulador', 'escenarios']) ? 'active' : '' }}" href="{{ route('admin.sans-monitor.index', ['tab' => 'biomarcadores']) }}">
                            <i class="fa fa-Sliders fa-heartbeat" style="width:20px; text-align:center; font-size:15px;"></i>
                            1. Predictor y Biomarcadores
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.sans-monitor.*') && request('tab') === 'seguimiento' ? 'active' : '' }}" href="{{ route('admin.sans-monitor.index', ['tab' => 'seguimiento']) }}">
                            <i class="fa fa-calendar-check-o" style="width:20px; text-align:center; font-size:15px;"></i>
                            2. Tiempo (+30 / +60 / +90 d)
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.sans-monitor.*') && in_array(request('tab'), ['escenarios', 'simulador']) ? 'active' : '' }}" href="{{ route('admin.sans-monitor.index', ['tab' => 'escenarios']) }}">
                            <i class="fa fa-cubes" style="width:20px; text-align:center; font-size:15px;"></i>
                            3. Escenarios (Estable/Empeora)
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.digital-twin') || request()->routeIs('admin.oct-analysis') ? 'active' : '' }}" href="{{ route('admin.digital-twin', ['mode' => 'dual']) }}">
                            <i class="fa fa-eye" style="width:20px; text-align:center; font-size:15px;"></i>
                            Visor 3D + Consola OCT
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.demo-theater*') ? 'active' : '' }}" href="{{ route('admin.demo-theater') }}">
                            <i class="fa fa-play-circle" style="width:20px; text-align:center; font-size:15px;"></i>
                            Demo Pitch (3 Minutos)
                        </a>
                    </li>
                </ul>
            </nav>

            <details style="padding: 0 0.75rem; margin-top: 0.5rem;">
                <summary style="cursor:pointer; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted-foreground); padding:0.5rem 0.5rem; list-style:none; display:flex; align-items:center; justify-content:space-between; border-top:1px solid var(--border);">
                    <span><i class="fa fa-folder-o"></i> Módulos de Apoyo y Admin</span>
                    <i class="fa fa-angle-down"></i>
                </summary>

                <nav class="nav-section" aria-label="Telemetría e IA" style="margin-top:0.35rem;">
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
                                Calculadora Óptica
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('admin.nasa-data*') ? 'active' : '' }}" href="{{ route('admin.nasa-data') }}">
                                <i class="fa fa-globe" style="width:20px; text-align:center; font-size:14px;"></i>
                                Repositorios NASA
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
                                Pesos SHAP (XAI)
                            </a>
                        </li>
                        @can('users.manage')
                        <li>
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                <i class="fa fa-users" style="width:20px; text-align:center; font-size:14px;"></i>
                                Usuarios
                            </a>
                        </li>
                        @endcan
                        @role('super-admin')
                        <li>
                            <a class="nav-link {{ request()->routeIs('admin.api-keys*') ? 'active' : '' }}" href="{{ route('admin.api-keys.index') }}">
                                <i class="fa fa-key" style="width:20px; text-align:center; font-size:14px;"></i>
                                Claves de APIs
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('admin.maintenance*') ? 'active' : '' }}" href="{{ route('admin.maintenance') }}">
                                <i class="fa fa-wrench" style="width:20px; text-align:center; font-size:14px;"></i>
                                Consola Artisan
                            </a>
                        </li>
                        @endrole
                    </ul>
                </nav>
            </details>
        </div>

        <div class="sidebar-user">
            <button class="user-button" id="user-menu-button" type="button" aria-expanded="false" aria-controls="user-menu">
                <span class="avatar">{{ strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}</span>
                <span class="user-copy">
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <span class="user-email">{{ auth()->user()->email }}</span>
                </span>
                <i class="fa fa-ellipsis-v" style="color:var(--muted-foreground); margin-left:auto;"></i>
            </button>
            <div class="user-menu" id="user-menu">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"><i class="fa fa-sign-out" style="margin-right:8px;"></i> Cerrar sesión</button>
                </form>
            </div>
        </div>
    </aside>

    <button class="sidebar-overlay" id="sidebar-overlay" type="button" aria-label="Cerrar menú"></button>

    <div class="admin-main">
        <header class="topbar">
            <button class="icon-btn mobile-menu" id="mobile-menu" type="button" aria-label="Abrir menú">
                <i class="fa fa-bars" style="font-size:16px;"></i>
            </button>
            <nav class="topnav" aria-label="Secciones del dashboard">
                <a class="active" href="{{ route('admin.dashboard') }}">Vista general</a>
                <a href="#actividad">Actividad</a>
                <a href="#progreso">Progreso</a>
            </nav>
            <span class="topbar-spacer"></span>
            <label class="search-box">
                <span class="sr-only">Buscar</span>
                <i class="fa fa-search icon" style="display:inline-grid; place-items:center; font-size:14px; top:10px;"></i>
                <input type="search" placeholder="Buscar…" disabled>
            </label>
            <button class="icon-btn" id="theme-toggle" type="button" aria-label="Cambiar tema">
                <i class="fa fa-moon-o" style="font-size:16px;"></i>
            </button>
        </header>

        <main class="main-content" id="main-content">
            @yield('content')
        </main>

        <footer class="admin-footer">{{ config('app.name') }} · Base preparada para NASA Space Apps Challenge</footer>
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
    })();
</script>
@stack('scripts')
</body>
</html>
