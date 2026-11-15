<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#030712">
    <title>SPACE VISION · Nexus × NASA Space Apps Challenge 2026</title>
    <link rel="icon" type="image/png" href="{{ request()->getBasePath() }}/images/letra_C_fondo_transparente.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        :root {
            color-scheme: dark;
            --primary: #38bdf8;
            --primary-glow: rgba(56, 189, 248, 0.25);
            --bg: #030712;
            --card-bg: #0b1329;
            --border: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 32px 20px 60px;
            color: var(--text-main);
            background: radial-gradient(circle at 50% 10%, #0d1a3a 0%, #030712 60%);
            display: flex;
            flex-direction: column;
            align-items: center;
            line-height: 1.6;
        }

        main {
            width: min(1040px, 100%);
            margin: 0 auto;
            text-align: center;
        }

        .logos {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: clamp(24px, 5vw, 56px);
            margin-bottom: 28px;
        }

        .logo-box {
            display: grid;
            place-items: center;
            height: 120px;
        }

        .logo-box.nexus {
            width: 140px;
        }

        .logo-box.space-apps {
            width: min(340px, 52vw);
        }

        .logo-box img {
            display: block;
            max-width: 100%;
            max-height: 90px;
            object-fit: contain;
        }

        .space-apps img {
            width: 100%;
            filter: grayscale(1) brightness(0) invert(1);
        }

        .nexus img {
            filter: grayscale(1) brightness(0) invert(1);
        }

        .badge-challenge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(56, 189, 248, 0.12);
            border: 1px solid rgba(56, 189, 248, 0.3);
            color: #38bdf8;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        h1 {
            font-size: clamp(2.2rem, 5vw, 3.8rem);
            font-weight: 900;
            letter-spacing: -.03em;
            margin: 0 0 12px;
            background: linear-gradient(180deg, #ffffff 30%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .lead-text {
            font-size: clamp(1rem, 2vw, 1.25rem);
            color: var(--text-muted);
            max-width: 780px;
            margin: 0 auto 32px;
        }

        .hero-actions {
            display: flex;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 48px;
        }

        .btn-main {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 26px;
            background: #38bdf8;
            color: #030712;
            font-weight: 800;
            font-size: 14px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 0 20px var(--primary-glow);
        }

        .btn-main:hover {
            background: #7dd3fc;
            transform: translateY(-2px);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #0f172a;
            color: #f1f5f9;
            font-weight: 700;
            font-size: 14px;
            border-radius: 8px;
            text-decoration: none;
            border: 1px solid var(--border);
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            border-color: #38bdf8;
            color: #38bdf8;
        }

        .systems-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            text-align: left;
            margin-bottom: 40px;
        }

        .system-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 22px;
            transition: transform 0.2s, border-color 0.2s;
        }

        .system-card:hover {
            border-color: #38bdf8;
            transform: translateY(-3px);
        }

        .system-card h3 {
            font-size: 16px;
            margin: 0 0 8px;
            color: #f8fafc;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .system-card p {
            font-size: 13px;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.5;
        }

        .demo-credentials-banner {
            background: #0f172a;
            border: 1px dashed #334155;
            border-radius: 10px;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            text-align: left;
            font-size: 13px;
        }

        .cred-badge {
            font-family: monospace;
            background: #1e293b;
            color: #38bdf8;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <main>
        {{-- Logos requeridos para verificación de identidad y tests --}}
        <div class="logos" aria-label="Nexus y NASA Space Apps Challenge">
            <div class="logo-box nexus">
                <img src="{{ request()->getBasePath() }}/images/nexus-logo.png" alt="Nexus Digital DB">
            </div>
            <div class="logo-box space-apps">
                <img src="{{ request()->getBasePath() }}/images/space-apps-logo.png" alt="NASA International Space Apps Challenge">
            </div>
        </div>

        <div class="badge-challenge">
            <i class="fa fa-rocket" style="margin-right:6px;"></i> NASA Space Apps Challenge 2026 · Querétaro, México
        </div>

        <h1>Nexus × NASA Space Apps Challenge</h1>
        <div style="font-size: clamp(1.4rem, 3vw, 2.2rem); font-weight: 800; color: #38bdf8; margin-bottom: 12px;">
            SPACE VISION
        </div>

        <p class="lead-text">
            Plataforma aeroespacial de neurooftalmología, telemetría visual y simulación en microgravedad para el monitoreo y comprensión del síndrome <strong>SANS</strong> en misiones de exploración profunda (Artemis y Marte).
        </p>

        <div class="hero-actions">
            <a class="btn-main" href="{{ route('admin.sans-monitor.index', ['tab' => 'biomarcadores']) }}">
                Explore the Demo · Launch SPACE VISION (No Login Required) &rarr;
            </a>
            <a class="btn-secondary" href="{{ route('admin.sans-monitor.index', ['tab' => 'seguimiento']) }}">
                <i class="fa fa-calendar-check-o" style="margin-right:6px;"></i> Timeline (+30 / +60 / +90 Days)
            </a>
            <a class="btn-secondary" href="{{ route('admin.sans-monitor.index', ['tab' => 'escenarios']) }}">
                <i class="fa fa-cubes" style="margin-right:6px;"></i> 3 Scenario Models (Stable / Worsens / Unscanned)
            </a>
        </div>

        {{-- Las 3 Pestañas Centrales del Predictor SANS --}}
        <div class="systems-grid">
            <div class="system-card">
                <h3><i class="fa fa-heartbeat" style="margin-right:8px; color:#38bdf8;"></i> 1. Predictor de 6 Biomarcadores</h3>
                <p>Formulario inicial que evalúa espesor medio por OCT, longitud axial del ojo, hipermetropía simulada (&Delta;SE), edema papilar (Frisén), presión intraocular (PIO) y exposición previa a inclinación cefálica de -6° en la Tierra.</p>
            </div>
            <div class="system-card">
                <h3><i class="fa fa-calendar-check-o" style="margin-right:8px; color:#38bdf8;"></i> 2. Antes · Durante · Después (+30/+60/+90)</h3>
                <p>Línea de tiempo longitudinal que mide el ojo antes del vuelo, durante la misión (días 10, 15, 30) y hace el corte posvuelo a los +30, +60 y +90 días.</p>
            </div>
            <div class="system-card">
                <h3><i class="fa fa-cubes" style="margin-right:8px; color:#38bdf8;"></i> 3. Escenarios: Estable · Empeora · Sin Escaneo</h3>
                <p>Comparación interactiva de los tres modelos fundamentales: un caso estable, un caso que empeora y un caso que no se escanea, junto con el visor anatómico 3D y OCT.</p>
            </div>
        </div>

        {{-- Acceso Público Inmediato sin Login + Credenciales Opcionales de Staff --}}
        <div class="demo-credentials-banner">
            <div>
                <strong style="color:#f8fafc;">Public Interactive Demo — Zero Login Required:</strong>
                <div style="color:var(--text-muted); font-size:12px; margin-top:2px;">
                    Cualquier juez o visitante puede ejecutar la demostración completa sin iniciar sesión en <span class="cred-badge">/demo</span> o <span class="cred-badge">/sans-monitor</span>. (Acceso administrativo opcional: <span class="cred-badge">surgeon@spacevision.nasa</span> / <span class="cred-badge">SpaceVision2026!</span>)
                </div>
                <div style="color:var(--text-muted); font-size:11px; margin-top:6px;">
                    Developed by NEXUS × Visual Rehab Center for the NASA International Space Apps Challenge 2026.
                </div>
            </div>
            <div>
                <a href="{{ route('admin.sans-monitor.index') }}" style="color:#38bdf8; font-weight:700; text-decoration:none;">Launch Public Demo &rarr;</a>
            </div>
        </div>
    </main>
</body>
</html>
