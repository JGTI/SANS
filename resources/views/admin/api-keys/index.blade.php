@extends('layouts.app')

@section('title', 'SPACE VISION — Claves de APIs & Diagrama de Arquitectura')

@section('content')
<div class="page-header" style="margin-bottom:24px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px;">
        <div>
            <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--muted-foreground); margin-bottom:4px;">
                Configuración del Sistema & Infraestructura
            </div>
            <h1 style="font-size:24px; font-weight:800; letter-spacing:-0.03em; margin:0 0 6px; color:var(--foreground);">
                Gestión de Claves de APIs & Arquitectura
            </h1>
            <p style="font-size:13px; color:var(--muted-foreground); margin:0;">
                Administración de credenciales de servicios externos, tokens internos del motor científico y topología arquitectónica de SPACE VISION.
            </p>
        </div>

        <div style="display:flex; align-items:center; gap:10px;">
            <form action="{{ route('admin.api-keys.sync-env') }}" method="POST" style="margin:0;" onsubmit="return confirm('¿Sincronizar las claves activas con el archivo .env de SPACE VISION?');">
                @csrf
                <button type="submit" class="btn btn-outline" style="padding:7px 14px; font-size:12px;" title="Actualiza el archivo .env en el servidor con los valores de la base de datos">
                    <i class="fa fa-refresh" style="margin-right:4px;"></i> Sincronizar con .env
                </button>
            </form>
            <button type="button" class="btn btn-primary" onclick="document.getElementById('modal-create-key').style.display='flex';" style="padding:7px 14px; font-size:12px;">
                + Registrar Nueva Clave de API
            </button>
        </div>
    </div>
</div>

@if (session('status'))
    <div class="card" style="padding:12px 18px; margin-bottom:20px; border-left:4px solid var(--foreground); background:var(--muted);">
        <div style="display:flex; align-items:center; gap:10px;">
            <i class="fa fa-check" style="font-size:14px; color:#22c55e;"></i>
            <span style="font-size:13px; font-weight:600; color:var(--foreground);">{{ session('status') }}</span>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="card" style="padding:12px 18px; margin-bottom:20px; border-left:4px solid #ef4444; background:var(--muted);">
        <div style="display:flex; align-items:center; gap:10px;">
            <i class="fa fa-exclamation-triangle" style="font-size:14px; color:#ef4444;"></i>
            <span style="font-size:13px; font-weight:600; color:var(--foreground);">{{ session('error') }}</span>
        </div>
    </div>
@endif

{{-- Tarjetas de Diagnóstico de APIs Críticas --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px; margin-bottom:24px;">
    <div class="card" style="padding:18px; margin:0;">
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted-foreground); margin-bottom:6px;">API de NASA (OSDR/DONKI/APOD)</div>
        <div style="display:flex; align-items:center; gap:8px;">
            <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#22c55e;"></span>
            <span style="font-size:18px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">{{ $nasaStatus }}</span>
        </div>
        <div style="font-size:11px; color:var(--muted-foreground); margin-top:6px;">
            Endpoint: <code>api.nasa.gov</code>
        </div>
    </div>

    <div class="card" style="padding:18px; margin:0;">
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted-foreground); margin-bottom:6px;">Python FastAPI Scientific Engine</div>
        <div style="display:flex; align-items:center; gap:8px;">
            <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:{{ $scientificEngineStatus ? '#22c55e' : '#eab308' }};"></span>
            <span style="font-size:18px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">
                {{ $scientificEngineStatus ? 'ONLINE (:8001)' : 'LOCAL PHP FALLBACK' }}
            </span>
        </div>
        <div style="font-size:11px; color:var(--muted-foreground); margin-top:6px;">
            Autenticación: Bearer Token interno
        </div>
    </div>

    <div class="card" style="padding:18px; margin:0;">
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted-foreground); margin-bottom:6px;">Google Cloud OAuth 2.0</div>
        <div style="display:flex; align-items:center; gap:8px;">
            <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#22c55e;"></span>
            <span style="font-size:18px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">CONFIGURADO</span>
        </div>
        <div style="font-size:11px; color:var(--muted-foreground); margin-top:6px;">
            Autenticación federada de astronautas
        </div>
    </div>

    <div class="card" style="padding:18px; margin:0;">
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted-foreground); margin-bottom:6px;">Total Claves Gestionadas</div>
        <div style="font-size:24px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">
            {{ count($apiKeys) }} Llaves
        </div>
        <div style="font-size:11px; color:var(--muted-foreground); margin-top:4px;">
            {{ count($groupedKeys) }} Categorías del sistema
        </div>
    </div>
