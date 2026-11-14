@extends('layouts.app')

@section('title', 'Astronaut Digital Twin · Gemelo Digital Neurooftalmológico 3D')

@push('styles')
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
<style>
    .twin-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) minmax(340px, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    @media (max-width: 1024px) {
        .twin-layout { grid-template-columns: 1fr; }
    }
    .viewport-card {
        background: #05070d;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
    }
    .viewport-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 18px;
        background: #090c15;
        border-bottom: 1px solid #1a2234;
        color: #e2e8f0;
        font-size: 12px;
        z-index: 10;
    }
    .viewport-hud {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 5;
    }
    .hud-reticle {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 120px;
        height: 120px;
        border: 1px dashed rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        pointer-events: none;
    }
    .hud-reticle::before,
    .hud-reticle::after {
        content: '';
        position: absolute;
        background: rgba(255, 255, 255, 0.3);
    }
    .hud-reticle::before {
        top: 50%;
        left: -10px;
        right: -10px;
        height: 1px;
    }
    .hud-reticle::after {
        left: 50%;
        top: -10px;
        bottom: -10px;
        width: 1px;
    }
    .hud-coords {
        position: absolute;
        bottom: 14px;
        left: 18px;
        font-family: ui-monospace, monospace;
        font-size: 11px;
        color: #94a3b8;
        line-height: 1.4;
        background: rgba(5, 7, 13, 0.75);
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid #1a2234;
        backdrop-filter: blur(4px);
    }
    .hud-scan-beam {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
        opacity: 0.4;
        animation: scanAnim 4s linear infinite;
    }
    @keyframes scanAnim {
        0% { top: 0; opacity: 0; }
        15% { opacity: 0.6; }
        85% { opacity: 0.6; }
        100% { top: 100%; opacity: 0; }
    }
    #threeCanvasContainer {
        width: 100%;
        height: 480px;
        position: relative;
        cursor: grab;
    }
    #threeCanvasContainer:active {
        cursor: grabbing;
    }
    .viewport-controls {
        position: absolute;
        top: 54px;
        right: 14px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        z-index: 10;
    }
    .view-btn {
        background: rgba(9, 12, 21, 0.85);
        color: #f1f5f9;
        border: 1px solid #1e293b;
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        backdrop-filter: blur(4px);
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-align: left;
    }
    .view-btn:hover {
        background: #1e293b;
        border-color: #475569;
    }
    .view-btn.active {
        background: #ffffff;
        color: #09090b;
        border-color: #ffffff;
    }
    .timeline-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 18px 22px;
        margin-bottom: 22px;
        box-shadow: var(--shadow);
    }
    .slider-rail {
        width: 100%;
        accent-color: var(--foreground);
        height: 8px;
        border-radius: 4px;
        cursor: pointer;
    }
    .gauge-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    .gauge-cell {
        background: var(--background);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 12px 14px;
    }
    .gauge-label {
        font-size: 11px;
        text-transform: uppercase;
        color: var(--muted-foreground);
        font-weight: 700;
        letter-spacing: .05em;
    }
    .gauge-val {
        font-size: 20px;
        font-weight: 800;
        font-family: ui-monospace, monospace;
        color: var(--foreground);
        margin-top: 4px;
    }
    .oct-scan-box {
        background: #09090b;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 14px;
        margin-top: 14px;
    }

    /* ZEISS Cirrus HD-OCT Workstation Styles (Clinical Mode) */
    .oct-workstation {
        background: #09090b;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.5);
        color: #f4f4f5;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    }
    .oct-topbar {
        background: #121215;
        border-bottom: 1px solid #27272a;
        padding: 10px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        font-size: 12px;
    }
    .oct-brand {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .oct-brand-logo {
        background: #ffffff;
        color: #000000;
        font-weight: 900;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 11px;
        letter-spacing: 0.1em;
    }
    .oct-grid-container {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        padding: 16px;
        background: #09090b;
    }
    @media (max-width: 1100px) {
        .oct-grid-container {
            grid-template-columns: 1fr;
        }
    }
    .oct-panel {
        background: #101014;
        border: 1px solid #27272a;
        border-radius: 6px;
        padding: 12px;
        position: relative;
    }
    .oct-panel-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #a1a1aa;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #1e1e24;
        padding-bottom: 4px;
    }
    .etdrs-circle-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 10px 0;
    }
    .oct-table {
        width: 100%;
        font-size: 11px;
        border-collapse: collapse;
    }
    .oct-table th {
        color: #71717a;
        text-align: left;
        padding: 6px 8px;
        border-bottom: 1px solid #27272a;
    }
    .oct-table td {
        padding: 7px 8px;
        border-bottom: 1px solid #1e1e24;
    }
    .etdrs-sector {
        transition: fill 0.25s ease;
        cursor: pointer;
    }
    .etdrs-text {
        font-size: 9px;
        font-weight: 700;
        fill: #ffffff;
        text-anchor: middle;
        dominant-baseline: central;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div>
        <div class="eyebrow">NASA HUMAN RESEARCH PROGRAM · HIGH-FIDELITY BIOMECHANICAL MODEL</div>
        <h1>Gemelo Digital Neurooftalmológico 3D</h1>
        <p>Modelo biomecánico tridimensional interactivo del globo ocular y vaina del nervio óptico bajo redistribución cefálica de fluidos (SANS).</p>
    </div>
    <div style="display:flex; gap:12px; align-items:center;">
        @if($astronaut)
            <img src="{{ $astronaut->avatar_url }}"
                 alt="{{ $astronaut->name }}"
                 style="width:44px;height:44px;border-radius:8px;object-fit:cover;border:2px solid var(--foreground);">
        @endif
        <label for="astro-select" style="font-size:13px; font-weight:600; color:var(--muted-foreground);">Tripulante:</label>
        <select id="astro-select" class="form-control" style="max-width:280px; height:36px;" onchange="location.href='?astro=' + this.value">
            @foreach ($astronauts as $item)
                <option value="{{ $item->code }}" @selected($astronaut && $astronaut->code === $item->code)>
                    {{ $item->code }} - {{ $item->name }} ({{ $item->agency }})
                </option>
            @endforeach
        </select>
    </div>
</div>

{{-- Control Deslizante de Cronología de Vuelo (FD0 -> FD120) --}}
<div class="timeline-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:10px;">
            <button type="button" class="btn btn-primary" id="play-pause-btn" style="padding:6px 14px; font-size:12px; min-height:34px;">
                <i class="fa fa-play" id="play-icon"></i> <span id="play-text">Simular Misión</span>
            </button>
            <span style="font-size:13px; color:var(--muted-foreground); font-weight:500;">
                Línea temporal de microgravedad en vuelo:
            </span>
        </div>
        <div style="font-size:15px; font-weight:800; font-family:ui-monospace, monospace; color:var(--foreground);" id="flight-day-badge">
            DÍA DE VUELO: FD0 (Línea Base Pre-Vuelo)
        </div>
    </div>
    <input type="range" min="0" max="{{ count($timelineDays) - 1 }}" value="0" step="1" class="slider-rail" id="day-slider">
    <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--muted-foreground); margin-top:8px; font-family:ui-monospace, monospace;">
        @foreach ($timelineDays as $idx => $d)
            <span style="cursor:pointer;" onclick="setSliderIndex({{ $idx }})">FD{{ $d }}</span>
        @endforeach
    </div>
</div>

{{-- Barra de Selección de Modo: 3D / Consola Tomográfica ZEISS Cirrus / Vista Dual --}}
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; flex-wrap:wrap; gap:12px;">
    <div class="tabs" style="margin:0;">
        <button type="button" class="tab active" id="tab-btn-3d" onclick="setWorkstationMode('3d')">
            <i class="fa fa-cube"></i> Gemelo Digital 3D (Three.js)
        </button>
        <button type="button" class="tab" id="tab-btn-oct" onclick="setWorkstationMode('oct')">
            <i class="fa fa-heartbeat"></i> Consola ZEISS Cirrus™ HD-OCT
        </button>
        <button type="button" class="tab" id="tab-btn-dual" onclick="setWorkstationMode('dual')">
            <i class="fa fa-th-large"></i> Vista Dual Simultánea (3D + OCT)
        </button>
    </div>

    <div style="font-size:12px; color:var(--muted-foreground); font-family:ui-monospace, monospace;">
        INSTRUMENTO: <strong style="color:var(--foreground);">ZEISS CIRRUS™ HD-OCT · SPECTRAL DOMAIN</strong>
    </div>
</div>

