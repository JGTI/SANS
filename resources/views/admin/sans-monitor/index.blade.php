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
        'Pendiente' => 'Pending',
        'COMPLETADO' => 'COMPLETED',
        'PENDIENTE' => 'PENDING',
        'SIN_MEDICION' => 'UNMEASURED',
        'CAMBIO_PERSISTENTE' => 'PERSISTENT_CHANGE',
        'RECUPERACION_PARCIAL' => 'PARTIAL_RECOVERY',
        'ESTABLE' => 'STABLE',
        'EMPEORA' => 'WORSENS',
        'SIN_ESCANEAR' => 'UNSCANNED',
    ];
    $t = fn (?string $val) => ($isEn && $val !== null && isset($trMap[$val])) ? $trMap[$val] : $val;

    // Normalizar las 3 pestañas principales manteniendo compatibilidad con enlaces directos
    $normalizedTab = match($activeTab) {
        'seguimiento', 'inclinacion' => 'seguimiento',
        'escenarios', 'simulador', 'estudios' => 'escenarios',
        default => 'biomarcadores',
    };
    $sharedParams = [
        'case_id' => $activeCase->case_id,
        'eye' => $selectedEye,
        'day' => $selectedDay,
        'range' => $selectedRange,
        'mode' => $timelineMode,
        'context' => $contextOpen ? '1' : '0',
    ];
    $bp = $biomarkerPredictor;
@endphp

@section('title', $isEn
    ? 'SANS Predictor — 6 Biomarkers, Timeline (+30/+60/+90d) & 3 Scenarios'
    : 'Predictor SANS — Biomarcadores, Línea de Tiempo (+30/+60/+90) y Escenarios')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div id="sans-monitor-workspace">
@if($activeTab === 'astronauta')
    @include('admin.sans-monitor.astronaut')
@else

{{-- CABECERA CIENTÍFICA COMPACTA (SIN PAJA PROMOCIONAL) --}}
<div class="card" style="padding:1.1rem 1.35rem;margin-bottom:1rem;border:1.5px solid var(--space-blue);">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;">
        <div>
            <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;margin-bottom:0.25rem;">
                <span class="interactive-pill" style="background:#07173F;color:#EAFE07;border-color:#07173F;font-size:0.68rem;">
                    <i class="fa fa-flask"></i>
                    {{ $isEn ? 'FOR GROUND SCIENTISTS & CLINICAL INVESTIGATORS' : 'MÓDULO EXCLUSIVO PARA CIENTÍFICOS E INVESTIGADORES EN TIERRA' }}
                </span>
                <span style="font-size:0.74rem;color:var(--muted-foreground);font-weight:600;">
                    {{ $isEn ? 'Source:' : 'Fuente:' }} <code>{{ $activeCase->source_type }}</code>
                </span>
            </div>
            <h1 style="font-size:1.3rem;font-weight:800;margin:0;">
                {{ $isEn
                    ? 'SANS Biomarker Predictor & Longitudinal Follow-up (+30 / +60 / +90 Days)'
                    : 'Predictor SANS por Biomarcadores y Seguimiento Longitudinal (+30 / +60 / +90 Días)' }}
            </h1>
        </div>

        @php
            $headerBadgeClass = match($caseEvaluation['case_operational_code']) {
                'LISTO_PARA_REVISION' => 'status-badge-ready',
                'PRIORIZAR_REVISION' => 'status-badge-prioritize',
                default => 'status-badge-missing',
            };
        @endphp
        <div style="display:flex;gap:0.55rem;align-items:center;flex-wrap:wrap;">
            <div style="padding:0.4rem 0.75rem;border:1px solid var(--border);border-radius:7px;text-align:right;background:var(--muted);">
                <div style="font-size:0.64rem;text-transform:uppercase;color:var(--muted-foreground);">
                    {{ $isEn ? 'Operational State' : 'Estado Operativo' }} ({{ $activeCase->case_id }})
                </div>
                <span class="{{ $headerBadgeClass }}">
                    <i class="fa {{ $caseEvaluation['case_operational_code'] === 'LISTO_PARA_REVISION' ? 'fa-check-circle' : ($caseEvaluation['case_operational_code'] === 'PRIORIZAR_REVISION' ? 'fa-exclamation-triangle' : 'fa-question-circle') }}"></i>
                    {{ $t($caseEvaluation['case_operational_text']) }}
                </span>
            </div>
            <div style="padding:0.4rem 0.75rem;border:1px solid var(--border);border-radius:7px;text-align:right;background:var(--muted);">
                <div style="font-size:0.64rem;text-transform:uppercase;color:var(--muted-foreground);">
                    {{ $isEn ? 'Fitness Verdict' : 'Dictamen de Aptitud' }}
                </div>
                <div style="font-size:0.86rem;font-weight:700;">{{ $t($caseEvaluation['fitness_display']) }}</div>
            </div>
            <a href="{{ route('public.astronaut') }}" class="btn btn-outline" style="padding:0.5rem 0.8rem;font-size:0.8rem;">
                <i class="fa fa-user-md"></i> {{ $isEn ? 'Astronaut View' : 'Vista Astronauta' }}
            </a>
            <a href="{{ route('public.digital-twin', ['mode' => 'dual', 'case_id' => $activeCase->case_id]) }}" class="btn btn-primary" style="padding:0.5rem 0.9rem;font-size:0.8rem;">
                <i class="fa fa-cube"></i> {{ $isEn ? '3D Viewer + OCT' : 'Visor 3D + OCT' }}
            </a>
        </div>
    </div>
</div>

@if(session('status'))
    <div class="card" style="padding:0.85rem 1rem;margin-bottom:1rem;border-left:4px solid var(--space-blue);">
        <i class="fa fa-check-circle"></i> {{ session('status') }}
    </div>
@endif

{{-- BARRA COMPACTA DE SUJETO Y OJO (SIN CONTROLES DUPLICADOS NI TEXTO DE RELLENO) --}}
@php
    $activeCrewMeta = \App\Models\AstronautProfile::crewMetaForCase($activeCase->case_id);
@endphp
<div class="card" style="padding:0.8rem 1rem;margin-bottom:0.9rem;border:1.5px solid var(--interactive-border);">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:0.75rem;flex-wrap:wrap;margin-bottom:0.6rem;">
        <div style="display:flex;align-items:center;gap:0.65rem;">
            <img src="{{ $activeCrewMeta['avatar'] }}"
                 alt="{{ $activeCrewMeta['name'] }}"
                 style="width:42px;height:42px;border-radius:7px;object-fit:cover;border:2px solid var(--space-blue);">
            <div>
                <div style="display:flex;align-items:center;gap:0.45rem;flex-wrap:wrap;">
                    <span style="font-size:0.94rem;font-weight:800;">{{ $activeCrewMeta['name'] }}</span>
                    <span class="interactive-pill" style="background:#0042A6;color:#ffffff;border-color:#0042A6;">
                        {{ $activeCase->case_id }} · {{ $t($activeCase->scenario_archetype) }}
                    </span>
                </div>
                <div style="font-size:0.72rem;color:var(--muted-foreground);">
                    {{ $activeCrewMeta['role'] }}
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.sans-monitor.index') }}" style="display:flex;gap:0.5rem;align-items:center;margin:0;">
            <input type="hidden" name="tab" value="{{ $normalizedTab }}">
            <input type="hidden" name="case_id" value="{{ $activeCase->case_id }}">
            <label style="font-size:0.74rem;font-weight:700;color:var(--interactive-text);margin:0;">
                <i class="fa fa-eye"></i> {{ $isEn ? 'Evaluated Eye:' : 'Ojo Evaluado:' }}
            </label>
            <select name="eye" class="form-control" style="width:auto;min-width:165px;padding:0.35rem 0.6rem;height:auto;" onchange="this.form.submit()" aria-label="{{ $isEn ? 'Select eye' : 'Seleccionar ojo' }}">
                <option value="OD" {{ $selectedEye === 'OD' ? 'selected' : '' }}>{{ $isEn ? 'Right Eye (OD)' : 'Ojo Derecho (OD)' }}</option>
                <option value="OS" {{ $selectedEye === 'OS' ? 'selected' : '' }}>{{ $isEn ? 'Left Eye (OS)' : 'Ojo Izquierdo (OS)' }}</option>
                <option value="BILATERAL" {{ $selectedEye === 'BILATERAL' ? 'selected' : '' }}>{{ $isEn ? 'Bilateral (OD + OS)' : 'Bilateral (OD + OS)' }}</option>
            </select>
        </form>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:0.5rem;">
        @foreach($cases as $c)
            @php
                $cm = \App\Models\AstronautProfile::crewMetaForCase($c->case_id);
                $isActiveCrew = $c->case_id === $activeCase->case_id;
            @endphp
            <a href="{{ route('admin.sans-monitor.index', ['case_id' => $c->case_id, 'eye' => $selectedEye, 'tab' => $normalizedTab]) }}"
               class="clickable-card {{ $isActiveCrew ? 'is-active' : '' }}"
               style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:0.5rem;padding:0.4rem 0.55rem;border-radius:7px;">
                <img src="{{ $cm['avatar'] }}"
                     alt="{{ $cm['name'] }}"
                     style="width:36px;height:36px;border-radius:6px;object-fit:cover;border:1.5px solid {{ $isActiveCrew ? '#0042A6' : 'var(--border)' }};flex-shrink:0;">
                <div style="min-width:0;overflow:hidden;flex:1;">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:0.25rem;">
                        <span style="font-size:0.74rem;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $cm['name'] }}</span>
                        <span class="interactive-pill" style="font-size:0.56rem;padding:0.08rem 0.35rem;">
                            {{ $isActiveCrew ? ($isEn ? 'ACTIVE' : 'ACTIVO') : ($isEn ? 'Load →' : 'Cargar →') }}
                        </span>
                    </div>
                    <div style="font-size:0.65rem;color:var(--muted-foreground);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $c->case_id }} · {{ $t($c->scenario_archetype) }}</div>
                </div>
            </a>
        @endforeach
    </div>
</div>

{{-- NAVEGADOR VISUAL DEL WIZARD / TABS DE 3 PASOS --}}
<div class="sans-grid-3" style="margin-bottom:1rem;" role="tablist" aria-label="{{ $isEn ? '3 Main Wizard Steps' : '3 Pasos Principales del Wizard' }}">
    {{-- PASO 1 --}}
    <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'biomarcadores'])) }}"
       data-wizard-tab="biomarcadores"
       role="tab"
       aria-selected="{{ $normalizedTab === 'biomarcadores' ? 'true' : 'false' }}"
       class="wizard-step-card {{ $normalizedTab === 'biomarcadores' ? 'is-active' : '' }}">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:0.4rem;">
            <span style="font-size:0.68rem;font-weight:800;letter-spacing:0.06em;text-transform:uppercase;opacity:0.92;">
                {{ $isEn ? 'INTAKE & PREDICTOR' : 'INGRESO Y PREDICTOR' }}
            </span>
            <span class="wizard-step-pill" data-wizard-pill
                  data-label-active="{{ $isEn ? '● ACTIVE' : '● ACTIVO' }}"
                  data-label-inactive="{{ $isEn ? 'OPEN →' : 'ABRIR →' }}">
                {{ $normalizedTab === 'biomarcadores' ? ($isEn ? '● ACTIVE' : '● ACTIVO') : ($isEn ? 'OPEN →' : 'ABRIR →') }}
            </span>
        </div>
        <div style="font-size:0.96rem;font-weight:800;">
            <i class="fa fa-heartbeat"></i> {{ $isEn ? 'Initial Form & 6 Biomarkers' : 'Formulario Inicial y 6 Biomarcadores' }}
        </div>
        <div style="font-size:0.75rem;opacity:0.88;">
            {{ $isEn ? 'Evaluate OCT, Axial Length, Hyperopia, Frisén, IOP & -6° HDT.' : 'Evalúa OCT, Longitud Axial, Hipermetropía, Frisén, PIO y -6° HDT.' }}
        </div>
    </a>

    {{-- PASO 2 --}}
    <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'seguimiento'])) }}"
       data-wizard-tab="seguimiento"
       role="tab"
       aria-selected="{{ $normalizedTab === 'seguimiento' ? 'true' : 'false' }}"
       class="wizard-step-card {{ $normalizedTab === 'seguimiento' ? 'is-active' : '' }}">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:0.4rem;">
            <span style="font-size:0.68rem;font-weight:800;letter-spacing:0.06em;text-transform:uppercase;opacity:0.92;">
                {{ $isEn ? 'LONGITUDINAL CUTS' : 'CORTES EN EL TIEMPO' }}
            </span>
            <span class="wizard-step-pill" data-wizard-pill
                  data-label-active="{{ $isEn ? '● ACTIVE' : '● ACTIVO' }}"
                  data-label-inactive="{{ $isEn ? 'OPEN →' : 'ABRIR →' }}">
                {{ $normalizedTab === 'seguimiento' ? ($isEn ? '● ACTIVE' : '● ACTIVO') : ($isEn ? 'OPEN →' : 'ABRIR →') }}
            </span>
        </div>
        <div style="font-size:0.96rem;font-weight:800;">
            <i class="fa fa-calendar"></i> {{ $isEn ? 'Before · During · After (+30, +60, +90 days)' : 'Antes · Durante · Después (+30, +60, +90 días)' }}
        </div>
        <div style="font-size:0.75rem;opacity:0.88;">
            {{ $isEn ? 'Track pre-flight baseline, in-flight shifts & post-flight recovery.' : 'Mide línea base pre-vuelo, cambios en misión y recuperación posvuelo.' }}
        </div>
    </a>

    {{-- PASO 3 --}}
    <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'escenarios'])) }}"
       data-wizard-tab="escenarios"
       role="tab"
       aria-selected="{{ $normalizedTab === 'escenarios' ? 'true' : 'false' }}"
       class="wizard-step-card {{ $normalizedTab === 'escenarios' ? 'is-active' : '' }}">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:0.4rem;">
            <span style="font-size:0.68rem;font-weight:800;letter-spacing:0.06em;text-transform:uppercase;opacity:0.92;">
                {{ $isEn ? 'REFERENCE ARCHETYPES' : 'ARQUETIPOS DE REFERENCIA' }}
            </span>
            <span class="wizard-step-pill" data-wizard-pill
                  data-label-active="{{ $isEn ? '● ACTIVE' : '● ACTIVO' }}"
                  data-label-inactive="{{ $isEn ? 'OPEN →' : 'ABRIR →' }}">
                {{ $normalizedTab === 'escenarios' ? ($isEn ? '● ACTIVE' : '● ACTIVO') : ($isEn ? 'OPEN →' : 'ABRIR →') }}
            </span>
        </div>
        <div style="font-size:0.96rem;font-weight:800;">
            <i class="fa fa-sliders"></i> {{ $isEn ? 'Scenario Models (Stable · Worsens · Unscanned)' : 'Escenarios Modelos (Estable · Empeora · No se escanea)' }}
        </div>
        <div style="font-size:0.75rem;opacity:0.88;">
            {{ $isEn ? 'Compare the 3 canonical models & anatomical clinical imaging.' : 'Compara los 3 modelos canónicos y sus vistas clínicas.' }}
        </div>
    </a>
</div>

<div style="display:flex;justify-content:flex-end;margin:-0.45rem 0 0.9rem;">
    <a href="#nasa-bibliography" style="display:inline-flex;align-items:center;gap:7px;padding:6px 12px;border-radius:7px;background:var(--card);border:1px solid var(--border);color:var(--foreground);font-size:0.76rem;font-weight:700;text-decoration:none;font-family:ui-monospace,monospace;">
        <i class="fa fa-book"></i>
        <span>{{ $isEn ? 'NASA Protocols & Scientific Bibliography (7 References)' : 'Protocolos NASA y Bibliografía Científica (7 Referencias)' }}</span>
        <i class="fa fa-arrow-down" style="font-size:10px;opacity:0.7;"></i>
    </a>
</div>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:0.85rem;margin-bottom:1.25rem;">
    @foreach(['OD' => ($isEn ? 'Right Eye (OD)' : 'Ojo Derecho (OD)'), 'OS' => ($isEn ? 'Left Eye (OS)' : 'Ojo Izquierdo (OS)')] as $eyeKey => $eyeTitle)
        @php
            $ev = $caseEvaluation['eyes'][$eyeKey];
            $eyeBadgeClass = match($ev['operational_code']) {
                'LISTO_PARA_REVISION' => 'status-badge-ready',
                'PRIORIZAR_REVISION' => 'status-badge-prioritize',
                default => 'status-badge-missing',
            };
        @endphp
        <div class="card" style="padding:0.9rem 1.1rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:0.5rem;flex-wrap:wrap;">
                <span style="font-weight:700;font-size:0.92rem;"><i class="fa fa-eye"></i> {{ $eyeTitle }}</span>
                <span class="{{ $eyeBadgeClass }}">
                    {{ $t($ev['operational_text']) }} · {{ $t($ev['trajectory_text']) }}
                </span>
            </div>
            <div style="font-size:0.8rem;color:var(--muted-foreground);margin-top:0.35rem;">
                {{ $isEn ? 'Baseline OCT:' : 'Línea base OCT:' }}
                <strong>{{ $ev['baseline_trt'] !== null ? $ev['baseline_trt'].' µm' : ($isEn ? 'Unscanned' : 'Sin escaneo') }}</strong> ·
                {{ $isEn ? 'Max Change' : 'Cambio Máx' }} (&Delta;TRT):
                <strong>{{ $ev['max_delta_trt'] !== null ? ($ev['max_delta_trt'] >= 0 ? '+'.$ev['max_delta_trt'] : $ev['max_delta_trt']).' µm' : ($isEn ? 'Unmeasured' : 'Sin medición') }}</strong>
            </div>
        </div>
    @endforeach