</div>

{{-- Inventario de Claves de API por Categoría --}}
<div style="display:flex; flex-direction:column; gap:24px; margin-bottom:36px;">
    @foreach ($groupedKeys as $category => $keys)
        <div class="card" style="padding:22px; margin:0;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:12px;">
                <div>
                    <h2 style="font-size:16px; font-weight:800; color:var(--foreground); margin:0 0 2px;">
                        {{ $category }}
                    </h2>
                    <span style="font-size:12px; color:var(--muted-foreground);">
                        {{ count($keys) }} clave(s) de integración en esta categoría
                    </span>
                </div>
            </div>

            <div style="display:flex; flex-direction:column; gap:16px;">
                @foreach ($keys as $key)
                    <div style="background:var(--muted); border:1px solid var(--border); border-radius:8px; padding:16px;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px; margin-bottom:8px;">
                            <div>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span style="font-family:ui-monospace, monospace; font-size:13px; font-weight:800; color:var(--foreground); background:#09090b; color:#ffffff; padding:2px 8px; border-radius:4px; border:1px solid #27272a;">
                                        {{ $key->key_name }}
                                    </span>
                                    <span style="font-size:13px; font-weight:700; color:var(--foreground);">
                                        {{ $key->service_label }}
                                    </span>
                                    <span style="font-size:10px; font-weight:700; text-transform:uppercase; padding:2px 6px; border-radius:4px; border:1px solid var(--border); background:var(--background); color:var(--muted-foreground);">
                                        {{ $key->status }}
                                    </span>
                                </div>
                                <p style="font-size:12px; color:var(--muted-foreground); margin:6px 0 0; line-height:1.4;">
                                    {{ $key->description }}
                                </p>
                            </div>

                            <div style="display:flex; align-items:center; gap:8px;">
                                <button type="button" class="btn btn-secondary" onclick="testApiKey({{ $key->id }}, this)" style="padding:4px 10px; font-size:11px;">
                                    <i class="fa fa-bolt" style="margin-right:4px;"></i> Probar Conexión
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="openEditModal({{ $key->id }}, '{{ addslashes($key->key_name) }}', '{{ addslashes($key->service_label) }}', '{{ addslashes($key->key_value) }}', '{{ addslashes($key->category) }}', '{{ addslashes($key->endpoint_url) }}', '{{ addslashes($key->description) }}', '{{ $key->status }}', {{ $key->is_secret ? 'true' : 'false' }})" style="padding:4px 10px; font-size:11px;">
                                    <i class="fa fa-pencil" style="margin-right:4px;"></i> Modificar
                                </button>
                                @if (!in_array($key->key_name, ['NASA_API_KEY', 'SCIENTIFIC_ENGINE_TOKEN', 'SCIENTIFIC_ENGINE_URL', 'GOOGLE_CLIENT_ID']))
                                    <form action="{{ route('admin.api-keys.destroy', $key->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas eliminar esta clave?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary" style="padding:4px 8px; font-size:11px; color:#ef4444;" title="Eliminar clave">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        {{-- Fila con el Valor Enmascarado y Endpoint --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:10px; padding-top:10px; border-top:1px dashed var(--border);">
                            <div>
                                <span style="font-size:10px; text-transform:uppercase; font-weight:700; color:var(--muted-foreground); display:block; margin-bottom:2px;">
                                    Valor de la Clave:
                                </span>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <input type="{{ $key->is_secret ? 'password' : 'text' }}" value="{{ $key->key_value }}" readonly class="form-control" id="input-val-{{ $key->id }}" style="font-family:ui-monospace, monospace; font-size:12px; height:30px; background:var(--background);" />
                                    @if ($key->is_secret)
                                        <button type="button" class="btn btn-secondary" onclick="togglePasswordVisibility('input-val-{{ $key->id }}', this)" style="padding:2px 8px; font-size:11px; height:30px;" title="Mostrar / Ocultar">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <span style="font-size:10px; text-transform:uppercase; font-weight:700; color:var(--muted-foreground); display:block; margin-bottom:2px;">
                                    Endpoint / Servicio Destino:
                                </span>
                                <div style="font-family:ui-monospace, monospace; font-size:12px; color:var(--foreground); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; padding:5px 0;">
                                    @if ($key->endpoint_url)
                                        <a href="{{ $key->endpoint_url }}" target="_blank" rel="noopener noreferrer" style="color:var(--foreground); text-decoration:underline;">
                                            {{ $key->endpoint_url }} <i class="fa fa-external-link" style="font-size:10px;"></i>
                                        </a>
                                    @else
                                        <span style="color:var(--muted-foreground);">— Sin endpoint directo</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div id="ping-result-{{ $key->id }}" style="display:none; margin-top:8px; font-size:11px; padding:6px 10px; border-radius:4px; font-family:ui-monospace, monospace;"></div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

{{-- SECCIÓN 2: DIAGRAMA DE ARQUITECTURA INTERACTIVO --}}
<div class="card" style="padding:26px; margin:0;" id="arquitectura">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:14px;">
        <div>
            <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--muted-foreground); margin-bottom:4px;">
                Topología y Flujo de Datos
            </div>
            <h2 style="font-size:20px; font-weight:800; color:var(--foreground); margin:0 0 4px;">
                Diagrama de Arquitectura de SPACE VISION
            </h2>
            <p style="font-size:13px; color:var(--muted-foreground); margin:0;">
                Interconexión entre la Estación Terrestre (Frontend), Orquestador Laravel 11, Microservicio Científico Python/FastAPI y APIs de NASA.
            </p>
        </div>

        <div style="display:flex; gap:8px;">
            <button type="button" class="btn btn-secondary" onclick="window.print()" style="font-size:11px; padding:6px 12px;">
                <i class="fa fa-print" style="margin-right:4px;"></i> Imprimir Diagrama
            </button>
        </div>
    </div>

    {{-- Diagrama Visual en SVG Monocromático de Alta Fidelidad --}}
    <div style="background:#09090b; border:1px solid #27272a; border-radius:10px; padding:24px; overflow-x:auto;">
        <svg viewBox="0 0 1100 680" style="width:100%; min-width:960px; height:auto; font-family:system-ui, -apple-system, sans-serif;">
            <defs>
                <marker id="arrow" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                    <path d="M 0 1 L 10 5 L 0 9 z" fill="#ffffff"/>
                </marker>
                <marker id="arrow-muted" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                    <path d="M 0 1 L 10 5 L 0 9 z" fill="#71717a"/>
                </marker>
            </defs>

            <!-- 1. CAPA CLIENTE / ESTACIÓN DE TIERRA -->
            <rect x="40" y="40" width="1020" height="90" rx="8" fill="#18181b" stroke="#3f3f46" stroke-width="1.5"/>
            <text x="60" y="70" fill="#ffffff" font-size="14" font-weight="800">1. CLIENTE & ESTACIÓN DE TIERRA (GROUND CONTROL / MISSION OPERATORS)</text>
            <text x="60" y="92" fill="#a1a1aa" font-size="12">Interfaz de Usuario Monocromática (AdminCN / Shadcn) · Three.js 3D WebGL (Gemelo Digital) · Chart.js Telemetría · Retícula HUD</text>
            
            <rect x="740" y="58" width="140" height="32" rx="4" fill="#09090b" stroke="#52525b" stroke-width="1"/>
            <text x="810" y="79" fill="#ffffff" font-size="11" font-weight="700" text-anchor="middle">WebGL Three.js 3D</text>

            <rect x="895" y="58" width="145" height="32" rx="4" fill="#09090b" stroke="#52525b" stroke-width="1"/>
            <text x="967" y="79" fill="#ffffff" font-size="11" font-weight="700" text-anchor="middle">Blade + Chart.js</text>

            <!-- FLECHA CAPA 1 -> CAPA 2 -->
            <line x1="550" y1="130" x2="550" y2="180" stroke="#ffffff" stroke-width="2" marker-end="url(#arrow)"/>
            <text x="560" y="160" fill="#a1a1aa" font-size="11" font-family="ui-monospace, monospace">HTTPS / JSON REST / Blade Views</text>

            <!-- 2. CAPA LARAVEL / BACKEND PRINCIPAL -->
            <rect x="40" y="180" width="1020" height="150" rx="8" fill="#18181b" stroke="#52525b" stroke-width="2"/>
            <text x="60" y="210" fill="#ffffff" font-size="15" font-weight="800">2. NÚCLEO ORQUESTADOR: LARAVEL 11 (PHP 8.3) / NEXUS PLATFORM</text>
            <text x="60" y="232" fill="#a1a1aa" font-size="12">Autenticación Híbrida · Spatie RBAC · VisionLabController · DigitalTwinController · MaintenanceController · Audit Logging</text>

            <!-- Submódulos Laravel -->
            <g transform="translate(60, 245)">
                <rect x="0" y="0" width="220" height="65" rx="6" fill="#09090b" stroke="#3f3f46" stroke-width="1"/>
                <text x="15" y="25" fill="#ffffff" font-size="12" font-weight="700">Seguridad & Sesiones</text>
                <text x="15" y="45" fill="#a1a1aa" font-size="10">Google OAuth / Bcrypt</text>

                <rect x="240" y="0" width="220" height="65" rx="6" fill="#09090b" stroke="#3f3f46" stroke-width="1"/>
                <text x="255" y="25" fill="#ffffff" font-size="12" font-weight="700">Ophthalmology Calculator</text>
                <text x="255" y="45" fill="#a1a1aa" font-size="10">SE = SPH + CYL/2 · LogMAR</text>

                <rect x="480" y="0" width="230" height="65" rx="6" fill="#09090b" stroke="#3f3f46" stroke-width="1"/>
                <text x="495" y="25" fill="#ffffff" font-size="12" font-weight="700">ScientificEngineService</text>
                <text x="495" y="45" fill="#a1a1aa" font-size="10">HTTP Client & Fallback Local</text>

                <rect x="730" y="0" width="230" height="65" rx="6" fill="#09090b" stroke="#3f3f46" stroke-width="1"/>
                <text x="745" y="25" fill="#ffffff" font-size="12" font-weight="700">MariaDB & Redis Queues</text>
                <text x="745" y="45" fill="#a1a1aa" font-size="10">Perfiles, Exámenes y SANS</text>
            </g>

            <!-- FLECHAS CAPA 2 -> EXTERNAS & PYTHON -->
            <!-- Hacia la izquierda: APIs Externas -->
            <path d="M 170 330 L 170 410" stroke="#71717a" stroke-width="1.5" marker-end="url(#arrow-muted)"/>
            <text x="80" y="375" fill="#71717a" font-size="10" font-family="ui-monospace, monospace">Google OAuth / Socialite</text>

            <!-- Hacia el centro: Python FastAPI -->
            <line x1="590" y1="330" x2="590" y2="410" stroke="#ffffff" stroke-width="2.5" marker-end="url(#arrow)"/>
            <text x="600" y="375" fill="#ffffff" font-size="11" font-weight="700" font-family="ui-monospace, monospace">Bearer Token Auth (:8001)</text>

            <!-- Hacia la derecha: APIs NASA -->
            <path d="M 850 330 L 850 410" stroke="#71717a" stroke-width="1.5" marker-end="url(#arrow-muted)"/>
            <text x="860" y="375" fill="#71717a" font-size="10" font-family="ui-monospace, monospace">NASA_API_KEY / HTTPS</text>

            <!-- 3. CAPA DE SERVICIOS INFERIORES: EXTERN + PYTHON + NASA -->
            <!-- 3A: Google Cloud OAuth -->
            <g transform="translate(40, 410)">
                <rect x="0" y="0" width="240" height="230" rx="8" fill="#18181b" stroke="#3f3f46" stroke-width="1.5"/>
                <text x="20" y="30" fill="#ffffff" font-size="13" font-weight="800">AUTENTICACIÓN EXTERNA</text>
                <text x="20" y="50" fill="#a1a1aa" font-size="11">Google Cloud Identity</text>

                <rect x="15" y="70" width="210" height="40" rx="4" fill="#09090b" stroke="#27272a"/>
                <text x="25" y="95" fill="#ffffff" font-size="11" font-family="ui-monospace, monospace">GOOGLE_CLIENT_ID</text>

                <rect x="15" y="120" width="210" height="40" rx="4" fill="#09090b" stroke="#27272a"/>
                <text x="25" y="145" fill="#ffffff" font-size="11" font-family="ui-monospace, monospace">GOOGLE_CLIENT_SECRET</text>

                <text x="20" y="190" fill="#a1a1aa" font-size="10">SSO para Investigadores y Médicos</text>
            </g>

            <!-- 3B: PYTHON SCIENTIFIC ENGINE (CENTRO) -->
            <g transform="translate(300, 410)">
                <rect x="0" y="0" width="500" height="230" rx="8" fill="#18181b" stroke="#ffffff" stroke-width="2"/>
                <text x="25" y="30" fill="#ffffff" font-size="14" font-weight="800">3. PYTHON SCIENTIFIC ENGINE (FASTAPI :8001)</text>
                <text x="25" y="50" fill="#a1a1aa" font-size="11">NumPy 2.5 · SciPy 1.18 · Scikit-learn 1.9 · Pandas 3.1</text>

                <g transform="translate(20, 65)">
                    <rect x="0" y="0" width="220" height="45" rx="4" fill="#09090b" stroke="#3f3f46"/>
                    <text x="12" y="20" fill="#ffffff" font-size="11" font-weight="700">Random Forest & GBM</text>
                    <text x="12" y="36" fill="#a1a1aa" font-size="10">Clasificador SANS k=5</text>

                    <rect x="235" y="0" width="220" height="45" rx="4" fill="#09090b" stroke="#3f3f46"/>
                    <text x="247" y="20" fill="#ffffff" font-size="11" font-weight="700">Isolation Forest & LOF</text>
                    <text x="247" y="36" fill="#a1a1aa" font-size="10">Detección de Anomalías</text>

                    <rect x="0" y="55" width="220" height="45" rx="4" fill="#09090b" stroke="#3f3f46"/>
                    <text x="12" y="75" fill="#ffffff" font-size="11" font-weight="700">Explainable AI (XAI)</text>
                    <text x="12" y="91" fill="#a1a1aa" font-size="10">Pesos SHAP y Atribución</text>

                    <rect x="235" y="55" width="220" height="45" rx="4" fill="#09090b" stroke="#3f3f46"/>
                    <text x="247" y="75" fill="#ffffff" font-size="11" font-weight="700">Longitudinal OLS & 95% CI</text>
                    <text x="247" y="91" fill="#a1a1aa" font-size="10">Proyección temporal FD180</text>

                    <rect x="0" y="110" width="455" height="35" rx="4" fill="#09090b" stroke="#3f3f46"/>
                    <text x="15" y="132" fill="#ffffff" font-size="11" font-family="ui-monospace, monospace">Token: SCIENTIFIC_ENGINE_TOKEN · Puertos: 8001</text>
                </g>
            </g>

            <!-- 3C: NASA DATA REPOSITORIES (DERECHA) -->
            <g transform="translate(820, 410)">
                <rect x="0" y="0" width="240" height="230" rx="8" fill="#18181b" stroke="#3f3f46" stroke-width="1.5"/>
                <text x="20" y="30" fill="#ffffff" font-size="13" font-weight="800">REPOSITORIOS DE NASA</text>
                <text x="20" y="50" fill="#a1a1aa" font-size="11">Datos Oficiales & Telemetría</text>

                <rect x="15" y="65" width="210" height="34" rx="4" fill="#09090b" stroke="#27272a"/>
                <text x="25" y="87" fill="#ffffff" font-size="11">NASA OSDR (Biología)</text>

                <rect x="15" y="105" width="210" height="34" rx="4" fill="#09090b" stroke="#27272a"/>
                <text x="25" y="127" fill="#ffffff" font-size="11">NASA NTRS (Evidencia HRP)</text>

                <rect x="15" y="145" width="210" height="34" rx="4" fill="#09090b" stroke="#27272a"/>
                <text x="25" y="167" fill="#ffffff" font-size="11">NASA DONKI (Clima Espacial)</text>

                <text x="20" y="205" fill="#a1a1aa" font-size="10" font-family="ui-monospace, monospace">Key: NASA_API_KEY</text>
            </g>
        </svg>
    </div>

    <div style="margin-top:16px; font-size:12px; color:var(--muted-foreground); line-height:1.6;">
        <strong>Notas de Implementación Arquitectónica:</strong> El diagrama refleja la segregación estricta entre el servicio orquestador Laravel (responsable de la gobernanza de usuarios, sesiones y persistencia relacional) y el microservicio científico Python (especializado en álgebra lineal, descomposición vectorial de Thibos, simulación biomecánica y aprendizaje automático). El flujo de autenticación interna mediante Bearer Token protege la superficie de ataque del cluster.
    </div>
</div>

{{-- MODAL: CREAR NUEVA CLAVE DE API --}}
<div id="modal-create-key" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center; padding:20px;">
    <div class="card" style="width:100%; max-width:540px; padding:24px; margin:0; background:var(--background); border:1px solid var(--border);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="font-size:16px; font-weight:800; color:var(--foreground); margin:0;">
                Registrar Nueva Clave de API
            </h3>
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-create-key').style.display='none';" style="padding:2px 8px;"><i class="fa fa-times"></i></button>
        </div>

        <form action="{{ route('admin.api-keys.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:12px;">
                <label style="font-size:11px; font-weight:700; color:var(--foreground); display:block; margin-bottom:4px;">
                    Nombre de Variable (ej: NASA_API_KEY, GEMINI_API_KEY):
                </label>
                <input type="text" name="key_name" required pattern="[A-Z0-9_]+" placeholder="NOMBRE_VARIABLE" class="form-control" style="font-family:ui-monospace, monospace; font-size:12px;">
            </div>

            <div style="margin-bottom:12px;">
                <label style="font-size:11px; font-weight:700; color:var(--foreground); display:block; margin-bottom:4px;">
                    Etiqueta de Servicio:
                </label>
                <input type="text" name="service_label" required placeholder="ej: OpenAI Vision API" class="form-control" style="font-size:12px;">
            </div>

            <div style="margin-bottom:12px;">
                <label style="font-size:11px; font-weight:700; color:var(--foreground); display:block; margin-bottom:4px;">
                    Categoría:
                </label>
                <select name="category" class="form-control" style="font-size:12px;">
                    <option value="Aeroespacial & Telemetría">Aeroespacial & Telemetría</option>
                    <option value="Motor Científico Python">Motor Científico Python</option>
                    <option value="Autenticación & Seguridad">Autenticación & Seguridad</option>
                    <option value="Inteligencia Artificial">Inteligencia Artificial</option>
                    <option value="General">General</option>
                </select>
            </div>

            <div style="margin-bottom:12px;">
                <label style="font-size:11px; font-weight:700; color:var(--foreground); display:block; margin-bottom:4px;">
                    Valor de la Clave / Token:
                </label>
                <input type="text" name="key_value" placeholder="Pega aquí la clave secreta o token..." class="form-control" style="font-family:ui-monospace, monospace; font-size:12px;">
            </div>

            <div style="margin-bottom:12px;">
                <label style="font-size:11px; font-weight:700; color:var(--foreground); display:block; margin-bottom:4px;">
                    Endpoint URL (Opcional):
                </label>
                <input type="url" name="endpoint_url" placeholder="https://api.servicio.com/v1" class="form-control" style="font-size:12px;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="font-size:11px; font-weight:700; color:var(--foreground); display:block; margin-bottom:4px;">
                    Descripción y Uso:
                </label>
                <textarea name="description" rows="2" placeholder="Propósito de la clave..." class="form-control" style="font-size:12px;"></textarea>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center;">
                <label style="display:flex; align-items:center; gap:6px; font-size:12px; color:var(--foreground); cursor:pointer;">
                    <input type="checkbox" name="is_secret" value="1" checked> Es clave secreta (enmascarar valor)
                </label>
                <button type="submit" class="btn btn-primary" style="padding:6px 16px; font-size:12px;">
                    Guardar Clave
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: EDITAR CLAVE EXISTENTE --}}
<div id="modal-edit-key" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center; padding:20px;">
    <div class="card" style="width:100%; max-width:540px; padding:24px; margin:0; background:var(--background); border:1px solid var(--border);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="font-size:16px; font-weight:800; color:var(--foreground); margin:0;" id="edit-title">
                Modificar Clave de API
            </h3>
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-edit-key').style.display='none';" style="padding:2px 8px;"><i class="fa fa-times"></i></button>
        </div>

        <form id="form-edit-key" action="" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom:12px;">
                <label style="font-size:11px; font-weight:700; color:var(--foreground); display:block; margin-bottom:4px;">
                    Nombre de Variable (Inmutable):
                </label>
                <input type="text" id="edit-key-name" readonly class="form-control" style="font-family:ui-monospace, monospace; font-size:12px; background:var(--muted);">
            </div>

            <div style="margin-bottom:12px;">
                <label style="font-size:11px; font-weight:700; color:var(--foreground); display:block; margin-bottom:4px;">
                    Etiqueta de Servicio:
                </label>
                <input type="text" name="service_label" id="edit-service-label" required class="form-control" style="font-size:12px;">
            </div>

            <div style="margin-bottom:12px;">
                <label style="font-size:11px; font-weight:700; color:var(--foreground); display:block; margin-bottom:4px;">
                    Categoría:
                </label>
                <select name="category" id="edit-category" class="form-control" style="font-size:12px;">
                    <option value="Aeroespacial & Telemetría">Aeroespacial & Telemetría</option>
                    <option value="Motor Científico Python">Motor Científico Python</option>
                    <option value="Autenticación & Seguridad">Autenticación & Seguridad</option>
                    <option value="Inteligencia Artificial">Inteligencia Artificial</option>
                    <option value="General">General</option>
                </select>
            </div>

            <div style="margin-bottom:12px;">
                <label style="font-size:11px; font-weight:700; color:var(--foreground); display:block; margin-bottom:4px;">
                    Valor de la Clave:
                </label>
                <input type="text" name="key_value" id="edit-key-value" class="form-control" style="font-family:ui-monospace, monospace; font-size:12px;">
            </div>

            <div style="margin-bottom:12px;">
                <label style="font-size:11px; font-weight:700; color:var(--foreground); display:block; margin-bottom:4px;">
                    Estado:
                </label>
                <select name="status" id="edit-status" class="form-control" style="font-size:12px;">
                    <option value="ACTIVE">ACTIVE (Habilitada y en uso)</option>
                    <option value="OPTIONAL">OPTIONAL (Opcional / Respaldo)</option>
                    <option value="DISABLED">DISABLED (Deshabilitada)</option>
                </select>
            </div>

            <div style="margin-bottom:12px;">
                <label style="font-size:11px; font-weight:700; color:var(--foreground); display:block; margin-bottom:4px;">
                    Endpoint URL:
                </label>
                <input type="url" name="endpoint_url" id="edit-endpoint-url" class="form-control" style="font-size:12px;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="font-size:11px; font-weight:700; color:var(--foreground); display:block; margin-bottom:4px;">
                    Descripción:
                </label>
                <textarea name="description" id="edit-description" rows="2" class="form-control" style="font-size:12px;"></textarea>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center;">
                <label style="display:flex; align-items:center; gap:6px; font-size:12px; color:var(--foreground); cursor:pointer;">
                    <input type="checkbox" name="is_secret" id="edit-is-secret" value="1"> Ocultar valor en vistas
                </label>
                <button type="submit" class="btn btn-primary" style="padding:6px 16px; font-size:12px;">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '<i class="fa fa-eye-slash"></i>';
    } else {
        input.type = 'password';
        btn.innerHTML = '<i class="fa fa-eye"></i>';
    }
}