{{-- ESTACIÓN DE TRABAJO CLÍNICA ZEISS CIRRUS™ HD-OCT (MACULAR CUBE 512x128) --}}
<div id="panel-cirrus-oct" class="oct-workstation" style="display:none; margin-bottom:24px;">
    {{-- Barra Superior de Instrumentación Clínica --}}
    <div class="oct-topbar">
        <div class="oct-brand">
            <span class="oct-brand-logo">ZEISS</span>
            <strong style="color:#ffffff; letter-spacing:0.04em;">Cirrus™ HD-OCT</strong>
            <span style="color:#71717a;">| SPECTRAL DOMAIN TECHNOLOGY</span>
        </div>
        <div style="display:flex; align-items:center; gap:16px;">
            <div>PACIENTE: <strong style="color:#ffffff;" id="oct-patient-info">{{ $astronaut->name }} ({{ $astronaut->code }})</strong></div>
            <div>PROTOCOLO: <strong style="color:#38bdf8;">Macular Cube 512x128</strong></div>
            <div>SIGNAL STRENGTH: <strong style="color:#22c55e;" id="oct-signal-badge">OD: 9/10 · OS: 10/10</strong></div>
        </div>
    </div>

    {{-- Matriz Clínica de 9 Paneles (Réplica Exacta del Monitor Hospitalario) --}}
    <div class="oct-grid-container">
        {{-- Fila 1 - Panel 1: Mapa de Espesor ILM-RPE (OD) --}}
        <div class="oct-panel">
            <div class="oct-panel-title">
                <span>ILM-RPE Thickness Map (OD)</span>
                <span style="color:#38bdf8; font-size:10px;">OJO DERECHO</span>
            </div>
            <div style="display:flex; justify-content:center; align-items:center; height:160px; position:relative;">
                <canvas id="octHeatmapOd" width="160" height="160" style="border-radius:50%; border:1px solid #27272a;"></canvas>
                {{-- Escala de calor lateral --}}
                <div style="position:absolute; right:4px; top:10px; bottom:10px; width:12px; border-radius:2px; background:linear-gradient(to top, #0284c7 0%, #22c55e 35%, #eab308 70%, #ef4444 100%); display:flex; flex-direction:column; justify-content:space-between; font-size:8px; color:#ffffff; padding:2px; text-shadow:0 0 2px #000;">
                    <span>500</span>
                    <span>300</span>
                    <span>100</span>
                </div>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:10px; color:#71717a; margin-top:6px;">
                <span>Fóvea Central: <strong style="color:#38bdf8;" id="oct-fovea-od-val">252 &mu;m</strong></span>
                <span>Unidades: Micras (&mu;m)</span>
            </div>
        </div>

        {{-- Fila 1 - Panel 2: Fondo de Ojo Infrarrojo OCT Fundus (OD / OS) --}}
        <div class="oct-panel" style="display:flex; flex-direction:column; justify-content:space-between;">
            <div class="oct-panel-title">
                <span>OCT Fundus Infrared Alignment</span>
                <span style="color:#a1a1aa; font-size:10px;">Macular 6x6 mm</span>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; padding:6px 0;">
                <div style="background:#05070a; border:1px solid #27272a; border-radius:4px; padding:8px; text-align:center;">
                    <div style="font-size:10px; color:#94a3b8; margin-bottom:4px;">OD (Fovea: 254, 66)</div>
                    <svg width="100" height="100" viewBox="0 0 100 100" style="margin:0 auto; background:#18181b; border-radius:50%;">
                        <circle cx="50" cy="50" r="45" fill="#27272a" stroke="#3f3f46" stroke-width="1"/>
                        <circle cx="50" cy="50" r="8" fill="#09090b"/>
                        <line x1="10" y1="50" x2="90" y2="50" stroke="#22c55e" stroke-width="1.5" stroke-dasharray="2,2"/>
                        <line x1="50" y1="10" x2="50" y2="90" stroke="#22c55e" stroke-width="1.5" stroke-dasharray="2,2"/>
                    </svg>
                </div>
                <div style="background:#05070a; border:1px solid #27272a; border-radius:4px; padding:8px; text-align:center;">
                    <div style="font-size:10px; color:#94a3b8; margin-bottom:4px;">OS (Fovea: 239, 69)</div>
                    <svg width="100" height="100" viewBox="0 0 100 100" style="margin:0 auto; background:#18181b; border-radius:50%;">
                        <circle cx="50" cy="50" r="45" fill="#27272a" stroke="#3f3f46" stroke-width="1"/>
                        <circle cx="50" cy="50" r="8" fill="#09090b"/>
                        <line x1="10" y1="50" x2="90" y2="50" stroke="#38bdf8" stroke-width="1.5" stroke-dasharray="2,2"/>
                        <line x1="50" y1="10" x2="50" y2="90" stroke="#38bdf8" stroke-width="1.5" stroke-dasharray="2,2"/>
                    </svg>
                </div>
            </div>
            <div style="font-size:10px; color:#71717a; text-align:center;">
                Sistema de seguimiento foveal LSO (Line Scanning Ophthalmoscope)
            </div>
        </div>

        {{-- Fila 1 - Panel 3: Mapa de Espesor ILM-RPE (OS) --}}
        <div class="oct-panel">
            <div class="oct-panel-title">
                <span>ILM-RPE Thickness Map (OS)</span>
                <span style="color:#22c55e; font-size:10px;">OJO IZQUIERDO</span>
            </div>
            <div style="display:flex; justify-content:center; align-items:center; height:160px; position:relative;">
                <canvas id="octHeatmapOs" width="160" height="160" style="border-radius:50%; border:1px solid #27272a;"></canvas>
                <div style="position:absolute; right:4px; top:10px; bottom:10px; width:12px; border-radius:2px; background:linear-gradient(to top, #0284c7 0%, #22c55e 35%, #eab308 70%, #ef4444 100%); display:flex; flex-direction:column; justify-content:space-between; font-size:8px; color:#ffffff; padding:2px; text-shadow:0 0 2px #000;">
                    <span>500</span>
                    <span>300</span>
                    <span>100</span>
                </div>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:10px; color:#71717a; margin-top:6px;">
                <span>Fóvea Central: <strong style="color:#22c55e;" id="oct-fovea-os-val">248 &mu;m</strong></span>
                <span>Unidades: Micras (&mu;m)</span>
            </div>
        </div>

        {{-- Fila 2 - Panel 4: Tomograma Horizontal B-Scan (OD) --}}
        <div class="oct-panel">
            <div class="oct-panel-title">
                <span>Horizontal B-Scan (OD)</span>
                <span id="bscan-od-label" style="color:#38bdf8; font-size:10px;">512x128 Axial</span>
            </div>
            <div style="background:#020408; border:1px solid #1e1e24; border-radius:4px; height:140px; position:relative; overflow:hidden; display:flex; align-items:center; justify-content:center;">
                <canvas id="bscanCanvasOd" width="360" height="140" style="width:100%; height:100%;"></canvas>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:10px; color:#71717a; margin-top:6px;">
                <span>Temporal</span>
                <span style="color:#a1a1aa;">Depresión Foveal Central</span>
                <span>Nasal</span>
            </div>
        </div>

        {{-- Fila 2 - Panel 5: Tabla Cuantitativa Central de Espesor ILM-RPE --}}
        <div class="oct-panel" style="display:flex; flex-direction:column; justify-content:space-between;">
            <div class="oct-panel-title">
                <span>Tabla Cuantitativa ILM - RPE</span>
                <span style="color:#a1a1aa; font-size:10px;">Cirrus Normative Match</span>
            </div>
            <table class="oct-table">
                <thead>
                    <tr>
                        <th>Parámetro Macular</th>
                        <th style="color:#38bdf8; text-align:right;">OD</th>
                        <th style="color:#22c55e; text-align:right;">OS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Thickness Central Subfield (&mu;m)</td>
                        <td style="text-align:right; font-weight:700;" id="table-central-od">252</td>
                        <td style="text-align:right; font-weight:700;" id="table-central-os">248</td>
                    </tr>
                    <tr>
                        <td>Volume Cube (mm&sup3;)</td>
                        <td style="text-align:right; font-weight:700;" id="table-vol-od">10.1</td>
                        <td style="text-align:right; font-weight:700;" id="table-vol-os">10.0</td>
                    </tr>
                    <tr>
                        <td>Thickness Avg Cube (&mu;m)</td>
                        <td style="text-align:right; font-weight:700;" id="table-avg-od">280</td>
                        <td style="text-align:right; font-weight:700;" id="table-avg-os">276</td>
                    </tr>
                    <tr>
                        <td>Asimetría Interocular (&Delta;OD-OS)</td>
                        <td colspan="2" style="text-align:right; font-weight:700; color:#eab308;" id="table-asym">+4 &mu;m</td>
                    </tr>
                </tbody>
            </table>
            <div style="font-size:10px; color:#71717a; text-align:center; padding-top:4px;">
                Criterio SANS NASA: &Delta;Espesor > 25 &mu;m sugiere congestión retrobulbar asimétrica
            </div>
        </div>

        {{-- Fila 2 - Panel 6: Tomograma Vertical B-Scan (OS) --}}
        <div class="oct-panel">
            <div class="oct-panel-title">
                <span>Vertical B-Scan (OS)</span>
                <span style="color:#22c55e; font-size:10px;">Meridiano Vertical</span>
            </div>
            <div style="background:#020408; border:1px solid #1e1e24; border-radius:4px; height:140px; position:relative; overflow:hidden; display:flex; align-items:center; justify-content:center;">
                <canvas id="bscanCanvasOs" width="360" height="140" style="width:100%; height:100%;"></canvas>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:10px; color:#71717a; margin-top:6px;">
                <span>Superior</span>
                <span style="color:#a1a1aa;">Corte Sagital Foveal</span>
                <span>Inferior</span>
            </div>
        </div>

        {{-- Fila 3 - Panel 7: Rejilla ETDRS de 9 Sectores (OD) --}}
        <div class="oct-panel">
            <div class="oct-panel-title">
                <span>Rejilla ETDRS 9 Sectores (OD)</span>
                <span style="color:#38bdf8; font-size:10px;">Deviation Map</span>
            </div>
            <div class="etdrs-circle-container">
                <svg id="etdrsSvgOd" width="160" height="160" viewBox="-100 -100 200 200">
                    {{-- Anillo Exterior (6 mm) --}}
                    <path d="M -63.6 -63.6 A 90 90 0 0 1 63.6 -63.6 L 39.6 -39.6 A 56 56 0 0 0 -39.6 -39.6 Z" id="sec-od-outer-sup" class="etdrs-sector" fill="#22c55e"/>
                    <text x="0" y="-72" class="etdrs-text" id="sec-od-outer-sup-val">280</text>

                    <path d="M 63.6 -63.6 A 90 90 0 0 1 63.6 63.6 L 39.6 39.6 A 56 56 0 0 0 39.6 -39.6 Z" id="sec-od-outer-nas" class="etdrs-sector" fill="#22c55e"/>
                    <text x="72" y="0" class="etdrs-text" id="sec-od-outer-nas-val">295</text>

                    <path d="M 63.6 63.6 A 90 90 0 0 1 -63.6 63.6 L -39.6 39.6 A 56 56 0 0 0 39.6 39.6 Z" id="sec-od-outer-inf" class="etdrs-sector" fill="#22c55e"/>
                    <text x="0" y="72" class="etdrs-text" id="sec-od-outer-inf-val">272</text>

                    <path d="M -63.6 63.6 A 90 90 0 0 1 -63.6 -63.6 L -39.6 -39.6 A 56 56 0 0 0 -39.6 39.6 Z" id="sec-od-outer-temp" class="etdrs-sector" fill="#22c55e"/>
                    <text x="-72" y="0" class="etdrs-text" id="sec-od-outer-temp-val">265</text>

                    {{-- Anillo Interior (3 mm) --}}
                    <path d="M -39.6 -39.6 A 56 56 0 0 1 39.6 -39.6 L 17 -17 A 24 24 0 0 0 -17 -17 Z" id="sec-od-inner-sup" class="etdrs-sector" fill="#22c55e"/>
                    <text x="0" y="-40" class="etdrs-text" id="sec-od-inner-sup-val">320</text>

                    <path d="M 39.6 -39.6 A 56 56 0 0 1 39.6 39.6 L 17 17 A 24 24 0 0 0 17 -17 Z" id="sec-od-inner-nas" class="etdrs-sector" fill="#22c55e"/>
                    <text x="40" y="0" class="etdrs-text" id="sec-od-inner-nas-val">325</text>

                    <path d="M 39.6 39.6 A 56 56 0 0 1 -39.6 39.6 L -17 17 A 24 24 0 0 0 17 17 Z" id="sec-od-inner-inf" class="etdrs-sector" fill="#22c55e"/>
                    <text x="0" y="40" class="etdrs-text" id="sec-od-inner-inf-val">318</text>

                    <path d="M -39.6 39.6 A 56 56 0 0 1 -39.6 -39.6 L -17 -17 A 24 24 0 0 0 -17 17 Z" id="sec-od-inner-temp" class="etdrs-sector" fill="#22c55e"/>
                    <text x="-40" y="0" class="etdrs-text" id="sec-od-inner-temp-val">310</text>

                    {{-- Círculo Foveal Central (1 mm) --}}
                    <circle cx="0" cy="0" r="24" id="sec-od-central" class="etdrs-sector" fill="#22c55e" stroke="#09090b" stroke-width="1.5"/>
                    <text x="0" y="0" class="etdrs-text" id="sec-od-central-val">252</text>
                </svg>
            </div>
            <div style="display:flex; justify-content:center; gap:8px; font-size:9px; color:#a1a1aa; margin-top:4px;">
                <span style="color:#22c55e;">&#9632; Normal (5-95%)</span>
                <span style="color:#eab308;">&#9632; Borderline (95-99%)</span>
                <span style="color:#ef4444;">&#9632; &gt;99%</span>
            </div>
        </div>

        {{-- Fila 3 - Panel 8: Estado Diagnóstico de Biomarcadores Estructurales SANS --}}
        <div class="oct-panel" style="display:flex; flex-direction:column; justify-content:space-between;">
            <div class="oct-panel-title">
                <span>SANS Structural Biomarker Diagnostics</span>
                <span style="color:#38bdf8; font-size:10px;">Telemetría NASA HRP</span>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin:4px 0;">
                <div style="background:#09090b; border:1px solid #27272a; border-radius:4px; padding:8px;">
                    <div style="font-size:10px; color:#71717a;">Elevación de Disco Óptico</div>
                    <div style="font-size:16px; font-weight:800; color:#38bdf8;" id="oct-disc-elevation">+0 &mu;m</div>
                </div>
                <div style="background:#09090b; border:1px solid #27272a; border-radius:4px; padding:8px;">
                    <div style="font-size:10px; color:#71717a;">Grado Frisén Estimado</div>
                    <div style="font-size:16px; font-weight:800; color:#22c55e;" id="oct-frisen-grade">Grado 0 (Normal)</div>
                </div>
                <div style="background:#09090b; border:1px solid #27272a; border-radius:4px; padding:8px;">
                    <div style="font-size:10px; color:#71717a;">Pliegues Coroideos</div>
                    <div style="font-size:13px; font-weight:700; color:#ffffff;" id="oct-folds-status">Ausentes</div>
                </div>
                <div style="background:#09090b; border:1px solid #27272a; border-radius:4px; padding:8px;">
                    <div style="font-size:10px; color:#71717a;">Aplanamiento Foveal</div>
                    <div style="font-size:13px; font-weight:700; color:#ffffff;" id="oct-foveal-flattening">0.0%</div>
                </div>
            </div>
            <div style="padding:6px; background:#18181b; border-radius:4px; font-size:10px; color:#a1a1aa; text-align:center;">
                Correlación clínica: Basado en protocolo de vuelo espacial OCT Spectralis/Cirrus de la ISS
            </div>
        </div>

        {{-- Fila 3 - Panel 9: Rejilla ETDRS de 9 Sectores (OS) --}}
        <div class="oct-panel">
            <div class="oct-panel-title">
                <span>Rejilla ETDRS 9 Sectores (OS)</span>
                <span style="color:#22c55e; font-size:10px;">Deviation Map</span>
            </div>
            <div class="etdrs-circle-container">
                <svg id="etdrsSvgOs" width="160" height="160" viewBox="-100 -100 200 200">
                    <path d="M -63.6 -63.6 A 90 90 0 0 1 63.6 -63.6 L 39.6 -39.6 A 56 56 0 0 0 -39.6 -39.6 Z" id="sec-os-outer-sup" class="etdrs-sector" fill="#22c55e"/>
                    <text x="0" y="-72" class="etdrs-text" id="sec-os-outer-sup-val">275</text>

                    <path d="M 63.6 -63.6 A 90 90 0 0 1 63.6 63.6 L 39.6 39.6 A 56 56 0 0 0 39.6 -39.6 Z" id="sec-os-outer-nas" class="etdrs-sector" fill="#22c55e"/>
                    <text x="72" y="0" class="etdrs-text" id="sec-os-outer-nas-val">288</text>

                    <path d="M 63.6 63.6 A 90 90 0 0 1 -63.6 63.6 L -39.6 39.6 A 56 56 0 0 0 39.6 39.6 Z" id="sec-os-outer-inf" class="etdrs-sector" fill="#22c55e"/>
                    <text x="0" y="72" class="etdrs-text" id="sec-os-outer-inf-val">268</text>

                    <path d="M -63.6 63.6 A 90 90 0 0 1 -63.6 -63.6 L -39.6 -39.6 A 56 56 0 0 0 -39.6 39.6 Z" id="sec-os-outer-temp" class="etdrs-sector" fill="#22c55e"/>
                    <text x="-72" y="0" class="etdrs-text" id="sec-os-outer-temp-val">262</text>

                    <path d="M -39.6 -39.6 A 56 56 0 0 1 39.6 -39.6 L 17 -17 A 24 24 0 0 0 -17 -17 Z" id="sec-os-inner-sup" class="etdrs-sector" fill="#22c55e"/>
                    <text x="0" y="-40" class="etdrs-text" id="sec-os-inner-sup-val">315</text>

                    <path d="M 39.6 -39.6 A 56 56 0 0 1 39.6 39.6 L 17 17 A 24 24 0 0 0 17 -17 Z" id="sec-os-inner-nas" class="etdrs-sector" fill="#22c55e"/>
                    <text x="40" y="0" class="etdrs-text" id="sec-os-inner-nas-val">318</text>

                    <path d="M 39.6 39.6 A 56 56 0 0 1 -39.6 39.6 L -17 17 A 24 24 0 0 0 17 17 Z" id="sec-os-inner-inf" class="etdrs-sector" fill="#22c55e"/>
                    <text x="0" y="40" class="etdrs-text" id="sec-os-inner-inf-val">312</text>

                    <path d="M -39.6 39.6 A 56 56 0 0 1 -39.6 -39.6 L -17 -17 A 24 24 0 0 0 -17 17 Z" id="sec-os-inner-temp" class="etdrs-sector" fill="#22c55e"/>
                    <text x="-40" y="0" class="etdrs-text" id="sec-os-inner-temp-val">308</text>

                    <circle cx="0" cy="0" r="24" id="sec-os-central" class="etdrs-sector" fill="#22c55e" stroke="#09090b" stroke-width="1.5"/>
                    <text x="0" y="0" class="etdrs-text" id="sec-os-central-val">248</text>
                </svg>
            </div>
            <div style="display:flex; justify-content:center; gap:8px; font-size:9px; color:#a1a1aa; margin-top:4px;">
                <span style="color:#22c55e;">&#9632; Normal (5-95%)</span>
                <span style="color:#eab308;">&#9632; Borderline (95-99%)</span>
                <span style="color:#ef4444;">&#9632; &gt;99%</span>
            </div>
        </div>
    </div>
