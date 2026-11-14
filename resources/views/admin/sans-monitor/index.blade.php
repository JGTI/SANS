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

{{-- CABECERA DE ALTO IMPACTO EJECUTIVO --}}
<div class="card" style="padding:1.35rem 1.5rem;margin-bottom:1.25rem;border:1px solid var(--foreground);">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;">
        <div>
            <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--muted-foreground);">
                <i class="fa fa-crosshairs"></i>
                {{ $isEn ? 'SPACE VISION · BIOMARKER-DRIVEN NEURO-OCULAR PREDICTOR' : 'SPACE VISION · PREDICTOR NEURO-OCULAR BASADO EN BIOMARCADORES' }}
            </div>
            <h1 style="font-size:1.45rem;font-weight:800;margin:0.2rem 0;">
                {{ $isEn
                    ? 'SANS Biomarker Predictor & Longitudinal Follow-up (+30 / +60 / +90 Days)'
                    : 'Predictor SANS por Biomarcadores y Seguimiento Longitudinal (+30 / +60 / +90 Días)' }}
            </h1>
            <p style="margin:0;color:var(--muted-foreground);font-size:0.86rem;">
                @if($isEn)
                    Enter subject data, evaluate the <strong>6 ocular biomarkers</strong>, track changes <strong>Before · During · After (+30, +60, +90 days)</strong>, and compare the <strong>3 reference scenario models</strong>.
                @else
                    Nutre los datos de la persona, evalúa los <strong>6 biomarcadores oculares</strong>, mide el cambio <strong>Antes · Durante · Después (+30, +60, +90 días)</strong> y compara los <strong>3 escenarios modelos</strong>.
                @endif
            </p>
        </div>

        @php
            $headerBadgeClass = match($caseEvaluation['case_operational_code']) {
                'LISTO_PARA_REVISION' => 'status-badge-ready',
                'PRIORIZAR_REVISION' => 'status-badge-prioritize',
                default => 'status-badge-missing',
            };
        @endphp
        <div style="display:flex;gap:0.6rem;align-items:center;flex-wrap:wrap;">
            <div style="padding:0.45rem 0.85rem;border:1px solid var(--border);border-radius:7px;text-align:right;background:var(--muted);">
                <div style="font-size:0.66rem;text-transform:uppercase;color:var(--muted-foreground);margin-bottom:0.15rem;">
                    {{ $isEn ? 'Operational State' : 'Estado Operativo' }} ({{ $activeCase->case_id }})
                </div>
                <span class="{{ $headerBadgeClass }}">
                    <i class="fa {{ $caseEvaluation['case_operational_code'] === 'LISTO_PARA_REVISION' ? 'fa-check-circle' : ($caseEvaluation['case_operational_code'] === 'PRIORIZAR_REVISION' ? 'fa-exclamation-triangle' : 'fa-question-circle') }}"></i>
                    {{ $t($caseEvaluation['case_operational_text']) }}
                </span>
            </div>
            <div style="padding:0.45rem 0.85rem;border:1px solid var(--border);border-radius:7px;text-align:right;background:var(--muted);">
                <div style="font-size:0.66rem;text-transform:uppercase;color:var(--muted-foreground);">
                    {{ $isEn ? 'Fitness Verdict' : 'Dictamen de Aptitud' }}
                </div>
                <div style="font-size:0.9rem;font-weight:700;">{{ $t($caseEvaluation['fitness_display']) }}</div>
            </div>
            <a href="{{ route('home', ['lang' => app()->getLocale()]) }}" class="btn btn-outline" style="padding:0.6rem 0.85rem;" title="{{ $isEn ? 'Reset Demo to default scenario' : 'Reiniciar demostración al estado inicial' }}">
                <i class="fa fa-refresh"></i> {{ $isEn ? 'Reset Demo' : 'Reiniciar Demo' }}
            </a>
            <a href="{{ route('public.digital-twin', ['mode' => 'dual']) }}" class="btn btn-primary" style="padding:0.6rem 1rem;">
                <i class="fa fa-cube"></i> {{ $isEn ? 'Open 3D Viewer + OCT' : 'Abrir Visor 3D + OCT' }} <i class="fa fa-external-link"></i>
            </a>
        </div>
    </div>
    <div style="margin-top:0.75rem;padding-top:0.65rem;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.5rem;font-size:0.76rem;color:var(--muted-foreground);">
        <span>
            <i class="fa fa-shield"></i>
            <strong>{{ $isEn ? 'Scientific Honesty & Provenance:' : 'Honestidad Científica y Procedencia:' }}</strong>
            {{ $isEn
                ? 'Source type: ' . $activeCase->source_type . ' (Calibrated synthetic/analog dataset). Prepares medical surveillance without issuing automated fit-for-duty verdicts.'
                : 'Origen de datos: ' . $activeCase->source_type . ' (Cohorte sintética/análoga calibrada). Prepara la vigilancia médica sin emitir un veredicto automático de aptitud.' }}
        </span>
        <span>
            <i class="fa fa-check-square-o"></i>
            {{ $isEn ? 'Public Demo Mode · Zero Login Required · WCAG 2.2 AA' : 'Modo Demo Público · Sin Login Requerido · WCAG 2.2 AA' }}
        </span>
    </div>