function openEditModal(id, name, label, val, cat, endpoint, desc, status, isSecret) {
    const form = document.getElementById('form-edit-key');
    form.action = "{{ url('admin/api-keys') }}/" + id;
    document.getElementById('edit-title').innerText = "Modificar: " + name;
    document.getElementById('edit-key-name').value = name;
    document.getElementById('edit-service-label').value = label;
    document.getElementById('edit-key-value').value = val;
    document.getElementById('edit-category').value = cat;
    document.getElementById('edit-endpoint-url').value = endpoint || '';
    document.getElementById('edit-description').value = desc || '';
    document.getElementById('edit-status').value = status;
    document.getElementById('edit-is-secret').checked = isSecret;
    document.getElementById('modal-edit-key').style.display = 'flex';
}

function testApiKey(id, btn) {
    const originalText = btn.innerText;
    btn.disabled = true;
    btn.innerText = '⏳ Probando...';
    const resBox = document.getElementById('ping-result-' + id);
    resBox.style.display = 'block';
    resBox.style.background = '#18181b';
    resBox.style.color = '#a1a1aa';
    resBox.innerText = 'Enviando solicitud de prueba...';

    fetch("{{ url('admin/api-keys') }}/" + id + "/ping", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerText = originalText;
        if (data.success) {
            resBox.style.background = '#09090b';
            resBox.style.border = '1px solid #22c55e';
            resBox.style.color = '#22c55e';
            resBox.innerHTML = '<i class="fa fa-check" style="margin-right:4px;"></i> ' + data.message;
        } else {
            resBox.style.background = '#09090b';
            resBox.style.border = '1px solid #ef4444';
            resBox.style.color = '#ef4444';
            resBox.innerHTML = '<i class="fa fa-times" style="margin-right:4px;"></i> ' + data.message;
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerText = originalText;
        resBox.style.background = '#09090b';
        resBox.style.border = '1px solid #ef4444';
        resBox.style.color = '#ef4444';
        resBox.innerText = 'Error al ejecutar prueba de red: ' + err.message;
    });
}
</script>
@endsection