</div>

<div class="twin-layout" id="panel-3d-layout">
    {{-- Columna Izquierda: Visor 3D WebGL (Three.js) con HUD Aeroespacial --}}
    <div class="viewport-card">
        <div class="viewport-header">
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="width:8px; height:8px; border-radius:50%; background:#22c55e; display:inline-block;"></span>
                <strong style="letter-spacing:0.04em;">Corte Sagital Anatómico · Modelo 3D en Tiempo Real</strong>
                <span style="color:#64748b;">· WebGL Hardware Accelerated</span>
            </div>
            <div style="display:flex; gap:10px; align-items:center;">
                <span class="badge-subtle" id="hud-tier-badge" style="background:#0f172a; border-color:#334155; color:#38bdf8;">
                    NOMINAL FD0
                </span>
            </div>
        </div>

        {{-- Contenedor del Canvas Three.js --}}
        <div id="threeCanvasContainer">
            {{-- HUD Capas de Información Aeroespacial --}}
            <div class="viewport-hud">
                <div class="hud-scan-beam"></div>
                <div class="hud-reticle"></div>

                <div class="hud-coords">
                    <div>POLO POSTERIOR (Z): <span id="hud-z-coord">-12.10 mm</span></div>
                    <div>DEFORMACIÓN AXIAL: <span id="hud-deform-mm">0.00 mm</span></div>
                    <div>PRESIÓN RETROBULBAR: <span id="hud-icp-val">15.0 mmHg</span></div>
                </div>
            </div>

            {{-- Selector de Perspectivas y Modos 3D --}}
            <div class="viewport-controls">
                <button type="button" class="view-btn active" id="btn-view-free" onclick="setPresetView('free')">
                    <i class="fa fa-arrows"></i> 3D Libre (Rotar)
                </button>
                <button type="button" class="view-btn" id="btn-view-sagittal" onclick="setPresetView('sagittal')">
                    <i class="fa fa-columns"></i> Corte Sagital
                </button>
                <button type="button" class="view-btn" id="btn-view-fundus" onclick="setPresetView('fundus')">
                    <i class="fa fa-eye"></i> Fondo de Ojo (Retina)
                </button>
                <button type="button" class="view-btn" id="btn-toggle-wireframe" onclick="toggleWireframe()">
                    <i class="fa fa-cube"></i> Modo Holográfico
                </button>
                <button type="button" class="view-btn" id="btn-reset-cam" onclick="resetCamera()">
                    <i class="fa fa-undo"></i> Reset Cámara
                </button>
            </div>
        </div>

        <div style="padding:10px 18px; background:#090c15; border-top:1px solid #1a2234; display:flex; justify-content:space-between; font-size:11px; color:#94a3b8; font-family:ui-monospace, monospace;">
            <span><i class="fa fa-mouse-pointer"></i> Arrastra con el cursor para rotar en 3D · Rueda para zoom</span>
            <span style="color:#f1f5f9;">VECTOR CEFÁLICO: <strong id="hud-vector-status" style="color:#4ade80;">GRADIENTE ESTABLE (1G)</strong></span>
        </div>
    </div>

    {{-- Columna Derecha: Telemetría Biomecánica & Biomarcadores SANS --}}
    <div style="display:flex; flex-direction:column; gap:16px;">
        {{-- Tarjeta 1: Parámetros Biomecánicos Cuantitativos --}}
        <div class="card" style="padding:20px; margin:0;">
            <div style="margin-bottom:14px; display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <h2 class="card-title" style="font-size:15px;">Telemetría Biomecánica (OD)</h2>
                    <p class="card-description">Parámetros anatómicos computados por el gemelo digital.</p>
                </div>
                <span class="badge-subtle" id="twin-risk-badge" style="font-weight:700;">RIESGO BAJO</span>
            </div>

            <div class="gauge-grid">
                <div class="gauge-cell">
                    <div class="gauge-label">Longitud Axial</div>
                    <div class="gauge-val" id="val-axial-len">24.20 mm</div>
                    <div style="font-size:10px; color:var(--muted-foreground); margin-top:2px;" id="val-axial-delta">Base pre-vuelo nominal</div>
                </div>

                <div class="gauge-cell">
                    <div class="gauge-label">Shift Hipermetrópico</div>
                    <div class="gauge-val" id="val-shift">+0.00 D</div>
                    <div style="font-size:10px; color:var(--muted-foreground); margin-top:2px;">&Delta;SE refracción en cabina</div>
                </div>

                <div class="gauge-cell">
                    <div class="gauge-label">Agudeza Snellen</div>
                    <div class="gauge-val" id="val-snellen">20/20</div>
                    <div style="font-size:10px; color:var(--muted-foreground); margin-top:2px;" id="val-logmar">LogMAR 0.000</div>
                </div>

                <div class="gauge-cell">
                    <div class="gauge-label">Presión Intraocular</div>
                    <div class="gauge-val" id="val-iop">15.0 mmHg</div>
                    <div style="font-size:10px; color:var(--muted-foreground); margin-top:2px;">Tonometría de misión</div>
                </div>
            </div>
        </div>

        {{-- Tarjeta 2: Signos Clínicos Estructurales & OCT --}}
        <div class="card" style="padding:20px; margin:0;">
            <div style="margin-bottom:12px;">
                <h2 class="card-title" style="font-size:15px;">Biomarcadores de Síndrome SANS</h2>
                <p class="card-description">Respuesta fisiológica al desplazamiento de 2L de fluidos cefálicos.</p>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px; font-size:13px;">
                <div style="display:flex; justify-content:space-between; align-items:center; padding:9px 12px; background:var(--muted); border-radius:6px;">
                    <span style="font-weight:500;">Edema Papilar (Escala Frisén)</span>
                    <strong id="val-frisen" style="font-family:ui-monospace, monospace;">Grado 0 (Normal)</strong>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; padding:9px 12px; background:var(--muted); border-radius:6px;">
                    <span style="font-weight:500;">Aplanamiento de Polo Posterior</span>
                    <strong id="val-flattening" style="font-family:ui-monospace, monospace;">Ausente</strong>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; padding:9px 12px; background:var(--muted); border-radius:6px;">
                    <span style="font-weight:500;">Pliegues Coroideos</span>
                    <strong id="val-folds" style="font-family:ui-monospace, monospace;">No detectados</strong>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; padding:9px 12px; background:var(--muted); border-radius:6px;">
                    <span style="font-weight:500;">Grosor Capa de Fibras (RNFL OCT)</span>
                    <strong id="val-rnfl" style="font-family:ui-monospace, monospace;">95 &mu;m</strong>
                </div>
            </div>

            {{-- Perfil de Tomografía OCT B-Scan Simulado --}}
            <div class="oct-scan-box">
                <div style="display:flex; justify-content:space-between; font-size:11px; color:#94a3b8; font-family:ui-monospace, monospace; margin-bottom:6px;">
                    <span>PERFIL RETINIANO OCT B-SCAN</span>
                    <span id="oct-elevation-val">ELEVACIÓN PAPILAR: 0 &mu;m</span>
                </div>
                <div style="height:48px; background:#000; border:1px solid #27272a; border-radius:4px; position:relative; overflow:hidden;">
                    <svg id="octCurveSvg" width="100%" height="48" preserveAspectRatio="none" viewBox="0 0 300 48">
                        <path id="octCurvePath" d="M 0 34 Q 75 34 110 32 Q 150 30 190 32 Q 225 34 300 34" fill="none" stroke="#22c55e" stroke-width="2.5"/>
                        <path id="octBaseCurve" d="M 0 34 Q 75 34 150 34 Q 225 34 300 34" fill="none" stroke="#52525b" stroke-width="1" stroke-dasharray="3,3"/>
                    </svg>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:10px; color:#71717a; margin-top:4px;">
                    <span>Temporal</span>
                    <span>Disco Óptico Central</span>
                    <span>Nasal</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Explicación Fisiopatológica del Modelo --}}
