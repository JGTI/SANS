<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#000000">
    <title>Iniciar sesión | {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ request()->getBasePath() }}/images/nexus-logo.png">
    <script>
        const savedTheme = localStorage.getItem('nexus-space-theme');
        document.documentElement.dataset.theme = savedTheme === 'dark' ? 'dark' : 'light';
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ request()->getBasePath() }}/css/space-admin.css?v={{ filemtime(public_path('css/space-admin.css')) }}">
</head>
<body>
<main class="auth-page">
    <section class="auth-visual" aria-label="Presentación de {{ config('app.name') }}">
        <a class="auth-brand" href="{{ route('home') }}">
            <span class="auth-brand-mark" aria-hidden="true">
                <img src="{{ request()->getBasePath() }}/images/nexus-logo.png" alt="Nexus" style="width: 24px; height: 24px; object-fit: contain; display: block;">
            </span>
            {{ config('app.name') }}
        </a>

        <div class="auth-message">
            <h2>Ideas que miran más allá.</h2>
            <p>El espacio de trabajo del equipo para investigar, construir y presentar nuestra solución para NASA Space Apps Challenge.</p>
        </div>

        <img src="{{ request()->getBasePath() }}/images/space-apps-logo.png" alt="NASA International Space Apps Challenge" style="position:relative;z-index:1;width:min(360px,70%);filter:grayscale(1) brightness(0) invert(1);">
    </section>

    <section class="auth-form-panel">
        <div class="auth-card">
            <a class="auth-mobile-brand" href="{{ route('home') }}">
                <span class="auth-brand-mark" aria-hidden="true" style="background: #ffffff; border: 1px solid rgba(0, 0, 0, 0.08);">
                    <img src="{{ request()->getBasePath() }}/images/nexus-logo.png" alt="Nexus" style="width: 24px; height: 24px; object-fit: contain; display: block;">
                </span>
                {{ config('app.name') }}
            </a>

            <h1>Iniciar sesión</h1>
            <p>Ingresa tus credenciales para acceder al espacio de trabajo.</p>

            @if ($errors->any())
                <div class="form-error" role="alert">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('login.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <div class="form-label-row">
                        <label class="form-label" for="email">Correo electrónico</label>
                    </div>
                    <input
                        class="form-control"
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        placeholder="nombre@ejemplo.com"
                        autocomplete="username"
                        maxlength="255"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <div class="form-label-row">
                        <label class="form-label" for="password">Contraseña</label>
                    </div>
                    <div class="form-control-wrap">
                        <input class="form-control has-action" id="password" name="password" type="password" placeholder="••••••••" autocomplete="current-password" required>
                        <button class="field-action" id="password-toggle" type="button" aria-label="Mostrar contraseña">
                            <i class="fa fa-eye" id="password-toggle-icon"></i>
                        </button>
                    </div>
                </div>

                <label class="checkbox-row">
                    <input name="remember" type="checkbox" value="1" @checked(old('remember'))>
                    Mantener la sesión iniciada
                </label>

                <button class="btn btn-primary btn-block" type="submit">
                    <i class="fa fa-sign-in" style="margin-right:8px;"></i>
                    Entrar
                </button>
            </form>

            <div class="auth-divider"><span>o continúa con</span></div>

            <a class="btn btn-outline btn-block" href="{{ route('auth.google.redirect') }}">
                <svg class="google-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#4285F4" d="M21.6 12.23c0-.71-.06-1.4-.18-2.07H12v3.92h5.38a4.6 4.6 0 0 1-2 3.02v2.54h3.24c1.9-1.75 2.98-4.33 2.98-7.41Z"/>
                    <path fill="#34A853" d="M12 22c2.7 0 4.98-.9 6.64-2.43l-3.24-2.54c-.9.6-2.05.96-3.4.96-2.61 0-4.82-1.76-5.61-4.13H3.04v2.62A10 10 0 0 0 12 22Z"/>
                    <path fill="#FBBC05" d="M6.39 13.86A6 6 0 0 1 6.08 12c0-.65.11-1.28.31-1.86V7.52H3.04A10 10 0 0 0 2 12c0 1.61.38 3.14 1.04 4.48l3.35-2.62Z"/>
                    <path fill="#EA4335" d="M12 6.01c1.47 0 2.79.51 3.83 1.5l2.87-2.88A9.63 9.63 0 0 0 12 2a10 10 0 0 0-8.96 5.52l3.35 2.62C7.18 7.77 9.39 6.01 12 6.01Z"/>
                </svg>
                Continuar con Google
            </a>

            {{-- Botones de autorrelleno para demostración de jueces --}}
            <div style="margin-top: 18px; padding: 12px; background: var(--muted); border: 1px dashed var(--border); border-radius: 8px; text-align: left;">
                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--foreground); margin-bottom: 6px;">
                    Demostración Rápida · Autocompletar Credencial:
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                    <button type="button" onclick="fillCreds('surgeon@spacevision.nasa', 'SpaceVision2026!')" style="font-size: 11px; padding: 5px 10px; border-radius: 4px; background: var(--foreground); color: var(--background); border: 1px solid var(--border); cursor: pointer; font-weight: 600;">
                        <i class="fa fa-user-md" style="margin-right:4px;"></i> Médico de Vuelo
                    </button>
                    <button type="button" onclick="fillCreds('researcher@spacevision.nasa', 'SpaceVision2026!')" style="font-size: 11px; padding: 5px 10px; border-radius: 4px; background: var(--foreground); color: var(--background); border: 1px solid var(--border); cursor: pointer; font-weight: 600;">
                        <i class="fa fa-flask" style="margin-right:4px;"></i> Investigador
                    </button>
                    <button type="button" onclick="fillCreds('admin@spacevision.nasa', 'SpaceVision2026!')" style="font-size: 11px; padding: 5px 10px; border-radius: 4px; background: var(--foreground); color: var(--background); border: 1px solid var(--border); cursor: pointer; font-weight: 600;">
                        <i class="fa fa-rocket" style="margin-right:4px;"></i> Director de Vuelo
                    </button>
                </div>
            </div>

            <div class="auth-footer"><a href="{{ route('home') }}">Volver al inicio</a></div>
        </div>
    </section>
</main>

<script>
    const password = document.getElementById('password');
    const toggle = document.getElementById('password-toggle');
    const toggleIcon = document.getElementById('password-toggle-icon');
    toggle.addEventListener('click', () => {
        const visible = password.type === 'text';
        password.type = visible ? 'password' : 'text';
        toggle.setAttribute('aria-label', visible ? 'Mostrar contraseña' : 'Ocultar contraseña');
        if (toggleIcon) {
            toggleIcon.className = visible ? 'fa fa-eye' : 'fa fa-eye-slash';
        }
    });

    function fillCreds(email, pass) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = pass;
    }
</script>
</body>
</html>