</div>

@if(session('status'))
    <div class="card" style="padding:0.85rem 1rem;margin-bottom:1rem;border-left:4px solid var(--space-blue);">
        <i class="fa fa-check-circle"></i> {{ session('status') }}
    </div>
@endif

{{-- BARRA COMPACTA DE SELECCIÓN DE EXPEDIENTE / OJO + GALERÍA IA DE ASTRONAUTAS + WIZARD DE 3 PASOS --}}
@php
    $activeCrewMeta = \App\Models\AstronautProfile::crewMetaForCase($activeCase->case_id);
@endphp
<div class="card" style="padding:0.95rem 1.15rem;margin-bottom:1rem;border:1.5px solid var(--interactive-border);">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;padding-bottom:0.75rem;border-bottom:1px solid var(--border);margin-bottom:0.75rem;">
        <div style="display:flex;align-items:center;gap:0.85rem;">
            <img src="{{ $activeCrewMeta['avatar'] }}"
                 alt="{{ $activeCrewMeta['name'] }}"
                 style="width:54px;height:54px;border-radius:8px;object-fit:cover;border:2px solid var(--space-blue);box-shadow:0 2px 8px rgba(0,66,166,0.2);">
            <div>
                <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
                    <span style="font-size:1rem;font-weight:800;">{{ $activeCrewMeta['name'] }}</span>
                    <span class="interactive-pill" style="background:#0042A6;color:#ffffff;border-color:#0042A6;">
                        <i class="fa fa-check-circle"></i> {{ $activeCase->case_id }} · {{ $t($activeCase->scenario_archetype) }}
                    </span>
                </div>
                <div style="font-size:0.76rem;color:var(--muted-foreground);margin-top:0.1rem;">
                    {{ $activeCrewMeta['role'] }} · {{ $isEn ? 'Click any astronaut card below or use the dropdowns to switch subject' : 'Haz clic en cualquier tarjeta de astronauta abajo o usa el selector para cambiar de sujeto' }}
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.sans-monitor.index') }}" style="display:flex;gap:0.6rem;align-items:center;flex-wrap:wrap;margin:0;">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            <label style="font-size:0.75rem;font-weight:700;color:var(--interactive-text);margin:0;">
                <i class="fa fa-user"></i> {{ $isEn ? 'Select Dossier:' : 'Elegir Expediente:' }}
            </label>
            <select name="case_id" class="form-control" style="width:auto;min-width:220px;padding:0.4rem 0.65rem;height:auto;" onchange="this.form.submit()" aria-label="{{ $isEn ? 'Select subject' : 'Seleccionar expediente' }}">
                @foreach($cases as $c)
                    @php $cm = \App\Models\AstronautProfile::crewMetaForCase($c->case_id); @endphp
                    <option value="{{ $c->case_id }}" {{ $c->case_id === $activeCase->case_id ? 'selected' : '' }}>
                        {{ $cm['name'] }} — {{ $c->case_id }} ({{ $t($c->scenario_archetype) }})
                    </option>
                @endforeach
            </select>
            <label style="font-size:0.75rem;font-weight:700;color:var(--interactive-text);margin:0;">
                <i class="fa fa-eye"></i> {{ $isEn ? 'Eye:' : 'Ojo:' }}
            </label>
            <select name="eye" class="form-control" style="width:auto;min-width:170px;padding:0.4rem 0.65rem;height:auto;" onchange="this.form.submit()" aria-label="{{ $isEn ? 'Select eye' : 'Seleccionar ojo' }}">
                <option value="OD" {{ $selectedEye === 'OD' ? 'selected' : '' }}>{{ $isEn ? 'Right Eye (OD)' : 'Ojo Derecho (OD)' }}</option>
                <option value="OS" {{ $selectedEye === 'OS' ? 'selected' : '' }}>{{ $isEn ? 'Left Eye (OS)' : 'Ojo Izquierdo (OS)' }}</option>
                <option value="BILATERAL" {{ $selectedEye === 'BILATERAL' ? 'selected' : '' }}>{{ $isEn ? 'Bilateral (OD + OS)' : 'Bilateral (OD + OS)' }}</option>
            </select>
        </form>
    </div>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.45rem;flex-wrap:wrap;gap:0.4rem;">
        <span style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:var(--interactive-text);">
            <i class="fa fa-hand-pointer-o"></i>
            {{ $isEn ? 'Interactive Crew Cohort — Click an astronaut to load their ocular record:' : 'Cohorte Interactiva de Tripulación — Haz clic en un astronauta para cargar su expediente:' }}
        </span>
    </div>

    {{-- TIRA VISUAL DE ASTRONAUTAS CON FOTO IA (1 CLIC PARA CAMBIAR DE SUJETO) --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(185px,1fr));gap:0.55rem;">
        @foreach($cases as $c)
            @php
                $cm = \App\Models\AstronautProfile::crewMetaForCase($c->case_id);
                $isActiveCrew = $c->case_id === $activeCase->case_id;
            @endphp
            <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['case_id' => $c->case_id])) }}"
               class="clickable-card {{ $isActiveCrew ? 'is-active' : '' }}"
               style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:0.55rem;padding:0.45rem 0.6rem;border-radius:7px;">
                <img src="{{ $cm['avatar'] }}"
                     alt="{{ $cm['name'] }}"
                     style="width:38px;height:38px;border-radius:6px;object-fit:cover;border:1.5px solid {{ $isActiveCrew ? '#0042A6' : 'var(--border)' }};flex-shrink:0;">
                <div style="min-width:0;overflow:hidden;flex:1;">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:0.25rem;">
                        <span style="font-size:0.75rem;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $cm['name'] }}</span>
                        <span class="interactive-pill" style="font-size:0.58rem;padding:0.1rem 0.38rem;">
                            {{ $isActiveCrew ? ($isEn ? 'ACTIVE' : 'ACTIVO') : ($isEn ? 'Select →' : 'Elegir →') }}
                        </span>
                    </div>
                    <div style="font-size:0.66rem;color:var(--muted-foreground);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $c->case_id }} · {{ $t($c->scenario_archetype) }}</div>
                </div>
            </a>
        @endforeach
    </div>