<div class="card" style="padding:20px;">
    <h3 style="font-size:14px; font-weight:700; margin:0 0 8px; color:var(--foreground);">
        <i class="fa fa-info-circle"></i> Fisiopatología de Microgravedad: El Modelo Biomecánico del Gemelo Digital
    </h3>
    <p style="margin:0; font-size:13px; color:var(--muted-foreground); line-height:1.6;">
        En la Tierra, el gradiente gravitacional mantiene la presión venosa intracraneal baja en bipedestación. En el espacio, la ausencia de gradiente hidrostático redistribuye ~2,000 ml de fluidos corporales cefálicamente hacia cabeza y cuello. Esto eleva de forma crónica la presión en el espacio subaracnoideo de la vaina del nervio óptico (ONSH), ejerciendo una carga mecánica sobre la lámina cribosa y el polo posterior del globo ocular. Como resultado observable, se genera aplanamiento escleral posterior, acortamiento de la longitud axial y un desplazamiento refractivo hipermetrópico donde el punto focal de luz converge detrás de la retina.
    </p>
</div>
@endsection

@push('scripts')
<script>
// Datos de telemetría pasados por el controlador
const simulationData = @json($simulationData);
const timelineDays = @json($timelineDays);
let currentCirrusData = @json($cirrusOct);
const currentScenario = @json($scenario);
const currentAstroCode = @json($astronaut ? $astronaut->code : 'ASTRO-01');
const currentAstroName = @json($astronaut ? $astronaut->name : 'CDR Alex Mercer');

