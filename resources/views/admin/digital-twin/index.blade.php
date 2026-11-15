@extends('layouts.app')

@php
    $isEn = app()->getLocale() === 'en';
    $trMap = [
        'Listo para revisión' => 'Ready for review',
        'Priorizar revisión' => 'Prioritize review',
        'Faltan datos' => 'Missing data',
        'Estable' => 'Stable',
        'En progresión' => 'Worsening / In progression',
        'Sin datos suficientes' => 'Insufficient data',
        'Aptitud no evaluada' => 'Fitness not evaluated',
        'Sin medición' => 'Unmeasured',
        'Sin escaneo' => 'Unscanned',
    ];
    $t = fn (?string $val) => ($isEn && $val !== null && isset($trMap[$val])) ? $trMap[$val] : $val;
    $headerBadgeClass = match($caseEvaluation['case_operational_code']) {
        'LISTO_PARA_REVISION' => 'status-badge-ready',
        'PRIORIZAR_REVISION' => 'status-badge-prioritize',
        default => 'status-badge-missing',
    };
@endphp

@section('title', 'Astronaut Digital Twin · Gemelo Digital Neurooftalmológico 3D & Consola OCT')

@push('styles')
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
<style>
    .twin-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(360px, 1fr);
        gap: 18px;
        margin-bottom: 18px;
    }
    @media (max-width: 1024px) {
        .twin-layout { grid-template-columns: 1fr; }
    }
    .viewport-card {
        background: #05070d;
        border: 1.5px solid #1e293b;
        border-radius: var(--radius);
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        display: flex;
        flex-direction: column;
    }
    .viewport-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 16px;
        background: #07173F;
        border-bottom: 1px solid #1e3a8a;
        color: #f8fafc;
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
        border: 1px dashed rgba(46, 150, 245, 0.28);
        border-radius: 50%;
        pointer-events: none;
    }
    .hud-reticle::before,
    .hud-reticle::after {
        content: '';
        position: absolute;
        background: rgba(46, 150, 245, 0.35);
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
        bottom: 12px;
        left: 14px;
        font-family: ui-monospace, monospace;
        font-size: 11px;
        color: #cbd5e1;
        line-height: 1.45;
        background: rgba(7, 23, 63, 0.85);
        padding: 7px 11px;
        border-radius: 6px;
        border: 1px solid #1e3a8a;
        backdrop-filter: blur(4px);
    }
    .hud-scan-beam {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(46, 150, 245, 0.85), transparent);
        opacity: 0.45;
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
        flex: 1;
        min-height: 500px;
        position: relative;
        cursor: grab;
    }
    #threeCanvasContainer canvas {
        display: block;
        width: 100% !important;
        height: 100% !important;
    }
    #threeCanvasContainer:active {
        cursor: grabbing;
    }
    .viewport-controls {
        position: absolute;
        top: 14px;
        right: 14px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        z-index: 10;
    }
    .view-btn {
        background: rgba(7, 23, 63, 0.88);
        color: #f1f5f9;
        border: 1px solid #1e3a8a;
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        backdrop-filter: blur(4px);
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-align: left;
    }
    .view-btn:hover {
        background: #0042A6;
        border-color: #2E96F5;
    }
    .view-btn.active {
        background: #0042A6;
        color: #EAFE07;
        border-color: #2E96F5;
        box-shadow: 0 0 0 2px rgba(46, 150, 245, 0.25);
    }
    .slider-rail {
        width: 100%;
        accent-color: #0042A6;
        height: 8px;
        border-radius: 4px;
        cursor: pointer;
    }
    .gauge-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    .gauge-cell {
        background: var(--background);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 10px 12px;
    }
    .gauge-label {
        font-size: 10px;
        text-transform: uppercase;
        color: var(--muted-foreground);
        font-weight: 700;
        letter-spacing: .04em;
    }
    .gauge-val {
        font-size: 18px;
        font-weight: 800;
        font-family: ui-monospace, monospace;
        color: var(--foreground);
        margin-top: 2px;
    }
    .oct-scan-box {
        background: #09090b;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 12px;
        margin-top: 10px;
    }
    .sample-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 11px;
        border-radius: 8px;
        border: 1.5px solid var(--border);
        background: var(--card);
        color: var(--foreground);
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .sample-pill:hover {
        border-color: #2E96F5;
        background: rgba(46, 150, 245, 0.08);
    }
    .sample-pill.active {
        background: #0042A6;
        color: #ffffff;
        border-color: #07173F;
        box-shadow: 0 3px 10px rgba(0, 66, 166, 0.25);
    }
    .sample-row {
        cursor: pointer;
        transition: background 0.12s ease;
    }
    .sample-row:hover {
        background: rgba(46, 150, 245, 0.08) !important;
    }
    .sample-row.active-sample-row {
        background: rgba(0, 66, 166, 0.12) !important;
        border-left: 4px solid #0042A6;
    }

    /* ZEISS Cirrus HD-OCT Workstation Styles (Clinical Mode) */
    .oct-workstation {
        background: #09090b;
        border: 1.5px solid #1e293b;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.35);
        color: #f4f4f5;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    }
    .oct-topbar {
        background: #07173F;
        border-bottom: 1px solid #1e3a8a;
        padding: 10px 16px;
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
        background: #EAFE07;
        color: #07173F;
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
        padding: 14px;
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
        padding: 8px 0;
    }
    .oct-table {
        width: 100%;
        font-size: 11px;
        border-collapse: collapse;
    }
    .oct-table th {
        color: #94a3b8;
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
{{-- 1. CABECERA CIENTÍFICA COMPACTA VINCULADA AL EXPEDIENTE SANS --}}
<div class="card" style="padding:1rem 1.25rem;margin-bottom:1rem;border:1.5px solid var(--space-blue);">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;">
        <div>
            <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;margin-bottom:0.25rem;">
                <span class="interactive-pill" style="background:#07173F;color:#EAFE07;border-color:#07173F;font-size:0.68rem;">
                    <i class="fa fa-flask"></i>
                    {{ $isEn ? 'FOR GROUND SCIENTISTS & CLINICAL INVESTIGATORS · 3D & OCT WORKSTATION' : 'MÓDULO PARA CIENTÍFICOS E INVESTIGADORES · VISOR 3D Y TOMOGRAFÍA OCT VINCULADA' }}
                </span>
                <span style="font-size:0.74rem;color:var(--muted-foreground);font-weight:600;">
                    Astronaut Digital Twin · <code>{{ $activeCase->case_id }}</code> ({{ $astronaut->code }})
                </span>
            </div>
            <h1 style="font-size:1.28rem;font-weight:800;margin:0;">
                {{ $isEn
                    ? 'Gemelo Digital Neurooftalmológico 3D & Linked ZEISS Cirrus™ HD-OCT Samples'
                    : 'Gemelo Digital Neurooftalmológico 3D y Muestras ZEISS Cirrus™ HD-OCT Vinculadas' }}
            </h1>
        </div>

        <div style="display:flex;gap:0.55rem;align-items:center;flex-wrap:wrap;">
            <div style="padding:0.38rem 0.7rem;border:1px solid var(--border);border-radius:7px;text-align:right;background:var(--muted);">
                <div style="font-size:0.62rem;text-transform:uppercase;color:var(--muted-foreground);">
                    {{ $isEn ? 'Case Result' : 'Resultado Expediente' }} ({{ $activeCase->case_id }})
                </div>
                <span class="{{ $headerBadgeClass }}">
                    <i class="fa {{ $caseEvaluation['case_operational_code'] === 'LISTO_PARA_REVISION' ? 'fa-check-circle' : ($caseEvaluation['case_operational_code'] === 'PRIORIZAR_REVISION' ? 'fa-exclamation-triangle' : 'fa-question-circle') }}"></i>
                    {{ $t($caseEvaluation['case_operational_text']) }}
                </span>
            </div>
            <div style="padding:0.38rem 0.7rem;border:1px solid var(--border);border-radius:7px;text-align:right;background:var(--muted);">
                <div style="font-size:0.62rem;text-transform:uppercase;color:var(--muted-foreground);">
                    {{ $isEn ? 'Signal Index' : 'Índice Predictor' }}
                </div>
                <div style="font-size:0.86rem;font-weight:800;color:var(--space-blue);">{{ $biomarkerPredictor['signal_index'] }} / 100</div>
            </div>
            <a href="{{ route('home', ['case_id' => $activeCase->case_id, 'tab' => 'biomarcadores']) }}" id="btn-open-predictor" class="btn btn-primary" style="padding:0.5rem 0.85rem;font-size:0.8rem;">
                <i class="fa fa-sliders"></i> {{ $isEn ? 'Open in SANS Predictor' : 'Abrir en Predictor SANS' }}
            </a>
        </div>
    </div>
</div>

{{-- 2. SELECTOR VISUAL DE LOS 6 ASTRONAUTAS / EXPEDIENTES (CON FOTOS A COLOR Y ESTADO OPERATIVO REAL) --}}
<div class="card" style="padding:0.9rem 1.15rem;margin-bottom:1rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:0.75rem;flex-wrap:wrap;margin-bottom:0.65rem;">
        <div style="display:flex;align-items:center;gap:0.75rem;">
            <img src="{{ $astronaut->avatar_url }}"
                 alt="{{ $astronaut->name }}"
                 style="width:46px;height:46px;border-radius:10px;object-fit:cover;border:2px solid var(--space-blue);flex-shrink:0;">
            <div>
                <div style="display:flex;align-items:center;gap:0.45rem;flex-wrap:wrap;">
                    <strong style="font-size:0.98rem;">{{ $astronaut->name }}</strong>
                    <span class="badge" style="font-size:0.68rem;">{{ $astronaut->code }} · {{ $activeCase->case_id }}</span>
                    <span style="font-size:0.74rem;color:var(--muted-foreground);font-weight:600;">{{ $astronaut->mission_name }}</span>
                </div>
                <div style="font-size:0.76rem;color:var(--muted-foreground);margin-top:0.12rem;">
                    <strong>OD:</strong> {{ $t($caseEvaluation['eyes']['OD']['operational_text']) }} ({{ $t($caseEvaluation['eyes']['OD']['trajectory_text']) }}) ·
                    <strong>OS:</strong> {{ $t($caseEvaluation['eyes']['OS']['operational_text']) }} ({{ $t($caseEvaluation['eyes']['OS']['trajectory_text']) }})
                </div>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:0.5rem;">
            <label for="astro-select" style="font-size:0.75rem;font-weight:700;color:var(--muted-foreground);">
                <i class="fa fa-user-astronaut"></i> {{ $isEn ? 'Select Astronaut:' : 'Seleccionar Astronauta:' }}
            </label>
            <select id="astro-select" class="form-control" style="width:auto;min-width:250px;height:34px;padding:0.25rem 0.6rem;font-size:0.8rem;font-weight:700;"
                    onchange="switchAstronaut(this.value)">
                @foreach ($crewRoster as $member)
                    <option value="{{ $member['astro_code'] }}" @selected($astronaut && $astronaut->code === $member['astro_code'])>
                        {{ $member['astro_code'] }} — {{ $member['name'] }} ({{ $member['case_id'] }} · {{ $t($member['operational_text']) }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- 6 Tarjetas Interactivas de Tripulación --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(175px,1fr));gap:0.5rem;">
        @foreach($crewRoster as $member)
            @php
                $isSelectedAstro = $member['case_id'] === $activeCase->case_id;
                $cBadge = match($member['operational_code']) {
                    'LISTO_PARA_REVISION' => 'status-badge-ready',
                    'PRIORIZAR_REVISION' => 'status-badge-prioritize',
                    default => 'status-badge-missing',
                };
            @endphp
            <a href="{{ route('public.digital-twin', ['astro' => $member['astro_code'], 'case_id' => $member['case_id'], 'mode' => $initialMode]) }}"
               class="clickable-card {{ $isSelectedAstro ? 'is-selected' : '' }}"
               style="display:flex;align-items:center;gap:0.55rem;padding:0.45rem 0.6rem;border-radius:8px;text-decoration:none;color:var(--foreground);">
                <img src="{{ $member['avatar'] }}"
                     alt="{{ $member['name'] }}"
                     style="width:36px;height:36px;border-radius:7px;object-fit:cover;border:1.5px solid {{ $isSelectedAstro ? 'var(--space-electric)' : 'var(--border)' }};flex-shrink:0;">
                <div style="min-width:0;flex:1;">
                    <div style="font-size:0.76rem;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $member['name'] }}
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:0.25rem;margin-top:0.12rem;">
                        <span style="font-size:0.63rem;color:var(--muted-foreground);font-family:monospace;">{{ $member['case_id'] }}</span>
                        <span class="{{ $cBadge }}" style="font-size:0.58rem;padding:0.06rem 0.32rem;">{{ $t($member['operational_text']) }}</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>

{{-- 3. SELECTOR DE MUESTRAS CLÍNICAS REALES Y RESULTADO VINCULADO (ANTES · DURANTE · POSVUELO +30/+60/+90d) --}}
<div class="card" style="padding:1rem 1.2rem;margin-bottom:1rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.65rem;flex-wrap:wrap;gap:0.6rem;">
        <div style="display:flex;align-items:center;gap:0.6rem;flex-wrap:wrap;">
            <button type="button" class="btn btn-primary" id="play-pause-btn" style="padding:0.35rem 0.8rem;font-size:0.78rem;min-height:32px;">
                <i class="fa fa-play" id="play-icon"></i> <span id="play-text">{{ $isEn ? 'Play Samples' : 'Recorrer Muestras' }}</span>
            </button>
            <div>
                <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;color:var(--space-blue);font-weight:800;">
                    {{ $isEn ? 'LINKED LONGITUDINAL SAMPLES (' . count($clinicalSamples) . ' RECORDS)' : 'MUESTRAS CLÍNICAS VINCULADAS AL EXPEDIENTE (' . count($clinicalSamples) . ' CORTES)' }}
                </div>
                <div style="font-size:0.86rem;font-weight:800;color:var(--foreground);" id="flight-day-badge">
                    {{ $clinicalSamples[$initialSampleIndex]['stage_es'] ?? 'FD0' }}
                </div>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
            <span id="sample-status-pill" class="status-badge-prioritize">
                {{ $clinicalSamples[$initialSampleIndex]['status_es'] ?? '' }}
            </span>
            <span style="font-size:0.74rem;font-family:ui-monospace,monospace;padding:0.25rem 0.55rem;background:var(--muted);border-radius:6px;border:1px solid var(--border);" id="sample-id-badge">
                ID: {{ $clinicalSamples[$initialSampleIndex]['sample_id'] ?? '' }}
            </span>
        </div>
    </div>

    {{-- Botones rápidos por muestra longitudinal --}}
    <div style="display:flex;gap:0.45rem;flex-wrap:wrap;margin-bottom:0.65rem;" id="sample-pills-container">
        @foreach ($clinicalSamples as $idx => $s)
            <button type="button"
                    class="sample-pill {{ $idx === $initialSampleIndex ? 'active' : '' }}"
                    id="sample-pill-{{ $idx }}"
                    onclick="setSliderIndex({{ $idx }})">
                <i class="fa {{ $s['has_oct_od'] ? ($s['status_code'] === 'PRIORIZAR_REVISION' ? 'fa-exclamation-triangle' : 'fa-check-circle') : 'fa-ban' }}"></i>
                <span>{{ $s['code_label'] }}</span>
            </button>
        @endforeach
    </div>

    <input type="range" min="0" max="{{ max(0, count($clinicalSamples) - 1) }}" value="{{ $initialSampleIndex }}" step="1" class="slider-rail" id="day-slider">

    {{-- Nota clínica vinculada a la muestra seleccionada + Tabla rápida de todas las muestras del astronauta --}}
    <div style="margin-top:0.65rem;padding:0.6rem 0.85rem;background:var(--muted);border-radius:8px;border-left:4px solid var(--space-blue);display:flex;justify-content:space-between;align-items:center;gap:0.75rem;flex-wrap:wrap;">
        <div style="font-size:0.8rem;line-height:1.45;">
            <strong style="color:var(--space-blue);"><i class="fa fa-link"></i> {{ $isEn ? 'Sample Verdict:' : 'Dictamen de la Muestra:' }}</strong>
            <span id="sample-clinical-note">{{ $isEn ? ($clinicalSamples[$initialSampleIndex]['note_en'] ?? '') : ($clinicalSamples[$initialSampleIndex]['note_es'] ?? '') }}</span>
        </div>
        <a href="{{ route('home', ['case_id' => $activeCase->case_id, 'tab' => 'seguimiento']) }}"
           id="sample-timeline-link"
           style="font-size:0.75rem;font-weight:800;color:var(--space-blue);text-decoration:none;white-space:nowrap;">
            {{ $isEn ? 'View in +30/+60/+90d Timeline' : 'Ver en Línea +30/+60/+90d' }} <i class="fa fa-arrow-right"></i>
        </a>
    </div>
</div>

{{-- 4. BARRA DE MODO DE TRABAJO: VISTA DUAL SIMULTÁNEA (POR DEFECTO) / 3D / CONSOLA OCT --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:10px;">
    <div class="tabs" style="margin:0;">
        <button type="button" class="tab {{ $initialMode === 'dual' ? 'active' : '' }}" id="tab-btn-dual" onclick="setWorkstationMode('dual')">
            <i class="fa fa-th-large"></i> {{ $isEn ? 'Simultaneous Dual View (3D + OCT)' : 'Vista Dual Simultánea (3D + OCT)' }}
        </button>
        <button type="button" class="tab {{ $initialMode === '3d' ? 'active' : '' }}" id="tab-btn-3d" onclick="setWorkstationMode('3d')">
            <i class="fa fa-cube"></i> {{ $isEn ? '3D Digital Twin (Three.js)' : 'Gemelo Digital 3D (Three.js)' }}
        </button>
        <button type="button" class="tab {{ $initialMode === 'oct' ? 'active' : '' }}" id="tab-btn-oct" onclick="setWorkstationMode('oct')">
            <i class="fa fa-heartbeat"></i> {{ $isEn ? 'ZEISS Cirrus™ HD-OCT Console' : 'Consola ZEISS Cirrus™ HD-OCT' }}
        </button>
    </div>

    <div style="font-size:11.5px;color:var(--muted-foreground);font-family:ui-monospace,monospace;">
        PROTOCOLO: <strong style="color:var(--foreground);">{{ $activeCase->applied_protocol_version_id ?? 'SIN PROTOCOLO PREVIO (AT-01)' }}</strong> ·
        INSTRUMENTO: <strong style="color:var(--space-blue);">ZEISS CIRRUS™ HD-OCT</strong>
    </div>
</div>

{{-- 5. VISOR 3D BIOMECÁNICO + TELEMETRÍA Y TABLA DE MUESTRAS --}}
<div class="twin-layout" id="panel-3d-layout">
    {{-- Columna Izquierda: Visor 3D WebGL (Three.js) con HUD Aeroespacial --}}
    <div class="viewport-card">
        <div class="viewport-header">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="width:8px;height:8px;border-radius:50%;background:#EAFE07;display:inline-block;"></span>
                <strong style="letter-spacing:0.04em;">Corte Sagital Anatómico · Modelo 3D en Tiempo Real</strong>
                <span style="color:#93c5fd;">· {{ $astronaut->name }} ({{ $activeCase->case_id }})</span>
            </div>
            <div style="display:flex;gap:10px;align-items:center;">
                <span class="badge-subtle" id="hud-tier-badge" style="background:#0042A6;border-color:#2E96F5;color:#EAFE07;font-weight:800;">
                    NOMINAL FD0
                </span>
            </div>
        </div>

        <div id="threeCanvasContainer">
            <div class="viewport-hud">
                <div class="hud-scan-beam"></div>
                <div class="hud-reticle"></div>

                <div class="hud-coords">
                    <div>MUESTRA ACTIVA: <strong id="hud-sample-code" style="color:#EAFE07;">FD0</strong></div>
                    <div>ESPESOR OCT (OD / OS): <strong id="hud-oct-pair" style="color:#38bdf8;">280 / 279 &mu;m</strong></div>
                    <div>POLO POSTERIOR (Z): <span id="hud-z-coord">-12.10 mm</span> · DEFORMACIÓN: <span id="hud-deform-mm">0.00 mm</span></div>
                    <div>PIO TONOMETRÍA: <span id="hud-icp-val">15.0 mmHg</span></div>
                </div>
            </div>

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

        <div style="padding:9px 16px;background:#07173F;border-top:1px solid #1e3a8a;display:flex;justify-content:space-between;font-size:11px;color:#cbd5e1;font-family:ui-monospace,monospace;">
            <span><i class="fa fa-mouse-pointer"></i> Arrastra para rotar · Rueda para zoom</span>
            <span style="color:#f1f5f9;">ESTADO BIOMECÁNICO: <strong id="hud-vector-status" style="color:#EAFE07;">GRADIENTE ESTABLE (1G)</strong></span>
        </div>
    </div>

    {{-- Columna Derecha: Los 6 Biomarcadores Vinculados a la Muestra + Matriz de Muestras del Astronauta --}}
    <div style="display:flex;flex-direction:column;gap:14px;">
        <div class="card" style="padding:16px;margin:0;">
            <div style="margin-bottom:10px;display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
                <div>
                    <h2 class="card-title" style="font-size:14.5px;margin:0;">
                        {{ $isEn ? '6 Linked SANS Biomarkers (Sample Telemetry)' : 'Los 6 Biomarcadores SANS de la Muestra Activa' }}
                    </h2>
                    <p class="card-description" style="font-size:11.5px;margin:2px 0 0;" id="telemetry-subtitle">
                        {{ $astronaut->name }} · {{ $activeCase->case_id }}
                    </p>
                </div>
                <span class="badge-subtle" id="twin-risk-badge" style="font-weight:800;">RIESGO BAJO</span>
            </div>

            <div class="gauge-grid">
                <div class="gauge-cell">
                    <div class="gauge-label">1. Espesor OCT TRT (OD / OS)</div>
                    <div class="gauge-val" id="val-oct-trt" style="font-size:16px;color:var(--space-blue);">280 / 279 &mu;m</div>
                    <div style="font-size:10px;color:var(--muted-foreground);margin-top:2px;" id="val-oct-delta">&Delta;TRT: +0 &mu;m (Umbral &ge; +20 &mu;m)</div>
                </div>

                <div class="gauge-cell">
                    <div class="gauge-label">2. Longitud Axial (OD)</div>
                    <div class="gauge-val" id="val-axial-len">24.10 mm</div>
                    <div style="font-size:10px;color:var(--muted-foreground);margin-top:2px;" id="val-axial-delta">Base pre-vuelo nominal</div>
                </div>

                <div class="gauge-cell">
                    <div class="gauge-label">3. Hipermetropía (&Delta;SE)</div>
                    <div class="gauge-val" id="val-shift">+0.00 D</div>
                    <div style="font-size:10px;color:var(--muted-foreground);margin-top:2px;">Agudeza: <strong id="val-snellen">20/20</strong> (<span id="val-logmar">LogMAR 0.000</span>)</div>
                </div>

                <div class="gauge-cell">
                    <div class="gauge-label">4. Edema Papilar (Frisén)</div>
                    <div class="gauge-val" id="val-frisen" style="font-size:15px;">Grado 0 (Normal)</div>
                    <div style="font-size:10px;color:var(--muted-foreground);margin-top:2px;">RNFL: <strong id="val-rnfl">96 &mu;m</strong></div>
                </div>

                <div class="gauge-cell">
                    <div class="gauge-label">5. Presión Intraocular (PIO)</div>
                    <div class="gauge-val" id="val-iop">14.5 mmHg</div>
                    <div style="font-size:10px;color:var(--muted-foreground);margin-top:2px;">Aplanamiento: <strong id="val-flattening">Ausente</strong></div>
                </div>

                <div class="gauge-cell">
                    <div class="gauge-label">6. Pliegues / Contexto -6&deg; HDT</div>
                    <div class="gauge-val" id="val-folds" style="font-size:14px;">No detectados</div>
                    <div style="font-size:10px;color:var(--muted-foreground);margin-top:2px;">Fuente: <code>{{ $activeCase->source_type }}</code></div>
                </div>
            </div>

            {{-- Perfil de Tomografía OCT B-Scan Simulado --}}
            <div class="oct-scan-box">
                <div style="display:flex;justify-content:space-between;font-size:10.5px;color:#94a3b8;font-family:ui-monospace,monospace;margin-bottom:5px;">
                    <span>PERFIL RETINIANO OCT B-SCAN (MUESTRA ACTIVA)</span>
                    <span id="oct-elevation-val" style="color:#EAFE07;">ELEVACIÓN PAPILAR: 0 &mu;m</span>
                </div>
                <div style="height:44px;background:#000;border:1px solid #27272a;border-radius:4px;position:relative;overflow:hidden;">
                    <svg id="octCurveSvg" width="100%" height="44" preserveAspectRatio="none" viewBox="0 0 300 48" style="display:block;width:100%;height:44px;">
                        <path id="octBaseCurve" d="M 0 36 L 300 36" fill="none" stroke="#52525b" stroke-width="1" stroke-dasharray="4,4"/>
                        <path id="octCurvePath" d="M 0 34 Q 75 34 110 30 Q 150 24 190 30 Q 225 34 300 34" fill="none" stroke="#22c55e" stroke-width="2.5"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Tabla Interactiva de Todas las Muestras del Astronauta --}}
        <div class="card" style="padding:14px 16px;margin:0;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                <strong style="font-size:12.5px;text-transform:uppercase;letter-spacing:0.04em;color:var(--space-blue);">
                    <i class="fa fa-table"></i> {{ $isEn ? 'Click Any Sample to Inspect in 3D & OCT' : 'Haz Clic en Cualquier Muestra para Inspeccionar en 3D y OCT' }}
                </strong>
                <span style="font-size:11px;color:var(--muted-foreground);">{{ $activeCase->case_id }}</span>
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:11.5px;">
                    <thead>
                        <tr style="border-bottom:1.5px solid var(--border);text-align:left;color:var(--muted-foreground);">
                            <th style="padding:5px 6px;">{{ $isEn ? 'Sample / Cut' : 'Muestra / Corte' }}</th>
                            <th style="padding:5px 6px;text-align:right;">OD TRT</th>
                            <th style="padding:5px 6px;text-align:right;">&Delta;OD</th>
                            <th style="padding:5px 6px;text-align:right;">OS TRT</th>
                            <th style="padding:5px 6px;">{{ $isEn ? 'Result' : 'Resultado' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clinicalSamples as $idx => $s)
                            <tr class="sample-row {{ $idx === $initialSampleIndex ? 'active-sample-row' : '' }}"
                                id="sample-table-row-{{ $idx }}"
                                onclick="setSliderIndex({{ $idx }})"
                                style="border-bottom:1px solid var(--border);">
                                <td style="padding:6px;font-weight:700;">
                                    {{ $s['code_label'] }}
                                    <div style="font-size:9.5px;color:var(--muted-foreground);font-family:monospace;">{{ $s['observed_at'] }}</div>
                                </td>
                                <td style="padding:6px;text-align:right;font-family:monospace;font-weight:700;">
                                    {{ $s['od_trt_um'] !== null ? number_format($s['od_trt_um'], 0) . ' µm' : ($isEn ? 'No OCT' : 'Sin OCT') }}
                                </td>
                                <td style="padding:6px;text-align:right;font-family:monospace;font-weight:800;color:{{ ($s['od_delta_um'] ?? 0) >= 20 ? '#dc2626' : (($s['od_delta_um'] ?? 0) >= 10 ? '#d97706' : 'var(--foreground)') }};">
                                    {{ $s['od_delta_um'] !== null ? sprintf('%+d µm', $s['od_delta_um']) : '---' }}
                                </td>
                                <td style="padding:6px;text-align:right;font-family:monospace;">
                                    @if($s['os_trt_um'] !== null)
                                        {{ number_format($s['os_trt_um'], 0) }} &mu;m
                                        @if($s['os_missing_baseline'])
                                            <span style="font-size:9px;color:#d97706;display:block;">(Sin base)</span>
                                        @endif
                                    @else
                                        <span style="color:var(--muted-foreground);">{{ $isEn ? 'No OCT' : 'Sin OCT' }}</span>
                                    @endif
                                </td>
                                <td style="padding:6px;">
                                    @php
                                        $rowBadge = match($s['status_code']) {
                                            'LISTO_PARA_REVISION' => 'status-badge-ready',
                                            'PRIORIZAR_REVISION' => 'status-badge-prioritize',
                                            default => 'status-badge-missing',
                                        };
                                    @endphp
                                    <span class="{{ $rowBadge }}" style="font-size:9.5px;padding:1px 6px;">
                                        {{ $s['status_code'] === 'LISTO_PARA_REVISION' ? ($isEn ? 'Ready' : 'Listo') : ($s['status_code'] === 'PRIORIZAR_REVISION' ? ($isEn ? 'Prioritize' : 'Priorizar') : ($isEn ? 'Missing' : 'Faltan datos')) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- 6. ESTACIÓN DE TRABAJO CLÍNICA ZEISS CIRRUS™ HD-OCT VINCULADA A LA MUESTRA --}}
<div id="panel-cirrus-oct" class="oct-workstation" style="display:block;margin-bottom:16px;">
    <div class="oct-topbar">
        <div class="oct-brand">
            <span class="oct-brand-logo">ZEISS</span>
            <strong style="color:#ffffff;letter-spacing:0.04em;">Cirrus™ HD-OCT</strong>
            <span style="color:#93c5fd;">| SPECTRAL DOMAIN TECHNOLOGY</span>
        </div>
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <div>EXPEDIENTE: <strong style="color:#EAFE07;" id="oct-patient-info">{{ $astronaut->name }} ({{ $activeCase->case_id }} · {{ $astronaut->code }})</strong></div>
            <div>PROTOCOLO: <strong style="color:#38bdf8;">Macular Cube 512x128 &amp; Peripapillary TRT</strong></div>
            <div>COBERTURA OCT: <strong style="color:#22c55e;" id="oct-signal-badge">OD: VÁLIDA · OS: VÁLIDA</strong></div>
        </div>
    </div>

    {{-- Aviso dinámico cuando la muestra seleccionada NO tiene escaneo OCT (ej. CASE-NOSCAN-03A o Corte +60d omitido) --}}
    <div id="oct-missing-banner" style="display:none;background:#451a03;border-bottom:1px solid #f59e0b;padding:10px 16px;color:#fef3c7;font-size:12px;">
        <i class="fa fa-exclamation-triangle" style="color:#fbbf24;"></i>
        <strong id="oct-missing-title">SIN ESCANEO OCT REGISTRADO EN ESTA MUESTRA:</strong>
        <span id="oct-missing-desc">El sistema no inventa ni interpola tomografías faltantes (Reglas AT-01 / AT-07 / AT-15 / AT-17).</span>
    </div>

    <div class="oct-grid-container">
        {{-- Fila 1 - Panel 1: Mapa de Espesor ILM-RPE (OD) --}}
        <div class="oct-panel">
            <div class="oct-panel-title">
                <span>ILM-RPE &amp; Peripapillary Map (OD)</span>
                <span style="color:#38bdf8;font-size:10px;" id="oct-panel-od-tag">OJO DERECHO</span>
            </div>
            <div style="display:flex;justify-content:center;align-items:center;height:155px;position:relative;">
                <canvas id="octHeatmapOd" width="155" height="155" style="border-radius:50%;border:1px solid #27272a;"></canvas>
                <div style="position:absolute;right:4px;top:10px;bottom:10px;width:12px;border-radius:2px;background:linear-gradient(to top, #0284c7 0%, #22c55e 35%, #eab308 70%, #ef4444 100%);display:flex;flex-direction:column;justify-content:space-between;font-size:8px;color:#ffffff;padding:2px;text-shadow:0 0 2px #000;">
                    <span>350</span>
                    <span>280</span>
                    <span>200</span>
                </div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:10px;color:#a1a1aa;margin-top:6px;">
                <span>TRT Peripapilar OD: <strong style="color:#38bdf8;" id="oct-fovea-od-val">280 &mu;m</strong></span>
                <span id="oct-delta-od-label">&Delta;TRT: +0 &mu;m</span>
            </div>
        </div>

        {{-- Fila 1 - Panel 2: Fondo de Ojo Infrarrojo OCT Fundus (OD / OS) --}}
        <div class="oct-panel" style="display:flex;flex-direction:column;justify-content:space-between;">
            <div class="oct-panel-title">
                <span>OCT Fundus Infrared Alignment</span>
                <span style="color:#a1a1aa;font-size:10px;" id="oct-sample-date-label">Macular 6x6 mm</span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;padding:6px 0;">
                <div style="background:#05070a;border:1px solid #27272a;border-radius:4px;padding:8px;text-align:center;">
                    <div style="font-size:10px;color:#94a3b8;margin-bottom:4px;" id="fundus-od-caption">OD (Base: 280 &mu;m)</div>
                    <svg width="96" height="96" viewBox="0 0 100 100" style="margin:0 auto;background:#18181b;border-radius:50%;">
                        <circle cx="50" cy="50" r="45" fill="#27272a" stroke="#3f3f46" stroke-width="1"/>
                        <circle cx="50" cy="50" r="9" id="fundus-disc-od" fill="#09090b" stroke="#22c55e" stroke-width="1.5"/>
                        <line x1="10" y1="50" x2="90" y2="50" stroke="#22c55e" stroke-width="1.5" stroke-dasharray="2,2"/>
                        <line x1="50" y1="10" x2="50" y2="90" stroke="#22c55e" stroke-width="1.5" stroke-dasharray="2,2"/>
                    </svg>
                </div>
                <div style="background:#05070a;border:1px solid #27272a;border-radius:4px;padding:8px;text-align:center;">
                    <div style="font-size:10px;color:#94a3b8;margin-bottom:4px;" id="fundus-os-caption">OS (Base: 279 &mu;m)</div>
                    <svg width="96" height="96" viewBox="0 0 100 100" style="margin:0 auto;background:#18181b;border-radius:50%;">
                        <circle cx="50" cy="50" r="45" fill="#27272a" stroke="#3f3f46" stroke-width="1"/>
                        <circle cx="50" cy="50" r="9" id="fundus-disc-os" fill="#09090b" stroke="#38bdf8" stroke-width="1.5"/>
                        <line x1="10" y1="50" x2="90" y2="50" stroke="#38bdf8" stroke-width="1.5" stroke-dasharray="2,2"/>
                        <line x1="50" y1="10" x2="50" y2="90" stroke="#38bdf8" stroke-width="1.5" stroke-dasharray="2,2"/>
                    </svg>
                </div>
            </div>
            <div style="font-size:10px;color:#a1a1aa;text-align:center;" id="oct-fundus-footer">
                Trazabilidad directa con observación <code>sans_metric_observations</code>
            </div>
        </div>

        {{-- Fila 1 - Panel 3: Mapa de Espesor ILM-RPE (OS) --}}
        <div class="oct-panel">
            <div class="oct-panel-title">
                <span>ILM-RPE &amp; Peripapillary Map (OS)</span>
                <span style="color:#22c55e;font-size:10px;" id="oct-panel-os-tag">OJO IZQUIERDO</span>
            </div>
            <div style="display:flex;justify-content:center;align-items:center;height:155px;position:relative;">
                <canvas id="octHeatmapOs" width="155" height="155" style="border-radius:50%;border:1px solid #27272a;"></canvas>
                <div style="position:absolute;right:4px;top:10px;bottom:10px;width:12px;border-radius:2px;background:linear-gradient(to top, #0284c7 0%, #22c55e 35%, #eab308 70%, #ef4444 100%);display:flex;flex-direction:column;justify-content:space-between;font-size:8px;color:#ffffff;padding:2px;text-shadow:0 0 2px #000;">
                    <span>350</span>
                    <span>280</span>
                    <span>200</span>
                </div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:10px;color:#a1a1aa;margin-top:6px;">
                <span>TRT Peripapilar OS: <strong style="color:#22c55e;" id="oct-fovea-os-val">279 &mu;m</strong></span>
                <span id="oct-delta-os-label">&Delta;TRT: +0 &mu;m</span>
            </div>
        </div>

        {{-- Fila 2 - Panel 4: Tomograma Horizontal B-Scan (OD) --}}
        <div class="oct-panel">
            <div class="oct-panel-title">
                <span>Horizontal B-Scan (OD)</span>
                <span id="bscan-od-label" style="color:#38bdf8;font-size:10px;">512x128 Axial</span>
            </div>
            <div style="background:#020408;border:1px solid #1e1e24;border-radius:4px;height:135px;position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;">
                <canvas id="bscanCanvasOd" width="360" height="135" style="width:100%;height:100%;"></canvas>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:10px;color:#71717a;margin-top:6px;">
                <span>Temporal</span>
                <span style="color:#a1a1aa;">Perfil ILM / RNFL / RPE (OD)</span>
                <span>Nasal</span>
            </div>
        </div>

        {{-- Fila 2 - Panel 5: Tabla Cuantitativa Central de Espesor ILM-RPE y TRT --}}
        <div class="oct-panel" style="display:flex;flex-direction:column;justify-content:space-between;">
            <div class="oct-panel-title">
                <span>Tabla Cuantitativa OCT (Muestra Activa)</span>
                <span style="color:#EAFE07;font-size:10px;" id="oct-table-sample-tag">FD0</span>
            </div>
            <table class="oct-table">
                <thead>
                    <tr>
                        <th>Parámetro OCT</th>
                        <th style="color:#38bdf8;text-align:right;">OD</th>
                        <th style="color:#22c55e;text-align:right;">OS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Peripapillary TRT (&mu;m)</td>
                        <td style="text-align:right;font-weight:800;color:#38bdf8;" id="table-avg-od">280</td>
                        <td style="text-align:right;font-weight:800;color:#22c55e;" id="table-avg-os">279</td>
                    </tr>
                    <tr>
                        <td>Cambio vs Base (&Delta;TRT)</td>
                        <td style="text-align:right;font-weight:700;" id="table-delta-od">+0 &mu;m</td>
                        <td style="text-align:right;font-weight:700;" id="table-delta-os">+0 &mu;m</td>
                    </tr>
                    <tr>
                        <td>Central Subfield (&mu;m)</td>
                        <td style="text-align:right;font-weight:700;" id="table-central-od">256</td>
                        <td style="text-align:right;font-weight:700;" id="table-central-os">255</td>
                    </tr>
                    <tr>
                        <td>Volume Cube (mm&sup3;)</td>
                        <td style="text-align:right;font-weight:700;" id="table-vol-od">10.1</td>
                        <td style="text-align:right;font-weight:700;" id="table-vol-os">10.0</td>
                    </tr>
                    <tr>
                        <td>Asimetría Interocular (&Delta;OD-OS)</td>
                        <td colspan="2" style="text-align:right;font-weight:800;color:#22c55e;" id="table-asym">+1 &mu;m</td>
                    </tr>
                </tbody>
            </table>
            <div style="font-size:9.5px;color:#a1a1aa;text-align:center;padding-top:4px;">
                Umbral SANS Monitor: &Delta;TRT &ge; +20 &mu;m activa «Priorizar revisión»
            </div>
        </div>

        {{-- Fila 2 - Panel 6: Tomograma Vertical B-Scan (OS) --}}
        <div class="oct-panel">
            <div class="oct-panel-title">
                <span>Vertical B-Scan (OS)</span>
                <span id="bscan-os-label" style="color:#22c55e;font-size:10px;">Meridiano Vertical</span>
            </div>
            <div style="background:#020408;border:1px solid #1e1e24;border-radius:4px;height:135px;position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;">
                <canvas id="bscanCanvasOs" width="360" height="135" style="width:100%;height:100%;"></canvas>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:10px;color:#71717a;margin-top:6px;">
                <span>Superior</span>
                <span style="color:#a1a1aa;">Perfil ILM / RNFL / RPE (OS)</span>
                <span>Inferior</span>
            </div>
        </div>

        {{-- Fila 3 - Panel 7: Rejilla ETDRS de 9 Sectores (OD) --}}
        <div class="oct-panel">
            <div class="oct-panel-title">
                <span>Rejilla ETDRS 9 Sectores (OD)</span>
                <span style="color:#38bdf8;font-size:10px;">Deviation Map</span>
            </div>
            <div class="etdrs-circle-container">
                <svg id="etdrsSvgOd" width="150" height="150" viewBox="-100 -100 200 200">
                    <path d="M -63.6 -63.6 A 90 90 0 0 1 63.6 -63.6 L 39.6 -39.6 A 56 56 0 0 0 -39.6 -39.6 Z" id="sec-od-outer-sup" class="etdrs-sector" fill="#22c55e"/>
                    <text x="0" y="-72" class="etdrs-text" id="sec-od-outer-sup-val">280</text>

                    <path d="M 63.6 -63.6 A 90 90 0 0 1 63.6 63.6 L 39.6 39.6 A 56 56 0 0 0 39.6 -39.6 Z" id="sec-od-outer-nas" class="etdrs-sector" fill="#22c55e"/>
                    <text x="72" y="0" class="etdrs-text" id="sec-od-outer-nas-val">295</text>

                    <path d="M 63.6 63.6 A 90 90 0 0 1 -63.6 63.6 L -39.6 39.6 A 56 56 0 0 0 39.6 39.6 Z" id="sec-od-outer-inf" class="etdrs-sector" fill="#22c55e"/>
                    <text x="0" y="72" class="etdrs-text" id="sec-od-outer-inf-val">272</text>

                    <path d="M -63.6 63.6 A 90 90 0 0 1 -63.6 -63.6 L -39.6 -39.6 A 56 56 0 0 0 -39.6 39.6 Z" id="sec-od-outer-temp" class="etdrs-sector" fill="#22c55e"/>
                    <text x="-72" y="0" class="etdrs-text" id="sec-od-outer-temp-val">265</text>

                    <path d="M -39.6 -39.6 A 56 56 0 0 1 39.6 -39.6 L 17 -17 A 24 24 0 0 0 -17 -17 Z" id="sec-od-inner-sup" class="etdrs-sector" fill="#22c55e"/>
                    <text x="0" y="-40" class="etdrs-text" id="sec-od-inner-sup-val">302</text>

                    <path d="M 39.6 -39.6 A 56 56 0 0 1 39.6 39.6 L 17 17 A 24 24 0 0 0 17 -17 Z" id="sec-od-inner-nas" class="etdrs-sector" fill="#22c55e"/>
                    <text x="40" y="0" class="etdrs-text" id="sec-od-inner-nas-val">306</text>

                    <path d="M 39.6 39.6 A 56 56 0 0 1 -39.6 39.6 L -17 17 A 24 24 0 0 0 17 17 Z" id="sec-od-inner-inf" class="etdrs-sector" fill="#22c55e"/>
                    <text x="0" y="40" class="etdrs-text" id="sec-od-inner-inf-val">298</text>

                    <path d="M -39.6 39.6 A 56 56 0 0 1 -39.6 -39.6 L -17 -17 A 24 24 0 0 0 -17 17 Z" id="sec-od-inner-temp" class="etdrs-sector" fill="#22c55e"/>
                    <text x="-40" y="0" class="etdrs-text" id="sec-od-inner-temp-val">292</text>

                    <circle cx="0" cy="0" r="24" id="sec-od-central" class="etdrs-sector" fill="#22c55e" stroke="#09090b" stroke-width="1.5"/>
                    <text x="0" y="0" class="etdrs-text" id="sec-od-central-val">256</text>
                </svg>
            </div>
            <div style="display:flex;justify-content:center;gap:8px;font-size:9px;color:#a1a1aa;margin-top:4px;">
                <span style="color:#22c55e;">&#9632; Normal (&Delta;&lt;10 &mu;m)</span>
                <span style="color:#eab308;">&#9632; Vigilancia (&Delta;10-19 &mu;m)</span>
                <span style="color:#ef4444;">&#9632; Alerta (&Delta;&ge;20 &mu;m)</span>
            </div>
        </div>

        {{-- Fila 3 - Panel 8: Estado Diagnóstico de Biomarcadores Estructurales SANS --}}
        <div class="oct-panel" style="display:flex;flex-direction:column;justify-content:space-between;">
            <div class="oct-panel-title">
                <span>SANS Structural Biomarker Diagnostics</span>
                <span style="color:#38bdf8;font-size:10px;">Telemetría NASA HRP</span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin:4px 0;">
                <div style="background:#09090b;border:1px solid #27272a;border-radius:4px;padding:8px;">
                    <div style="font-size:10px;color:#71717a;">Elevación de Disco Óptico</div>
                    <div style="font-size:15px;font-weight:800;color:#38bdf8;" id="oct-disc-elevation">+0 &mu;m</div>
                </div>
                <div style="background:#09090b;border:1px solid #27272a;border-radius:4px;padding:8px;">
                    <div style="font-size:10px;color:#71717a;">Grado Frisén Muestra</div>
                    <div style="font-size:15px;font-weight:800;color:#22c55e;" id="oct-frisen-grade">Grado 0 (Normal)</div>
                </div>
                <div style="background:#09090b;border:1px solid #27272a;border-radius:4px;padding:8px;">
                    <div style="font-size:10px;color:#71717a;">Pliegues Coroideos</div>
                    <div style="font-size:12.5px;font-weight:700;color:#ffffff;" id="oct-folds-status">Ausentes</div>
                </div>
                <div style="background:#09090b;border:1px solid #27272a;border-radius:4px;padding:8px;">
                    <div style="font-size:10px;color:#71717a;">Acortamiento Axial</div>
                    <div style="font-size:12.5px;font-weight:700;color:#ffffff;" id="oct-foveal-flattening">24.10 mm</div>
                </div>
            </div>
            <div style="padding:6px;background:#18181b;border-radius:4px;font-size:10px;color:#EAFE07;text-align:center;" id="oct-operational-verdict">
                Dictamen de aptitud: Aptitud no evaluada (Requiere firma humana RF-11)
            </div>
        </div>

        {{-- Fila 3 - Panel 9: Rejilla ETDRS de 9 Sectores (OS) --}}
        <div class="oct-panel">
            <div class="oct-panel-title">
                <span>Rejilla ETDRS 9 Sectores (OS)</span>
                <span style="color:#22c55e;font-size:10px;">Deviation Map</span>
            </div>
            <div class="etdrs-circle-container">
                <svg id="etdrsSvgOs" width="150" height="150" viewBox="-100 -100 200 200">
                    <path d="M -63.6 -63.6 A 90 90 0 0 1 63.6 -63.6 L 39.6 -39.6 A 56 56 0 0 0 -39.6 -39.6 Z" id="sec-os-outer-sup" class="etdrs-sector" fill="#22c55e"/>
                    <text x="0" y="-72" class="etdrs-text" id="sec-os-outer-sup-val">275</text>

                    <path d="M 63.6 -63.6 A 90 90 0 0 1 63.6 63.6 L 39.6 39.6 A 56 56 0 0 0 39.6 -39.6 Z" id="sec-os-outer-nas" class="etdrs-sector" fill="#22c55e"/>
                    <text x="72" y="0" class="etdrs-text" id="sec-os-outer-nas-val">288</text>

                    <path d="M 63.6 63.6 A 90 90 0 0 1 -63.6 63.6 L -39.6 39.6 A 56 56 0 0 0 39.6 39.6 Z" id="sec-os-outer-inf" class="etdrs-sector" fill="#22c55e"/>
                    <text x="0" y="72" class="etdrs-text" id="sec-os-outer-inf-val">268</text>

                    <path d="M -63.6 63.6 A 90 90 0 0 1 -63.6 -63.6 L -39.6 -39.6 A 56 56 0 0 0 -39.6 39.6 Z" id="sec-os-outer-temp" class="etdrs-sector" fill="#22c55e"/>
                    <text x="-72" y="0" class="etdrs-text" id="sec-os-outer-temp-val">262</text>

                    <path d="M -39.6 -39.6 A 56 56 0 0 1 39.6 -39.6 L 17 -17 A 24 24 0 0 0 -17 -17 Z" id="sec-os-inner-sup" class="etdrs-sector" fill="#22c55e"/>
                    <text x="0" y="-40" class="etdrs-text" id="sec-os-inner-sup-val">295</text>

                    <path d="M 39.6 -39.6 A 56 56 0 0 1 39.6 39.6 L 17 17 A 24 24 0 0 0 17 -17 Z" id="sec-os-inner-nas" class="etdrs-sector" fill="#22c55e"/>
                    <text x="40" y="0" class="etdrs-text" id="sec-os-inner-nas-val">298</text>

                    <path d="M 39.6 39.6 A 56 56 0 0 1 -39.6 39.6 L -17 17 A 24 24 0 0 0 17 17 Z" id="sec-os-inner-inf" class="etdrs-sector" fill="#22c55e"/>
                    <text x="0" y="40" class="etdrs-text" id="sec-os-inner-inf-val">292</text>

                    <path d="M -39.6 39.6 A 56 56 0 0 1 -39.6 -39.6 L -17 -17 A 24 24 0 0 0 -17 17 Z" id="sec-os-inner-temp" class="etdrs-sector" fill="#22c55e"/>
                    <text x="-40" y="0" class="etdrs-text" id="sec-os-inner-temp-val">288</text>

                    <circle cx="0" cy="0" r="24" id="sec-os-central" class="etdrs-sector" fill="#22c55e" stroke="#09090b" stroke-width="1.5"/>
                    <text x="0" y="0" class="etdrs-text" id="sec-os-central-val">255</text>
                </svg>
            </div>
            <div style="display:flex;justify-content:center;gap:8px;font-size:9px;color:#a1a1aa;margin-top:4px;">
                <span style="color:#22c55e;">&#9632; Normal (&Delta;&lt;10 &mu;m)</span>
                <span style="color:#eab308;">&#9632; Vigilancia (&Delta;10-19 &mu;m)</span>
                <span style="color:#ef4444;">&#9632; Alerta (&Delta;&ge;20 &mu;m)</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const clinicalSamples = @json($clinicalSamples);
const simulationData = @json($simulationData);
const timelineDays = @json($timelineDays);
let currentCirrusData = @json($cirrusOct);
const currentScenario = @json($scenario);
const currentAstroCode = @json($astronaut ? $astronaut->code : 'ASTRO-01');
const currentAstroName = @json($astronaut ? $astronaut->name : 'CDR Alex Mercer');
const currentCaseId = @json($activeCase->case_id);
const isEnLocale = @json($isEn);
let activeWorkstationMode = @json($initialMode ?? 'dual');

let currentIndex = {{ (int) $initialSampleIndex }};
let playInterval = null;

function switchAstronaut(astroCode) {
    window.location.href = `{{ route('public.digital-twin') }}?astro=${encodeURIComponent(astroCode)}&mode=${encodeURIComponent(activeWorkstationMode)}`;
}

// ==========================================================================
// CONTROL DE MODOS DE LA ESTACIÓN: 3D / CIRRUS OCT / VISTA DUAL
// ==========================================================================
function setWorkstationMode(mode) {
    activeWorkstationMode = mode;
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
        renderSampleOnCirrusOct(clinicalSamples[currentIndex]);
    } else {
        btndual.classList.add('active');
        p3d.style.display = 'grid';
        poct.style.display = 'block';
        renderSampleOnCirrusOct(clinicalSamples[currentIndex]);
        setTimeout(onWindowResize, 30);
        setTimeout(onWindowResize, 150);
    }

    if (window.history && window.history.replaceState) {
        const url = new URL(window.location.href);
        url.searchParams.set('mode', mode);
        window.history.replaceState({}, '', url.toString());
    }
}

// ==========================================================================
// RENDERIZADO CLÍNICO DE TOMOGRAFÍA ZEISS CIRRUS™ HD-OCT VINCULADO A MUESTRA
// ==========================================================================
function renderOctHeatmap(canvasId, trtUm, hasOct) {
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

    if (!hasOct || trtUm === null) {
        ctx.strokeStyle = '#f59e0b';
        ctx.lineWidth = 1.5;
        ctx.setLineDash([4, 4]);
        ctx.beginPath();
        ctx.arc(cx, cy, 66, 0, Math.PI * 2);
        ctx.stroke();
        ctx.setLineDash([]);

        ctx.fillStyle = '#fbbf24';
        ctx.font = 'bold 11px ui-monospace, monospace';
        ctx.textAlign = 'center';
        ctx.fillText('SIN ESCANEO OCT', cx, cy - 6);
        ctx.fillStyle = '#a1a1aa';
        ctx.font = '9px ui-monospace, monospace';
        ctx.fillText('No se interpola', cx, cy + 10);
        return;
    }

    const grad = ctx.createRadialGradient(cx, cy, 4, cx, cy, 70);

    if (trtUm >= 300) {
        grad.addColorStop(0, '#ef4444');
        grad.addColorStop(0.32, '#f59e0b');
        grad.addColorStop(0.62, '#22c55e');
        grad.addColorStop(0.85, '#0284c7');
        grad.addColorStop(1, '#05070a');
    } else if (trtUm >= 289) {
        grad.addColorStop(0, '#eab308');
        grad.addColorStop(0.35, '#22c55e');
        grad.addColorStop(0.72, '#0284c7');
        grad.addColorStop(1, '#05070a');
    } else {
        grad.addColorStop(0, '#0284c7');
        grad.addColorStop(0.28, '#22c55e');
        grad.addColorStop(0.65, '#22c55e');
        grad.addColorStop(0.88, '#0284c7');
        grad.addColorStop(1, '#05070a');
    }

    ctx.fillStyle = grad;
    ctx.beginPath();
    ctx.arc(cx, cy, 70, 0, Math.PI * 2);
    ctx.fill();

    ctx.strokeStyle = 'rgba(255, 255, 255, 0.28)';
    ctx.lineWidth = 1;
    ctx.setLineDash([2, 2]);
    ctx.beginPath();
    ctx.arc(cx, cy, 22, 0, Math.PI * 2);
    ctx.stroke();

    ctx.beginPath();
    ctx.arc(cx, cy, 48, 0, Math.PI * 2);
    ctx.stroke();
    ctx.setLineDash([]);

    ctx.fillStyle = '#ffffff';
    ctx.beginPath();
    ctx.arc(cx, cy, 2, 0, Math.PI * 2);
    ctx.fill();
}

function renderSampleBScanCanvas(canvasId, trtUm, deltaUm, hasOct, hasFolds) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const w = canvas.width;
    const h = canvas.height;

    ctx.clearRect(0, 0, w, h);
    ctx.fillStyle = '#05070a';
    ctx.fillRect(0, 0, w, h);

    if (!hasOct || trtUm === null) {
        ctx.strokeStyle = '#52525b';
        ctx.setLineDash([4, 4]);
        ctx.beginPath();
        ctx.moveTo(20, h / 2);
        ctx.lineTo(w - 20, h / 2);
        ctx.stroke();
        ctx.setLineDash([]);

        ctx.fillStyle = '#fbbf24';
        ctx.font = 'bold 11px ui-monospace, monospace';
        ctx.textAlign = 'center';
        ctx.fillText('TOMOGRAFÍA OCT NO REGISTRADA EN ESTE CORTE', w / 2, h / 2 - 8);
        ctx.fillStyle = '#a1a1aa';
        ctx.font = '10px ui-monospace, monospace';
        ctx.fillText('Estado operativo: Faltan datos (No se asume estabilidad)', w / 2, h / 2 + 10);
        return;
    }

    // Speckle noise característico de Spectral Domain OCT
    ctx.fillStyle = 'rgba(255, 255, 255, 0.07)';
    for (let i = 0; i < 220; i++) {
        const rx = (i * 37) % w;
        const ry = 18 + ((i * 53) % (h - 32));
        ctx.fillRect(rx, ry, 1, 1);
    }

    const n = 96;
    const stepX = w / (n - 1);
    const edemaBoost = Math.max(0, (trtUm - 278.0) * 0.75);

    const rpeY = [];
    const rnflY = [];
    const ilmY = [];

    for (let i = 0; i < n; i++) {
        const normX = (i / (n - 1)) * 2 - 1; // -1 to +1
        const foldWave = hasFolds ? Math.sin(normX * 18) * 2.2 : 0;
        const rpe = 104 + foldWave;
        // Depresión foveal central o engrosamiento peripapilar
        const pit = Math.exp(-Math.pow(normX * 3.2, 2)) * 18;
        const rimSwelling = Math.exp(-Math.pow((Math.abs(normX) - 0.38) * 4.2, 2)) * (14 + edemaBoost);
        const ilm = rpe - 36 + pit - rimSwelling;
        const rnfl = ilm + 12 + (edemaBoost * 0.25);

        rpeY.push(rpe);
        rnflY.push(rnfl);
        ilmY.push(ilm);
    }

    // 1. RPE / Bruch's Membrane (Oro)
    ctx.beginPath();
    for (let i = 0; i < n; i++) {
        const x = i * stepX;
        if (i === 0) ctx.moveTo(x, rpeY[i]);
        else ctx.lineTo(x, rpeY[i]);
    }
    ctx.strokeStyle = '#eab308';
    ctx.lineWidth = 2.4;
    ctx.stroke();

    // 2. RNFL / IPL (Cian)
    ctx.beginPath();
    for (let i = 0; i < n; i++) {
        const x = i * stepX;
        if (i === 0) ctx.moveTo(x, rnflY[i]);
        else ctx.lineTo(x, rnflY[i]);
    }
    ctx.strokeStyle = 'rgba(56, 189, 248, 0.8)';
    ctx.lineWidth = 1.6;
    ctx.stroke();

    // 3. ILM (Verde si normal, Amarillo/Rojo si engrosado)
    const ilmColor = (deltaUm !== null && deltaUm >= 20) ? '#ef4444' : ((deltaUm !== null && deltaUm >= 10) ? '#eab308' : '#22c55e');
    ctx.beginPath();
    for (let i = 0; i < n; i++) {
        const x = i * stepX;
        if (i === 0) ctx.moveTo(x, ilmY[i]);
        else ctx.lineTo(x, ilmY[i]);
    }
    ctx.strokeStyle = ilmColor;
    ctx.lineWidth = 2.3;
    ctx.stroke();

    // Relleno translúcido
    ctx.lineTo(w, rpeY[n - 1]);
    for (let i = n - 1; i >= 0; i--) {
        ctx.lineTo(i * stepX, rpeY[i]);
    }
    ctx.closePath();
    ctx.fillStyle = (deltaUm !== null && deltaUm >= 20) ? 'rgba(239, 68, 68, 0.14)' : 'rgba(34, 197, 94, 0.09)';
    ctx.fill();
}

function updateEtdrsEyeGrid(prefix, trtUm, deltaUm, hasOct, missingBaseline) {
    const sectors = [
        { id: `sec-${prefix}-central`, offset: -24 },
        { id: `sec-${prefix}-inner-sup`, offset: +18 },
        { id: `sec-${prefix}-inner-nas`, offset: +24 },
        { id: `sec-${prefix}-inner-inf`, offset: +16 },
        { id: `sec-${prefix}-inner-temp`, offset: +10 },
        { id: `sec-${prefix}-outer-sup`, offset: +2 },
        { id: `sec-${prefix}-outer-nas`, offset: +12 },
        { id: `sec-${prefix}-outer-inf`, offset: -4 },
        { id: `sec-${prefix}-outer-temp`, offset: -10 },
    ];

    sectors.forEach(sec => {
        const pathEl = document.getElementById(sec.id);
        const txtEl = document.getElementById(sec.id + '-val');
        if (!hasOct || trtUm === null) {
            if (pathEl) pathEl.setAttribute('fill', '#27272a');
            if (txtEl) txtEl.textContent = '---';
            return;
        }
        const val = Math.round(trtUm + sec.offset);
        let fill = '#22c55e';
        if (missingBaseline) {
            fill = '#eab308';
        } else if (deltaUm !== null && deltaUm >= 20) {
            fill = (sec.offset >= 10) ? '#ef4444' : '#eab308';
        } else if (deltaUm !== null && deltaUm >= 10) {
            fill = (sec.offset >= 10) ? '#eab308' : '#22c55e';
        }
        if (pathEl) pathEl.setAttribute('fill', fill);
        if (txtEl) txtEl.textContent = val;
    });
}

function renderSampleOnCirrusOct(sample) {
    if (!sample) return;

    const hasOd = Boolean(sample.has_oct_od && sample.od_trt_um !== null);
    const hasOs = Boolean(sample.has_oct_os && sample.os_trt_um !== null);
    const odTrt = sample.od_trt_um;
    const osTrt = sample.os_trt_um;
    const odDelta = sample.od_delta_um;
    const osDelta = sample.os_delta_um;

    // Mostrar u ocultar banner de falta de OCT en esta muestra
    const missingBanner = document.getElementById('oct-missing-banner');
    if (!hasOd || !hasOs || sample.os_missing_baseline) {
        missingBanner.style.display = 'block';
        document.getElementById('oct-missing-title').textContent = !hasOd && !hasOs
            ? `MUESTRA SIN ESCANEO OCT (${sample.code_label}):`
            : `COBERTURA OCT INCOMPLETA EN ${sample.code_label}:`;
        document.getElementById('oct-missing-desc').textContent = isEnLocale ? sample.note_en : sample.note_es;
    } else {
        missingBanner.style.display = 'none';
    }

    const sigBadge = document.getElementById('oct-signal-badge');
    sigBadge.textContent = `OD: ${hasOd ? 'VÁLIDA (9/10)' : 'SIN ESCANEO'} · OS: ${hasOs ? (sample.os_missing_baseline ? 'SIN BASE PRE-VUELO' : 'VÁLIDA (10/10)') : 'SIN ESCANEO'}`;
    sigBadge.style.color = (hasOd && hasOs && !sample.os_missing_baseline) ? '#22c55e' : '#fbbf24';

    document.getElementById('oct-table-sample-tag').textContent = sample.code_label;
    document.getElementById('oct-sample-date-label').textContent = `Fecha: ${sample.observed_at}`;

    // Tabla central Cirrus
    document.getElementById('table-avg-od').textContent = hasOd ? `${Math.round(odTrt)} µm` : 'SIN OCT';
    document.getElementById('table-avg-os').textContent = hasOs ? `${Math.round(osTrt)} µm` : 'SIN OCT';

    const dOdEl = document.getElementById('table-delta-od');
    dOdEl.textContent = odDelta !== null ? `${odDelta >= 0 ? '+' : ''}${Math.round(odDelta)} µm` : '---';
    dOdEl.style.color = (odDelta !== null && odDelta >= 20) ? '#ef4444' : ((odDelta !== null && odDelta >= 10) ? '#eab308' : '#22c55e');

    const dOsEl = document.getElementById('table-delta-os');
    dOsEl.textContent = osDelta !== null ? `${osDelta >= 0 ? '+' : ''}${Math.round(osDelta)} µm` : (sample.os_missing_baseline && hasOs ? 'SIN BASE' : '---');
    dOsEl.style.color = sample.os_missing_baseline ? '#fbbf24' : '#22c55e';

    document.getElementById('table-central-od').textContent = sample.od_central_fovea_um !== null ? `${sample.od_central_fovea_um} µm` : '---';
    document.getElementById('table-central-os').textContent = sample.os_central_fovea_um !== null ? `${sample.os_central_fovea_um} µm` : '---';
    document.getElementById('table-vol-od').textContent = sample.od_volume_mm3 !== null ? sample.od_volume_mm3 : '---';
    document.getElementById('table-vol-os').textContent = sample.os_volume_mm3 !== null ? sample.os_volume_mm3 : '---';

    const asymEl = document.getElementById('table-asym');
    if (hasOd && hasOs) {
        const asym = Math.round(odTrt - osTrt);
        asymEl.textContent = `${asym >= 0 ? '+' : ''}${asym} µm`;
        asymEl.style.color = Math.abs(asym) >= 15 ? '#ef4444' : '#22c55e';
    } else {
        asymEl.textContent = 'No comparable (Falta OCT)';
        asymEl.style.color = '#fbbf24';
    }

    document.getElementById('oct-fovea-od-val').textContent = hasOd ? `${Math.round(odTrt)} µm` : 'SIN OCT';
    document.getElementById('oct-fovea-os-val').textContent = hasOs ? `${Math.round(osTrt)} µm` : 'SIN OCT';
    document.getElementById('oct-delta-od-label').textContent = odDelta !== null ? `ΔTRT: ${odDelta >= 0 ? '+' : ''}${Math.round(odDelta)} µm` : 'Sin línea base';
    document.getElementById('oct-delta-os-label').textContent = osDelta !== null ? `ΔTRT: ${osDelta >= 0 ? '+' : ''}${Math.round(osDelta)} µm` : (sample.os_missing_baseline ? 'Sin base pre-vuelo (AT-05)' : 'Sin medición');

    document.getElementById('fundus-od-caption').textContent = sample.od_base_um !== null ? `OD (Base: ${Math.round(sample.od_base_um)} µm)` : 'OD (Sin Base OCT)';
    document.getElementById('fundus-os-caption').textContent = sample.os_base_um !== null ? `OS (Base: ${Math.round(sample.os_base_um)} µm)` : 'OS (Sin Base OCT)';

    // Biomarcadores estructurales del panel 8
    const elev = odDelta !== null ? Math.max(0, Math.round(odDelta * 1.4)) : 0;
    document.getElementById('oct-disc-elevation').textContent = hasOd ? `+${elev} µm` : 'Sin OCT';
    const frisGrade = Number(sample.frisen_grade || 0);
    const frisEl = document.getElementById('oct-frisen-grade');
    frisEl.textContent = `Grado ${frisGrade} (${frisGrade === 0 ? 'Normal' : (frisGrade === 1 ? 'Leve' : 'Moderado')})`;
    frisEl.style.color = frisGrade >= 2 ? '#ef4444' : (frisGrade === 1 ? '#eab308' : '#22c55e');

    const foldsEl = document.getElementById('oct-folds-status');
    foldsEl.textContent = sample.choroidal_folds ? 'DETECTADOS (Pliegues)' : 'Ausentes';
    foldsEl.style.color = sample.choroidal_folds ? '#ef4444' : '#ffffff';

    document.getElementById('oct-foveal-flattening').textContent = `${Number(sample.axial_length_mm || 24.10).toFixed(2)} mm`;

    // Renderizar Heatmaps, B-Scans y Rejillas ETDRS con los datos reales de la muestra
    renderOctHeatmap('octHeatmapOd', odTrt, hasOd);
    renderOctHeatmap('octHeatmapOs', osTrt, hasOs);
    renderSampleBScanCanvas('bscanCanvasOd', odTrt, odDelta, hasOd, Boolean(sample.choroidal_folds));
    renderSampleBScanCanvas('bscanCanvasOs', osTrt, osDelta, hasOs, false);
    updateEtdrsEyeGrid('od', odTrt, odDelta, hasOd, false);
    updateEtdrsEyeGrid('os', osTrt, osDelta, hasOs, Boolean(sample.os_missing_baseline));
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
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x05070d);
    scene.fog = new THREE.FogExp2(0x05070d, 0.025);

    const width = container.clientWidth || 640;
    const height = container.clientHeight || 440;
    camera = new THREE.PerspectiveCamera(40, width / height, 0.1, 1000);
    camera.position.set(24, 8, 22);

    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.shadowMap.enabled = true;
    container.appendChild(renderer.domElement);

    controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
    controls.autoRotate = true;
    controls.autoRotateSpeed = 1.6;
    controls.maxDistance = 55;
    controls.minDistance = 8;
    controls.target.set(0, 0, 0);
    window.__twinUserDragging = false;
    window.__twinMultiAxis = true;
    controls.addEventListener('start', () => { window.__twinUserDragging = true; });
    controls.addEventListener('end', () => { window.__twinUserDragging = false; });

    const ambientLight = new THREE.AmbientLight(0xffffff, 0.85);
    scene.add(ambientLight);

    const dirLight1 = new THREE.DirectionalLight(0xffffff, 1.2);
    dirLight1.position.set(20, 30, 20);
    scene.add(dirLight1);

    const dirLight2 = new THREE.DirectionalLight(0x38bdf8, 0.6);
    dirLight2.position.set(-20, -15, -20);
    scene.add(dirLight2);

    const gridHelper = new THREE.GridHelper(30, 30, 0x1e293b, 0x0f172a);
    gridHelper.position.y = -8;
    scene.add(gridHelper);

    buildEyeAnatomy();
    animate();
    window.addEventListener('resize', onWindowResize);
    if (typeof ResizeObserver !== 'undefined') {
        new ResizeObserver(() => onWindowResize()).observe(container);
    }
    setTimeout(onWindowResize, 50);
}

function buildEyeAnatomy() {
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
    scleraGeo.userData = { originalPositions: scleraGeo.attributes.position.clone() };

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

    const irisGeo = new THREE.RingGeometry(1.2, 3.4, 32);
    const irisMat = new THREE.MeshStandardMaterial({
        color: 0x1e3a8a,
        side: THREE.DoubleSide,
        roughness: 0.6
    });
    irisMesh = new THREE.Mesh(irisGeo, irisMat);
    irisMesh.rotation.y = Math.PI / 2;
    irisMesh.position.x = 6.0;
    scene.add(irisMesh);

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

    const discGeo = new THREE.CylinderGeometry(1.2, 1.2, 0.4, 32);
    discGeo.rotateZ(Math.PI / 2);
    const discMat = new THREE.MeshStandardMaterial({
        color: 0xe2e8f0,
        roughness: 0.4
    });
    opticDiscMesh = new THREE.Mesh(discGeo, discMat);
    opticDiscMesh.position.x = -7.2;
    scene.add(opticDiscMesh);

    rayLinesGroup = new THREE.Group();
    const rayMat = new THREE.LineDashedMaterial({
        color: 0xEAFE07,
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

    const focalGeo = new THREE.SphereGeometry(0.28, 16, 16);
    const focalMat = new THREE.MeshBasicMaterial({ color: 0xEAFE07 });
    const focalSphere = new THREE.Mesh(focalGeo, focalMat);
    focalSphere.name = 'focalSphere';
    focalSphere.position.set(-7.2, 0, 0);
    rayLinesGroup.add(focalSphere);
    scene.add(rayLinesGroup);

    forceVectorGroup = new THREE.Group();
    const dir = new THREE.Vector3(1, 0, 0);
    const origin1 = new THREE.Vector3(-14, 2.2, 0);
    const origin2 = new THREE.Vector3(-14, -2.2, 0);
    const arrow1 = new THREE.ArrowHelper(dir, origin1, 3.8, 0x2E96F5, 0.8, 0.4);
    const arrow2 = new THREE.ArrowHelper(dir, origin2, 3.8, 0x2E96F5, 0.8, 0.4);
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
    if (controls) controls.update();

    const time = Date.now() * 0.0025;
    if (typeof eyeGroup !== 'undefined' && eyeGroup && window.__twinMultiAxis && !window.__twinUserDragging) {
        eyeGroup.rotation.y += 0.0045;
        eyeGroup.rotation.x = Math.sin(time * 0.55) * 0.16;
        eyeGroup.rotation.z = Math.cos(time * 0.38) * 0.07;
        eyeGroup.position.y = Math.sin(time * 0.9) * 0.22;
    }

    if (forceVectorGroup) {
        forceVectorGroup.children.forEach((arrow, i) => {
            arrow.position.x = -14 + Math.sin(time * 1.2 + i) * 0.35;
        });
    }

    if (renderer && scene && camera) {
        renderer.render(scene, camera);
    }
}

function setPresetView(mode) {
    document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));

    if (mode === 'free') {
        document.getElementById('btn-view-free').classList.add('active');
        controls.autoRotate = true;
        controls.autoRotateSpeed = 1.6;
        window.__twinMultiAxis = true;
    } else {
        controls.autoRotate = false;
        window.__twinMultiAxis = false;
        if (typeof eyeGroup !== 'undefined' && eyeGroup) {
            eyeGroup.rotation.set(0, 0, 0);
            eyeGroup.position.y = 0;
        }
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
        scleraMesh.material.color.setHex(0x2E96F5);
    } else {
        btn.classList.remove('active');
        scleraMesh.material.color.setHex(0xf1f5f9);
    }
}

// ==========================================================================
// ACTUALIZACIÓN REACTIVA DE MUESTRA EN 3D + CONSOLA CIRRUS + TABLA
// ==========================================================================
function renderDay(index) {
    currentIndex = index;
    const sample = clinicalSamples[index] || clinicalSamples[0];
    if (!sample) return;

    // 1. Actualizar píldoras y filas activas de la tabla de muestras
    document.querySelectorAll('.sample-pill').forEach((el, idx) => {
        el.classList.toggle('active', idx === index);
    });
    document.querySelectorAll('.sample-row').forEach((el, idx) => {
        el.classList.toggle('active-sample-row', idx === index);
    });

    // 2. Cabecera de muestra y notas clínicas
    document.getElementById('flight-day-badge').textContent = isEnLocale ? sample.stage_en : sample.stage_es;
    document.getElementById('sample-id-badge').textContent = `ID: ${sample.sample_id} · ${sample.observed_at}`;
    document.getElementById('sample-clinical-note').textContent = isEnLocale ? sample.note_en : sample.note_es;

    const statusPill = document.getElementById('sample-status-pill');
    statusPill.textContent = isEnLocale ? sample.status_en : sample.status_es;
    statusPill.className = sample.status_code === 'LISTO_PARA_REVISION'
        ? 'status-badge-ready'
        : (sample.status_code === 'PRIORIZAR_REVISION' ? 'status-badge-prioritize' : 'status-badge-missing');

    // Actualizar botón "Abrir en Predictor SANS" con los valores exactos de esta muestra
    const predBtn = document.getElementById('btn-open-predictor');
    if (predBtn) {
        const params = new URLSearchParams({
            case_id: currentCaseId,
            tab: 'biomarcadores',
            oct_trt_baseline_um: sample.od_base_um !== null ? sample.od_base_um : '',
            oct_trt_current_um: sample.od_trt_um !== null ? sample.od_trt_um : '',
            axial_length_mm: sample.axial_length_mm !== null ? sample.axial_length_mm : '',
            hyperopia_sim_d: sample.od_shift !== null ? sample.od_shift : '',
            papilledema_frisen: sample.frisen_grade !== null ? sample.frisen_grade : 0,
            iop_mmhg: sample.iop !== null ? sample.iop : '',
        });
        predBtn.href = `{{ route('home') }}?` + params.toString();
    }

    // 3. Los 6 Biomarcadores de la Muestra
    const hasOd = Boolean(sample.has_oct_od && sample.od_trt_um !== null);
    const hasOs = Boolean(sample.has_oct_os && sample.os_trt_um !== null);
    const odStr = hasOd ? `${Math.round(sample.od_trt_um)} µm` : 'Sin OCT';
    const osStr = hasOs ? `${Math.round(sample.os_trt_um)} µm` : 'Sin OCT';
    document.getElementById('val-oct-trt').textContent = `${odStr} / ${osStr}`;
    document.getElementById('val-oct-delta').textContent = sample.od_delta_um !== null
        ? `ΔTRT OD: ${sample.od_delta_um >= 0 ? '+' : ''}${Math.round(sample.od_delta_um)} µm (Base ${Math.round(sample.od_base_um)} µm)`
        : 'Sin escaneo OCT en este corte';

    const shiftVal = Number(sample.od_shift || 0);
    document.getElementById('val-shift').textContent = (shiftVal >= 0 ? '+' : '') + shiftVal.toFixed(2) + ' D';
    document.getElementById('val-snellen').textContent = sample.od_snellen || '20/20';
    document.getElementById('val-logmar').textContent = 'LogMAR ' + Number(sample.od_logmar || 0).toFixed(3);
    document.getElementById('val-iop').textContent = Number(sample.iop || 15).toFixed(1) + ' mmHg';

    const frisen = Number(sample.frisen_grade || 0);
    document.getElementById('val-frisen').textContent = frisen === 0 ? 'Grado 0 (Normal)' : `Grado ${frisen} (Edema)`;

    const isFlattening = Boolean(sample.globe_flattening);
    document.getElementById('val-flattening').textContent = isFlattening ? 'Detectado' : 'Ausente';

    const isFolds = Boolean(sample.choroidal_folds);
    document.getElementById('val-folds').textContent = isFolds ? 'Pliegues presentes' : 'No detectados';

    document.getElementById('val-rnfl').textContent = sample.rnfl !== null ? `${sample.rnfl} µm` : 'Sin OCT';

    const currentAxialLen = Number(sample.axial_length_mm || 24.10);
    const axialDeltaMm = Math.max(0, 24.10 - currentAxialLen);
    document.getElementById('val-axial-len').textContent = currentAxialLen.toFixed(2) + ' mm';
    document.getElementById('val-axial-delta').textContent = axialDeltaMm > 0.01
        ? `-${axialDeltaMm.toFixed(2)} mm de acortamiento axial`
        : 'Longitud axial basal nominal';

    // 4. HUD Aeroespacial 3D
    document.getElementById('hud-sample-code').textContent = sample.code_label;
    document.getElementById('hud-oct-pair').textContent = `${odStr} / ${osStr}`;
    const postZMm = (-12.10 + axialDeltaMm).toFixed(2);
    document.getElementById('hud-z-coord').textContent = postZMm + ' mm';
    document.getElementById('hud-deform-mm').textContent = (axialDeltaMm > 0.01 ? '-' : '') + axialDeltaMm.toFixed(2) + ' mm';
    document.getElementById('hud-icp-val').textContent = Number(sample.iop || 15).toFixed(1) + ' mmHg';

    const hudBadge = document.getElementById('hud-tier-badge');
    const riskBadge = document.getElementById('twin-risk-badge');
    const vectorStatus = document.getElementById('hud-vector-status');

    if (sample.status_code === 'PRIORIZAR_REVISION') {
        hudBadge.textContent = `PRIORIZAR REVISIÓN · ${sample.code_label}`;
        riskBadge.textContent = 'PRIORIZAR REVISIÓN';
        vectorStatus.textContent = 'ENGROSAMIENTO OCT / DESPLAZAMIENTO CEFÁLICO';
        vectorStatus.style.color = '#f87171';
    } else if (sample.status_code === 'FALTAN_DATOS') {
        hudBadge.textContent = `FALTAN DATOS · ${sample.code_label}`;
        riskBadge.textContent = 'FALTAN DATOS';
        vectorStatus.textContent = 'COBERTURA OCT INCOMPLETA EN CORTE';
        vectorStatus.style.color = '#fbbf24';
    } else {
        hudBadge.textContent = `LISTO PARA REVISIÓN · ${sample.code_label}`;
        riskBadge.textContent = 'ESTABLE / NOMINAL';
        vectorStatus.textContent = 'ESTABILIDAD LONGITUDINAL';
        vectorStatus.style.color = '#4ade80';
    }

    // 5. Mini curva B-Scan (Curva Bézier completa de 0 a 300 sin corte)
    const elevUm = sample.od_delta_um !== null ? Math.max(0, Math.round(sample.od_delta_um)) : 0;
    document.getElementById('oct-elevation-val').textContent = hasOd ? `ΔTRT MUESTRA: +${elevUm} µm` : 'SIN ESCANEO OCT';
    const peakY = hasOd ? (34 - Math.min(24, Math.round(elevUm * 0.8))) : 36;
    const midControlY = hasOd ? Math.max(4, peakY - 6) : 36;
    const curveEl = document.getElementById('octCurvePath');
    curveEl.setAttribute('d', `M 0 36 Q 70 36 108 ${peakY} Q 150 ${midControlY} 192 ${peakY} Q 230 36 300 36`);
    curveEl.setAttribute('stroke', !hasOd ? '#fbbf24' : (elevUm >= 20 ? '#ef4444' : (elevUm >= 10 ? '#eab308' : '#22c55e')));

    // 6. Deformación de malla 3D vinculada a la muestra
    if (scleraMesh) {
        const geo = scleraMesh.geometry;
        const posAttr = geo.attributes.position;
        const origPos = geo.userData.originalPositions;
        const flatteningScale = isFlattening ? (0.14 + (shiftVal * 0.14)) : (axialDeltaMm * 0.25);

        for (let i = 0; i < posAttr.count; i++) {
            const ox = origPos.getX(i);
            if (ox < 0) {
                const weight = Math.abs(ox) / 7.5;
                posAttr.setX(i, ox * (1.0 - (flatteningScale * weight)));
            } else {
                posAttr.setX(i, ox);
            }
        }
        posAttr.needsUpdate = true;
        geo.computeVertexNormals();

        const newPostX = -7.5 * (1.0 - (flatteningScale * 0.8));
        opticDiscMesh.position.x = newPostX + 0.3;
        opticDiscMesh.scale.set(1, 1 + (frisen * 0.4), 1 + (frisen * 0.4));
        opticNerveMesh.position.x = newPostX - 3.8;

        const focalObj = rayLinesGroup.getObjectByName('focalSphere');
        if (focalObj) {
            focalObj.position.x = -7.2 + (shiftVal * 0.85);
        }
    }

    // 7. Actualizar Consola ZEISS Cirrus HD-OCT con los valores exactos de esta muestra
    renderSampleOnCirrusOct(sample);
}

function setSliderIndex(idx) {
    document.getElementById('day-slider').value = idx;
    renderDay(idx);
}

document.getElementById('day-slider').addEventListener('input', function() {
    renderDay(parseInt(this.value));
});

document.getElementById('play-pause-btn').addEventListener('click', function() {
    if (playInterval) {
        clearInterval(playInterval);
        playInterval = null;
        document.getElementById('play-icon').className = 'fa fa-play';
        document.getElementById('play-text').textContent = isEnLocale ? 'Play Samples' : 'Recorrer Muestras';
    } else {
        document.getElementById('play-icon').className = 'fa fa-pause';
        document.getElementById('play-text').textContent = isEnLocale ? 'Pause' : 'Pausar';
        playInterval = setInterval(() => {
            let next = (currentIndex + 1) % clinicalSamples.length;
            setSliderIndex(next);
        }, 1800);
    }
});

document.addEventListener('DOMContentLoaded', function() {
    initThreeEngine();
    renderDay(currentIndex);
    const urlParams = new URLSearchParams(window.location.search);
    const mode = urlParams.get('mode') || activeWorkstationMode || 'dual';
    setWorkstationMode(mode);
});
</script>
@endpush