</div>

{{-- NAVEGADOR VISUAL DEL WIZARD / TABS DE 3 PASOS --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.45rem;flex-wrap:wrap;gap:0.4rem;">
    <span style="font-size:0.74rem;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:var(--interactive-text);">
        <i class="fa fa-compass"></i>
        {{ $isEn ? '3-Step Evaluation Wizard — Click Step 1, Step 2, or Step 3 to switch views:' : 'Wizard de Evaluación en 3 Pasos — Haz clic en el Paso 1, Paso 2 o Paso 3 para cambiar de vista:' }}
    </span>
</div>
<div class="sans-grid-3" style="margin-bottom:1.25rem;" role="tablist" aria-label="{{ $isEn ? '3 Main Wizard Steps' : '3 Pasos Principales del Wizard' }}">
    {{-- PASO 1 --}}
    <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'biomarcadores'])) }}"
       data-wizard-tab="biomarcadores"
       role="tab"
       aria-selected="{{ $normalizedTab === 'biomarcadores' ? 'true' : 'false' }}"
       class="wizard-step-card {{ $normalizedTab === 'biomarcadores' ? 'is-active' : '' }}">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:0.4rem;">
            <span style="font-size:0.68rem;font-weight:800;letter-spacing:0.06em;text-transform:uppercase;opacity:0.92;">
                {{ $isEn ? 'STEP 1 OF 3 · INTAKE & PREDICTOR' : 'PASO 1 DE 3 · INGRESO Y PREDICTOR' }}
            </span>
            <span class="wizard-step-pill" data-wizard-pill
                  data-label-active="{{ $isEn ? '● ACTIVE STEP' : '● PASO ACTIVO' }}"
                  data-label-inactive="{{ $isEn ? 'CLICK TO OPEN →' : 'CLIC PARA ABRIR →' }}">
                {{ $normalizedTab === 'biomarcadores' ? ($isEn ? '● ACTIVE STEP' : '● PASO ACTIVO') : ($isEn ? 'CLICK TO OPEN →' : 'CLIC PARA ABRIR →') }}
            </span>
        </div>
        <div style="font-size:0.96rem;font-weight:800;">
            <i class="fa fa-heartbeat"></i> {{ $isEn ? '1. Initial Form & 6 Biomarkers' : '1. Formulario Inicial y 6 Biomarcadores' }}
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
                {{ $isEn ? 'STEP 2 OF 3 · LONGITUDINAL CUTS' : 'PASO 2 DE 3 · CORTES EN EL TIEMPO' }}
            </span>
            <span class="wizard-step-pill" data-wizard-pill
                  data-label-active="{{ $isEn ? '● ACTIVE STEP' : '● PASO ACTIVO' }}"
                  data-label-inactive="{{ $isEn ? 'CLICK TO OPEN →' : 'CLIC PARA ABRIR →' }}">
                {{ $normalizedTab === 'seguimiento' ? ($isEn ? '● ACTIVE STEP' : '● PASO ACTIVO') : ($isEn ? 'CLICK TO OPEN →' : 'CLIC PARA ABRIR →') }}
            </span>
        </div>
        <div style="font-size:0.96rem;font-weight:800;">
            <i class="fa fa-calendar"></i> {{ $isEn ? '2. Before · During · After (+30, +60, +90 days)' : '2. Antes · Durante · Después (+30, +60, +90 días)' }}
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
                {{ $isEn ? 'STEP 3 OF 3 · REFERENCE ARCHETYPES' : 'PASO 3 DE 3 · ARQUETIPOS DE REFERENCIA' }}
            </span>
            <span class="wizard-step-pill" data-wizard-pill
                  data-label-active="{{ $isEn ? '● ACTIVE STEP' : '● PASO ACTIVO' }}"
                  data-label-inactive="{{ $isEn ? 'CLICK TO OPEN →' : 'CLIC PARA ABRIR →' }}">
                {{ $normalizedTab === 'escenarios' ? ($isEn ? '● ACTIVE STEP' : '● PASO ACTIVO') : ($isEn ? 'CLICK TO OPEN →' : 'CLIC PARA ABRIR →') }}
            </span>
        </div>
        <div style="font-size:0.96rem;font-weight:800;">
            <i class="fa fa-sliders"></i> {{ $isEn ? '3. Scenario Models (Stable · Worsens · Unscanned)' : '3. Escenarios Modelos (Estable · Empeora · No se escanea)' }}
        </div>
        <div style="font-size:0.75rem;opacity:0.88;">
            {{ $isEn ? 'Compare the 3 canonical models & anatomical SVG schematics.' : 'Compara los 3 modelos canónicos y sus esquemas anatómicos SVG.' }}
        </div>
    </a>