let currentIndex = 0;
let playInterval = null;

// ==========================================================================
// CONTROL DE MODOS DE LA ESTACIÓN: 3D / CIRRUS OCT / VISTA DUAL
// ==========================================================================
function setWorkstationMode(mode) {
    const p3d = document.getElementById('panel-3d-layout');
    const poct = document.getElementById('panel-cirrus-oct');
    const btn3d = document.getElementById('tab-btn-3d');
    const btnoct = document.getElementById('tab-btn-oct');
    const btndual = document.getElementById('tab-btn-dual');

    [btn3d, btnoct, btndual].forEach(b => b.classList.remove('active'));

    if (mode === '3d') {
        btn3d.classList.add('active');
        p3d.style.display = 'grid';
        poct.style.display = 'none';
        setTimeout(onWindowResize, 30);
        setTimeout(onWindowResize, 150);
    } else if (mode === 'oct') {
        btnoct.classList.add('active');
        p3d.style.display = 'none';
        poct.style.display = 'block';
        updateCirrusOctDisplay(currentCirrusData);
    } else if (mode === 'dual') {
        btndual.classList.add('active');
        p3d.style.display = 'grid';
        poct.style.display = 'block';
        updateCirrusOctDisplay(currentCirrusData);
        setTimeout(onWindowResize, 30);
        setTimeout(onWindowResize, 150);
    }
}

// ==========================================================================
// RENDERIZADO CLÍNICO DE TOMOGRAFÍA ZEISS CIRRUS™ HD-OCT
// ==========================================================================
function renderOctHeatmap(canvasId, centralUm, isOd) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const w = canvas.width;
    const h = canvas.height;
    const cx = w / 2;
    const cy = h / 2;

    ctx.clearRect(0, 0, w, h);
    ctx.fillStyle = '#05070a';
    ctx.fillRect(0, 0, w, h);

    const grad = ctx.createRadialGradient(cx, cy, 4, cx, cy, 74);

    if (centralUm >= 320) {
        grad.addColorStop(0, '#ef4444');
        grad.addColorStop(0.28, '#f59e0b');
        grad.addColorStop(0.60, '#22c55e');
        grad.addColorStop(0.85, '#0284c7');
        grad.addColorStop(1, '#05070a');
    } else if (centralUm >= 285) {
        grad.addColorStop(0, '#eab308');
        grad.addColorStop(0.32, '#22c55e');
        grad.addColorStop(0.70, '#0284c7');
        grad.addColorStop(1, '#05070a');
    } else {
        grad.addColorStop(0, '#0369a1');
        grad.addColorStop(0.22, '#0284c7');
        grad.addColorStop(0.48, '#22c55e');
        grad.addColorStop(0.78, '#0284c7');
        grad.addColorStop(1, '#05070a');
    }

    ctx.fillStyle = grad;
    ctx.beginPath();
    ctx.arc(cx, cy, 74, 0, Math.PI * 2);
    ctx.fill();

    ctx.strokeStyle = 'rgba(255, 255, 255, 0.28)';
    ctx.lineWidth = 1;
    ctx.setLineDash([2, 2]);
    ctx.beginPath();
    ctx.arc(cx, cy, 22, 0, Math.PI * 2);
    ctx.stroke();

    ctx.beginPath();
    ctx.arc(cx, cy, 50, 0, Math.PI * 2);
    ctx.stroke();
    ctx.setLineDash([]);

    ctx.fillStyle = '#ffffff';
    ctx.beginPath();
    ctx.arc(cx, cy, 2, 0, Math.PI * 2);
    ctx.fill();
}