</div>

@if($contextOpen)
<div class="card" style="padding:0.9rem 1.1rem;margin-bottom:1.25rem;border:1px dashed var(--border);">
    <strong><i class="fa fa-list-alt"></i> {{ $isEn ? 'Case Context:' : 'Contexto del Caso:' }}</strong>
    {{ $isEn
        ? 'Displayed as supporting research context (never alters ocular status or suggested follow-up action).'
        : 'Visible como información de apoyo (no altera el estado ocular ni la acción sugerida).' }}
</div>
@endif

{{-- =============================================================================================== --}}
{{-- PESTAÑA 1: FORMULARIO INICIAL DE LA PERSONA + LOS 6 BIOMARCADORES Y PREDICTOR EN VIVO            --}}
{{-- =============================================================================================== --}}
<div id="wizard-panel-biomarcadores" data-wizard-panel="biomarcadores" style="{{ $normalizedTab === 'biomarcadores' ? '' : 'display:none;' }}">
{{-- ESCÁNER DE FONDO DE OJO & EDEMA PAPILAR (DOWNLINK DESDE ASTRONAUTA -> PYTHON CV REAL) --}}
<div class="card" style="padding:1.25rem;border:1px solid var(--border);border-radius:10px;margin-bottom:1.25rem;background:var(--card);color:var(--foreground);" data-fundus-scanner>
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;border-bottom:1px solid var(--border);padding-bottom:0.85rem;margin-bottom:1rem;">
        <div>
            <span style="font-family:ui-monospace,monospace;font-size:0.7rem;font-weight:800;color:var(--muted-foreground);letter-spacing:0.06em;">
                {{ $isEn ? 'ORBITAL DOWNLINK · RETINAL FUNDUS CV WORKSTATION + TONOMETER IOP' : 'ENLACE ORBITAL · ESTACIÓN CV DE FONDO DE OJO + PIO TONÓMETRO' }}
            </span>
            <h2 style="font-size:1.15rem;font-weight:800;margin:2px 0 0;color:var(--foreground);">
                <i class="fa fa-bullseye"></i>
                {{ $isEn ? 'Ground Fundus Scanner & Papilledema Analyzer (Python CV)' : 'Escáner Científico de Fondo de Ojo y Edema Papilar (Python CV)' }}
            </h2>
        </div>
        <div style="display:flex;align-items:center;gap:0.6rem;flex-wrap:wrap;">
            <span id="fundusActiveIopBadge" style="font-family:ui-monospace,monospace;font-size:0.74rem;padding:5px 11px;border-radius:6px;background:var(--muted);border:1px solid var(--border);color:var(--foreground);font-weight:700;">
                {{ $isEn ? 'Tonometer IOP:' : 'PIO Tonómetro:' }} {{ $orbitalTransmission['iop_od'] }} / {{ $orbitalTransmission['iop_os'] }} mmHg
            </span>
            <label style="background:var(--card);color:var(--foreground);border:1px solid var(--border);border-radius:7px;padding:0.52rem 0.9rem;font-size:0.8rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;margin:0;">
                <i class="fa fa-upload"></i> {{ $isEn ? 'Upload Photo' : 'Subir Foto' }}
                <input type="file" id="cvUploadFileInput" accept="image/*" hidden>
            </label>
            <button type="button" id="btnOpenCvCamera" style="background:var(--card);color:var(--foreground);border:1px solid var(--border);border-radius:7px;padding:0.52rem 0.9rem;font-size:0.8rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa fa-camera"></i> {{ $isEn ? 'Camera' : 'Activar Cámara' }}
            </button>
            <button type="button" id="btnRunFundusScanner" style="background:var(--space-neon);color:#09090b;border:1px solid #c7d900;border-radius:7px;padding:0.55rem 1.05rem;font-size:0.82rem;font-weight:800;cursor:pointer;display:inline-flex;align-items:center;gap:7px;">
                <i class="fa fa-crosshairs"></i> {{ $isEn ? 'Run Python CV Scanner' : 'Ejecutar Escáner Python CV' }}
            </button>
        </div>
    </div>

    {{-- Modal de cámara en vivo (getUserMedia + Voltear Cámara + Flash) --}}
    <div id="cvCameraBox" role="dialog" aria-modal="true" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(9,9,11,0.84);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:1rem;">
        <div style="width:100%;max-width:680px;background:var(--card);color:var(--foreground);border:1px solid var(--border);border-radius:14px;padding:1rem 1.1rem;box-shadow:0 24px 60px rgba(0,0,0,0.45);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;gap:0.5rem;">
                <strong style="font-size:0.9rem;display:inline-flex;align-items:center;gap:0.45rem;">
                    <i class="fa fa-video-camera"></i> {{ $isEn ? 'Live Camera Capture → Python CV' : 'Captura de Cámara en Vivo → Python CV' }}
                </strong>
                <button type="button" id="btnCloseCvCamera" style="background:var(--muted);color:var(--foreground);border:1px solid var(--border);border-radius:7px;padding:0.35rem 0.75rem;font-size:0.78rem;font-weight:700;cursor:pointer;">
                    <i class="fa fa-times"></i> {{ $isEn ? 'Close' : 'Cerrar' }}
                </button>
            </div>
            <div style="position:relative;background:#09090b;border-radius:10px;overflow:hidden;border:1px solid var(--border);">
                <video id="cvCameraVideo" autoplay playsinline muted style="width:100%;max-height:60vh;min-height:260px;object-fit:contain;display:block;"></video>
            </div>
            <div id="cvCameraError" style="display:none;margin-top:0.55rem;font-size:0.76rem;color:#ef4444;font-weight:600;"></div>
            <div style="display:flex;justify-content:space-between;align-items:center;gap:0.5rem;flex-wrap:wrap;margin-top:0.85rem;">
                <div style="display:flex;gap:0.45rem;flex-wrap:wrap;">
                    <button type="button" id="btnFlipCvCamera" style="background:var(--muted);color:var(--foreground);border:1px solid var(--border);border-radius:7px;padding:0.48rem 0.85rem;font-size:0.78rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                        <i class="fa fa-refresh"></i> {{ $isEn ? 'Flip Camera' : 'Voltear Cámara' }}
                    </button>
                    <button type="button" id="btnToggleCvFlash" style="background:var(--muted);color:var(--foreground);border:1px solid var(--border);border-radius:7px;padding:0.48rem 0.85rem;font-size:0.78rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                        <i class="fa fa-bolt"></i> <span>{{ $isEn ? 'Enable Flash' : 'Activar Flash' }}</span>
                    </button>
                </div>
                <button type="button" id="btnCaptureCvFrame" style="background:var(--space-neon);color:#09090b;border:1px solid #09090b;border-radius:7px;padding:0.5rem 1.05rem;font-size:0.8rem;font-weight:800;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                    <i class="fa fa-camera"></i> {{ $isEn ? 'Capture & Scan in Python' : 'Tomar Foto y Analizar' }}
                </button>
            </div>
        </div>
    </div>

    {{-- Galería longitudinal multi-foto (captura en vivo + archivo de misión) --}}
    <div style="margin-bottom:1rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.45rem;flex-wrap:wrap;gap:0.5rem;">
            <span style="font-family:ui-monospace,monospace;font-size:0.7rem;font-weight:800;color:var(--muted-foreground);text-transform:uppercase;">
                {{ $isEn ? 'Select Orbital Fundus Capture to Process in Python CV (8 captures)' : 'Selecciona Captura de Fondo de Ojo para Procesar en Python CV (8 capturas)' }}
            </span>
            <span style="font-family:ui-monospace,monospace;font-size:0.68rem;color:var(--muted-foreground);">
                {{ $isEn ? 'Click any capture to run Pillow + NumPy + SciPy pixel analysis' : 'Haz clic en cualquier captura para ejecutar análisis real en Pillow + NumPy + SciPy' }}
            </span>
        </div>
        <div id="fundusGalleryStrip" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:0.55rem;">
            <button type="button" class="sv-fundus-thumb is-selected"
                data-eye="OD" data-iop="{{ $orbitalTransmission['iop_od'] }}" data-preset="{{ $orbitalTransmission['od_preset'] }}"
                data-filename="{{ $orbitalTransmission['od_image'] }}" data-src="{{ asset($orbitalTransmission['od_image']) }}"
                style="text-align:left;background:#09090b;color:#fff;border:2px solid #09090b;border-radius:8px;padding:0.4rem;cursor:pointer;">
                <img src="{{ asset($orbitalTransmission['od_image']) }}" alt="OD Live" style="width:100%;height:72px;object-fit:cover;border-radius:5px;display:block;background:#000;">
                <div style="font-family:ui-monospace,monospace;font-size:10px;margin-top:4px;font-weight:700;">OD · {{ $isEn ? 'Live Downlink' : 'Enlace Vivo' }}</div>
                <div style="font-family:ui-monospace,monospace;font-size:9.5px;color:#a1a1aa;">{{ $orbitalTransmission['iop_od'] }} mmHg</div>
            </button>
            <button type="button" class="sv-fundus-thumb"
                data-eye="OS" data-iop="{{ $orbitalTransmission['iop_os'] }}" data-preset="{{ $orbitalTransmission['os_preset'] }}"
                data-filename="{{ $orbitalTransmission['os_image'] }}" data-src="{{ asset($orbitalTransmission['os_image']) }}"
                style="text-align:left;background:#18181b;color:#fff;border:1px solid #d4d4d8;border-radius:8px;padding:0.4rem;cursor:pointer;">
                <img src="{{ asset($orbitalTransmission['os_image']) }}" alt="OS Live" style="width:100%;height:72px;object-fit:cover;border-radius:5px;display:block;background:#000;">
                <div style="font-family:ui-monospace,monospace;font-size:10px;margin-top:4px;font-weight:700;">OS · {{ $isEn ? 'Live Downlink' : 'Enlace Vivo' }}</div>
                <div style="font-family:ui-monospace,monospace;font-size:9.5px;color:#a1a1aa;">{{ $orbitalTransmission['iop_os'] }} mmHg</div>
            </button>
            @foreach($fundusGallery as $item)
            <button type="button" class="sv-fundus-thumb"
                data-eye="{{ $item['eye'] }}" data-iop="{{ $item['iop'] }}" data-preset="{{ $item['preset'] }}"
                data-filename="{{ $item['filename'] }}" data-src="{{ asset($item['image']) }}"
                style="text-align:left;background:#18181b;color:#fff;border:1px solid #d4d4d8;border-radius:8px;padding:0.4rem;cursor:pointer;">
                <img src="{{ asset($item['image']) }}" alt="{{ $item['label_en'] }}" style="width:100%;height:72px;object-fit:cover;border-radius:5px;display:block;background:#000;">
                <div style="font-family:ui-monospace,monospace;font-size:10px;margin-top:4px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item['eye'] }} · {{ $isEn ? $item['label_en'] : $item['label_es'] }}</div>
                <div style="font-family:ui-monospace,monospace;font-size:9.5px;color:#a1a1aa;">{{ $item['day'] }} · {{ $item['iop'] }} mmHg</div>
            </button>
            @endforeach
        </div>
    </div>

    {{-- Visor multi-canal de Computer Vision (Original + 3 Mapas Python SciPy/NumPy) --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(175px,1fr));gap:0.7rem;margin-bottom:1rem;">
        <div style="background:#09090b;border:1px solid #27272a;border-radius:8px;padding:0.55rem;color:#fff;position:relative;overflow:hidden;">
            <div style="display:flex;justify-content:space-between;font-family:ui-monospace,monospace;font-size:0.68rem;margin-bottom:0.35rem;">
                <strong id="cvOriginalLabel">OD · {{ $isEn ? 'Original Fundus' : 'Fondo Original' }}</strong>
                <span id="cvOriginalIop" style="color:#a1a1aa;">{{ $orbitalTransmission['iop_od'] }} mmHg</span>
            </div>
            <img id="cvOriginalImg" src="{{ asset($orbitalTransmission['od_image']) }}" alt="Original Fundus" style="width:100%;height:175px;object-fit:contain;border-radius:6px;display:block;background:#000;">
            <div class="sv-scan-laser" style="display:none;position:absolute;left:0;right:0;height:2px;background:#38bdf8;box-shadow:0 0 12px #38bdf8;top:22%;"></div>
        </div>
        <div style="background:#09090b;border:1px solid #27272a;border-radius:8px;padding:0.55rem;color:#fff;">
            <div style="display:flex;justify-content:space-between;font-family:ui-monospace,monospace;font-size:0.68rem;margin-bottom:0.35rem;">
                <strong>{{ $isEn ? 'Sobel Disc Rim' : 'Borde Sobel Papila' }}</strong>
                <span style="color:#38bdf8;">SciPy</span>
            </div>
            <img id="cvEdgeImg" src="{{ asset($orbitalTransmission['od_image']) }}" alt="Sobel Disc Edge Map" style="width:100%;height:175px;object-fit:contain;border-radius:6px;display:block;background:#000;">
        </div>
        <div style="background:#09090b;border:1px solid #27272a;border-radius:8px;padding:0.55rem;color:#fff;">
            <div style="display:flex;justify-content:space-between;font-family:ui-monospace,monospace;font-size:0.68rem;margin-bottom:0.35rem;">
                <strong>{{ $isEn ? 'Edema Halo Map' : 'Mapa Halo Edema' }}</strong>
                <span style="color:#facc15;">NumPy</span>
            </div>
            <img id="cvHeatmapImg" src="{{ asset($orbitalTransmission['od_image']) }}" alt="Edema Elevation Heatmap" style="width:100%;height:175px;object-fit:contain;border-radius:6px;display:block;background:#000;">
        </div>
        <div style="background:#09090b;border:1px solid #27272a;border-radius:8px;padding:0.55rem;color:#fff;">
            <div style="display:flex;justify-content:space-between;font-family:ui-monospace,monospace;font-size:0.68rem;margin-bottom:0.35rem;">
                <strong>{{ $isEn ? 'Choroidal Folds' : 'Pliegues Coroideos' }}</strong>
                <span style="color:#4ade80;">Bandpass</span>
            </div>
            <img id="cvFoldsImg" src="{{ asset($orbitalTransmission['od_image']) }}" alt="Choroidal Folds Ridge Map" style="width:100%;height:175px;object-fit:contain;border-radius:6px;display:block;background:#000;">
        </div>
    </div>

    <div id="fundusScanResultBox" style="background:var(--muted);border:1px solid var(--border);border-radius:8px;padding:0.95rem 1.05rem;font-size:0.82rem;color:var(--foreground);">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:0.5rem;flex-wrap:wrap;margin-bottom:0.6rem;">
            <span style="font-family:ui-monospace,monospace;font-size:0.72rem;font-weight:800;color:var(--foreground);">
                {{ $isEn ? 'PYTHON CV REAL-PIXEL INTERPRETATION OUTPUT' : 'SALIDA DE INTERPRETACIÓN PYTHON CV (PÍXELES REALES)' }}
            </span>
            <span id="fundusEngineTelemetry" style="font-family:ui-monospace,monospace;font-size:0.69rem;color:var(--muted-foreground);">
                {{ $isEn ? 'Initializing Python 3.14 + NumPy + SciPy scanner…' : 'Inicializando escáner Python 3.14 + NumPy + SciPy…' }}
            </span>
        </div>
        <div id="fundusScanPlaceholder" style="color:var(--muted-foreground);line-height:1.5;">
            {{ $isEn ? 'Running real pixel-level CV analysis on orbital fundus capture…' : 'Ejecutando análisis real de píxeles en captura orbital de fondo de ojo…' }}
        </div>
        <div id="fundusScanMetrics" style="display:none;"></div>
    </div>
</div>

<script>
(() => {
    const btn = document.getElementById('btnRunFundusScanner');
    if (!btn || btn.dataset.bound === 'true') return;
    btn.dataset.bound = 'true';
    const isEn = @json($isEn);
    const thumbs = [...document.querySelectorAll('#fundusGalleryStrip .sv-fundus-thumb')];
    let activeCapture = {
        eye: thumbs[0]?.dataset.eye || 'OD',
        iop: Number(thumbs[0]?.dataset.iop || @json($orbitalTransmission['iop_od'])),
        preset: thumbs[0]?.dataset.preset || @json($orbitalTransmission['od_preset']),
        filename: thumbs[0]?.dataset.filename || @json($orbitalTransmission['od_image']),
        src: thumbs[0]?.dataset.src || @json(asset($orbitalTransmission['od_image'])),
    };

    async function runFundusScan() {
        btn.disabled = true;
        btn.innerHTML = `<i class="fa fa-spinner fa-spin"></i> ${isEn ? 'Processing pixels in Python CV…' : 'Procesando píxeles en Python CV…'}`;
        document.getElementById('cvOriginalImg').src = activeCapture.src;
        document.getElementById('cvOriginalLabel').textContent = `${activeCapture.eye} · ${isEn ? 'Original Fundus' : 'Fondo Original'}`;
        document.getElementById('cvOriginalIop').textContent = `${activeCapture.iop.toFixed(1)} mmHg`;
        ['cvEdgeImg', 'cvHeatmapImg', 'cvFoldsImg'].forEach(id => {
            const img = document.getElementById(id);
            if (img) { img.style.opacity = '0.28'; img.style.filter = 'blur(2px)'; }
        });
        document.querySelectorAll('.sv-scan-laser').forEach(el => {
            el.style.display = 'block';
            el.animate([{ top: '14%' }, { top: '88%' }, { top: '14%' }], { duration: 900, iterations: 2 });
        });

        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const res = await fetch(@json(route('public.sans-monitor.fundus-scan')), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({
                    case_id: @json($activeCase->case_id),
                    eye: activeCapture.eye,
                    iop_mmhg: activeCapture.iop,
                    demo_preset: activeCapture.preset,
                    image_filename: activeCapture.filename,
                    image_data_url: activeCapture.image_data_url || null,
                })
            });
            const data = await res.json();
            if (data.cv_overlays?.disc_edge_map) document.getElementById('cvEdgeImg').src = data.cv_overlays.disc_edge_map;
            if (data.cv_overlays?.edema_heatmap) document.getElementById('cvHeatmapImg').src = data.cv_overlays.edema_heatmap;
            if (data.cv_overlays?.folds_ridge_map) document.getElementById('cvFoldsImg').src = data.cv_overlays.folds_ridge_map;
            ['cvEdgeImg', 'cvHeatmapImg', 'cvFoldsImg'].forEach(id => {
                const img = document.getElementById(id);
                if (img) { img.style.opacity = '1'; img.style.filter = 'none'; }
            });

            const tel = document.getElementById('fundusEngineTelemetry');
            if (tel) {
                tel.textContent = data.python_executed
                    ? `PYTHON EXECUTED · ${data.image_resolution || '384x384'} · ${(data.pixels_analyzed || 0).toLocaleString()} px · ${data.processing_ms} ms · Disc (${(data.disc_center_px || [0,0]).join(', ')})`
                    : `${data.engine || 'Fallback PHP'}`;
            }

            document.getElementById('fundusScanPlaceholder').style.display = 'none';
            const box = document.getElementById('fundusScanMetrics');
            box.style.display = 'block';
            const interp = isEn ? data.clinical_interpretation?.en : data.clinical_interpretation?.es;
            const iopNote = isEn ? data.iop_interpretation?.en : data.iop_interpretation?.es;
            const safety = isEn ? data.safety_notice?.en : data.safety_notice?.es;
            const traceRows = (data.stage_trace || []).map(st => `
                <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;padding:4px 8px;border-bottom:1px solid var(--border);font-family:ui-monospace,monospace;font-size:11px;">
                    <span><strong style="color:var(--foreground);">[${st.step}/5] ${st.name}</strong> <span style="color:var(--muted-foreground);">(${st.lib})</span></span>
                    <span style="color:var(--muted-foreground);text-align:right;">${st.detail} · <strong style="color:var(--foreground);">${st.ms} ms</strong></span>
                </div>
            `).join('');
            box.innerHTML = `
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:8px;margin-bottom:10px;font-family:ui-monospace,monospace;">
                    <div style="background:var(--card);border:1px solid var(--border);border-radius:6px;padding:7px 9px;"><small style="display:block;color:var(--muted-foreground);font-size:10px;">FRISÉN GRADE</small><strong style="font-size:14px;color:var(--foreground);">Grade ${data.frisen_grade_estimate}</strong></div>
                    <div style="background:var(--card);border:1px solid var(--border);border-radius:6px;padding:7px 9px;"><small style="display:block;color:var(--muted-foreground);font-size:10px;">SOBEL RIM SHARPNESS</small><strong style="font-size:14px;color:var(--foreground);">${Math.round((data.disc_margin_sharpness_index || 0) * 100)}%</strong></div>
                    <div style="background:var(--card);border:1px solid var(--border);border-radius:6px;padding:7px 9px;"><small style="display:block;color:var(--muted-foreground);font-size:10px;">PERIPAPILLARY HALO</small><strong style="font-size:14px;color:var(--foreground);">${Math.round((data.peripapillary_edema_halo || 0) * 100)}%</strong></div>
                    <div style="background:var(--card);border:1px solid var(--border);border-radius:6px;padding:7px 9px;"><small style="display:block;color:var(--muted-foreground);font-size:10px;">CHOROIDAL FOLDS</small><strong style="font-size:14px;color:var(--foreground);">${data.choroidal_folds_detected ? (isEn ? 'Detected' : 'Detectados') : (isEn ? 'None' : 'Ausentes')}</strong></div>
                    <div style="background:var(--card);border:1px solid var(--border);border-radius:6px;padding:7px 9px;"><small style="display:block;color:var(--muted-foreground);font-size:10px;">VESSEL TORTUOSITY</small><strong style="font-size:14px;color:var(--foreground);">${Math.round((data.vessel_tortuosity_index || 0) * 100)}%</strong></div>
                    <div style="background:var(--card);border:1px solid var(--border);border-radius:6px;padding:7px 9px;"><small style="display:block;color:var(--muted-foreground);font-size:10px;">TONOMETER IOP</small><strong style="font-size:14px;color:var(--foreground);">${data.iop_mmhg} mmHg</strong></div>
                </div>
                ${traceRows ? `<div style="background:var(--card);border:1px solid var(--border);border-radius:6px;margin-bottom:10px;overflow:hidden;">${traceRows}</div>` : ''}
                <p style="margin:0 0 6px;color:var(--foreground);font-weight:700;">${interp}</p>
                <p style="margin:0 0 6px;color:var(--muted-foreground);font-size:12px;"><strong>${isEn ? 'Tonometer IOP Correlation' : 'Correlación PIO Tonómetro'} (${data.iop_mmhg} mmHg):</strong> ${iopNote}</p>
                <div style="font-size:11px;color:var(--muted-foreground);border-top:1px solid var(--border);padding-top:6px;">${safety}</div>
            `;
        } finally {
            ['cvEdgeImg', 'cvHeatmapImg', 'cvFoldsImg'].forEach(id => {
                const img = document.getElementById(id);
                if (img) { img.style.opacity = '1'; img.style.filter = 'none'; }
            });
            document.querySelectorAll('.sv-scan-laser').forEach(el => { el.style.display = 'none'; });
            btn.disabled = false;
            btn.innerHTML = `<i class="fa fa-crosshairs"></i> ${isEn ? 'Run Python CV Scanner' : 'Ejecutar Escáner Python CV'}`;
        }
    }

    thumbs.forEach(thumb => {
        thumb.addEventListener('click', () => {
            thumbs.forEach(t => {
                t.style.border = '1px solid #d4d4d8';
                t.style.background = '#18181b';
            });
            thumb.style.border = '2px solid #09090b';
            thumb.style.background = '#09090b';
            activeCapture = {
                eye: thumb.dataset.eye,
                iop: Number(thumb.dataset.iop),
                preset: thumb.dataset.preset,
                filename: thumb.dataset.filename,
                src: thumb.dataset.src,
                image_data_url: null,
            };
            runFundusScan();
        });
    });

    const uploadInput = document.getElementById('cvUploadFileInput');
    uploadInput?.addEventListener('change', (e) => {
        const file = e.target.files?.[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = () => {
            activeCapture = {
                ...activeCapture,
                filename: file.name,
                src: reader.result,
                image_data_url: reader.result,
            };
            runFundusScan();
        };
        reader.readAsDataURL(file);
    });

    const camBox = document.getElementById('cvCameraBox');
    const camVideo = document.getElementById('cvCameraVideo');
    const camErr = document.getElementById('cvCameraError');
    const btnFlash = document.getElementById('btnToggleCvFlash');
    let camStream = null;
    let camFacingMode = 'environment';
    let flashOn = false;

    function updateFlashBtn() {
        if (!btnFlash) return;
        btnFlash.style.background = flashOn ? 'var(--space-neon)' : 'var(--muted)';
        btnFlash.style.color = flashOn ? '#09090b' : 'var(--foreground)';
        btnFlash.innerHTML = `<i class="fa fa-bolt"></i> <span>${flashOn ? (isEn ? 'Flash On' : 'Flash Activado') : (isEn ? 'Enable Flash' : 'Activar Flash')}</span>`;
    }

    function stopCvStream() {
        if (camStream) {
            camStream.getTracks().forEach(t => t.stop());
            camStream = null;
        }
        flashOn = false;
        updateFlashBtn();
        if (camVideo) camVideo.srcObject = null;
    }

    function stopCvCamera() {
        stopCvStream();
        if (camBox) camBox.style.display = 'none';
    }

    async function startCvCamera(forceSwitch = false) {
        if (!camBox || !camVideo) return;
        camBox.style.display = 'flex';
        if (camErr) { camErr.style.display = 'none'; camErr.textContent = ''; }
        const prevTrack = camStream?.getVideoTracks?.()?.[0];
        const prevDeviceId = prevTrack?.getSettings?.()?.deviceId || null;
        stopCvStream();
        if (forceSwitch) {
            await new Promise(r => setTimeout(r, 80));
        }
        try {
            if (!navigator.mediaDevices?.getUserMedia) {
                throw new Error(isEn ? 'Camera API requires HTTPS or localhost.' : 'La API de cámara requiere HTTPS o localhost.');
            }
            const attempts = [
                { video: { facingMode: { exact: camFacingMode } }, audio: false },
                { video: { facingMode: { ideal: camFacingMode } }, audio: false },
            ];
            if (forceSwitch && navigator.mediaDevices.enumerateDevices) {
                try {
                    const devs = (await navigator.mediaDevices.enumerateDevices()).filter(d => d.kind === 'videoinput');
                    if (devs.length > 1) {
                        const idx = devs.findIndex(d => d.deviceId === prevDeviceId);
                        const nextDev = devs[(idx + 1) % devs.length];
                        if (nextDev?.deviceId) {
                            attempts.splice(1, 0, { video: { deviceId: { exact: nextDev.deviceId } }, audio: false });
                        }
                    }
                } catch (_) {}
            }
            attempts.push({ video: true, audio: false });

            let lastErr = null;
            for (const constraints of attempts) {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia(constraints);
                    const newDevId = stream.getVideoTracks?.()?.[0]?.getSettings?.()?.deviceId || null;
                    if (forceSwitch && prevDeviceId && newDevId && newDevId === prevDeviceId && constraints !== attempts[attempts.length - 1]) {
                        stream.getTracks().forEach(t => t.stop());
                        continue;
                    }
                    camStream = stream;
                    break;
                } catch (err) {
                    lastErr = err;
                }
            }
            if (!camStream) throw (lastErr || new Error('Camera unavailable'));

            const activeFacing = camStream.getVideoTracks?.()?.[0]?.getSettings?.()?.facingMode || camFacingMode;
            camVideo.style.transform = activeFacing === 'user' ? 'scaleX(-1)' : 'none';
            camVideo.srcObject = camStream;
        } catch (err) {
            if (camErr) {
                camErr.style.display = 'block';
                camErr.textContent = err?.message || (isEn ? 'Unable to access camera.' : 'No se pudo acceder a la cámara.');
            }
        }
    }

    document.getElementById('btnOpenCvCamera')?.addEventListener('click', () => startCvCamera(false));
    document.getElementById('btnCloseCvCamera')?.addEventListener('click', stopCvCamera);
    camBox?.addEventListener('click', (e) => {
        if (e.target === camBox) stopCvCamera();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && camBox && camBox.style.display !== 'none') stopCvCamera();
    });

    document.getElementById('btnFlipCvCamera')?.addEventListener('click', () => {
        camFacingMode = camFacingMode === 'environment' ? 'user' : 'environment';
        startCvCamera(true);
    });

    btnFlash?.addEventListener('click', async () => {
        const track = camStream?.getVideoTracks?.()?.[0];
        if (!track) return;
        const caps = track.getCapabilities ? track.getCapabilities() : {};
        if (!caps.torch) {
            if (camErr) {
                camErr.style.display = 'block';
                camErr.textContent = isEn ? 'Flash / torch is not supported by this camera.' : 'El flash / linterna no está disponible en esta cámara.';
            }
            return;
        }
        try {
            flashOn = !flashOn;
            await track.applyConstraints({ advanced: [{ torch: flashOn }] });
            updateFlashBtn();
            if (camErr) camErr.style.display = 'none';
        } catch (err) {
            flashOn = false;
            updateFlashBtn();
            if (camErr) {
                camErr.style.display = 'block';
                camErr.textContent = err?.message || (isEn ? 'Could not toggle flash.' : 'No se pudo activar el flash.');
            }
        }
    });

    document.getElementById('btnCaptureCvFrame')?.addEventListener('click', () => {
        if (!camVideo || !camVideo.videoWidth) return;
        const c = document.createElement('canvas');
        c.width = camVideo.videoWidth;
        c.height = camVideo.videoHeight;
        const ctx = c.getContext('2d');
        if (camFacingMode === 'user') {
            ctx.translate(c.width, 0);
            ctx.scale(-1, 1);
        }
        ctx.drawImage(camVideo, 0, 0, c.width, c.height);
        const dataUrl = c.toDataURL('image/png');
        stopCvCamera();
        activeCapture = {
            ...activeCapture,
            filename: 'camera-capture.png',
            src: dataUrl,
            image_data_url: dataUrl,
        };
        runFundusScan();
    });

    btn.addEventListener('click', runFundusScan);
    runFundusScan();
})();
</script>