</div>

{{-- Resumen compacto permanente por ojo --}}
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
                        {{ $isEn ? '1. Mean OCT Thickness (µm)' : '1. Espesor Medio por OCT (µm)' }}
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
                        {{ $isEn ? '2. Eye Axial Length (mm)' : '2. Longitud Axial del Ojo (mm)' }}
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
                        {{ $isEn ? '3. Simulated Hyperopia (ΔSE in D)' : '3. Hipermetropía Simulada (ΔSE en D)' }}
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
<div class="card" style="padding:0.85rem 1.15rem;margin-bottom:1.25rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;">
    <div style="font-size:0.8rem;color:var(--muted-foreground);">
        <i class="fa fa-compass"></i>
        <strong>{{ $isEn ? 'Current Step: 1 / 3 (Initial Form & 6 Biomarkers)' : 'Paso Actual: 1 / 3 (Formulario Inicial y 6 Biomarcadores)' }}</strong>
    </div>
    <div>
        <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'seguimiento'])) }}" data-wizard-tab="seguimiento" class="btn btn-primary" style="font-weight:700;">
            {{ $isEn ? 'Next: Step 2 (Timeline +30/+60/+90d)' : 'Siguiente: Paso 2 (Tiempo +30/+60/+90d)' }} <i class="fa fa-arrow-right"></i>
        </a>
    </div>