function renderBScanCanvas(canvasId, bscanData, isOd) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const w = canvas.width;
    const h = canvas.height;

    ctx.clearRect(0, 0, w, h);
    ctx.fillStyle = '#05070a';
    ctx.fillRect(0, 0, w, h);

    // Speckle noise característico de Spectral Domain OCT
    ctx.fillStyle = 'rgba(255, 255, 255, 0.08)';
    for (let i = 0; i < 280; i++) {
        const rx = Math.random() * w;
        const ry = 25 + Math.random() * 90;
        ctx.fillRect(rx, ry, 1, 1);
    }

    if (!bscanData || !bscanData.ilm_layer) return;

    const n = bscanData.ilm_layer.length;
    const stepX = w / (n - 1);

    function mapY(valUm) {
        return 125 - ((valUm - 20) / 320.0) * 95;
    }

    // 1. RPE / Bruch's Membrane (Hiperreflectiva inferior - oro)
    ctx.beginPath();
    for (let i = 0; i < n; i++) {
        const x = i * stepX;
        const y = mapY(bscanData.rpe_layer[i]);
        if (i === 0) ctx.moveTo(x, y);
        else ctx.lineTo(x, y);
    }
    ctx.strokeStyle = '#eab308';
    ctx.lineWidth = 2.5;
    ctx.stroke();

    // 2. RNFL / IPL (Banda intermedia - cian)
    ctx.beginPath();
    for (let i = 0; i < n; i++) {
        const x = i * stepX;
        const y = mapY(bscanData.rnfl_layer[i]);
        if (i === 0) ctx.moveTo(x, y);
        else ctx.lineTo(x, y);
    }
    ctx.strokeStyle = 'rgba(56, 189, 248, 0.75)';
    ctx.lineWidth = 1.5;
    ctx.stroke();

    // 3. ILM (Membrana limitante interna foveal - verde)
    ctx.beginPath();
    for (let i = 0; i < n; i++) {
        const x = i * stepX;
        const y = mapY(bscanData.ilm_layer[i]);
        if (i === 0) ctx.moveTo(x, y);
        else ctx.lineTo(x, y);
    }
    ctx.strokeStyle = '#22c55e';
    ctx.lineWidth = 2.2;
    ctx.stroke();

    // Relleno de capas retinianas
    ctx.lineTo(w, mapY(bscanData.rpe_layer[n - 1]));
    for (let i = n - 1; i >= 0; i--) {
        const x = i * stepX;
        const y = mapY(bscanData.rpe_layer[i]);
        ctx.lineTo(x, y);
    }
    ctx.closePath();
    ctx.fillStyle = 'rgba(34, 197, 94, 0.08)';
    ctx.fill();

    // Retícula vertical foveal central
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.22)';
    ctx.setLineDash([2, 2]);
    ctx.beginPath();
    ctx.moveTo(w / 2, 8);
    ctx.lineTo(w / 2, h - 8);
    ctx.stroke();
    ctx.setLineDash([]);
}

function updateCirrusOctDisplay(octData) {
    if (!octData || !octData.summary_table) return;

    const st = octData.summary_table;
    document.getElementById('table-central-od').textContent = Math.round(st.central_subfield_um.od);
    document.getElementById('table-central-os').textContent = Math.round(st.central_subfield_um.os);
    document.getElementById('table-vol-od').textContent = st.volume_cube_mm3.od;
    document.getElementById('table-vol-os').textContent = st.volume_cube_mm3.os;
    document.getElementById('table-avg-od').textContent = Math.round(st.avg_thickness_cube_um.od);
    document.getElementById('table-avg-os').textContent = Math.round(st.avg_thickness_cube_um.os);

    const asym = Math.round(st.central_subfield_um.od - st.central_subfield_um.os);
    const asymEl = document.getElementById('table-asym');
    asymEl.textContent = (asym >= 0 ? '+' : '') + asym + ' \u00B5m';
    asymEl.style.color = Math.abs(asym) >= 25 ? '#ef4444' : (Math.abs(asym) >= 15 ? '#eab308' : '#22c55e');

    document.getElementById('oct-fovea-od-val').textContent = Math.round(st.central_subfield_um.od) + ' \u00B5m';
    document.getElementById('oct-fovea-os-val').textContent = Math.round(st.central_subfield_um.os) + ' \u00B5m';

    const bio = octData.biomarkers || {};
    document.getElementById('oct-disc-elevation').textContent = `+${bio.optic_disc_elevation_um || 0} \u00B5m`;
    const frisGrade = bio.frisen_estimated_grade || 0;
    const frisEl = document.getElementById('oct-frisen-grade');
    frisEl.textContent = `Grado ${frisGrade} (${frisGrade === 0 ? 'Normal' : (frisGrade === 1 ? 'Mínimo' : (frisGrade === 2 ? 'Moderado' : 'Severo'))})`;
    frisEl.style.color = frisGrade >= 2 ? '#ef4444' : (frisGrade === 1 ? '#eab308' : '#22c55e');

    document.getElementById('oct-folds-status').textContent = bio.choroidal_folds_present ? 'DETECTADOS (Pliegues)' : 'Ausentes';
    document.getElementById('oct-folds-status').style.color = bio.choroidal_folds_present ? '#ef4444' : '#ffffff';
    document.getElementById('oct-foveal-flattening').textContent = `${bio.foveal_pit_flattening_pct || 0}%`;

    const colorMap = {
        'NORMAL_GREEN': '#22c55e',
        'BORDERLINE_YELLOW': '#eab308',
        'OUTSIDE_NORMAL_RED': '#ef4444',
        'ATROPHY_RED': '#ef4444',
    };

    // Actualizar Sectores ETDRS de OD
    if (octData.etdrs_grid && octData.etdrs_grid.od) {
        const odGrid = octData.etdrs_grid.od;
        const secMapping = [
            { key: 'central', id: 'sec-od-central' },
            { key: 'inner_superior', id: 'sec-od-inner-sup' },
            { key: 'inner_nasal', id: 'sec-od-inner-nas' },
            { key: 'inner_inferior', id: 'sec-od-inner-inf' },
            { key: 'inner_temporal', id: 'sec-od-inner-temp' },
            { key: 'outer_superior', id: 'sec-od-outer-sup' },
            { key: 'outer_nasal', id: 'sec-od-outer-nas' },
            { key: 'outer_inferior', id: 'sec-od-outer-inf' },
            { key: 'outer_temporal', id: 'sec-od-outer-temp' },
        ];

        secMapping.forEach(item => {
            const val = Math.round(odGrid.values[item.key] || 250);
            const status = odGrid.normative_status[item.key] || 'NORMAL_GREEN';
            const color = colorMap[status] || '#22c55e';
            const pathEl = document.getElementById(item.id);
            const txtEl = document.getElementById(item.id + '-val');
            if (pathEl) pathEl.setAttribute('fill', color);
            if (txtEl) txtEl.textContent = val;
        });
    }

    // Actualizar Sectores ETDRS de OS
    if (octData.etdrs_grid && octData.etdrs_grid.os) {
        const osGrid = octData.etdrs_grid.os;
        const secMappingOs = [
            { key: 'central', id: 'sec-os-central' },
            { key: 'inner_superior', id: 'sec-os-inner-sup' },
            { key: 'inner_nasal', id: 'sec-os-inner-nas' },
            { key: 'inner_inferior', id: 'sec-os-inner-inf' },
            { key: 'inner_temporal', id: 'sec-os-inner-temp' },
            { key: 'outer_superior', id: 'sec-os-outer-sup' },
            { key: 'outer_nasal', id: 'sec-os-outer-nas' },
            { key: 'outer_inferior', id: 'sec-os-outer-inf' },
            { key: 'outer_temporal', id: 'sec-os-outer-temp' },
        ];

        secMappingOs.forEach(item => {
            const val = Math.round(osGrid.values[item.key] || 248);
            const status = osGrid.normative_status[item.key] || 'NORMAL_GREEN';
            const color = colorMap[status] || '#22c55e';
            const pathEl = document.getElementById(item.id);
            const txtEl = document.getElementById(item.id + '-val');
            if (pathEl) pathEl.setAttribute('fill', color);
            if (txtEl) txtEl.textContent = val;
        });
    }

    renderOctHeatmap('octHeatmapOd', st.central_subfield_um.od, true);
    renderOctHeatmap('octHeatmapOs', st.central_subfield_um.os, false);

    if (octData.b_scans) {
        if (octData.b_scans.od && octData.b_scans.od.horizontal) {
            renderBScanCanvas('bscanCanvasOd', octData.b_scans.od.horizontal, true);
        }
        if (octData.b_scans.os && octData.b_scans.os.vertical) {
            renderBScanCanvas('bscanCanvasOs', octData.b_scans.os.vertical, false);
        }
    }
}

// ==========================================================================
// MOTOR 3D INTERACTIVO (THREE.JS) - GEMELO DIGITAL OCULAR
// ==========================================================================
let scene, camera, renderer, controls;
let scleraMesh, corneaMesh, lensMesh, irisMesh, opticNerveMesh, opticDiscMesh;
let rayLinesGroup, forceVectorGroup;
let isWireframe = false;

const container = document.getElementById('threeCanvasContainer');

function initThreeEngine() {
    // 1. Escena & Niebla
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x05070d);
    scene.fog = new THREE.FogExp2(0x05070d, 0.025);

    // 2. Cámara de Perspectiva
    const width = container.clientWidth;
    const height = container.clientHeight;
    camera = new THREE.PerspectiveCamera(40, width / height, 0.1, 1000);
    camera.position.set(24, 8, 22);

    // 3. Renderer WebGL con antialias
    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.shadowMap.enabled = true;
    container.appendChild(renderer.domElement);

    // 4. Controles Orbitales (Mouse drag & zoom)
    controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
    controls.maxDistance = 55;
    controls.minDistance = 8;
    controls.target.set(0, 0, 0);

    // 5. Iluminación Aeroespacial / Quirúrgica
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.85);
    scene.add(ambientLight);

    const dirLight1 = new THREE.DirectionalLight(0xffffff, 1.2);
    dirLight1.position.set(20, 30, 20);
    scene.add(dirLight1);

    const dirLight2 = new THREE.DirectionalLight(0x38bdf8, 0.6);
    dirLight2.position.set(-20, -15, -20);
    scene.add(dirLight2);

    // 6. Cuadrícula de Referencia Biomédica (Grid en el piso)
    const gridHelper = new THREE.GridHelper(30, 30, 0x1e293b, 0x0f172a);
    gridHelper.position.y = -8;
    scene.add(gridHelper);

    // 7. Construcción de Malla Anatómica 3D del Ojo
    buildEyeAnatomy();

    // 8. Loop de Animación
    animate();

    // 9. Resize Listener
    window.addEventListener('resize', onWindowResize);
}