<div style="display:grid;grid-template-columns:1.15fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">

    {{-- COLUMNA IZQUIERDA: FORMULARIO DE CAPTURA Y SIMULACIÓN DE LOS 6 BIOMARCADORES --}}
    <div class="card" style="padding:1.35rem;border:1.5px solid var(--interactive-border);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.85rem;flex-wrap:wrap;gap:0.4rem;">
            <h2 style="font-size:1.1rem;font-weight:800;margin:0;">
                <i class="fa fa-sliders" style="color:var(--interactive-text);"></i>
                {{ $isEn ? 'Feed Predictor — The 6 Key Biomarkers' : 'Nutrir Predictor — Los 6 Biomarcadores Clave' }} ({{ $activeCase->case_id }})
            </h2>
            <span class="interactive-pill">
                <i class="fa fa-pencil"></i> {{ $isEn ? 'Editable Inputs · Click Run Below' : 'Campos Editables · Pulsa Ejecutar Abajo' }}
            </span>
        </div>

        <form method="GET" action="{{ route('admin.sans-monitor.index') }}">
            <input type="hidden" name="tab" value="biomarcadores">
            <input type="hidden" name="case_id" value="{{ $activeCase->case_id }}">
            <input type="hidden" name="eye" value="{{ $selectedEye }}">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.85rem;margin-bottom:1rem;">
                {{-- 1. Espesor medio por OCT --}}
                <div class="biomarker-input-box">
                    <label style="font-size:0.76rem;font-weight:700;display:block;margin-bottom:0.25rem;">
                        <i class="fa fa-align-justify"></i>
                        {{ $isEn ? 'Mean OCT Thickness (µm)' : 'Espesor Medio por OCT (µm)' }}
                    </label>
                    <div style="display:flex;gap:0.4rem;">
                        <input type="number" step="0.1" name="oct_trt_baseline_um" class="form-control"
                               value="{{ $bp['inputs']['oct_trt_baseline_um'] }}" placeholder="{{ $isEn ? 'Baseline (280)' : 'Basal (280)' }}">
                        <input type="number" step="0.1" name="oct_trt_current_um" class="form-control"
                               value="{{ $bp['inputs']['oct_trt_current_um'] }}" placeholder="{{ $isEn ? 'Current (306)' : 'Actual (306)' }}">
                    </div>
                    <div style="font-size:0.72rem;color:var(--muted-foreground);margin-top:0.25rem;">
                        {{ $isEn ? 'Baseline vs Current (Trigger ΔTRT ≥ +20 µm)' : 'Línea base vs Actual (Disparador ΔTRT ≥ +20 µm)' }}
                    </div>
                </div>

                {{-- 2. Longitud Axial del Ojo --}}
                <div class="biomarker-input-box">
                    <label style="font-size:0.76rem;font-weight:700;display:block;margin-bottom:0.25rem;">
                        <i class="fa fa-arrows-h"></i>
                        {{ $isEn ? 'Eye Axial Length (mm)' : 'Longitud Axial del Ojo (mm)' }}
                    </label>
                    <input type="number" step="0.01" name="axial_length_mm" class="form-control"
                           value="{{ $bp['inputs']['axial_length_mm'] }}" placeholder="23.82 mm">
                    <div style="font-size:0.72rem;color:var(--muted-foreground);margin-top:0.25rem;">
                        {{ $isEn ? 'Baseline ref:' : 'Basal ref:' }} {{ $bp['inputs']['axial_baseline_mm'] }} mm ({{ $isEn ? 'Axial shortening in microgravity' : 'Acortamiento axial en microgravedad' }})
                    </div>
                </div>

                {{-- 3. Hipermetropía Simulada --}}
                <div class="biomarker-input-box">
                    <label style="font-size:0.76rem;font-weight:700;display:block;margin-bottom:0.25rem;">
                        <i class="fa fa-eye"></i>
                        {{ $isEn ? 'Simulated Hyperopia (ΔSE in D)' : 'Hipermetropía Simulada (ΔSE en D)' }}
                    </label>
                    <input type="number" step="0.25" name="hyperopia_sim_d" class="form-control"
                           value="{{ $bp['inputs']['hyperopia_sim_d'] }}" placeholder="+0.75 D">
                    <div style="font-size:0.72rem;color:var(--muted-foreground);margin-top:0.25rem;">
                        {{ $isEn ? 'Hyperopic refractive shift (+0.50 D to +1.50 D)' : 'Desplazamiento refractivo hipermetrópico (+0.50 D a +1.50 D)' }}
                    </div>
                </div>

                {{-- 4. Edema Papilar (Grado Frisén) --}}
                <div class="biomarker-input-box">
                    <label style="font-size:0.76rem;font-weight:700;display:block;margin-bottom:0.25rem;">
                        <i class="fa fa-dot-circle-o"></i>
                        {{ $isEn ? '4. Papilledema (Frisén Scale 0–5)' : '4. Edema Papilar (Escala Frisén 0–5)' }}
                    </label>
                    @php
                        $frisenOptions = $isEn ? [
                            0 => 'Grade 0 — Normal / No edema',
                            1 => 'Grade 1 — Mild nasal blurring (C-shaped halo)',
                            2 => 'Grade 2 — Early circumferential elevation',
                            3 => 'Grade 3 — Moderate optic nerve head edema',
                            4 => 'Grade 4 — Severe edema with cup obliteration',
                            5 => 'Grade 5 — Critical optic dome protrusion',
                        ] : [
                            0 => 'Grado 0 — Normal / Sin edema',
                            1 => 'Grado 1 — Borramiento nasal leve (Halo C)',
                            2 => 'Grado 2 — Elevación circunferencial temprana',
                            3 => 'Grado 3 — Edema moderado de cabeza óptica',
                            4 => 'Grado 4 — Edema severo con obliteración de copa',
                            5 => 'Grado 5 — Protrusión crítica de domo óptico',
                        ];
                    @endphp
                    <select name="papilledema_frisen" class="form-control">
                        @foreach($frisenOptions as $fg => $flabel)
                            <option value="{{ $fg }}" {{ (string)$bp['inputs']['papilledema_frisen'] === (string)$fg ? 'selected' : '' }}>{{ $flabel }}</option>
                        @endforeach
                    </select>
                    <div style="font-size:0.72rem;color:var(--muted-foreground);margin-top:0.25rem;">
                        {{ $isEn ? 'Primary neuro-ocular prognostic target' : 'Objetivo principal de pronóstico neuro-ocular' }}
                    </div>
                </div>

                {{-- 5. Presión Intraocular (PIO) --}}
                <div class="biomarker-input-box">
                    <label style="font-size:0.76rem;font-weight:700;display:block;margin-bottom:0.25rem;">
                        <i class="fa fa-tachometer"></i>
                        {{ $isEn ? '5. Intraocular Pressure — IOP (mmHg)' : '5. Presión Intraocular — PIO (mmHg)' }}
                    </label>
                    <input type="number" step="0.5" name="iop_mmhg" class="form-control"
                           value="{{ $bp['inputs']['iop_mmhg'] }}" placeholder="18.5 mmHg">
                    <div style="font-size:0.72rem;color:var(--muted-foreground);margin-top:0.25rem;">
                        {{ $isEn ? 'Ocular tonometry (Earth normal range: 10–21 mmHg)' : 'Tonometría ocular (Rango normal terrestre: 10–21 mmHg)' }}
                    </div>
                </div>

                {{-- 6. Exposición previa a inclinación cefálica de -6° en la Tierra --}}
                <div class="biomarker-input-box">
                    <label style="font-size:0.76rem;font-weight:700;display:block;margin-bottom:0.25rem;">
                        <i class="fa fa-level-down"></i>
                        {{ $isEn ? '6. Prior -6° Head-Down Tilt on Earth' : '6. Exposición Previa a -6° en la Tierra' }}
                    </label>
                    <select name="prior_hdt6_exposure" class="form-control">
                        <option value="SI_14_DIAS" {{ str_starts_with((string)$bp['inputs']['prior_hdt6_exposure'], 'SI') ? 'selected' : '' }}>
                            {{ $isEn ? 'Yes — -6° HDT Analog (280 → 304 → 284 µm)' : 'Sí — Análogo -6° HDT (280 → 304 → 284 µm)' }}
                        </option>
                        <option value="NO" {{ (string)$bp['inputs']['prior_hdt6_exposure'] === 'NO' ? 'selected' : '' }}>
                            {{ $isEn ? 'No — No prior -6° tilt exposure' : 'No — Sin antecedente de inclinación -6°' }}
                        </option>
                    </select>
                    <div style="font-size:0.72rem;color:var(--muted-foreground);margin-top:0.25rem;">
                        {{ $isEn ? 'Head-down tilt bed rest (-6° HDT)' : 'Reposo en cama con inclinación cefálica (-6° HDT)' }}
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;padding:0.75rem;font-weight:800;font-size:0.92rem;">
                <i class="fa fa-bolt"></i>
                {{ $isEn ? 'Run Predictor with these 6 Biomarkers' : 'Ejecutar Predictor con estos 6 Biomarcadores' }}
            </button>
        </form>
    </div>

    {{-- COLUMNA DERECHA: SALIDA DEL PREDICTOR DE BIOMARCADORES + REGISTRO RÁPIDO DE PERSONA --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;">
        <div class="card" style="padding:1.35rem;border:1.5px solid var(--space-blue);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.6rem;">
                <h2 style="font-size:1.05rem;font-weight:800;margin:0;">
                    <i class="fa fa-line-chart" style="color:var(--interactive-text);"></i>
                    {{ $isEn ? 'Biomarker Predictor Output' : 'Resultado del Predictor por Biomarcadores' }}
                </h2>
                <span class="interactive-pill" style="background:#0042A6;color:#ffffff;border-color:#0042A6;padding:0.3rem 0.65rem;font-size:0.76rem;">
                    {{ $isEn ? 'Signal Index:' : 'Índice de Señal:' }} {{ $bp['signal_index'] }} / 100
                </span>
            </div>

            <div style="padding:0.75rem;border:1px solid var(--interactive-border);background:var(--interactive-bg);border-radius:6px;margin-bottom:0.85rem;">
                <div style="font-size:0.72rem;text-transform:uppercase;font-weight:700;color:var(--interactive-text);">
                    {{ $isEn ? 'Biomarker-Based Projection' : 'Proyección Basada en Biomarcadores' }}
                </div>
                <div style="font-size:1rem;font-weight:800;margin-top:0.15rem;">{{ $bp['predicted_trajectory'] }}</div>
                <div style="font-size:0.76rem;color:var(--muted-foreground);margin-top:0.2rem;">
                    {{ $isEn
                        ? 'Experimental prognosis status: Not evaluated (Requires validated prognostic model P2 — deterministic rule engine active)'
                        : $caseEvaluation['future_outlook_banner']['experimental_prognosis_status'] }}
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;margin-bottom:0.85rem;">
                @foreach($bp['biomarker_cards'] as $card)
                    <div style="padding:0.55rem 0.7rem;border:1px solid var(--border);border-radius:4px;">
                        <div style="font-size:0.72rem;color:var(--muted-foreground);">{{ $card['label'] }}</div>
                        <div style="font-size:0.86rem;font-weight:700;">{{ $card['value'] }}</div>
                    </div>
                @endforeach
            </div>

            <div style="font-size:0.8rem;">
                <strong>{{ $isEn ? 'Signals Detected by Engine:' : 'Señales Detectadas por el Motor:' }}</strong>
                <ul style="margin:0.35rem 0 0;padding-left:1.2rem;">
                    @foreach($bp['signals'] as $sig)
                        <li>{{ $sig }}</li>
                    @endforeach
                </ul>
            </div>

            <div style="position:relative;height:185px;width:100%;margin-top:0.85rem;">
                <canvas id="biomarkerImpactChart"></canvas>
            </div>
        </div>

        {{-- Formulario Inicial Compacto para Registrar Nueva Persona --}}
        <details class="card collapsible-card" style="padding:1rem;">
            <summary style="cursor:pointer;font-weight:700;font-size:0.9rem;display:flex;justify-content:space-between;align-items:center;gap:0.5rem;">
                <span><i class="fa fa-user-plus" style="color:var(--interactive-text);"></i> {{ $isEn ? 'Initial Form: Register New Subject / Dossier' : 'Formulario Inicial: Registrar Nueva Persona / Expediente' }}</span>
                <span class="interactive-pill">{{ $isEn ? 'Click to expand +' : 'Clic para abrir +' }}</span>
            </summary>
            <form method="POST" action="{{ route('admin.sans-monitor.cases.store') }}" style="margin-top:0.85rem;">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.6rem;margin-bottom:0.6rem;">
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;">{{ $isEn ? 'Subject ID / Pseudonym' : 'ID Persona / Pseudónimo' }}</label>
                        <input type="text" name="case_id" class="form-control" placeholder="CASE-ASTRONAUT-09" required>
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;">{{ $isEn ? 'Current Phase' : 'Fase Actual' }}</label>
                        <select name="mission_phase" class="form-control">
                            <option value="PRE_VUELO">{{ $isEn ? 'Before flight (PRE_FLIGHT)' : 'Antes del vuelo (PRE_VUELO)' }}</option>
                            <option value="EN_VUELO">{{ $isEn ? 'During flight (IN_FLIGHT)' : 'Durante el vuelo (EN_VUELO)' }}</option>
                            <option value="POSVUELO">{{ $isEn ? 'After flight (POST_FLIGHT)' : 'Después del vuelo (POSVUELO)' }}</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;">{{ $isEn ? 'Mission Duration (Days)' : 'Duración Misión (Días)' }}</label>
                        <input type="number" name="planned_duration_days" class="form-control" value="90">
                        <input type="hidden" name="planned_duration_state" value="VALOR">
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;">{{ $isEn ? 'Previous Flights' : 'Vuelos Previos' }}</label>
                        <select name="previous_flights_state" class="form-control">
                            <option value="VALOR">{{ $isEn ? 'With prior flights' : 'Con vuelos previos' }}</option>
                            <option value="NO_APLICA">{{ $isEn ? 'First flight (N/A)' : 'Primer vuelo (No aplica)' }}</option>
                            <option value="DESCONOCIDO">{{ $isEn ? 'Unknown' : 'Desconocido' }}</option>
                        </select>
                        <input type="hidden" name="previous_flights" value="1">
                    </div>
                </div>
                <input type="hidden" name="source_type" value="FICTICIO_DEMO">
                <input type="hidden" name="applied_protocol_state" value="DESCONOCIDO">
                <div style="margin-bottom:0.6rem;">
                    <label style="font-size:0.72rem;font-weight:600;">{{ $isEn ? 'Initial Reason / Note' : 'Motivo / Nota Inicial' }}</label>
                    <input type="text" name="reason_for_review" class="form-control" value="{{ $isEn ? 'Intake for ocular biomarker prediction' : 'Ingreso para predicción por biomarcadores oculares' }}" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    <i class="fa fa-save"></i> {{ $isEn ? 'Create Subject & Evaluate' : 'Crear Persona y Evaluar' }}
                </button>
            </form>
        </details>
    </div>
</div>

{{-- Matriz Predictiva de Susceptibilidad en 4 Perfiles (Full-Width, SANS+ vs SANS- para Futuros Candidatos) --}}
@php
    $isSusceptible = $activeCase->scenario_archetype === 'EMPEORA';
    $isMissingOct = ($bp['inputs']['oct_trt_current_um'] ?? null) === null;
    $matchLabel = $isMissingOct
        ? ($isEn ? 'Insufficient Data (Missing OCT)' : 'Datos Insuficientes (Sin OCT)')
        : ($isSusceptible
            ? ($isEn ? 'High Similarity to SANS+ Cohort' : 'Alta Similitud con Cohorte SANS+')
            : ($isEn ? 'Aligned with SANS- Resilient Cohort' : 'Alineado con Cohorte Resiliente SANS-'));
    $matchColor = $isMissingOct ? '#64748b' : ($isSusceptible ? '#d97706' : '#16a34a');
@endphp
<div class="card" style="padding:1rem;margin-top:1rem;border:1px solid var(--border);background:var(--card);">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.75rem;flex-wrap:wrap;margin-bottom:0.75rem;">
        <div>
            <div style="font-family:ui-monospace,monospace;font-size:0.68rem;font-weight:800;color:var(--muted-foreground);text-transform:uppercase;letter-spacing:0.05em;">
                {{ $isEn ? '4-DOMAIN PREDICTIVE SUSCEPTIBILITY MODEL · SANS+ VS. SANS- COHORTS' : 'MODELO PREDICTIVO DE SUSCEPTIBILIDAD EN 4 PERFILES · COHORTES SANS+ VS. SANS-' }}
            </div>
            <h3 style="font-size:0.95rem;font-weight:800;margin:0.2rem 0 0;">
                {{ $isEn
                    ? 'Pre-Flight & In-Flight Susceptibility Pattern Matching for Future Spaceflight Candidates'
                    : 'Identificación de Patrones de Susceptibilidad al SANS para Futuros Candidatos a Misiones Espaciales' }}
            </h3>
        </div>
        <span style="font-family:ui-monospace,monospace;font-size:0.7rem;font-weight:800;padding:0.25rem 0.65rem;border-radius:6px;background:var(--muted);border:1.5px solid {{ $matchColor }};color:{{ $matchColor }};">
            {{ $matchLabel }}
        </span>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:0.65rem;font-size:0.76rem;">
        <div style="padding:0.65rem 0.75rem;border:1px solid var(--border);border-radius:8px;background:var(--muted);">
            <div style="font-weight:800;margin-bottom:0.25rem;"><i class="fa fa-arrows-h"></i> {{ $isEn ? 'Biometric Profile' : 'Perfil Biométrico' }}</div>
            <div style="color:var(--muted-foreground);line-height:1.4;">
                {{ $isEn ? 'Active AL:' : 'Long. Axial:' }} <strong style="color:var(--foreground);">{{ $bp['inputs']['axial_length_mm'] ?? '23.8' }} mm</strong><br>
                {{ $isEn ? 'SANS+ pattern: shorter/flattened globe + -6° HDT shift' : 'Patrón SANS+: globo más corto/aplanado + respuesta -6° HDT' }}
            </div>
        </div>
        <div style="padding:0.65rem 0.75rem;border:1px solid var(--border);border-radius:8px;background:var(--muted);">
            <div style="font-weight:800;margin-bottom:0.25rem;"><i class="fa fa-eye"></i> {{ $isEn ? 'Ophthalmological Profile' : 'Perfil Oftalmológico' }}</div>
            <div style="color:var(--muted-foreground);line-height:1.4;">
                {{ $isEn ? 'ΔTRT / Frisén:' : 'ΔTRT / Frisén:' }} <strong style="color:var(--foreground);">{{ $bp['delta_trt_um'] !== null ? '+'.$bp['delta_trt_um'].' µm' : 'N/A' }} · G{{ $bp['inputs']['papilledema_frisen'] ?? 'N/E' }}</strong><br>
                {{ $isEn ? 'SANS+ pattern: ΔTRT ≥ +20 µm, +0.75 D, choroidal folds' : 'Patrón SANS+: ΔTRT ≥ +20 µm, +0.75 D, pliegues coroideos' }}
            </div>
        </div>
        <div style="padding:0.65rem 0.75rem;border:1px solid var(--border);border-radius:8px;background:var(--muted);">
            <div style="font-weight:800;margin-bottom:0.25rem;"><i class="fa fa-sitemap"></i> {{ $isEn ? 'Neurological Profile' : 'Perfil Neurológico' }}</div>
            <div style="color:var(--muted-foreground);line-height:1.4;">
                {{ $isEn ? 'IOP / Translaminar:' : 'PIO / Translaminar:' }} <strong style="color:var(--foreground);">{{ $bp['inputs']['iop_mmhg'] }} mmHg</strong><br>
                {{ $isEn ? 'SANS+ pattern: optic nerve sheath distension & CSF cephalic shift' : 'Patrón SANS+: distensión de vaina óptica y flujo cefálico de LCR' }}
            </div>
        </div>
        <div style="padding:0.65rem 0.75rem;border:1px solid var(--border);border-radius:8px;background:var(--muted);">
            <div style="font-weight:800;margin-bottom:0.25rem;"><i class="fa fa-flask"></i> {{ $isEn ? 'Metabolic & Cofactors' : 'Perfil Metabólico y Cofactores' }}</div>
            <div style="color:var(--muted-foreground);line-height:1.4;">
                {{ $isEn ? '1-Carbon / B-Vitamins / CO₂' : 'Metabolismo 1-C / Vit. B / CO₂' }}<br>
                {{ $isEn ? 'Zwart et al. (NASA): MTRR/SHMT1 polymorphisms + low folate/B12' : 'Zwart et al. (NASA): polimorfismos MTRR/SHMT1 + folato/B12 bajos' }}
            </div>
        </div>
    </div>

    {{-- Simulador Predictivo Interactivo para Futuros Candidatos Espaciales (Scikit-learn) --}}
    <form id="candidate-predict-form" onsubmit="return window.runCandidatePredictor(event)" style="margin-top:0.85rem;padding-top:0.85rem;border-top:1px solid var(--border);">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:0.75rem;flex-wrap:wrap;margin-bottom:0.55rem;">
            <span style="font-size:0.78rem;font-weight:800;">
                <i class="fa fa-sliders"></i> {{ $isEn ? 'Future Mission Candidate Susceptibility Simulator (Python Scikit-learn)' : 'Simulador Predictivo para Futuros Candidatos a Misión (Python Scikit-learn)' }}
            </span>
            <button type="submit" id="candidate-predict-btn" style="background:#EAFE07;color:#09090b;border:1px solid #09090b;padding:0.35rem 0.85rem;border-radius:6px;font-size:0.74rem;font-weight:800;cursor:pointer;">
                <i class="fa fa-calculator"></i> {{ $isEn ? 'Run Predictive Model' : 'Ejecutar Modelo Predictivo' }}
            </button>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(135px,1fr));gap:0.5rem;font-size:0.72rem;">
            <label style="display:flex;flex-direction:column;gap:2px;">
                <span style="color:var(--muted-foreground);font-weight:600;">{{ $isEn ? 'Axial Length (mm)' : 'Long. Axial (mm)' }}</span>
                <input type="number" step="0.1" id="pred-axial" value="23.2" class="form-control" style="padding:0.28rem 0.45rem;font-size:0.76rem;">
            </label>
            <label style="display:flex;flex-direction:column;gap:2px;">
                <span style="color:var(--muted-foreground);font-weight:600;">{{ $isEn ? '-6° HDT ΔTRT (µm)' : '-6° HDT ΔTRT (µm)' }}</span>
                <input type="number" step="1" id="pred-hdt" value="20" class="form-control" style="padding:0.28rem 0.45rem;font-size:0.76rem;">
            </label>
            <label style="display:flex;flex-direction:column;gap:2px;">
                <span style="color:var(--muted-foreground);font-weight:600;">{{ $isEn ? 'Mission Days' : 'Días Misión' }}</span>
                <input type="number" step="10" id="pred-days" value="180" class="form-control" style="padding:0.28rem 0.45rem;font-size:0.76rem;">
            </label>
            <label style="display:flex;flex-direction:column;gap:2px;">
                <span style="color:var(--muted-foreground);font-weight:600;">{{ $isEn ? 'ONSD (mm)' : 'Vaina Óptica (mm)' }}</span>
                <input type="number" step="0.1" id="pred-onsd" value="5.8" class="form-control" style="padding:0.28rem 0.45rem;font-size:0.76rem;">
            </label>
            <label style="display:flex;flex-direction:column;gap:2px;">
                <span style="color:var(--muted-foreground);font-weight:600;">{{ $isEn ? '1-Carbon / Vit B' : '1-Carbono / Vit B' }}</span>
                <select id="pred-oc" class="form-control" style="padding:0.28rem 0.45rem;font-size:0.76rem;">
                    <option value="low">{{ $isEn ? 'Low Risk' : 'Riesgo Bajo' }}</option>
                    <option value="moderate" selected>{{ $isEn ? 'Moderate' : 'Moderado' }}</option>
                    <option value="high">{{ $isEn ? 'High (Polymorphism)' : 'Alto (Polimorfismo)' }}</option>
                </select>
            </label>
            <label style="display:flex;flex-direction:column;gap:2px;">
                <span style="color:var(--muted-foreground);font-weight:600;">{{ $isEn ? 'Cabin CO₂' : 'CO₂ en Cabina' }}</span>
                <select id="pred-co2" class="form-control" style="padding:0.28rem 0.45rem;font-size:0.76rem;">
                    <option value="nominal" selected>{{ $isEn ? 'Nominal' : 'Nominal' }}</option>
                    <option value="elevated">{{ $isEn ? 'Elevated' : 'Elevado' }}</option>
                </select>
            </label>
        </div>
        <div id="candidate-predict-output" style="display:none;margin-top:0.65rem;padding:0.65rem 0.85rem;border-radius:8px;border:1px solid var(--border);background:var(--muted);font-size:0.76rem;"></div>
    </form>
</div>

<script>
window.runCandidatePredictor = async function (e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    const btn = document.getElementById('candidate-predict-btn');
    const out = document.getElementById('candidate-predict-output');
    if (!out) return false;
    const orig = btn ? btn.innerHTML : '';
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ...'; }
    try {
        const res = await fetch(`{{ route('public.sans-monitor.predict-candidate') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                axial_length_mm: parseFloat(document.getElementById('pred-axial').value || 23.2),
                hdt6_delta_trt_um: parseFloat(document.getElementById('pred-hdt').value || 20),
                mission_duration_days: parseInt(document.getElementById('pred-days').value || 180, 10),
                onsd_mm: parseFloat(document.getElementById('pred-onsd').value || 5.8),
                one_carbon_risk: document.getElementById('pred-oc').value,
                co2_sensitivity: document.getElementById('pred-co2').value,
                baseline_iop_mmhg: {{ (float) ($bp['inputs']['iop_mmhg'] ?? 15.5) }},
                baseline_trt_um: {{ (float) ($bp['inputs']['oct_trt_baseline_um'] ?? 280) }},
            }),
        });
        if (!res.ok) {
            throw new Error(`HTTP ${res.status}`);
        }
        const d = await res.json();
        const isEn = @json($isEn);
        const dc = d.domain_contributions || {};
        const pct = d.susceptibility_probability_pct ?? 0;
        const col = pct >= 65 ? '#dc2626' : (pct >= 35 ? '#d97706' : '#16a34a');
        out.style.display = 'block';
        out.innerHTML = `
            <div style="display:flex;justify-content:space-between;align-items:center;gap:0.5rem;flex-wrap:wrap;margin-bottom:0.35rem;">
                <strong>${isEn ? 'Estimated SANS Susceptibility:' : 'Susceptibilidad Estimada al SANS:'} <span style="color:${col};font-size:0.95rem;font-family:ui-monospace,monospace;">${pct}% (${d.risk_tier})</span></strong>
                <span style="font-family:ui-monospace,monospace;font-size:0.68rem;color:var(--muted-foreground);">${d.engine || ''}</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.4rem;margin-bottom:0.35rem;font-family:ui-monospace,monospace;font-size:0.68rem;">
                <div>Bio: <strong>${dc.biometric_pct ?? 0}%</strong></div>
                <div>Oph: <strong>${dc.ophthalmological_pct ?? 0}%</strong></div>
                <div>Neu: <strong>${dc.neurological_pct ?? 0}%</strong></div>
                <div>Met: <strong>${dc.metabolic_pct ?? 0}%</strong></div>
            </div>
            <div style="color:var(--foreground);font-weight:600;">${(d.recommended_cadence && (isEn ? d.recommended_cadence.en : d.recommended_cadence.es)) || ''}</div>
            <div style="color:var(--muted-foreground);font-size:0.68rem;margin-top:2px;">${(d.safety_notice && (isEn ? d.safety_notice.en : d.safety_notice.es)) || ''}</div>
        `;
    } catch (err) {
        console.error(err);
        out.style.display = 'block';
        out.innerHTML = `<span style="color:#dc2626;font-weight:700;">Error: ${err.message || 'Predictor unavailable'}</span>`;
    } finally {
        if (btn) { btn.disabled = false; btn.innerHTML = orig; }
    }
    return false;
};
</script>
</div>

{{-- =============================================================================================== --}}
{{-- PESTAÑA 2: MEDIR EL TIEMPO: ANTES · DURANTE · DESPUÉS (CORTE DE +30, +60 Y +90 DÍAS)            --}}
{{-- =============================================================================================== --}}
<div id="wizard-panel-seguimiento" data-wizard-panel="seguimiento" style="{{ $normalizedTab === 'seguimiento' ? '' : 'display:none;' }}">
<div class="card" style="padding:1.35rem;margin-bottom:1.25rem;">
    <h2 style="font-size:1.15rem;font-weight:800;margin:0 0 0.35rem;">
        <i class="fa fa-clock-o"></i>
        {{ $isEn
            ? 'Longitudinal Eye Evolution: Before · During · After (+30, +60 & +90 Days)'
            : 'Evolución Longitudinal del Ojo: Antes · Durante · Después (+30, +60 y +90 Días)' }}
    </h2>
    <p style="font-size:0.84rem;color:var(--muted-foreground);margin:0 0 1.1rem;">
        @if($isEn)
            Compare how the eye was <strong>Before</strong> flight/exposure, how it changed <strong>During</strong> (Days 10, 15, 30), and the exact cut <strong>After flight at +30, +60, and +90 days</strong>.
        @else
            Compara cómo estaba el ojo <strong>Antes</strong> del vuelo/exposición, cómo cambió <strong>Durante</strong> (Días 10, 15, 30) y el corte exacto <strong>Después del vuelo a +30, +60 y +90 días</strong>.
        @endif
    </p>

    {{-- 3 Bloques Temporales Claros: ANTES -> DURANTE -> DESPUÉS --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.25rem;">
        <div style="padding:1rem;border:1px solid var(--border);border-radius:6px;">
            <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;color:var(--muted-foreground);">
                {{ $isEn ? '1. BEFORE FLIGHT (Baseline)' : '1. ANTES DEL VUELO (Línea Base)' }}
            </div>
            <div style="font-size:1.4rem;font-weight:800;margin:0.3rem 0;">
                {{ $caseEvaluation['eyes']['OD']['baseline_trt'] !== null ? $caseEvaluation['eyes']['OD']['baseline_trt'].' µm' : ($isEn ? 'No baseline' : 'Sin línea base') }}
            </div>
            <div style="font-size:0.8rem;color:var(--muted-foreground);">
                {{ $isEn ? 'Pre-flight reference (Day 0) to measure any subsequent shift.' : 'Referencia pre-vuelo (Día 0) para medir cualquier desplazamiento posterior.' }}
            </div>
        </div>

        <div style="padding:1rem;border:1px solid var(--border);border-radius:6px;">
            <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;color:var(--muted-foreground);">
                {{ $isEn ? '2. DURING FLIGHT (Days 10 → 15 → 30)' : '2. DURANTE EL VUELO (Días 10 → 15 → 30)' }}
            </div>
            <div style="font-size:1.4rem;font-weight:800;margin:0.3rem 0;">
                @if($caseEvaluation['eyes']['OD']['max_delta_trt'] !== null)
                    {{ $caseEvaluation['eyes']['OD']['latest_trt'] }} µm (&Delta; +{{ $caseEvaluation['eyes']['OD']['max_delta_trt'] }} µm)
                @else
                    {{ $isEn ? 'Unscanned in orbit' : 'Sin escaneo en órbita' }}
                @endif
            </div>
            <div style="font-size:0.8rem;color:var(--muted-foreground);">
                {{ $isEn
                    ? 'In the worsening case: +8 µm (Day 10) → +14 µm (Day 15) → +26 µm (Day 30).'
                    : 'En el caso en progresión: +8 µm (Día 10) → +14 µm (Día 15) → +26 µm (Día 30).' }}
            </div>
        </div>

        <div style="padding:1rem;border:1px solid var(--border);border-radius:6px;">
            <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;color:var(--muted-foreground);">
                {{ $isEn ? '3. PRIOR -6° TILT EXPOSURE ON EARTH' : '3. EXPOSICIÓN PREVIA -6° EN TIERRA' }}
            </div>
            <div style="font-size:1.4rem;font-weight:800;margin:0.3rem 0;">
                280 &rarr; 304 &rarr; 284 &mu;m
            </div>
            <div style="font-size:0.8rem;color:var(--muted-foreground);">
                @if($isEn)
                    Immediate shift of <strong>+24 µm</strong> and recovery to <strong>+4 µm</strong> at +30 days post-tilt.
                @else
                    Cambio inmediato de <strong>+24 &mu;m</strong> y descenso a <strong>+4 &mu;m</strong> a los +30 días de finalizar la inclinación.
                @endif
            </div>
        </div>
    </div>

    {{-- CORTE POSVUELO A +30, +60 Y +90 DÍAS --}}
    <h3 style="font-size:1rem;font-weight:800;margin:0 0 0.75rem;">
        <i class="fa fa-calendar-check-o"></i>
        {{ $isEn ? 'Post-Flight Cut: Controls at +30, +60 & +90 Days' : 'Corte Después del Vuelo: Controles a +30, +60 y +90 Días' }}
    </h3>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
        @foreach([30, 60, 90] as $milestone)
            @php $visit = $followupVisits->firstWhere('target_milestone_day', $milestone); @endphp
            <div style="padding:1.1rem;border:1px solid var(--foreground);border-radius:6px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.4rem;">
                    <span style="font-size:1.05rem;font-weight:800;">
                        {{ $isEn ? "Cut +{$milestone} Days" : "Corte +{$milestone} Días" }}
                    </span>
                    <span class="badge" style="border:1px solid var(--border);font-weight:700;">
                        {{ $visit && $visit->status === 'SIN_MEDICION' ? ($isEn ? 'Unmeasured' : 'Sin medición') : $t($visit->status ?? 'Pendiente') }}
                    </span>
                </div>
                @if($visit)
                    <div style="font-size:0.82rem;margin-bottom:0.25rem;">
                        <strong>{{ $isEn ? 'Window:' : 'Ventana:' }}</strong> {{ $visit->window_label }}
                    </div>
                    <div style="font-size:0.82rem;margin-bottom:0.25rem;">
                        <strong>{{ $isEn ? 'Control date:' : 'Fecha control:' }}</strong>
                        {{ $visit->actual_date ?? ($isEn ? 'Unmeasured (Never interpolated)' : 'Sin medición (No se interpola)') }}
                    </div>
                    <div style="font-size:0.8rem;color:var(--muted-foreground);">
                        {{ $isEn ? 'State:' : 'Estado:' }}
                        <strong>{{ $visit->status === 'SIN_MEDICION' ? ($isEn ? 'Unmeasured' : 'Sin medición') : $t($visit->finding_summary_state) }}</strong>
                    </div>
                @else
                    <div style="font-size:0.82rem;color:var(--muted-foreground);">
                        {{ $isEn ? "Follow-up pending for +{$milestone} days cut." : "Seguimiento pendiente para el corte de +{$milestone} días." }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Gráfica Longitudinal Chart.js --}}
    <div style="margin-top:1.25rem;padding:1rem;border:1px solid var(--border);border-radius:6px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
            <strong style="font-size:0.9rem;">
                <i class="fa fa-area-chart"></i>
                {{ $isEn
                    ? 'OCT Thickness Curve (µm): Before → During (10, 15, 30) → Post-Flight Cut (+30, +60, +90 days)'
                    : 'Curva de Espesor OCT (µm): Antes → Durante (10, 15, 30) → Corte Posvuelo (+30, +60, +90 días)' }}
            </strong>
            <span style="font-size:0.75rem;color:var(--muted-foreground);">
                {{ $isEn ? 'Missing +60d cut is never interpolated' : 'El corte +60 sin dato no se interpola' }}
            </span>
        </div>
        <div style="position:relative;height:230px;width:100%;">
            <canvas id="timelineCutChart"></canvas>
        </div>
    </div>

    {{-- Reproductor interactivo de Hitos (AT-03 y AT-04: Histórico con available_day vs Simulación Sintética) --}}
    <div style="margin-top:1rem;padding:0.9rem 1rem;border:1px solid var(--border);border-radius:6px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;">
        <div>
            <div style="font-size:0.78rem;font-weight:800;">
                <i class="fa fa-step-forward"></i>
                {{ $isEn
                    ? "Interactive Playback (AT-03 / AT-04) — Active Day: Day {$selectedDay} ({$timelineMode})"
                    : "Reproductor de Hitos (AT-03 / AT-04) — Día Activo: Día {$selectedDay} ({$timelineMode})" }}
            </div>
            <div style="font-size:0.76rem;color:var(--muted-foreground);margin-top:0.15rem;">
                {{ $timelineState['status_banner'] ?? '' }}
            </div>
        </div>
        <div style="display:flex;gap:0.35rem;flex-wrap:wrap;align-items:center;">
            @foreach([0, 10, 15, 20, 30, 180] as $dBtn)
                <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'seguimiento', 'day' => $dBtn])) }}"
                   class="btn {{ (int)$selectedDay === $dBtn ? 'btn-primary' : 'btn-outline' }}" style="padding:0.3rem 0.6rem;font-size:0.74rem;">
                    {{ $isEn ? "Day {$dBtn}" : "Día {$dBtn}" }}
                </a>
            @endforeach
            <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'seguimiento', 'mode' => $timelineMode === 'HISTORICO' ? 'SIMULACION' : 'HISTORICO'])) }}"
               class="btn btn-outline" style="padding:0.3rem 0.65rem;font-size:0.74rem;">
                <i class="fa fa-exchange"></i>
                {{ $timelineMode === 'HISTORICO'
                    ? ($isEn ? 'Switch to Synthetic Simulation' : 'Cambiar a Simulación Sintética')
                    : ($isEn ? 'Switch to Historical Record' : 'Cambiar a Registro Histórico') }}
            </a>
        </div>
    </div>
</div>
</div>

{{-- =============================================================================================== --}}
{{-- PESTAÑA 3: LOS 3 ESCENARIOS MODELOS (1. ESTABLE · 2. EMPEORA · 3. NO SE ESCANEA)                --}}
{{-- =============================================================================================== --}}
<div id="wizard-panel-escenarios" data-wizard-panel="escenarios" style="{{ $normalizedTab === 'escenarios' ? '' : 'display:none;' }}">
<div class="card" style="padding:1.35rem;margin-bottom:1.25rem;">
    <h2 style="font-size:1.15rem;font-weight:800;margin:0 0 0.35rem;">
        <i class="fa fa-cubes"></i>
        {{ $isEn
            ? '3 Reference Scenario Models: Stable · Worsens · Unscanned'
            : 'Comparador de los 3 Escenarios Modelos: Estable · Empeora · No se Escanea' }}
    </h2>
    <p style="font-size:0.84rem;color:var(--muted-foreground);margin:0 0 1rem;">
        {{ $isEn
            ? 'Click any of the 3 reference scenarios to immediately load its biomarkers, longitudinal curve, and anatomical schematic.'
            : 'Haz clic en cualquiera de los 3 escenarios para cargar instantáneamente sus biomarcadores, su curva temporal y su representación anatómica.' }}
    </p>

    {{-- TARJETAS DE 1 CLIC PARA LOS 3 ESCENARIOS MODELOS (CON RETRATO IA DEL ASTRONAUTA) --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.25rem;">
        {{-- Escenario 1: Estable --}}
        <a href="{{ route('admin.sans-monitor.index', ['case_id' => 'CASE-STABLE-01', 'tab' => 'escenarios']) }}"
           class="clickable-card {{ $activeCase->case_id === 'CASE-STABLE-01' ? 'is-active' : '' }}"
           style="text-decoration:none;color:inherit;padding:1.15rem;border-radius:8px;display:flex;flex-direction:column;justify-content:space-between;gap:0.7rem;">
            <div>
                <div style="display:flex;align-items:center;gap:0.65rem;margin-bottom:0.5rem;">
                    <img src="{{ asset('images/astronauts/astro-02.jpg') }}?v=color2" alt="Dr. Sara Lindqvist" style="width:44px;height:44px;border-radius:6px;object-fit:cover;border:1px solid var(--border);">
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;justify-content:space-between;align-items:center;gap:0.35rem;flex-wrap:wrap;">
                            <span style="font-size:0.95rem;font-weight:800;">
                                <i class="fa fa-check-circle" style="color:#16a34a;"></i> {{ $isEn ? '1. Stable Scenario' : '1. Escenario Estable' }}
                            </span>
                            <span class="status-badge-ready">{{ $isEn ? 'Ready for review' : 'Listo para revisión' }}</span>
                        </div>
                        <div style="font-size:0.74rem;color:var(--muted-foreground);">Dr. Sara Lindqvist · ASTRO-02</div>
                    </div>
                </div>
                <div style="font-size:0.82rem;color:var(--muted-foreground);">
                    <strong>CASE-STABLE-01:</strong>
                    {{ $isEn
                        ? 'Baseline OCT 278 µm → 284 µm (ΔTRT +6 µm), Frisén 0/5, stable axial length (24.08 mm).'
                        : 'OCT basal 278 µm → 284 µm (ΔTRT +6 µm), Frisén 0/5, longitud axial estable (24.08 mm).' }}
                </div>
            </div>
            <div style="padding-top:0.55rem;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
                <span class="interactive-pill">
                    <i class="fa {{ $activeCase->case_id === 'CASE-STABLE-01' ? 'fa-dot-circle-o' : 'fa-hand-pointer-o' }}"></i>
                    {{ $activeCase->case_id === 'CASE-STABLE-01'
                        ? ($isEn ? 'ACTIVE SCENARIO' : 'ESCENARIO ACTIVO')
                        : ($isEn ? 'Click to load scenario →' : 'Clic para cargar escenario →') }}
                </span>
            </div>
        </a>

        {{-- Escenario 2: Empeora --}}
        <a href="{{ route('admin.sans-monitor.index', ['case_id' => 'CASE-PROG-02', 'tab' => 'escenarios']) }}"
           class="clickable-card {{ $activeCase->case_id === 'CASE-PROG-02' ? 'is-active' : '' }}"
           style="text-decoration:none;color:inherit;padding:1.15rem;border-radius:8px;display:flex;flex-direction:column;justify-content:space-between;gap:0.7rem;">
            <div>
                <div style="display:flex;align-items:center;gap:0.65rem;margin-bottom:0.5rem;">
                    <img src="{{ asset('images/astronauts/astro-01.jpg') }}?v=color2" alt="CDR Alex Mercer" style="width:44px;height:44px;border-radius:6px;object-fit:cover;border:1px solid var(--border);">
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;justify-content:space-between;align-items:center;gap:0.35rem;flex-wrap:wrap;">
                            <span style="font-size:0.95rem;font-weight:800;">
                                <i class="fa fa-exclamation-triangle" style="color:#d97706;"></i> {{ $isEn ? '2. Worsening Scenario' : '2. Escenario que Empeora' }}
                            </span>
                            <span class="status-badge-prioritize">{{ $isEn ? 'Prioritize review' : 'Priorizar revisión' }}</span>
                        </div>
                        <div style="font-size:0.74rem;color:var(--muted-foreground);">CDR Alex Mercer · ASTRO-01</div>
                    </div>
                </div>
                <div style="font-size:0.82rem;color:var(--muted-foreground);">
                    <strong>CASE-PROG-02:</strong>
                    {{ $isEn
                        ? 'Baseline OCT 280 µm → 306 µm (ΔTRT +26 µm at day 30), hyperopia +0.75 D, Frisén edema 2/5.'
                        : 'OCT basal 280 µm → 306 µm (ΔTRT +26 µm al día 30), hipermetropía +0.75 D, edema Frisén 2/5.' }}
                </div>
            </div>
            <div style="padding-top:0.55rem;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
                <span class="interactive-pill">
                    <i class="fa {{ $activeCase->case_id === 'CASE-PROG-02' ? 'fa-dot-circle-o' : 'fa-hand-pointer-o' }}"></i>
                    {{ $activeCase->case_id === 'CASE-PROG-02'
                        ? ($isEn ? 'ACTIVE SCENARIO' : 'ESCENARIO ACTIVO')
                        : ($isEn ? 'Click to load scenario →' : 'Clic para cargar escenario →') }}
                </span>
            </div>
        </a>

        {{-- Escenario 3: No se escanea --}}
        <a href="{{ route('admin.sans-monitor.index', ['case_id' => 'CASE-NOSCAN-03A', 'tab' => 'escenarios']) }}"
           class="clickable-card {{ $activeCase->case_id === 'CASE-NOSCAN-03A' ? 'is-active' : '' }}"
           style="text-decoration:none;color:inherit;padding:1.15rem;border-radius:8px;display:flex;flex-direction:column;justify-content:space-between;gap:0.7rem;">
            <div>
                <div style="display:flex;align-items:center;gap:0.65rem;margin-bottom:0.5rem;">
                    <img src="{{ asset('images/astronauts/astro-03.jpg') }}?v=color2" alt="Kenji Sato" style="width:44px;height:44px;border-radius:6px;object-fit:cover;border:1px solid var(--border);">
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;justify-content:space-between;align-items:center;gap:0.35rem;flex-wrap:wrap;">
                            <span style="font-size:0.95rem;font-weight:800;">
                                <i class="fa fa-question-circle" style="color:#4f46e5;"></i> {{ $isEn ? '3. Unscanned Scenario' : '3. Escenario No se Escanea' }}
                            </span>
                            <span class="status-badge-missing">{{ $isEn ? 'Missing data' : 'Faltan datos' }}</span>
                        </div>
                        <div style="font-size:0.74rem;color:var(--muted-foreground);">Kenji Sato · ASTRO-03</div>
                    </div>
                </div>
                <div style="font-size:0.82rem;color:var(--muted-foreground);">
                    <strong>CASE-NOSCAN-03A:</strong>
                    {{ $isEn
                        ? 'No OCT scan recorded (has IOP of 16 mmHg, but is never assumed stable without OCT).'
                        : 'Sin escaneo OCT registrado (cuenta con PIO de 16 mmHg, pero nunca se asume como estable por falta de OCT).' }}
                </div>
            </div>
            <div style="padding-top:0.55rem;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
                <span class="interactive-pill">
                    <i class="fa {{ $activeCase->case_id === 'CASE-NOSCAN-03A' ? 'fa-dot-circle-o' : 'fa-hand-pointer-o' }}"></i>
                    {{ $activeCase->case_id === 'CASE-NOSCAN-03A'
                        ? ($isEn ? 'ACTIVE SCENARIO' : 'ESCENARIO ACTIVO')
                        : ($isEn ? 'Click to load scenario →' : 'Clic para cargar escenario →') }}
                </span>
            </div>
        </a>
    </div>

    {{-- Vistas Clínicas de Imagen Médica del Escenario Activo (MRI Sagital · Fundus Real · SD-OCT B-Scan) --}}
    @php
        $isWorsening = $activeCase->scenario_archetype === 'EMPEORA';
        $isUnscanned = $bp['inputs']['oct_trt_current_um'] === null;
        $frisenVal = $bp['inputs']['papilledema_frisen'] ?? null;
        $frisenHigh = ($frisenVal !== null && $frisenVal >= 2) || $isWorsening;
        $deltaTrt = $bp['delta_trt_um'] ?? 0;
        $fundusImg = $isUnscanned
            ? asset('images/sans/fundus/fundus-os-baseline.jpg')
            : ($isWorsening ? asset('images/sans/fundus/fundus-edema.png') : asset('images/sans/fundus/fundus-normal.jpg'));
    @endphp
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1rem;">
        {{-- 1. Axial Length & Posterior Pole (Sagittal Ocular MRI / B-Scan) --}}
        <div style="border:1px solid var(--border);border-radius:10px;padding:0.9rem;background:var(--card);">
            <h3 style="font-size:0.86rem;font-weight:800;margin:0 0 0.55rem;display:flex;justify-content:space-between;align-items:center;gap:0.5rem;">
                <span><i class="fa fa-circle-o"></i> {{ $isEn ? 'Axial Length & Posterior Pole (Sagittal)' : 'Longitud Axial y Polo Posterior (Sagital)' }}</span>
                <span style="font-family:ui-monospace,monospace;font-size:0.65rem;padding:2px 6px;border-radius:4px;background:var(--muted);border:1px solid var(--border);color:var(--foreground);">{{ $isWorsening ? 'FLATTENING' : ($isUnscanned ? 'PENDING' : 'NOMINAL') }}</span>
            </h3>
            <div style="position:relative;height:184px;border-radius:8px;overflow:hidden;background:#030712;border:1px solid #1e293b;">
                <canvas id="scenarioSagittalCanvas" width="440" height="220" style="width:100%;height:100%;display:block;"></canvas>
                <div style="position:absolute;top:8px;left:10px;font-family:ui-monospace,monospace;font-size:0.66rem;font-weight:700;color:#e2e8f0;background:rgba(2,6,23,0.72);padding:2px 7px;border-radius:4px;border:1px solid rgba(148,163,184,0.22);">
                    {{ $isEn ? 'AXIAL LENGTH' : 'LONGITUD AXIAL' }} · {{ $activeCase->case_id }}
                </div>
                <div style="position:absolute;bottom:8px;left:10px;right:10px;display:flex;justify-content:space-between;font-family:ui-monospace,monospace;font-size:0.66rem;color:#cbd5e1;background:rgba(2,6,23,0.72);padding:3px 8px;border-radius:4px;">
                    <span>{{ $isEn ? 'Simulated hyperopia:' : 'Hipermetropía simulada:' }} <strong style="color:{{ $isWorsening ? '#fbbf24' : '#4ade80' }};">+{{ $bp['inputs']['hyperopia_sim_d'] }} D</strong></span>
                    <span>AL {{ $bp['inputs']['axial_length_mm'] ?? '23.8' }} mm</span>
                </div>
            </div>
        </div>

        {{-- 2. Papilledema & Optic Disc (Real Clinical Fundus Photo + Reticle HUD) --}}
        <div style="border:1px solid var(--border);border-radius:10px;padding:0.9rem;background:var(--card);">
            <h3 style="font-size:0.86rem;font-weight:800;margin:0 0 0.55rem;display:flex;justify-content:space-between;align-items:center;gap:0.5rem;">
                <span><i class="fa fa-dot-circle-o"></i> {{ $isEn ? 'Papilledema & Optic Disc (Fundus)' : 'Edema Papilar y Disco Óptico (Fondo)' }}</span>
                <span style="font-family:ui-monospace,monospace;font-size:0.65rem;padding:2px 6px;border-radius:4px;background:var(--muted);border:1px solid var(--border);color:var(--foreground);">FRISÉN {{ $frisenVal ?? 'N/E' }}</span>
            </h3>
            <div style="position:relative;height:184px;border-radius:8px;overflow:hidden;background:#050507;border:1px solid #1e293b;display:flex;align-items:center;justify-content:center;">
                <canvas id="scenarioFundusCanvas" width="440" height="220" style="width:100%;height:100%;display:block;"></canvas>
                <div style="position:absolute;top:8px;left:10px;font-family:ui-monospace,monospace;font-size:0.66rem;font-weight:700;color:#f8fafc;background:rgba(2,6,23,0.75);padding:2px 7px;border-radius:4px;border:1px solid rgba(148,163,184,0.22);">
                    {{ $isEn ? 'PAPILLEDEMA' : 'EDEMA PAPILAR' }} (FRISÉN {{ $frisenVal ?? 'N/E' }})
                </div>
                <div style="position:absolute;bottom:8px;left:10px;right:10px;display:flex;justify-content:space-between;font-family:ui-monospace,monospace;font-size:0.66rem;color:#e2e8f0;background:rgba(2,6,23,0.75);padding:3px 8px;border-radius:4px;">
                    <span>{{ $isEn ? 'IOP:' : 'PIO:' }} <strong>{{ $bp['inputs']['iop_mmhg'] }} mmHg</strong></span>
                    <span style="color:{{ $frisenHigh ? '#fbbf24' : '#4ade80' }};font-weight:700;">{{ $frisenHigh ? 'HALO + FOLDS' : ($isUnscanned ? 'BASELINE ONLY' : 'SHARP RIM') }}</span>
                </div>
            </div>
        </div>

        {{-- 3. Mean OCT Thickness (Procedural SD-OCT B-Scan) --}}
        <div style="border:1px solid var(--border);border-radius:10px;padding:0.9rem;background:var(--card);">
            <h3 style="font-size:0.86rem;font-weight:800;margin:0 0 0.55rem;display:flex;justify-content:space-between;align-items:center;gap:0.5rem;">
                <span><i class="fa fa-align-justify"></i> {{ $isEn ? 'Mean OCT Thickness (Retinal Layers)' : 'Espesor Medio por OCT (Capas)' }}</span>
                <span style="font-family:ui-monospace,monospace;font-size:0.65rem;padding:2px 6px;border-radius:4px;background:var(--muted);border:1px solid var(--border);color:var(--foreground);">SD-OCT</span>
            </h3>
            @if($isUnscanned)
                <div style="height:184px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:0.45rem;background:linear-gradient(180deg,#050a14 0%,#09090b 100%);color:#e2e8f0;padding:1rem;text-align:center;font-size:0.8rem;border-radius:8px;border:1px dashed #475569;">
                    <span style="font-family:ui-monospace,monospace;font-size:0.7rem;color:#94a3b8;letter-spacing:0.06em;">SD-OCT B-SCAN · UNMEASURED</span>
                    <span>{{ $isEn
                        ? 'Scenario "Unscanned": OCT not evaluable (Never drawn as normal nor classified as stable).'
                        : 'Escenario «No se escanea»: OCT no evaluable (No se dibuja como normal ni se clasifica como estable).' }}</span>
                </div>
            @else
                <div style="position:relative;height:184px;border-radius:8px;overflow:hidden;background:#030712;border:1px solid #1e293b;">
                    <canvas id="scenarioOctCanvas" width="440" height="220" style="width:100%;height:100%;display:block;"></canvas>
                    <div style="position:absolute;top:8px;left:10px;font-family:ui-monospace,monospace;font-size:0.66rem;font-weight:700;color:#f8fafc;background:rgba(2,6,23,0.75);padding:2px 7px;border-radius:4px;border:1px solid rgba(148,163,184,0.22);">
                        {{ $isEn ? 'OCT THICKNESS:' : 'ESPESOR OCT:' }} {{ $bp['inputs']['oct_trt_current_um'] }} µm
                    </div>
                    <div style="position:absolute;bottom:8px;left:10px;right:10px;display:flex;justify-content:space-between;font-family:ui-monospace,monospace;font-size:0.66rem;color:#cbd5e1;background:rgba(2,6,23,0.75);padding:3px 8px;border-radius:4px;">
                        <span>{{ $isEn ? 'Change vs baseline:' : 'Cambio vs basal:' }} <strong style="color:{{ $isWorsening ? '#EAFE07' : '#4ade80' }};">{{ $bp['delta_trt_um'] !== null ? '+'.$bp['delta_trt_um'].' µm' : 'N/A' }}</strong></span>
                        <span>ILM · RNFL · RPE</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
    (function () {
        const isWorsening = @json($isWorsening);
        const isUnscanned = @json($isUnscanned);
        const frisenHigh = @json($frisenHigh);
        const deltaTrt = @json((float) ($deltaTrt ?? 0));

        // 0. Procedural Ophthalmoscope Retinal Fundus & Optic Disc Renderer (Zero static images)
        const funCanvas = document.getElementById('scenarioFundusCanvas');
        if (funCanvas) {
            const ctx = funCanvas.getContext('2d');
            const w = funCanvas.width, h = funCanvas.height;
            ctx.fillStyle = '#030407';
            ctx.fillRect(0, 0, w, h);

            const cx = w * 0.5, cy = h * 0.5, radius = 98;
            ctx.save();
            ctx.beginPath();
            ctx.arc(cx, cy, radius, 0, Math.PI * 2);
            ctx.clip();

            // Choroidal / retinal pigment background
            const bg = ctx.createRadialGradient(cx + 26, cy, 8, cx, cy, radius);
            if (isUnscanned) {
                bg.addColorStop(0, '#334155');
                bg.addColorStop(0.65, '#1e293b');
                bg.addColorStop(1, '#09090b');
            } else {
                bg.addColorStop(0, '#d97706');
                bg.addColorStop(0.32, '#9a3412');
                bg.addColorStop(0.78, '#451208');
                bg.addColorStop(1, '#120503');
            }
            ctx.fillStyle = bg;
            ctx.fillRect(cx - radius, cy - radius, radius * 2, radius * 2);

            // Subtle RPE micro-texture
            const img = ctx.getImageData(cx - radius, cy - radius, radius * 2, radius * 2);
            const p = img.data;
            for (let i = 0; i < p.length; i += 4) {
                const j = ((Math.random() - 0.5) * 18) | 0;
                p[i] = Math.max(0, Math.min(255, p[i] + j));
                p[i + 1] = Math.max(0, Math.min(255, p[i + 1] + (j * 0.7) | 0));
            }
            ctx.putImageData(img, cx - radius, cy - radius);

            // Macular fovea (temporal left of disc)
            const mac = ctx.createRadialGradient(cx - 32, cy + 4, 2, cx - 32, cy + 4, 22);
            mac.addColorStop(0, 'rgba(15, 5, 2, 0.72)');
            mac.addColorStop(1, 'rgba(15, 5, 2, 0)');
            ctx.fillStyle = mac;
            ctx.beginPath();
            ctx.arc(cx - 32, cy + 4, 22, 0, Math.PI * 2);
            ctx.fill();

            // Optic Nerve Head (nasal right) + Peripapillary Edema Halo
            const discX = cx + 30, discY = cy;
            if (frisenHigh && !isUnscanned) {
                const halo = ctx.createRadialGradient(discX, discY, 10, discX, discY, 36);
                halo.addColorStop(0, 'rgba(254, 240, 138, 0.85)');
                halo.addColorStop(0.45, 'rgba(251, 191, 36, 0.42)');
                halo.addColorStop(1, 'rgba(239, 68, 68, 0)');
                ctx.fillStyle = halo;
                ctx.beginPath();
                ctx.arc(discX, discY, 36, 0, Math.PI * 2);
                ctx.fill();

                // Choroidal folds between disc and macula
                ctx.strokeStyle = 'rgba(254, 240, 138, 0.24)';
                ctx.lineWidth = 1.2;
                for (let k = -3; k <= 3; k++) {
                    ctx.beginPath();
                    ctx.moveTo(discX - 55, discY + k * 7);
                    ctx.quadraticCurveTo(discX - 32, discY + k * 6 - 3, discX - 14, discY + k * 5);
                    ctx.stroke();
                }
            }

            // Optic disc & physiological cup
            const discGrad = ctx.createRadialGradient(discX, discY, 3, discX, discY, frisenHigh ? 20 : 15);
            discGrad.addColorStop(0, isUnscanned ? '#94a3b8' : '#fef9c3');
            discGrad.addColorStop(0.65, isUnscanned ? '#64748b' : '#fde047');
            discGrad.addColorStop(1, isUnscanned ? 'rgba(100,116,139,0)' : 'rgba(234,88,12,0.15)');
            ctx.fillStyle = discGrad;
            ctx.beginPath();
            ctx.arc(discX, discY, frisenHigh ? 20 : 15, 0, Math.PI * 2);
            ctx.fill();

            // Branching retinal vascular arcades
            const drawVessel = (x0, y0, cx1, cy1, x1, y1, width, color) => {
                ctx.strokeStyle = color;
                ctx.lineWidth = width;
                ctx.lineCap = 'round';
                ctx.beginPath();
                ctx.moveTo(x0, y0);
                ctx.quadraticCurveTo(cx1, cy1, x1, y1);
                ctx.stroke();
            };
            const vCol = isUnscanned ? 'rgba(148,163,184,0.45)' : 'rgba(127, 29, 29, 0.88)';
            const aCol = isUnscanned ? 'rgba(203,213,225,0.4)' : 'rgba(185, 28, 28, 0.78)';
            const vw = frisenHigh ? 2.6 : 1.9;
            drawVessel(discX, discY - 3, discX - 18, discY - 46, discX - 88, discY - 36, vw, vCol);
            drawVessel(discX, discY + 3, discX - 18, discY + 46, discX - 88, discY + 38, vw, vCol);
            drawVessel(discX + 2, discY - 4, discX + 22, discY - 38, discX + 54, discY - 64, 1.5, aCol);
            drawVessel(discX + 2, discY + 4, discX + 22, discY + 38, discX + 54, discY + 64, 1.5, aCol);
            drawVessel(discX - 24, discY - 32, discX - 48, discY - 22, discX - 66, discY - 12, 1.1, aCol);
            drawVessel(discX - 24, discY + 32, discX - 48, discY + 22, discX - 66, discY + 14, 1.1, aCol);

            ctx.restore();
        }

        // 1. Sagittal Ocular MRI / B-Scan Renderer
        const sagCanvas = document.getElementById('scenarioSagittalCanvas');
        if (sagCanvas) {
            const ctx = sagCanvas.getContext('2d');
            const w = sagCanvas.width, h = sagCanvas.height;
            ctx.fillStyle = '#030712';
            ctx.fillRect(0, 0, w, h);

            // Subtle MRI noise grain
            const imgData = ctx.getImageData(0, 0, w, h);
            const d = imgData.data;
            for (let i = 0; i < d.length; i += 4) {
                const n = (Math.random() * 14) | 0;
                d[i] += n; d[i+1] += n + 2; d[i+2] += n + 6;
            }
            ctx.putImageData(imgData, 0, 0);

            // Orbital fat & extraocular muscles (posterior)
            const orbGrad = ctx.createRadialGradient(210, 110, 40, 210, 110, 175);
            orbGrad.addColorStop(0, 'rgba(30, 58, 138, 0.28)');
            orbGrad.addColorStop(1, 'rgba(2, 6, 23, 0)');
            ctx.fillStyle = orbGrad;
            ctx.fillRect(0, 0, w, h);

            // Optic nerve & CSF sheath
            const sheathH = isWorsening ? 26 : 16;
            const nerveGrad = ctx.createLinearGradient(255, 110 - sheathH, 255, 110 + sheathH);
            nerveGrad.addColorStop(0, isWorsening ? 'rgba(56,189,248,0.55)' : 'rgba(148,163,184,0.28)');
            nerveGrad.addColorStop(0.3, 'rgba(226,232,240,0.65)');
            nerveGrad.addColorStop(0.5, 'rgba(100,116,139,0.75)');
            nerveGrad.addColorStop(0.7, 'rgba(226,232,240,0.65)');
            nerveGrad.addColorStop(1, isWorsening ? 'rgba(56,189,248,0.55)' : 'rgba(148,163,184,0.28)');
            ctx.fillStyle = nerveGrad;
            ctx.beginPath();
            ctx.moveTo(255, 110 - sheathH);
            ctx.lineTo(395, 110 - sheathH - 4);
            ctx.lineTo(395, 110 + sheathH + 4);
            ctx.lineTo(255, 110 + sheathH);
            ctx.closePath();
            ctx.fill();

            // Globe vitreous cavity with realistic T2-MRI radial glow
            const rx = isWorsening ? 68 : 78;
            const ry = 72;
            const cx = 195, cy = 110;
            const vitGrad = ctx.createRadialGradient(cx - 14, cy - 12, 8, cx, cy, rx + 8);
            vitGrad.addColorStop(0, 'rgba(56, 189, 248, 0.34)');
            vitGrad.addColorStop(0.75, 'rgba(14, 165, 233, 0.16)');
            vitGrad.addColorStop(0.94, 'rgba(226, 232, 240, 0.85)');
            vitGrad.addColorStop(1, 'rgba(148, 163, 184, 0.15)');

            ctx.fillStyle = vitGrad;
            ctx.beginPath();
            ctx.ellipse(cx, cy, rx, ry, 0, 0, Math.PI * 2);
            ctx.fill();

            // Anterior cornea + crystalline lens
            ctx.fillStyle = 'rgba(186, 230, 253, 0.25)';
            ctx.strokeStyle = 'rgba(224, 242, 254, 0.85)';
            ctx.lineWidth = 2.2;
            ctx.beginPath();
            ctx.moveTo(cx - rx + 8, cy - 34);
            ctx.quadraticCurveTo(cx - rx - 22, cy, cx - rx + 8, cy + 34);
            ctx.stroke();
            ctx.fill();

            ctx.fillStyle = 'rgba(241, 245, 249, 0.32)';
            ctx.beginPath();
            ctx.ellipse(cx - rx + 22, cy, 10, 26, 0, 0, Math.PI * 2);
            ctx.fill();

            // Posterior flattening marker when worsening
            if (isWorsening) {
                ctx.strokeStyle = 'rgba(251, 191, 36, 0.9)';
                ctx.lineWidth = 1.8;
                ctx.setLineDash([4, 3]);
                ctx.beginPath();
                ctx.ellipse(cx, cy, 78, ry, 0, -0.7, 0.7);
                ctx.stroke();
                ctx.setLineDash([]);
            }
        }

        // 2. Procedural Spectral-Domain OCT B-Scan Renderer
        const octCanvas = document.getElementById('scenarioOctCanvas');
        if (octCanvas) {
            const ctx = octCanvas.getContext('2d');
            const w = octCanvas.width, h = octCanvas.height;
            ctx.fillStyle = '#04070d';
            ctx.fillRect(0, 0, w, h);

            // Authentic SD-OCT speckle noise across B-Scan
            const img = ctx.createImageData(w, h);
            const data = img.data;
            const elevation = Math.min(34, Math.max(4, deltaTrt * 0.95));

            for (let x = 0; x < w; x++) {
                const nx = (x - w * 0.5) / (w * 0.32);
                const gaussian = Math.exp(-nx * nx);
                // Normal has a subtle foveal dip; elevated SANS has thickening dome
                const ilmY = isWorsening
                    ? 98 - elevation * gaussian
                    : 88 + 14 * Math.exp(-Math.pow((x - w * 0.5) / 26, 2)) - elevation * 0.3 * gaussian;
                const rnflBottom = ilmY + (isWorsening ? 16 + elevation * 0.28 * gaussian : 10);
                const oplY = rnflBottom + 24;
                const ezY = 148;
                const rpeY = 158;

                for (let y = 0; y < h; y++) {
                    const idx = (y * w + x) * 4;
                    let intensity = 8 + Math.random() * 10;

                    if (y >= ilmY && y <= rpeY + 26) {
                        const speckle = 0.55 + Math.random() * 0.9;
                        if (Math.abs(y - ilmY) < 2.4) {
                            intensity = 225 * speckle; // ILM hyper-reflective
                        } else if (y > ilmY && y < rnflBottom) {
                            intensity = (isWorsening ? 175 : 135) * speckle; // RNFL
                        } else if (y >= rnflBottom && y < oplY) {
                            intensity = 68 * speckle; // GCL/IPL
                        } else if (Math.abs(y - oplY) < 4) {
                            intensity = 115 * speckle; // OPL
                        } else if (y > oplY && y < ezY) {
                            intensity = 38 * speckle; // ONL hyporeflective
                        } else if (Math.abs(y - ezY) < 2.2) {
                            intensity = 175 * speckle; // Ellipsoid Zone
                        } else if (Math.abs(y - rpeY) < 3.5) {
                            intensity = 240 * speckle; // RPE / Bruch
                        } else if (y > rpeY) {
                            // Choroid vessels
                            const vessel = Math.sin(x * 0.14 + y * 0.25) > 0.45 ? 0.35 : 0.85;
                            intensity = 82 * speckle * vessel * Math.max(0, 1 - (y - rpeY) / 34);
                        }
                    }

                    const v = Math.min(255, Math.max(0, intensity | 0));
                    // Subtle warm-cyan clinical OCT tint
                    data[idx] = v;
                    data[idx + 1] = Math.min(255, (v * 0.96) | 0);
                    data[idx + 2] = Math.min(255, (v * 0.88) | 0);
                    data[idx + 3] = 255;
                }
            }
            ctx.putImageData(img, 0, 0);

            // Overlay clean ILM/RPE reference lines
            ctx.strokeStyle = isWorsening ? 'rgba(234, 254, 7, 0.85)' : 'rgba(34, 211, 238, 0.8)';
            ctx.lineWidth = 1.4;
            ctx.beginPath();
            for (let x = 20; x < w - 20; x += 4) {
                const nx = (x - w * 0.5) / (w * 0.32);
                const gaussian = Math.exp(-nx * nx);
                const y = isWorsening
                    ? 98 - elevation * gaussian
                    : 88 + 14 * Math.exp(-Math.pow((x - w * 0.5) / 26, 2)) - elevation * 0.3 * gaussian;
                if (x === 20) ctx.moveTo(x, y); else ctx.lineTo(x, y);
            }
            ctx.stroke();
        }
    })();
    </script>
</div>
</div>

{{-- CATÁLOGO DE PROTOCOLOS CIENTÍFICOS NASA Y 7 REFERENCIAS CANÓNICAS (RF-05 A RF-08, AT-02) --}}
<details open id="nasa-bibliography" class="card collapsible-card" style="padding:1.25rem 1.35rem;margin-bottom:1.25rem;border:1px solid var(--border);border-radius:10px;background:var(--card);color:var(--foreground);scroll-margin-top:78px;">
    <summary style="cursor:pointer;font-weight:800;font-size:0.98rem;display:flex;justify-content:space-between;align-items:center;gap:0.75rem;flex-wrap:wrap;">
        <span style="display:inline-flex;align-items:center;gap:9px;color:var(--foreground);">
            <span style="display:inline-grid;place-items:center;width:30px;height:30px;border-radius:7px;background:var(--primary);color:var(--primary-foreground);font-size:13px;"><i class="fa fa-book"></i></span>
            <span>
                <small style="display:block;font-family:ui-monospace,monospace;font-size:0.66rem;font-weight:800;letter-spacing:0.08em;color:var(--muted-foreground);text-transform:uppercase;">
                    {{ $isEn ? 'NASA OCHMO · HRP · JAMA · SCIENTIFIC BIBLIOGRAPHY' : 'NASA OCHMO · HRP · JAMA · BIBLIOGRAFÍA CIENTÍFICA' }}
                </small>
                {{ $isEn
                    ? 'NASA Clinical Protocols, Previously Applied Version & 7 Scientific References (AT-02)'
                    : 'Protocolos Clínicos NASA, Versión Previa Aplicada y 7 Referencias Científicas (AT-02)' }}
            </span>
        </span>
        <span class="interactive-pill" style="font-family:ui-monospace,monospace;font-size:0.72rem;">{{ count($catalogSearch['followup_matches']) }} {{ $isEn ? 'Verified Sources' : 'Fuentes Verificadas' }}</span>
    </summary>

    <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border);font-size:0.8rem;">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:0.75rem;margin-bottom:1rem;">
            <div style="padding:0.75rem 0.9rem;border:1px solid var(--border);border-radius:8px;background:var(--muted);">
                <strong style="display:block;font-family:ui-monospace,monospace;font-size:0.72rem;color:var(--muted-foreground);margin-bottom:0.35rem;text-transform:uppercase;">
                    {{ $isEn ? 'Previously Applied Protocol on Subject:' : 'Protocolo Aplicado Previamente en la Ficha:' }}
                </strong>
                @if($catalogSearch['previously_applied'])
                    <div style="color:var(--foreground);font-weight:600;">
                        <code style="background:var(--primary);color:var(--primary-foreground);padding:2px 6px;border-radius:4px;font-size:0.72rem;">{{ $catalogSearch['previously_applied']->version_id }}</code>
                        <span style="margin-left:4px;">{{ $catalogSearch['previously_applied']->title }} ({{ $catalogSearch['previously_applied']->issuer ?? 'NASA' }})</span>
                    </div>
                @else
                    <div style="color:var(--muted-foreground);">{{ $isEn ? 'Unknown / Not recorded (Does not block evaluation)' : 'Desconocido / No registrado (No bloquea evaluación)' }}</div>
                @endif
            </div>

            @if(!empty($catalogSearch['newer_verified_versions']))
                <div style="padding:0.75rem 0.9rem;border:1px solid var(--border);border-radius:8px;background:var(--muted);">
                    <strong style="display:block;font-family:ui-monospace,monospace;font-size:0.72rem;color:var(--foreground);margin-bottom:0.35rem;text-transform:uppercase;">
                        {{ $isEn ? 'Newer Verified Version Available (Never auto-replaces selection):' : 'Versión Posterior Verificada Disponible (Nunca reemplaza sola la selección):' }}
                    </strong>
                    @foreach($catalogSearch['newer_verified_versions'] as $nv)
                        <div style="color:var(--foreground);font-weight:600;margin-bottom:2px;">
                            <code style="background:var(--card);border:1px solid var(--border);color:var(--foreground);padding:2px 6px;border-radius:4px;font-size:0.72rem;">{{ $nv->version_id }}</code>
                            <span style="margin-left:4px;">{{ $nv->title }} ({{ $nv->publication_date ?? '2024' }})</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div style="font-family:ui-monospace,monospace;font-size:0.72rem;font-weight:800;color:var(--muted-foreground);text-transform:uppercase;margin-bottom:0.55rem;">
            {{ $isEn ? 'Curated Catalog (Protocols vs Technical Standards vs Research Articles):' : 'Catálogo Curado (Protocolos vs Estándares Técnicos vs Artículos de Investigación):' }}
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:0.6rem;">
            @foreach($catalogSearch['followup_matches'] as $match)
                <article style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.75rem;padding:0.7rem 0.85rem;border:1px solid var(--border);border-radius:8px;background:var(--muted);">
                    <div style="min-width:0;">
                        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:4px;">
                            <span style="font-family:ui-monospace,monospace;font-size:0.65rem;font-weight:800;padding:2px 6px;border-radius:4px;background:var(--accent);color:var(--foreground);border:1px solid var(--border);">[{{ $match['document_type_badge'] }}]</span>
                            <code style="font-family:ui-monospace,monospace;font-size:0.72rem;font-weight:700;color:var(--foreground);">{{ $match['protocol']->version_id }}</code>
                        </div>
                        <div style="color:var(--foreground);font-weight:600;line-height:1.35;">{{ $match['protocol']->title }}</div>
                    </div>
                    @if($match['protocol']->source_url)
                        <a href="{{ $match['protocol']->source_url }}" target="_blank" rel="noopener"
                           style="flex-shrink:0;display:inline-flex;align-items:center;gap:5px;padding:5px 9px;border-radius:6px;background:var(--card);border:1px solid var(--border);color:var(--foreground);font-family:ui-monospace,monospace;font-size:0.7rem;font-weight:700;text-decoration:none;">
                            <i class="fa fa-external-link"></i> NASA/Source
                        </a>
                    @endif
                </article>
            @endforeach
        </div>

        {{-- 4. NASA Open Science Live APIs (OSDR + NTRS) --}}
        <div style="margin-top:1.15rem;padding-top:1rem;border-top:1px solid var(--border);">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:0.75rem;flex-wrap:wrap;margin-bottom:0.65rem;">
                <div style="font-family:ui-monospace,monospace;font-size:0.72rem;font-weight:800;color:var(--muted-foreground);text-transform:uppercase;">
                    <i class="fa fa-database"></i> {{ $isEn ? 'NASA Open Science APIs (Live OSDR Datasets & NTRS Technical Reports):' : 'APIs Abiertas de NASA (Datasets OSDR y Reportes Técnicos NTRS en vivo):' }}
                </div>
                <form id="nasa-live-search-form" style="display:flex;gap:0.45rem;align-items:center;flex-wrap:wrap;" onsubmit="return window.runNasaLiveSearch(event)">
                    <input type="text" id="nasa-live-query" value="SANS ocular microgravity" placeholder="{{ $isEn ? 'Search NASA OSDR & NTRS...' : 'Buscar en NASA OSDR y NTRS...' }}"
                           style="padding:0.35rem 0.65rem;border-radius:6px;border:1px solid var(--border);background:var(--card);color:var(--foreground);font-size:0.76rem;min-width:220px;">
                    <button type="submit" id="nasa-live-search-btn"
                            style="background:#EAFE07;color:#09090b;border:1px solid #09090b;padding:0.35rem 0.75rem;border-radius:6px;font-size:0.74rem;font-weight:800;cursor:pointer;">
                        <i class="fa fa-search"></i> {{ $isEn ? 'Query NASA API' : 'Consultar NASA' }}
                    </button>
                </form>
            </div>
            <div id="nasa-live-results" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:0.6rem;">
                @foreach(($nasaSnapshot['osdr'] ?? []) as $osdrItem)
                    <article style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.75rem;padding:0.7rem 0.85rem;border:1px solid var(--border);border-radius:8px;background:var(--muted);">
                        <div style="min-width:0;">
                            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:4px;">
                                <span style="font-family:ui-monospace,monospace;font-size:0.65rem;font-weight:800;padding:2px 6px;border-radius:4px;background:#07173F;color:#ffffff;">NASA OSDR</span>
                                <code style="font-family:ui-monospace,monospace;font-size:0.72rem;font-weight:700;color:var(--foreground);">{{ $osdrItem['accession'] ?? 'OSD' }}</code>
                            </div>
                            <div style="color:var(--foreground);font-weight:600;line-height:1.35;">{{ $osdrItem['title'] ?? 'NASA OSDR Study' }}</div>
                            <div style="font-size:0.72rem;color:var(--muted-foreground);margin-top:2px;">{{ $osdrItem['organism'] ?? '' }} · {{ $osdrItem['assay'] ?? '' }}</div>
                        </div>
                        @if(!empty($osdrItem['dataset_url']))
                            <a href="{{ $osdrItem['dataset_url'] }}" target="_blank" rel="noopener"
                               style="flex-shrink:0;display:inline-flex;align-items:center;gap:5px;padding:5px 9px;border-radius:6px;background:var(--card);border:1px solid var(--border);color:var(--foreground);font-family:ui-monospace,monospace;font-size:0.7rem;font-weight:700;text-decoration:none;">
                                <i class="fa fa-external-link"></i> OSDR
                            </a>
                        @endif
                    </article>
                @endforeach
                @foreach(($nasaSnapshot['ntrs'] ?? []) as $ntrsItem)
                    <article style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.75rem;padding:0.7rem 0.85rem;border:1px solid var(--border);border-radius:8px;background:var(--muted);">
                        <div style="min-width:0;">
                            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:4px;">
                                <span style="font-family:ui-monospace,monospace;font-size:0.65rem;font-weight:800;padding:2px 6px;border-radius:4px;background:#0042A6;color:#ffffff;">NASA NTRS</span>
                                <code style="font-family:ui-monospace,monospace;font-size:0.72rem;font-weight:700;color:var(--foreground);">{{ $ntrsItem['year'] ?? 'NASA' }}</code>
                            </div>
                            <div style="color:var(--foreground);font-weight:600;line-height:1.35;">{{ $ntrsItem['title'] ?? 'NASA Technical Paper' }}</div>
                            <div style="font-size:0.72rem;color:var(--muted-foreground);margin-top:2px;">{{ $ntrsItem['authors'] ?? 'NASA HRP' }}</div>
                        </div>
                        @if(!empty($ntrsItem['url']))
                            <a href="{{ $ntrsItem['url'] }}" target="_blank" rel="noopener"
                               style="flex-shrink:0;display:inline-flex;align-items:center;gap:5px;padding:5px 9px;border-radius:6px;background:var(--card);border:1px solid var(--border);color:var(--foreground);font-family:ui-monospace,monospace;font-size:0.7rem;font-weight:700;text-decoration:none;">
                                <i class="fa fa-external-link"></i> NTRS
                            </a>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</details>

<script>
window.runNasaLiveSearch = async function (e) {
    if (e) e.preventDefault();
    const input = document.getElementById('nasa-live-query');
    const btn = document.getElementById('nasa-live-search-btn');
    const box = document.getElementById('nasa-live-results');
    if (!input || !box) return false;
    const q = encodeURIComponent(input.value.trim() || 'SANS ocular microgravity');
    const orig = btn ? btn.innerHTML : '';
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ...'; }
    try {
        const res = await fetch(`{{ route('public.sans-monitor.nasa-search') }}?q=${q}`);
        const data = await res.json();
        const esc = (s) => String(s || '').replace(/[&<>"']/g, (m) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
        let html = '';
        (data.osdr || []).forEach((o) => {
            html += `<article style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.75rem;padding:0.7rem 0.85rem;border:1px solid var(--border);border-radius:8px;background:var(--muted);">
                <div style="min-width:0;">
                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:4px;">
                        <span style="font-family:ui-monospace,monospace;font-size:0.65rem;font-weight:800;padding:2px 6px;border-radius:4px;background:#07173F;color:#ffffff;">NASA OSDR</span>
                        <code style="font-family:ui-monospace,monospace;font-size:0.72rem;font-weight:700;color:var(--foreground);">${esc(o.accession)}</code>
                    </div>
                    <div style="color:var(--foreground);font-weight:600;line-height:1.35;">${esc(o.title)}</div>
                    <div style="font-size:0.72rem;color:var(--muted-foreground);margin-top:2px;">${esc(o.organism)} · ${esc(o.assay)}</div>
                </div>
                ${o.dataset_url ? `<a href="${esc(o.dataset_url)}" target="_blank" rel="noopener" style="flex-shrink:0;display:inline-flex;align-items:center;gap:5px;padding:5px 9px;border-radius:6px;background:var(--card);border:1px solid var(--border);color:var(--foreground);font-family:ui-monospace,monospace;font-size:0.7rem;font-weight:700;text-decoration:none;"><i class="fa fa-external-link"></i> OSDR</a>` : ''}
            </article>`;
        });
        (data.ntrs || []).forEach((n) => {
            html += `<article style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.75rem;padding:0.7rem 0.85rem;border:1px solid var(--border);border-radius:8px;background:var(--muted);">
                <div style="min-width:0;">
                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:4px;">
                        <span style="font-family:ui-monospace,monospace;font-size:0.65rem;font-weight:800;padding:2px 6px;border-radius:4px;background:#0042A6;color:#ffffff;">NASA NTRS</span>
                        <code style="font-family:ui-monospace,monospace;font-size:0.72rem;font-weight:700;color:var(--foreground);">${esc(n.year)}</code>
                    </div>
                    <div style="color:var(--foreground);font-weight:600;line-height:1.35;">${esc(n.title)}</div>
                    <div style="font-size:0.72rem;color:var(--muted-foreground);margin-top:2px;">${esc(n.authors)}</div>
                </div>
                ${n.url ? `<a href="${esc(n.url)}" target="_blank" rel="noopener" style="flex-shrink:0;display:inline-flex;align-items:center;gap:5px;padding:5px 9px;border-radius:6px;background:var(--card);border:1px solid var(--border);color:var(--foreground);font-family:ui-monospace,monospace;font-size:0.7rem;font-weight:700;text-decoration:none;"><i class="fa fa-external-link"></i> NTRS</a>` : ''}
            </article>`;
        });
        box.innerHTML = html || '<div style="font-size:0.78rem;color:var(--muted-foreground);">No results found.</div>';
    } catch (err) {
        console.error(err);
    } finally {
        if (btn) { btn.disabled = false; btn.innerHTML = orig; }
    }
    return false;
};
</script>

<script id="sans-monitor-chart-data" type="application/json">
{!! json_encode([
    'isEn' => $isEn,
    'impacts' => [
        $bp['biomarker_cards']['oct_thickness']['impact'] ?? 0,
        $bp['biomarker_cards']['axial_length']['impact'] ?? 0,
        $bp['biomarker_cards']['simulated_hyperopia']['impact'] ?? 0,
        $bp['biomarker_cards']['papilledema']['impact'] ?? 0,
        $bp['biomarker_cards']['iop']['impact'] ?? 0,
        $bp['biomarker_cards']['hdt6_exposure']['impact'] ?? 0,
    ],
]) !!}
</script>
@endif
</div>

<script>
(function () {
    let bioChartInstance = null;
    let timeChartInstance = null;

    function renderSansCharts() {
        const dataEl = document.getElementById('sans-monitor-chart-data');
        if (!dataEl || typeof Chart === 'undefined') return;
        const cfg = JSON.parse(dataEl.textContent || '{}');
        const isDark = document.documentElement.dataset.theme === 'dark';
        const textColor = isDark ? '#f4f4f5' : '#09090b';
        const brandBlue = isDark ? '#2E96F5' : '#0042A6';
        const gridColor = isDark ? 'rgba(255,255,255,0.12)' : 'rgba(9,9,11,0.1)';
        const isEn = !!cfg.isEn;

        if (bioChartInstance) { bioChartInstance.destroy(); bioChartInstance = null; }
        if (timeChartInstance) { timeChartInstance.destroy(); timeChartInstance = null; }

        const bioCanvas = document.getElementById('biomarkerImpactChart');
        if (bioCanvas) {
            bioChartInstance = new Chart(bioCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: isEn
                        ? ['1. OCT Thickness', '2. Axial Length', '3. Hyperopia', '4. Papilledema', '5. IOP', '6. -6° Analog']
                        : ['1. Espesor OCT', '2. Long. Axial', '3. Hipermetropía', '4. Edema Papilar', '5. PIO', '6. Análogo -6°'],
                    datasets: [{
                        label: isEn ? 'Predictive Signal Weight' : 'Peso en Señal Predictiva',
                        data: cfg.impacts || [0, 0, 0, 0, 0, 0],
                        backgroundColor: brandBlue,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { color: textColor, font: { size: 10 } }, grid: { display: false } },
                        y: { beginAtZero: true, max: 45, ticks: { color: textColor, font: { size: 10 } }, grid: { color: gridColor } }
                    }
                }
            });
        }

        const timeCanvas = document.getElementById('timelineCutChart');
        if (timeCanvas) {
            timeChartInstance = new Chart(timeCanvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: isEn
                        ? ['Before (Baseline)', 'During Day 10', 'During Day 15', 'During Day 30', 'Cut +30d', 'Cut +60d (Unmeasured)', 'Cut +90d']
                        : ['Antes (Basal)', 'Durante Día 10', 'Durante Día 15', 'Durante Día 30', 'Corte +30d', 'Corte +60d (Sin dato)', 'Corte +90d'],
                    datasets: [
                        {
                            label: isEn ? 'Worsening Scenario OD (µm)' : 'Escenario en Progresión OD (µm)',
                            data: [280, 288, 294, 306, 295, null, 285],
                            borderColor: brandBlue,
                            backgroundColor: brandBlue,
                            borderWidth: 2.5,
                            pointRadius: 4,
                            spanGaps: false
                        },
                        {
                            label: isEn ? 'Stable Scenario (µm)' : 'Escenario Estable (µm)',
                            data: [278, 281, 283, 284, 281, 279, 278],
                            borderColor: '#16a34a',
                            borderDash: [5, 4],
                            borderWidth: 2,
                            pointRadius: 3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { labels: { color: textColor, font: { size: 11 } } }
                    },
                    scales: {
                        x: { ticks: { color: textColor, font: { size: 11 } }, grid: { color: gridColor } },
                        y: { ticks: { color: textColor, font: { size: 11 } }, grid: { color: gridColor } }
                    }
                }
            });
        }
    }

    function activateWizardTabInPlace(tabName) {
        const workspace = document.getElementById('sans-monitor-workspace');
        if (!workspace) return;

        workspace.querySelectorAll('[data-wizard-panel]').forEach(panel => {
            panel.style.display = (panel.getAttribute('data-wizard-panel') === tabName) ? '' : 'none';
        });

        workspace.querySelectorAll('[role="tablist"] [data-wizard-tab]').forEach(tabEl => {
            const isTarget = tabEl.getAttribute('data-wizard-tab') === tabName;
            tabEl.setAttribute('aria-selected', isTarget ? 'true' : 'false');
            tabEl.classList.toggle('is-active', isTarget);
            const pill = tabEl.querySelector('[data-wizard-pill]');
            if (pill) {
                pill.textContent = isTarget
                    ? (pill.getAttribute('data-label-active') || '● ACTIVE STEP')
                    : (pill.getAttribute('data-label-inactive') || 'CLICK TO OPEN →');
            }
        });

        workspace.querySelectorAll('input[name="tab"]').forEach(inp => {
            inp.value = tabName;
        });

        const url = new URL(window.location.href);
        url.searchParams.set('tab', tabName);
        window.history.replaceState({}, '', url.toString());

        if (bioChartInstance) bioChartInstance.resize();
        if (timeChartInstance) timeChartInstance.resize();
    }

    async function loadWorkspaceInPlace(targetUrl) {
        const scrollY = window.scrollY;
        sessionStorage.setItem('sans_scroll_y', String(scrollY));
        try {
            const response = await fetch(targetUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) {
                window.location.href = targetUrl;
                return;
            }
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newWorkspace = doc.getElementById('sans-monitor-workspace');
            const currentWorkspace = document.getElementById('sans-monitor-workspace');
            if (newWorkspace && currentWorkspace) {
                currentWorkspace.innerHTML = newWorkspace.innerHTML;
                window.history.replaceState({}, '', targetUrl);
                renderSansCharts();
                window.scrollTo({ top: scrollY, behavior: 'instant' });
            } else {
                window.location.href = targetUrl;
            }
        } catch (err) {
            window.location.href = targetUrl;
        }
    }

    document.addEventListener('click', function (e) {
        const workspace = document.getElementById('sans-monitor-workspace');
        if (!workspace || !workspace.contains(e.target)) return;

        // 1. Si es cambio de pestaña del Wizard (Paso 1, 2 o 3) sin cambiar de sujeto/día -> cambio instantáneo 0ms
        const wizardTabBtn = e.target.closest('[data-wizard-tab]');
        if (wizardTabBtn && !e.metaKey && !e.ctrlKey) {
            e.preventDefault();
            activateWizardTabInPlace(wizardTabBtn.getAttribute('data-wizard-tab'));
            return;
        }

        // 2. Si es clic en un escenario, astronauta o botón de día dentro del workspace -> actualizar in-place sin subir al top
        const link = e.target.closest('a[href]');
        if (link && !link.target && !e.metaKey && !e.ctrlKey) {
            const href = link.getAttribute('href');
            if (href && (href.includes('sans-monitor') || href.includes('case_id=') || href.includes('day=') || href.includes('mode='))) {
                e.preventDefault();
                loadWorkspaceInPlace(link.href);
            }
        }
    });

    document.addEventListener('change', function (e) {
        const workspace = document.getElementById('sans-monitor-workspace');
        if (!workspace || !workspace.contains(e.target)) return;
        const select = e.target.closest('select[name="case_id"], select[name="eye"]');
        if (select && select.form) {
            e.preventDefault();
            e.stopImmediatePropagation();
            const formData = new FormData(select.form);
            const url = new URL(select.form.action || window.location.href, window.location.origin);
            url.search = new URLSearchParams(formData).toString();
            loadWorkspaceInPlace(url.toString());
        }
    }, true);

    document.addEventListener('submit', function (e) {
        if (e.defaultPrevented) return;
        const workspace = document.getElementById('sans-monitor-workspace');
        if (!workspace || !workspace.contains(e.target)) return;
        const form = e.target.closest('form');
        if (form && (form.id === 'candidate-predict-form' || form.id === 'nasa-live-search-form')) {
            return;
        }
        if (form && (form.method || 'GET').toUpperCase() === 'GET') {
            e.preventDefault();
            const formData = new FormData(form);
            const url = new URL(form.action || window.location.href, window.location.origin);
            url.search = new URLSearchParams(formData).toString();
            loadWorkspaceInPlace(url.toString());
        } else if (form) {
            sessionStorage.setItem('sans_scroll_y', String(window.scrollY));
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        renderSansCharts();
        const savedScroll = sessionStorage.getItem('sans_scroll_y');
        if (savedScroll !== null) {
            sessionStorage.removeItem('sans_scroll_y');
            window.scrollTo({ top: parseInt(savedScroll, 10) || 0, behavior: 'instant' });
        }
    });
})();
</script>
@endsection