</div>
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
<div class="card" style="padding:0.85rem 1.15rem;margin-bottom:1.25rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;">
    <div style="font-size:0.8rem;color:var(--muted-foreground);">
        <i class="fa fa-compass"></i>
        <strong>{{ $isEn ? 'Current Step: 2 / 3 (Before · During · After +30/+60/+90d)' : 'Paso Actual: 2 / 3 (Antes · Durante · Después +30/+60/+90d)' }}</strong>
    </div>
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
        <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'biomarcadores'])) }}" data-wizard-tab="biomarcadores" class="btn btn-outline" style="font-weight:700;">
            <i class="fa fa-arrow-left"></i> {{ $isEn ? 'Previous: Step 1 (Biomarkers)' : 'Anterior: Paso 1 (Biomarcadores)' }}
        </a>
        <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'escenarios'])) }}" data-wizard-tab="escenarios" class="btn btn-primary" style="font-weight:700;">
            {{ $isEn ? 'Next: Step 3 (3 Scenario Models)' : 'Siguiente: Paso 3 (3 Escenarios)' }} <i class="fa fa-arrow-right"></i>
        </a>
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

    {{-- Vistas Esquemáticas Anatómicas del Escenario Activo --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
        <div style="border:1px solid var(--border);border-radius:6px;padding:1rem;">
            <h3 style="font-size:0.9rem;font-weight:700;margin:0 0 0.5rem;">
                <i class="fa fa-circle-o"></i> {{ $isEn ? '1. Axial Length & Posterior Pole (Sagittal)' : '1. Longitud Axial y Polo Posterior (Sagital)' }}
            </h3>
            <svg viewBox="0 0 260 140" style="width:100%;height:130px;background:#09090b;border-radius:4px;">
                <ellipse cx="125" cy="70" rx="{{ $activeCase->scenario_archetype === 'EMPEORA' ? 50 : 56 }}" ry="52" fill="none" stroke="#ffffff" stroke-width="2"/>
                <line x1="68" y1="70" x2="182" y2="70" stroke="#a1a1aa" stroke-dasharray="4 3" stroke-width="1.5"/>
                <rect x="175" y="57" width="35" height="26" fill="none" stroke="#ffffff" stroke-width="1.5"/>
                <text x="14" y="22" fill="#ffffff" font-size="10" font-family="monospace">{{ $isEn ? 'AXIAL LENGTH' : 'LONGITUD AXIAL' }} · {{ $activeCase->case_id }}</text>
                <text x="14" y="128" fill="#a1a1aa" font-size="9" font-family="monospace">{{ $isEn ? 'Simulated hyperopia:' : 'Hipermetropía simulada:' }} +{{ $bp['inputs']['hyperopia_sim_d'] }} D</text>
            </svg>
        </div>

        <div style="border:1px solid var(--border);border-radius:6px;padding:1rem;">
            <h3 style="font-size:0.9rem;font-weight:700;margin:0 0 0.5rem;">
                <i class="fa fa-dot-circle-o"></i> {{ $isEn ? '2. Papilledema & Optic Disc (Fundus)' : '2. Edema Papilar y Disco Óptico (Fondo)' }}
            </h3>
            <svg viewBox="0 0 260 140" style="width:100%;height:130px;background:#09090b;border-radius:4px;">
                <circle cx="130" cy="70" r="52" fill="none" stroke="#ffffff" stroke-width="1.5"/>
                <circle cx="155" cy="70" r="{{ $activeCase->scenario_archetype === 'EMPEORA' ? 19 : 12 }}" fill="none" stroke="#ffffff" stroke-width="2" stroke-dasharray="{{ $activeCase->scenario_archetype === 'EMPEORA' ? '3 2' : 'none' }}"/>
                <text x="14" y="22" fill="#ffffff" font-size="10" font-family="monospace">{{ $isEn ? 'PAPILLEDEMA' : 'EDEMA PAPILAR' }} (FRISÉN {{ $bp['inputs']['papilledema_frisen'] ?? 'N/E' }})</text>
                <text x="14" y="128" fill="#a1a1aa" font-size="9" font-family="monospace">{{ $isEn ? 'IOP:' : 'PIO:' }} {{ $bp['inputs']['iop_mmhg'] }} mmHg</text>
            </svg>
        </div>

        <div style="border:1px solid var(--border);border-radius:6px;padding:1rem;">
            <h3 style="font-size:0.9rem;font-weight:700;margin:0 0 0.5rem;">
                <i class="fa fa-align-justify"></i> {{ $isEn ? '3. Mean OCT Thickness (Retinal Layers)' : '3. Espesor Medio por OCT (Capas)' }}
            </h3>
            @if($caseEvaluation['eyes'][$simEye]['coverage_code'] === 'SIN_ESCANEOS_PREVIOS')
                <div style="height:130px;display:flex;align-items:center;justify-content:center;background:#09090b;color:#ffffff;padding:1rem;text-align:center;font-size:0.82rem;border-radius:4px;">
                    {{ $isEn
                        ? 'Scenario "Unscanned": OCT not evaluable (Never drawn as normal nor classified as stable).'
                        : 'Escenario «No se escanea»: OCT no evaluable (No se dibuja como normal ni se clasifica como estable).' }}
                </div>
            @else
                <svg viewBox="0 0 260 140" style="width:100%;height:130px;background:#09090b;border-radius:4px;">
                    <path d="M 15 55 Q 130 {{ $activeCase->scenario_archetype === 'EMPEORA' ? 28 : 48 }} 245 55" fill="none" stroke="#ffffff" stroke-width="2"/>
                    <path d="M 15 80 Q 130 78 245 80" fill="none" stroke="#a1a1aa" stroke-width="1.5" stroke-dasharray="4 2"/>
                    <path d="M 15 106 Q 130 106 245 106" fill="none" stroke="#ffffff" stroke-width="2"/>
                    <text x="14" y="22" fill="#ffffff" font-size="10" font-family="monospace">{{ $isEn ? 'OCT THICKNESS:' : 'ESPESOR OCT:' }} {{ $bp['inputs']['oct_trt_current_um'] }} µm</text>
                    <text x="14" y="128" fill="#a1a1aa" font-size="9" font-family="monospace">{{ $isEn ? 'Change vs baseline:' : 'Cambio vs basal:' }} {{ $bp['delta_trt_um'] !== null ? '+'.$bp['delta_trt_um'].' µm' : 'N/A' }}</text>
                </svg>
            @endif
        </div>
    </div>
</div>
<div class="card" style="padding:0.85rem 1.15rem;margin-bottom:1.25rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;">
    <div style="font-size:0.8rem;color:var(--muted-foreground);">
        <i class="fa fa-compass"></i>
        <strong>{{ $isEn ? 'Current Step: 3 / 3 (3 Reference Scenario Models)' : 'Paso Actual: 3 / 3 (3 Escenarios Modelos de Referencia)' }}</strong>
    </div>
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
        <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'seguimiento'])) }}" data-wizard-tab="seguimiento" class="btn btn-outline" style="font-weight:700;">
            <i class="fa fa-arrow-left"></i> {{ $isEn ? 'Previous: Step 2 (Timeline +30/+60/+90d)' : 'Anterior: Paso 2 (Tiempo +30/+60/+90d)' }}
        </a>
        <a href="{{ url('/digital-twin?mode=dual') }}" class="btn btn-primary" style="font-weight:700;">
            <i class="fa fa-eye"></i> {{ $isEn ? 'Continue to 3D Eye + OCT Viewer' : 'Continuar al Visor 3D + OCT' }} <i class="fa fa-arrow-right"></i>
        </a>
    </div>