function buildEyeAnatomy() {
    // A. Esclera (Globo Ocular) - Esfera deformable
    // Radio base: 7.5 unidades (representando los ~24mm anatómicos)
    const scleraGeo = new THREE.SphereGeometry(7.5, 48, 48);
    const scleraMat = new THREE.MeshPhysicalMaterial({
        color: 0xf1f5f9,
        roughness: 0.35,
        metalness: 0.1,
        transmission: 0.15,
        opacity: 0.92,
        transparent: true,
        clearcoat: 0.6,
        clearcoatRoughness: 0.2
    });
    scleraMesh = new THREE.Mesh(scleraGeo, scleraMat);
    scene.add(scleraMesh);

    // Guardar posiciones de vértices originales para deformación biomecánica
    scleraGeo.userData = { originalPositions: scleraGeo.attributes.position.clone() };

    // B. Córnea Anterior (Transparente y abombada hacia +X)
    const corneaGeo = new THREE.SphereGeometry(3.6, 32, 32, 0, Math.PI * 2, 0, Math.PI / 2.2);
    const corneaMat = new THREE.MeshPhysicalMaterial({
        color: 0xffffff,
        roughness: 0.05,
        transmission: 0.88,
        transparent: true,
        opacity: 0.95,
        ior: 1.376
    });
    corneaMesh = new THREE.Mesh(corneaGeo, corneaMat);
    corneaMesh.rotation.z = -Math.PI / 2;
    corneaMesh.position.x = 6.2;
    scene.add(corneaMesh);

    // C. Iris (Anillo en +X)
    const irisGeo = new THREE.RingGeometry(1.2, 3.4, 32);
    const irisMat = new THREE.MeshStandardMaterial({
        color: 0x1e293b,
        side: THREE.DoubleSide,
        roughness: 0.6
    });
    irisMesh = new THREE.Mesh(irisGeo, irisMat);
    irisMesh.rotation.y = Math.PI / 2;
    irisMesh.position.x = 6.0;
    scene.add(irisMesh);

    // D. Cristalino (Lens)
    const lensGeo = new THREE.SphereGeometry(2.2, 32, 16);
    lensGeo.scale(0.5, 1, 1);
    const lensMat = new THREE.MeshPhysicalMaterial({
        color: 0xe2e8f0,
        transmission: 0.8,
        transparent: true,
        roughness: 0.1
    });
    lensMesh = new THREE.Mesh(lensGeo, lensMat);
    lensMesh.position.x = 4.8;
    scene.add(lensMesh);

    // E. Vaina del Nervio Óptico (Saliendo por -X hacia atrás)
    const nerveGeo = new THREE.CylinderGeometry(1.6, 1.8, 8, 32);
    nerveGeo.rotateZ(Math.PI / 2);
    const nerveMat = new THREE.MeshStandardMaterial({
        color: 0x334155,
        roughness: 0.7,
        metalness: 0.1
    });
    opticNerveMesh = new THREE.Mesh(nerveGeo, nerveMat);
    opticNerveMesh.position.x = -10.5;
    scene.add(opticNerveMesh);

    // F. Disco Óptico (Papila / Elevación Frisén)
    const discGeo = new THREE.CylinderGeometry(1.2, 1.2, 0.4, 32);
    discGeo.rotateZ(Math.PI / 2);
    const discMat = new THREE.MeshStandardMaterial({
        color: 0xe2e8f0,
        roughness: 0.4
    });
    opticDiscMesh = new THREE.Mesh(discGeo, discMat);
    opticDiscMesh.position.x = -7.2;
    scene.add(opticDiscMesh);

    // G. Rayos de Luz y Foco Refractivo
    rayLinesGroup = new THREE.Group();
    const rayMat = new THREE.LineDashedMaterial({
        color: 0xffffff,
        dashSize: 0.4,
        gapSize: 0.2,
        linewidth: 1.5
    });

    const ray1Geo = new THREE.BufferGeometry().setFromPoints([
        new THREE.Vector3(16, 2.5, 0),
        new THREE.Vector3(6.2, 1.2, 0),
        new THREE.Vector3(-7.2, 0, 0)
    ]);
    const ray2Geo = new THREE.BufferGeometry().setFromPoints([
        new THREE.Vector3(16, -2.5, 0),
        new THREE.Vector3(6.2, -1.2, 0),
        new THREE.Vector3(-7.2, 0, 0)
    ]);
    const ray1 = new THREE.Line(ray1Geo, rayMat);
    const ray2 = new THREE.Line(ray2Geo, rayMat);
    ray1.computeLineDistances();
    ray2.computeLineDistances();
    rayLinesGroup.add(ray1);
    rayLinesGroup.add(ray2);

    // Esfera indicadora del Punto Focal
    const focalGeo = new THREE.SphereGeometry(0.25, 16, 16);
    const focalMat = new THREE.MeshBasicMaterial({ color: 0xffffff });
    const focalSphere = new THREE.Mesh(focalGeo, focalMat);
    focalSphere.name = 'focalSphere';
    focalSphere.position.set(-7.2, 0, 0);
    rayLinesGroup.add(focalSphere);

    scene.add(rayLinesGroup);

    // H. Vectores de Fuerza de Presión Cefálica (Flechas de Empuje)
    forceVectorGroup = new THREE.Group();
    const dir = new THREE.Vector3(1, 0, 0);
    const origin1 = new THREE.Vector3(-14, 2.2, 0);
    const origin2 = new THREE.Vector3(-14, -2.2, 0);
    const arrow1 = new THREE.ArrowHelper(dir, origin1, 3.8, 0x94a3b8, 0.8, 0.4);
    const arrow2 = new THREE.ArrowHelper(dir, origin2, 3.8, 0x94a3b8, 0.8, 0.4);
    forceVectorGroup.add(arrow1);
    forceVectorGroup.add(arrow2);
    scene.add(forceVectorGroup);
}

function onWindowResize() {
    if (!container || !renderer || !camera) return;
    const width = container.clientWidth;
    const height = container.clientHeight;
    if (width > 0 && height > 0) {
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height);
    }
}

function animate() {
    requestAnimationFrame(animate);
    controls.update();

    // Pulso suave en las flechas de fuerza si hay presión en microgravedad
    const time = Date.now() * 0.003;
    if (forceVectorGroup) {
        forceVectorGroup.children.forEach((arrow, i) => {
            arrow.position.x = -14 + Math.sin(time + i) * 0.3;
        });
    }

    renderer.render(scene, camera);
}

// ==========================================================================
// VISTAS PRECONFIGURADAS DE CÁMARA
// ==========================================================================
function setPresetView(mode) {
    document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));

    if (mode === 'free') {
        document.getElementById('btn-view-free').classList.add('active');
        controls.autoRotate = true;
        controls.autoRotateSpeed = 1.0;
    } else {
        controls.autoRotate = false;
    }

    if (mode === 'sagittal') {
        document.getElementById('btn-view-sagittal').classList.add('active');
        camera.position.set(0, 0, 24);
        camera.lookAt(0, 0, 0);
        controls.target.set(0, 0, 0);
    } else if (mode === 'fundus') {
        document.getElementById('btn-view-fundus').classList.add('active');
        camera.position.set(24, 0, 0);
        camera.lookAt(0, 0, 0);
        controls.target.set(0, 0, 0);
    }
}

function resetCamera() {
    controls.autoRotate = false;
    camera.position.set(24, 8, 22);
    controls.target.set(0, 0, 0);
    document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById('btn-view-free').classList.add('active');
}

function toggleWireframe() {
    isWireframe = !isWireframe;
    scleraMesh.material.wireframe = isWireframe;
    corneaMesh.material.wireframe = isWireframe;
    opticNerveMesh.material.wireframe = isWireframe;
    const btn = document.getElementById('btn-toggle-wireframe');
    if (isWireframe) {
        btn.classList.add('active');
        scleraMesh.material.color.setHex(0xffffff);
    } else {
        btn.classList.remove('active');
        scleraMesh.material.color.setHex(0xf1f5f9);
    }
}

// ==========================================================================
// ACTUALIZACIÓN TELEMÉTRICA Y DEFORMACIÓN 3D POR DÍA DE VUELO
// ==========================================================================
function renderDay(index) {
    currentIndex = index;
    const day = timelineDays[index];
    const data = simulationData[day] || {};

    // 1. Textos principales
    document.getElementById('flight-day-badge').textContent = `DÍA DE VUELO: FD${day}` + (day === 0 ? ' (Línea Base Pre-Vuelo)' : '');

    const shiftVal = Number(data.od_shift || 0);
    document.getElementById('val-shift').textContent = (shiftVal > 0 ? '+' : '') + shiftVal.toFixed(2) + ' D';

    document.getElementById('val-snellen').textContent = data.od_snellen || '20/20';
    document.getElementById('val-logmar').textContent = 'LogMAR ' + Number(data.od_logmar || 0).toFixed(3);
    document.getElementById('val-iop').textContent = Number(data.iop || 15).toFixed(1) + ' mmHg';

    const frisen = Number(data.frisen_grade || 0);
    document.getElementById('val-frisen').textContent = frisen === 0 ? 'Grado 0 (Normal)' : `Grado ${frisen} (Edema)`;

    const isFlattening = Boolean(data.globe_flattening);
    document.getElementById('val-flattening').textContent = isFlattening ? 'POSITIVO (Detectado)' : 'Ausente';

    const isFolds = Boolean(data.choroidal_folds);
    document.getElementById('val-folds').textContent = isFolds ? 'PRESENTES (Detectados)' : 'No detectados';

    const rnflVal = Number(data.rnfl || 95);
    document.getElementById('val-rnfl').textContent = rnflVal + ' \u00B5m';

    // 2. Cálculo axial métrico de deformación
    // En microgravedad prolongada, el aplanamiento reduce ~0.65 mm la longitud axial
    const axialDeltaMm = isFlattening ? Math.min(0.85, 0.25 + (shiftVal * 0.4)) : 0.00;
    const currentAxialLen = 24.20 - axialDeltaMm;
    document.getElementById('val-axial-len').textContent = currentAxialLen.toFixed(2) + ' mm';
    document.getElementById('val-axial-delta').textContent = axialDeltaMm > 0 ? `-${axialDeltaMm.toFixed(2)} mm de acortamiento axial` : 'Base pre-vuelo nominal';

    // 3. HUD status y coordenadas
    const postZMm = (-12.10 + axialDeltaMm).toFixed(2);
    document.getElementById('hud-z-coord').textContent = postZMm + ' mm';
    document.getElementById('hud-deform-mm').textContent = (axialDeltaMm > 0 ? '-' : '') + axialDeltaMm.toFixed(2) + ' mm';
    
    const retroIcp = (15.0 + (day > 0 ? (day * 0.04) : 0)).toFixed(1);
    document.getElementById('hud-icp-val').textContent = retroIcp + ' mmHg';

    const hudBadge = document.getElementById('hud-tier-badge');
    const riskBadge = document.getElementById('twin-risk-badge');
    const vectorStatus = document.getElementById('hud-vector-status');

    if (data.risk_tier === 'severe' || data.risk_tier === 'high') {
        hudBadge.textContent = 'ALERTA SANS TIER 3';
        riskBadge.textContent = 'RIESGO ALTO';
        vectorStatus.textContent = 'SOBREPRESIÓN RETROBULBAR CRÍTICA';
        vectorStatus.style.color = '#f87171';
    } else if (data.risk_tier === 'moderate' || data.risk_tier === 'medium') {
        hudBadge.textContent = 'ALERTA SANS TIER 2';
        riskBadge.textContent = 'RIESGO MODERADO';
        vectorStatus.textContent = 'INCREMENTO CEFÁLICO MODERADO';
        vectorStatus.style.color = '#fbbf24';
    } else {
        hudBadge.textContent = 'NOMINAL FD' + day;
        riskBadge.textContent = 'RIESGO BAJO';
        vectorStatus.textContent = 'GRADIENTE ESTABLE (1G)';
        vectorStatus.style.color = '#4ade80';
    }

    // 4. Curva OCT B-Scan Reactiva
    const elevationUm = Math.round((rnflVal - 95) * 2.5);
    document.getElementById('oct-elevation-val').textContent = `ELEVACIÓN PAPILAR: +${Math.max(0, elevationUm)} \u00B5m`;
    const peakY = 32 - Math.min(22, (frisen * 8));
    document.getElementById('octCurvePath').setAttribute('d', 
        `M 0 34 Q 75 34 110 ${peakY + 4} Q 150 ${peakY} Q 190 ${peakY + 4} Q 225 34 300 34`
    );

    // ======================================================================
    // 5. DEFORMACIÓN DE VÉRTICES DE LA MALLA 3D (THREE.JS)
    // ======================================================================
    if (scleraMesh) {
        const geo = scleraMesh.geometry;
        const posAttr = geo.attributes.position;
        const origPos = geo.userData.originalPositions;

        // Factor de compresión escleral en el polo posterior (-X)
        const flatteningScale = isFlattening ? (0.15 + (shiftVal * 0.12)) : 0.0;

        for (let i = 0; i < posAttr.count; i++) {
            const ox = origPos.getX(i);
            const oy = origPos.getY(i);
            const oz = origPos.getZ(i);

            // Si el vértice está en el hemisferio posterior (X < 0)
            if (ox < 0) {
                // Cuanto más posterior sea X, mayor es el aplastamiento hacia el centro
                const weight = Math.abs(ox) / 7.5;
                const newX = ox * (1.0 - (flatteningScale * weight));
                posAttr.setX(i, newX);
            } else {
                posAttr.setX(i, ox);
            }
        }
        posAttr.needsUpdate = true;
        geo.computeVertexNormals();

        // Mover el disco óptico y nervio de acuerdo a la pared posterior aplanada
        const newPostX = -7.5 * (1.0 - (flatteningScale * 0.8));
        opticDiscMesh.position.x = newPostX + 0.3;
        opticDiscMesh.scale.set(1, 1 + (frisen * 0.4), 1 + (frisen * 0.4));
        opticNerveMesh.position.x = newPostX - 3.8;

        // Desplazar el punto focal: al aplanarse la retina hacia adelante (+X),
        // el punto de foco de luz queda situado POR DETRÁS de la retina aplanada
        const focalObj = rayLinesGroup.getObjectByName('focalSphere');
        if (focalObj) {
            focalObj.position.x = -7.2 + (shiftVal * 0.8);
        }
    }

    // 6. Actualización Reactiva de la Estación Tomográfica ZEISS Cirrus HD-OCT
    fetch(`{{ route('public.digital-twin.oct-telemetry') }}?code=${currentAstroCode}&name=${encodeURIComponent(currentAstroName)}&day=${day}&scenario=${currentScenario}`)
        .then(res => res.json())
        .then(octData => {
            currentCirrusData = octData;
            updateCirrusOctDisplay(octData);
        })
        .catch(err => {
            console.debug('OCT telemetry fetch:', err);
        });
}

function setSliderIndex(idx) {
    document.getElementById('day-slider').value = idx;
    renderDay(idx);
}

document.getElementById('day-slider').addEventListener('input', function() {
    renderDay(parseInt(this.value));
});

// Control Play / Pause de Simulación Continua
document.getElementById('play-pause-btn').addEventListener('click', function() {
    if (playInterval) {
        clearInterval(playInterval);
        playInterval = null;
        document.getElementById('play-icon').className = 'fa fa-play';
        document.getElementById('play-text').textContent = 'Simular Misión';
    } else {
        document.getElementById('play-icon').className = 'fa fa-pause';
        document.getElementById('play-text').textContent = 'Pausar';
        playInterval = setInterval(() => {
            let next = (currentIndex + 1) % timelineDays.length;
            setSliderIndex(next);
        }, 1800);
    }
});

// Inicializar Three.js y marco basal inicial
document.addEventListener('DOMContentLoaded', function() {
    initThreeEngine();
    renderDay(0);
    if (currentCirrusData) {
        updateCirrusOctDisplay(currentCirrusData);
    }
    const initialMode = @json($initialMode ?? '3d');
    const urlParams = new URLSearchParams(window.location.search);
    const mode = urlParams.get('mode') || initialMode;
    if (mode === 'oct' || mode === 'dual') {
        setWorkstationMode(mode);
    }
});
</script>
@endpush