</div>
</div>

{{-- CAJONES DESPLEGABLES DE EVIDENCIA CIENTÍFICA NASA Y MATRIZ DEL JURADO (SIN RECARGAR LA VISTA PRINCIPAL) --}}
<div class="sans-grid-2" style="margin-bottom:1.25rem;">
    {{-- Cajón 1: Protocolos NASA y 7 Referencias Canónicas (RF-05 a RF-08, AT-02) --}}
    <details class="card collapsible-card" style="padding:1rem 1.2rem;">
        <summary style="cursor:pointer;font-weight:800;font-size:0.9rem;display:flex;justify-content:space-between;align-items:center;gap:0.5rem;">
            <span>
                <i class="fa fa-book" style="color:var(--interactive-text);"></i>
                {{ $isEn
                    ? 'NASA Protocols, Previously Applied Version & 7 Scientific References (AT-02)'
                    : 'Protocolos NASA, Versión Previa Aplicada y 7 Referencias Científicas (AT-02)' }}
            </span>
            <span class="interactive-pill">{{ $isEn ? 'Click to expand +' : 'Clic para abrir +' }}</span>
        </summary>
        <div style="margin-top:0.85rem;font-size:0.8rem;">
            <div style="padding:0.65rem;border:1px solid var(--border);border-radius:5px;margin-bottom:0.65rem;">
                <strong>{{ $isEn ? '1. Previously Applied Protocol on Subject:' : '1. Protocolo Aplicado Previamente en la Ficha:' }}</strong>
                @if($catalogSearch['previously_applied'])
                    <div>
                        <code>{{ $catalogSearch['previously_applied']->version_id }}</code> —
                        {{ $catalogSearch['previously_applied']->title }} ({{ $catalogSearch['previously_applied']->issuer ?? 'NASA' }})
                    </div>
                @else
                    <div>{{ $isEn ? 'Unknown / Not recorded (Does not block evaluation)' : 'Desconocido / No registrado (No bloquea evaluación)' }}</div>
                @endif
            </div>

            @if(!empty($catalogSearch['newer_verified_versions']))
                <div style="padding:0.65rem;border:1px solid var(--border);border-radius:5px;margin-bottom:0.65rem;">
                    <strong>{{ $isEn ? '2. Newer Verified Version Available (Never auto-replaces selection):' : '2. Versión Posterior Verificada Disponible (Nunca reemplaza sola la selección):' }}</strong>
                    @foreach($catalogSearch['newer_verified_versions'] as $nv)
                        <div>
                            <code>{{ $nv->version_id }}</code> — {{ $nv->title }} ({{ $nv->publication_date ?? '2024' }})
                        </div>
                    @endforeach
                </div>
            @endif

            <div style="font-weight:700;margin-bottom:0.35rem;">
                {{ $isEn ? '3. Curated Catalog (Protocols vs Technical Standards vs Research Articles):' : '3. Catálogo Curado (Protocolos vs Estándares Técnicos vs Artículos de Investigación):' }}
            </div>
            <ul style="margin:0;padding-left:1.15rem;">
                @foreach($catalogSearch['followup_matches'] as $match)
                    <li style="margin-bottom:0.3rem;">
                        <strong>[{{ $match['document_type_badge'] }}]</strong>
                        <code>{{ $match['protocol']->version_id }}</code> — {{ $match['protocol']->title }}
                        @if($match['protocol']->source_url)
                            <a href="{{ $match['protocol']->source_url }}" target="_blank" rel="noopener" style="text-decoration:underline;margin-left:4px;color:var(--interactive-text);font-weight:700;">
                                <i class="fa fa-external-link"></i> NASA/Source
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </details>

    {{-- Cajón 2: Matriz de Evaluación NASA Space Apps (Impact, Creativity, Validity, Relevance, Presentation) --}}
    <details class="card collapsible-card" style="padding:1rem 1.2rem;">
        <summary style="cursor:pointer;font-weight:800;font-size:0.9rem;display:flex;justify-content:space-between;align-items:center;gap:0.5rem;">
            <span>
                <i class="fa fa-trophy" style="color:var(--interactive-text);"></i>
                {{ $isEn
                    ? 'NASA Space Apps Judging Guide Matrix (Impact · Creativity · Validity · Relevance · Presentation)'
                    : 'Matriz para el Jurado NASA Space Apps (Impacto · Creatividad · Validez · Relevancia · Presentación)' }}
            </span>
            <span class="interactive-pill">{{ $isEn ? 'Click to expand +' : 'Clic para abrir +' }}</span>
        </summary>
        <div style="margin-top:0.85rem;font-size:0.8rem;display:grid;gap:0.45rem;">
            <div>
                <strong><i class="fa fa-bullseye"></i> Impact:</strong>
                {{ $isEn
                    ? 'Structures longitudinal ocular surveillance (Before, During, and +30/+60/+90d post-flight) and -6° HDT Earth analogs without replacing medical judgment.'
                    : 'Estructura la vigilancia ocular longitudinal (Antes, Durante y +30/+60/+90d posvuelo) y análogos terrestres -6° HDT sin sustituir el juicio médico.' }}
            </div>
            <div>
                <strong><i class="fa fa-lightbulb-o"></i> Creativity:</strong>
                {{ $isEn
                    ? 'Combines a 6-biomarker explainable predictor, 3 reference scenario models (Stable, Worsens, Unscanned), and a dual 3D WebGL + OCT workstation.'
                    : 'Combina un predictor explicable de 6 biomarcadores, 3 escenarios modelos (Estable, Empeora, No se escanea) y estación dual 3D WebGL + OCT.' }}
            </div>
            <div>
                <strong><i class="fa fa-flask"></i> Validity:</strong>
                {{ $isEn
                    ? 'Deterministic ophthalmology (SE, LogMAR, Thibos vectors), strict separation of historical vs synthetic interpolation, and 99 automated tests (77 PHPUnit + 22 Pytest).'
                    : 'Oftalmología determinista (SE, LogMAR, vectores de Thibos), separación estricta entre histórico y simulación sintética, y 99 tests automatizados.' }}
            </div>
            <div>
                <strong><i class="fa fa-rocket"></i> Relevance:</strong>
                {{ $isEn
                    ? 'Grounded in NASA OCHMO-MTB-001, NASA-STD-3001, SLS-SPEC-159, NASAHRP SANS Evidence Report, OSDR, and NTRS.'
                    : 'Fundamentado en NASA OCHMO-MTB-001, NASA-STD-3001, SLS-SPEC-159, NASAHRP SANS Evidence Report, OSDR y NTRS.' }}
            </div>
            <div>
                <strong><i class="fa fa-desktop"></i> Presentation:</strong>
                {{ $isEn
                    ? 'Zero-login public access at root URL (/), bilingual ES/EN switch, WCAG 2.2 AA high-contrast theme, and NEXUS × Visual Rehab Center attribution.'
                    : 'Acceso público sin login en la raíz (/), selector bilingüe ES/EN, alto contraste WCAG 2.2 AA y atribución NEXUS × Visual Rehab Center.' }}
            </div>
        </div>
    </details>
</div>

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
        const workspace = document.getElementById('sans-monitor-workspace');
        if (!workspace || !workspace.contains(e.target)) return;
        const form = e.target.closest('form');
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
