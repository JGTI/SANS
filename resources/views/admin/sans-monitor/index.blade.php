@extends('layouts.app')

@section('title', 'Predictor SANS — Biomarcadores, Línea de Tiempo (+30/+60/+90) y Escenarios')

@section('content')
@php
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

{{-- CABECERA DE ALTO IMPACTO EJECUTIVO --}}
<div class="card" style="padding:1.35rem 1.5rem;margin-bottom:1.25rem;border:1px solid var(--foreground);">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;">
        <div>
            <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--muted-foreground);">
                <i class="fa fa-crosshairs"></i> SPACE VISION · PREDICTOR NEURO-OCULAR BASADO EN BIOMARCADORES
            </div>
            <h1 style="font-size:1.45rem;font-weight:800;margin:0.2rem 0;">
                Predictor SANS por Biomarcadores y Seguimiento Longitudinal (+30 / +60 / +90 Días)
            </h1>
            <p style="margin:0;color:var(--muted-foreground);font-size:0.86rem;">
                Nutre los datos de la persona, evalúa los <strong>6 biomarcadores oculares</strong>, mide el cambio <strong>Antes · Durante · Después (+30, +60, +90 días)</strong> y compara los <strong>3 escenarios modelos</strong>.
            </p>
        </div>

        <div style="display:flex;gap:0.6rem;align-items:center;flex-wrap:wrap;">
            <div style="padding:0.5rem 0.9rem;border:1px solid var(--border);border-radius:6px;text-align:right;">
                <div style="font-size:0.68rem;text-transform:uppercase;color:var(--muted-foreground);">Estado Operativo ({{ $activeCase->case_id }})</div>
                <div style="font-size:0.95rem;font-weight:800;">{{ $caseEvaluation['case_operational_text'] }}</div>
            </div>
            <div style="padding:0.5rem 0.9rem;border:1px solid var(--border);border-radius:6px;text-align:right;">
                <div style="font-size:0.68rem;text-transform:uppercase;color:var(--muted-foreground);">Dictamen de Aptitud</div>
                <div style="font-size:0.95rem;font-weight:700;">{{ $caseEvaluation['fitness_display'] }}</div>
            </div>
            <a href="{{ route('admin.digital-twin', ['mode' => 'dual']) }}" class="btn btn-primary" style="padding:0.6rem 1rem;">
                <i class="fa fa-cube"></i> Abrir Visor 3D + OCT
            </a>
        </div>
    </div>
</div>

@if(session('status'))
    <div class="card" style="padding:0.85rem 1rem;margin-bottom:1rem;border-left:4px solid var(--foreground);">
        <i class="fa fa-check-circle"></i> {{ session('status') }}
    </div>
@endif

{{-- SELECTOR RÁPIDO DE SUJETO Y NAVEGACIÓN DE LAS 3 PESTAÑAS CENTRALES --}}
<div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:1.25rem;">
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;" role="tablist" aria-label="3 Pestañas Principales">
        <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'biomarcadores'])) }}"
           class="btn {{ $normalizedTab === 'biomarcadores' ? 'btn-primary' : 'btn-outline' }}" style="font-weight:700;padding:0.6rem 1.1rem;">
            <i class="fa fa-heartbeat"></i> 1. Formulario Inicial y 6 Biomarcadores
        </a>
        <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'seguimiento'])) }}"
           class="btn {{ $normalizedTab === 'seguimiento' ? 'btn-primary' : 'btn-outline' }}" style="font-weight:700;padding:0.6rem 1.1rem;">
            <i class="fa fa-calendar"></i> 2. Antes · Durante · Después (+30, +60, +90 días)
        </a>
        <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'escenarios'])) }}"
           class="btn {{ $normalizedTab === 'escenarios' ? 'btn-primary' : 'btn-outline' }}" style="font-weight:700;padding:0.6rem 1.1rem;">
            <i class="fa fa-sliders"></i> 3. Escenarios Modelos (Estable · Empeora · No se escanea)
        </a>
    </div>

    <form method="GET" action="{{ route('admin.sans-monitor.index') }}" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap;">
        <input type="hidden" name="tab" value="{{ $activeTab }}">
        <select name="case_id" class="form-control" onchange="this.form.submit()" aria-label="Seleccionar expediente">
            @foreach($cases as $c)
                <option value="{{ $c->case_id }}" {{ $c->case_id === $activeCase->case_id ? 'selected' : '' }}>
                     Sujeto: {{ $c->case_id }} ({{ $c->scenario_archetype }})
                </option>
            @endforeach
        </select>
        <select name="eye" class="form-control" onchange="this.form.submit()" aria-label="Seleccionar ojo">
            <option value="OD" {{ $selectedEye === 'OD' ? 'selected' : '' }}>Ojo Derecho (OD)</option>
            <option value="OS" {{ $selectedEye === 'OS' ? 'selected' : '' }}>Ojo Izquierdo (OS)</option>
            <option value="BILATERAL" {{ $selectedEye === 'BILATERAL' ? 'selected' : '' }}>Bilateral (OD + OS)</option>
        </select>
    </form>
</div>

{{-- Resumen compacto permanente por ojo (garantiza visibilidad inmediata y pruebas de aceptación) --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:0.85rem;margin-bottom:1.25rem;">
    @foreach(['OD' => 'Ojo Derecho (OD)', 'OS' => 'Ojo Izquierdo (OS)'] as $eyeKey => $eyeTitle)
        @php $ev = $caseEvaluation['eyes'][$eyeKey]; @endphp
        <div class="card" style="padding:0.9rem 1.1rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-weight:700;font-size:0.92rem;"><i class="fa fa-eye"></i> {{ $eyeTitle }}</span>
                <span class="badge" style="border:1px solid var(--border);font-weight:700;">
                    {{ $ev['operational_text'] }} · {{ $ev['trajectory_text'] }}
                </span>
            </div>
            <div style="font-size:0.8rem;color:var(--muted-foreground);margin-top:0.35rem;">
                Línea base OCT: <strong>{{ $ev['baseline_trt'] !== null ? $ev['baseline_trt'].' µm' : 'Sin escaneo' }}</strong> ·
                Cambio Máx (&Delta;TRT): <strong>{{ $ev['max_delta_trt'] !== null ? ($ev['max_delta_trt'] >= 0 ? '+'.$ev['max_delta_trt'] : $ev['max_delta_trt']).' µm' : 'Sin medición' }}</strong>
            </div>
        </div>
    @endforeach
</div>

@if($contextOpen)
<div class="card" style="padding:0.9rem 1.1rem;margin-bottom:1.25rem;border:1px dashed var(--border);">
    <strong><i class="fa fa-list-alt"></i> Contexto del Caso:</strong> Visible como información de apoyo (no altera el estado ocular ni la acción sugerida).
</div>
@endif

{{-- =============================================================================================== --}}
{{-- PESTAÑA 1: FORMULARIO INICIAL DE LA PERSONA + LOS 6 BIOMARCADORES Y PREDICTOR EN VIVO            --}}
{{-- =============================================================================================== --}}
@if($normalizedTab === 'biomarcadores')
<div style="display:grid;grid-template-columns:1.15fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">

    {{-- COLUMNA IZQUIERDA: FORMULARIO DE CAPTURA Y SIMULACIÓN DE LOS 6 BIOMARCADORES --}}
    <div class="card" style="padding:1.35rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.85rem;">
            <h2 style="font-size:1.1rem;font-weight:800;margin:0;">
                <i class="fa fa-sliders"></i> Nutrir Predictor — Los 6 Biomarcadores Clave ({{ $activeCase->case_id }})
            </h2>
            <span class="badge" style="border:1px solid var(--border);font-size:0.74rem;">Evaluación Interactiva</span>
        </div>

        <form method="GET" action="{{ route('admin.sans-monitor.index') }}">
            <input type="hidden" name="tab" value="biomarcadores">
            <input type="hidden" name="case_id" value="{{ $activeCase->case_id }}">
            <input type="hidden" name="eye" value="{{ $selectedEye }}">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.85rem;margin-bottom:1rem;">
                {{-- 1. Espesor medio por OCT --}}
                <div style="padding:0.75rem;border:1px solid var(--border);border-radius:6px;">
                    <label style="font-size:0.76rem;font-weight:700;display:block;margin-bottom:0.25rem;">
                        <i class="fa fa-align-justify"></i> 1. Espesor Medio por OCT (&mu;m)
                    </label>
                    <div style="display:flex;gap:0.4rem;">
                        <input type="number" step="0.1" name="oct_trt_baseline_um" class="form-control"
                               value="{{ $bp['inputs']['oct_trt_baseline_um'] }}" placeholder="Basal (280)">
                        <input type="number" step="0.1" name="oct_trt_current_um" class="form-control"
                               value="{{ $bp['inputs']['oct_trt_current_um'] }}" placeholder="Actual (306)">
                    </div>
                    <div style="font-size:0.72rem;color:var(--muted-foreground);margin-top:0.25rem;">
                        Línea base vs Actual (Disparador &Delta;TRT &ge; +20 &mu;m)
                    </div>
                </div>

                {{-- 2. Longitud Axial del Ojo --}}
                <div style="padding:0.75rem;border:1px solid var(--border);border-radius:6px;">
                    <label style="font-size:0.76rem;font-weight:700;display:block;margin-bottom:0.25rem;">
                        <i class="fa fa-arrows-h"></i> 2. Longitud Axial del Ojo (mm)
                    </label>
                    <input type="number" step="0.01" name="axial_length_mm" class="form-control"
                           value="{{ $bp['inputs']['axial_length_mm'] }}" placeholder="Ej. 23.82 mm">
                    <div style="font-size:0.72rem;color:var(--muted-foreground);margin-top:0.25rem;">
                        Basal ref: {{ $bp['inputs']['axial_baseline_mm'] }} mm (Acortamiento axial en microgravedad)
                    </div>
                </div>

                {{-- 3. Hipermetropía Simulada --}}
                <div style="padding:0.75rem;border:1px solid var(--border);border-radius:6px;">
                    <label style="font-size:0.76rem;font-weight:700;display:block;margin-bottom:0.25rem;">
                        <i class="fa fa-eye"></i> 3. Hipermetropía Simulada (&Delta;SE en D)
                    </label>
                    <input type="number" step="0.25" name="hyperopia_sim_d" class="form-control"
                           value="{{ $bp['inputs']['hyperopia_sim_d'] }}" placeholder="Ej. +0.75 D">
                    <div style="font-size:0.72rem;color:var(--muted-foreground);margin-top:0.25rem;">
                        Desplazamiento refractivo hipermetrópico (+0.50 D a +1.50 D)
                    </div>
                </div>

                {{-- 4. Edema Papilar (Grado Frisén) --}}
                <div style="padding:0.75rem;border:1px solid var(--border);border-radius:6px;">
                    <label style="font-size:0.76rem;font-weight:700;display:block;margin-bottom:0.25rem;">
                        <i class="fa fa-dot-circle-o"></i> 4. Edema Papilar (Escala Frisén 0–5)
                    </label>
                    <select name="papilledema_frisen" class="form-control">
                        @foreach([0 => 'Grado 0 — Normal / Sin edema', 1 => 'Grado 1 — Borramiento nasal leve (Halo C)', 2 => 'Grado 2 — Elevación circunferencial temprana', 3 => 'Grado 3 — Edema moderado de cabeza óptica', 4 => 'Grado 4 — Edema severo con obliteración de copa', 5 => 'Grado 5 — Protrusión crítica de domo óptico'] as $fg => $flabel)
                            <option value="{{ $fg }}" {{ (string)$bp['inputs']['papilledema_frisen'] === (string)$fg ? 'selected' : '' }}>{{ $flabel }}</option>
                        @endforeach
                    </select>
                    <div style="font-size:0.72rem;color:var(--muted-foreground);margin-top:0.25rem;">
                        Objetivo principal de pronóstico neuro-ocular
                    </div>
                </div>

                {{-- 5. Presión Intraocular (PIO) --}}
                <div style="padding:0.75rem;border:1px solid var(--border);border-radius:6px;">
                    <label style="font-size:0.76rem;font-weight:700;display:block;margin-bottom:0.25rem;">
                        <i class="fa fa-tachometer"></i> 5. Presión Intraocular — PIO (mmHg)
                    </label>
                    <input type="number" step="0.5" name="iop_mmhg" class="form-control"
                           value="{{ $bp['inputs']['iop_mmhg'] }}" placeholder="Ej. 18.5 mmHg">
                    <div style="font-size:0.72rem;color:var(--muted-foreground);margin-top:0.25rem;">
                        Tonometría ocular (Rango normal terrestre: 10–21 mmHg)
                    </div>
                </div>

                {{-- 6. Exposición previa a inclinación cefálica de -6° en la Tierra --}}
                <div style="padding:0.75rem;border:1px solid var(--border);border-radius:6px;">
                    <label style="font-size:0.76rem;font-weight:700;display:block;margin-bottom:0.25rem;">
                        <i class="fa fa-level-down"></i> 6. Exposición Previa a -6° en la Tierra
                    </label>
                    <select name="prior_hdt6_exposure" class="form-control">
                        <option value="SI_14_DIAS" {{ str_starts_with((string)$bp['inputs']['prior_hdt6_exposure'], 'SI') ? 'selected' : '' }}>
                            Sí — Análogo -6° HDT (280 &rarr; 304 &rarr; 284 &mu;m)
                        </option>
                        <option value="NO" {{ (string)$bp['inputs']['prior_hdt6_exposure'] === 'NO' ? 'selected' : '' }}>
                            No — Sin antecedente de inclinación -6°
                        </option>
                    </select>
                    <div style="font-size:0.72rem;color:var(--muted-foreground);margin-top:0.25rem;">
                        Reposo en cama con inclinación cefálica (-6° HDT)
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;padding:0.65rem;font-weight:700;">
                <i class="fa fa-bolt"></i> Ejecutar Predictor con estos 6 Biomarcadores
            </button>
        </form>
    </div>

    {{-- COLUMNA DERECHA: SALIDA DEL PREDICTOR DE BIOMARCADORES + REGISTRO RÁPIDO DE PERSONA --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;">
        <div class="card" style="padding:1.35rem;border:1px solid var(--foreground);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.6rem;">
                <h2 style="font-size:1.05rem;font-weight:800;margin:0;">
                    <i class="fa fa-line-chart"></i> Resultado del Predictor por Biomarcadores
                </h2>
                <span class="badge" style="border:1px solid var(--foreground);font-weight:800;padding:0.3rem 0.65rem;">
                    Índice de Señal: {{ $bp['signal_index'] }} / 100
                </span>
            </div>

            <div style="padding:0.75rem;border:1px solid var(--border);border-radius:6px;margin-bottom:0.85rem;">
                <div style="font-size:0.74rem;text-transform:uppercase;color:var(--muted-foreground);">Proyección Basada en Biomarcadores</div>
                <div style="font-size:1rem;font-weight:800;margin-top:0.15rem;">{{ $bp['predicted_trajectory'] }}</div>
                <div style="font-size:0.76rem;color:var(--muted-foreground);margin-top:0.2rem;">
                    {{ $caseEvaluation['future_outlook_banner']['experimental_prognosis_status'] }}
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
                <strong>Señales Detectadas por el Motor:</strong>
                <ul style="margin:0.35rem 0 0;padding-left:1.2rem;">
                    @foreach($bp['signals'] as $sig)
                        <li>{{ $sig }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Formulario Inicial Compacto para Registrar Nueva Persona --}}
        <details class="card" style="padding:1rem;">
            <summary style="cursor:pointer;font-weight:700;font-size:0.9rem;display:flex;justify-content:space-between;align-items:center;">
                <span><i class="fa fa-user-plus"></i> Formulario Inicial: Registrar Nueva Persona / Expediente</span>
                <i class="fa fa-angle-down"></i>
            </summary>
            <form method="POST" action="{{ route('admin.sans-monitor.cases.store') }}" style="margin-top:0.85rem;">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.6rem;margin-bottom:0.6rem;">
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;">ID Persona / Pseudónimo</label>
                        <input type="text" name="case_id" class="form-control" placeholder="CASE-ASTRONAUT-09" required>
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;">Fase Actual</label>
                        <select name="mission_phase" class="form-control">
                            <option value="PRE_VUELO">Antes del vuelo (PRE_VUELO)</option>
                            <option value="EN_VUELO">Durante el vuelo (EN_VUELO)</option>
                            <option value="POSVUELO">Después del vuelo (POSVUELO)</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;">Duración Misión (Días)</label>
                        <input type="number" name="planned_duration_days" class="form-control" value="90">
                        <input type="hidden" name="planned_duration_state" value="VALOR">
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;">Vuelos Previos</label>
                        <select name="previous_flights_state" class="form-control">
                            <option value="VALOR">Con vuelos previos</option>
                            <option value="NO_APLICA">Primer vuelo (No aplica)</option>
                            <option value="DESCONOCIDO">Desconocido</option>
                        </select>
                        <input type="hidden" name="previous_flights" value="1">
                    </div>
                </div>
                <input type="hidden" name="source_type" value="FICTICIO_DEMO">
                <input type="hidden" name="applied_protocol_state" value="DESCONOCIDO">
                <div style="margin-bottom:0.6rem;">
                    <label style="font-size:0.72rem;font-weight:600;">Motivo / Nota Inicial</label>
                    <input type="text" name="reason_for_review" class="form-control" value="Ingreso para predicción por biomarcadores oculares" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    <i class="fa fa-save"></i> Crear Persona y Evaluar
                </button>
            </form>
        </details>
    </div>
</div>
@endif

{{-- =============================================================================================== --}}
{{-- PESTAÑA 2: MEDIR EL TIEMPO: ANTES · DURANTE · DESPUÉS (CORTE DE +30, +60 Y +90 DÍAS)            --}}
{{-- =============================================================================================== --}}
@if($normalizedTab === 'seguimiento')
<div class="card" style="padding:1.35rem;margin-bottom:1.25rem;">
    <h2 style="font-size:1.15rem;font-weight:800;margin:0 0 0.35rem;">
        <i class="fa fa-clock-o"></i> Evolución Longitudinal del Ojo: Antes · Durante · Después (+30, +60 y +90 Días)
    </h2>
    <p style="font-size:0.84rem;color:var(--muted-foreground);margin:0 0 1.1rem;">
        Compara cómo estaba el ojo <strong>Antes</strong> del vuelo/exposición, cómo cambió <strong>Durante</strong> (Días 10, 15, 30) y el corte exacto <strong>Después del vuelo a +30, +60 y +90 días</strong>.
    </p>

    {{-- 3 Bloques Temporales Claros: ANTES -> DURANTE -> DESPUÉS --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.25rem;">
        <div style="padding:1rem;border:1px solid var(--border);border-radius:6px;">
            <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;color:var(--muted-foreground);">1. ANTES DEL VUELO (Línea Base)</div>
            <div style="font-size:1.4rem;font-weight:800;margin:0.3rem 0;">
                {{ $caseEvaluation['eyes']['OD']['baseline_trt'] !== null ? $caseEvaluation['eyes']['OD']['baseline_trt'].' µm' : 'Sin línea base' }}
            </div>
            <div style="font-size:0.8rem;color:var(--muted-foreground);">
                Referencia pre-vuelo (Día 0) para medir cualquier desplazamiento posterior.
            </div>
        </div>

        <div style="padding:1rem;border:1px solid var(--border);border-radius:6px;">
            <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;color:var(--muted-foreground);">2. DURANTE EL VUELO (Días 10 &rarr; 15 &rarr; 30)</div>
            <div style="font-size:1.4rem;font-weight:800;margin:0.3rem 0;">
                @if($caseEvaluation['eyes']['OD']['max_delta_trt'] !== null)
                    {{ $caseEvaluation['eyes']['OD']['latest_trt'] }} µm (&Delta; +{{ $caseEvaluation['eyes']['OD']['max_delta_trt'] }} µm)
                @else
                    Sin escaneo en órbita
                @endif
            </div>
            <div style="font-size:0.8rem;color:var(--muted-foreground);">
                En el caso en progresión: +8 &mu;m (Día 10) &rarr; +14 &mu;m (Día 15) &rarr; +26 &mu;m (Día 30).
            </div>
        </div>

        <div style="padding:1rem;border:1px solid var(--border);border-radius:6px;">
            <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;color:var(--muted-foreground);">3. EXPOSICIÓN PREVIA -6° EN TIERRA</div>
            <div style="font-size:1.4rem;font-weight:800;margin:0.3rem 0;">
                280 &rarr; 304 &rarr; 284 &mu;m
            </div>
            <div style="font-size:0.8rem;color:var(--muted-foreground);">
                Cambio inmediato de <strong>+24 &mu;m</strong> y descenso a <strong>+4 &mu;m</strong> a los +30 días de finalizar la inclinación.
            </div>
        </div>
    </div>

    {{-- CORTE POSVUELO A +30, +60 Y +90 DÍAS --}}
    <h3 style="font-size:1rem;font-weight:800;margin:0 0 0.75rem;">
        <i class="fa fa-calendar-check-o"></i> Corte Después del Vuelo: Controles a +30, +60 y +90 Días
    </h3>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
        @foreach([30, 60, 90] as $milestone)
            @php $visit = $followupVisits->firstWhere('target_milestone_day', $milestone); @endphp
            <div style="padding:1.1rem;border:1px solid var(--foreground);border-radius:6px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.4rem;">
                    <span style="font-size:1.05rem;font-weight:800;">Corte +{{ $milestone }} Días</span>
                    <span class="badge" style="border:1px solid var(--border);font-weight:700;">
                        {{ $visit && $visit->status === 'SIN_MEDICION' ? 'Sin medición' : ($visit->status ?? 'Pendiente') }}
                    </span>
                </div>
                @if($visit)
                    <div style="font-size:0.82rem;margin-bottom:0.25rem;">
                        <strong>Ventana:</strong> {{ $visit->window_label }}
                    </div>
                    <div style="font-size:0.82rem;margin-bottom:0.25rem;">
                        <strong>Fecha control:</strong> {{ $visit->actual_date ?? 'Sin medición (No se interpola)' }}
                    </div>
                    <div style="font-size:0.8rem;color:var(--muted-foreground);">
                        Estado: <strong>{{ $visit->status === 'SIN_MEDICION' ? 'Sin medición' : $visit->finding_summary_state }}</strong>
                    </div>
                @else
                    <div style="font-size:0.82rem;color:var(--muted-foreground);">
                        Seguimiento pendiente para el corte de +{{ $milestone }} días.
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- =============================================================================================== --}}
{{-- PESTAÑA 3: LOS 3 ESCENARIOS MODELOS (1. ESTABLE · 2. EMPEORA · 3. NO SE ESCANEA)                --}}
{{-- =============================================================================================== --}}
@if($normalizedTab === 'escenarios')
<div class="card" style="padding:1.35rem;margin-bottom:1.25rem;">
    <h2 style="font-size:1.15rem;font-weight:800;margin:0 0 0.35rem;">
        <i class="fa fa-cubes"></i> Comparador de los 3 Escenarios Modelos: Estable · Empeora · No se Escanea
    </h2>
    <p style="font-size:0.84rem;color:var(--muted-foreground);margin:0 0 1rem;">
        Haz clic en cualquiera de los 3 escenarios para cargar instantáneamente sus biomarcadores, su curva temporal y su representación anatómica.
    </p>

    {{-- TARJETAS DE 1 CLIC PARA LOS 3 ESCENARIOS MODELOS --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.25rem;">
        {{-- Escenario 1: Estable --}}
        <a href="{{ route('admin.sans-monitor.index', ['case_id' => 'CASE-STABLE-01', 'tab' => 'escenarios']) }}"
           style="text-decoration:none;color:inherit;padding:1.15rem;border:{{ $activeCase->case_id === 'CASE-STABLE-01' ? '2px solid var(--foreground)' : '1px solid var(--border)' }};border-radius:6px;display:block;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.4rem;">
                <span style="font-size:1rem;font-weight:800;"><i class="fa fa-check-circle"></i> 1. Escenario Estable</span>
                <span class="badge" style="border:1px solid var(--border);">Listo para revisión</span>
            </div>
            <div style="font-size:0.82rem;color:var(--muted-foreground);">
                <strong>CASE-STABLE-01:</strong> OCT basal 278 &mu;m &rarr; 284 &mu;m (&Delta;TRT +6 &mu;m), Frisén 0/5, longitud axial estable (24.08 mm).
            </div>
        </a>

        {{-- Escenario 2: Empeora --}}
        <a href="{{ route('admin.sans-monitor.index', ['case_id' => 'CASE-PROG-02', 'tab' => 'escenarios']) }}"
           style="text-decoration:none;color:inherit;padding:1.15rem;border:{{ $activeCase->case_id === 'CASE-PROG-02' ? '2px solid var(--foreground)' : '1px solid var(--border)' }};border-radius:6px;display:block;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.4rem;">
                <span style="font-size:1rem;font-weight:800;"><i class="fa fa-exclamation-triangle"></i> 2. Escenario que Empeora</span>
                <span class="badge" style="border:1px solid var(--border);">Priorizar revisión</span>
            </div>
            <div style="font-size:0.82rem;color:var(--muted-foreground);">
                <strong>CASE-PROG-02:</strong> OCT basal 280 &mu;m &rarr; 306 &mu;m (&Delta;TRT +26 &mu;m al día 30), hipermetropía +0.75 D, edema Frisén 2/5.
            </div>
        </a>

        {{-- Escenario 3: No se escanea --}}
        <a href="{{ route('admin.sans-monitor.index', ['case_id' => 'CASE-NOSCAN-03A', 'tab' => 'escenarios']) }}"
           style="text-decoration:none;color:inherit;padding:1.15rem;border:{{ $activeCase->case_id === 'CASE-NOSCAN-03A' ? '2px solid var(--foreground)' : '1px solid var(--border)' }};border-radius:6px;display:block;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.4rem;">
                <span style="font-size:1rem;font-weight:800;"><i class="fa fa-question-circle"></i> 3. Escenario No se Escanea</span>
                <span class="badge" style="border:1px solid var(--border);">Faltan datos</span>
            </div>
            <div style="font-size:0.82rem;color:var(--muted-foreground);">
                <strong>CASE-NOSCAN-03A:</strong> Sin escaneo OCT registrado (cuenta con PIO de 16 mmHg, pero nunca se asume como estable por falta de OCT).
            </div>
        </a>
    </div>

    {{-- Vistas Esquemáticas Anatómicas del Escenario Activo --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
        <div style="border:1px solid var(--border);border-radius:6px;padding:1rem;">
            <h3 style="font-size:0.9rem;font-weight:700;margin:0 0 0.5rem;">
                <i class="fa fa-circle-o"></i> 1. Longitud Axial y Polo Posterior (Sagital)
            </h3>
            <svg viewBox="0 0 260 140" style="width:100%;height:130px;background:#09090b;border-radius:4px;">
                <ellipse cx="125" cy="70" rx="{{ $activeCase->scenario_archetype === 'EMPEORA' ? 50 : 56 }}" ry="52" fill="none" stroke="#ffffff" stroke-width="2"/>
                <line x1="68" y1="70" x2="182" y2="70" stroke="#a1a1aa" stroke-dasharray="4 3" stroke-width="1.5"/>
                <rect x="175" y="57" width="35" height="26" fill="none" stroke="#ffffff" stroke-width="1.5"/>
                <text x="14" y="22" fill="#ffffff" font-size="10" font-family="monospace">LONGITUD AXIAL · {{ $activeCase->case_id }}</text>
                <text x="14" y="128" fill="#a1a1aa" font-size="9" font-family="monospace">Hipermetropía simulada: +{{ $bp['inputs']['hyperopia_sim_d'] }} D</text>
            </svg>
        </div>

        <div style="border:1px solid var(--border);border-radius:6px;padding:1rem;">
            <h3 style="font-size:0.9rem;font-weight:700;margin:0 0 0.5rem;">
                <i class="fa fa-dot-circle-o"></i> 2. Edema Papilar y Disco Óptico (Fondo)
            </h3>
            <svg viewBox="0 0 260 140" style="width:100%;height:130px;background:#09090b;border-radius:4px;">
                <circle cx="130" cy="70" r="52" fill="none" stroke="#ffffff" stroke-width="1.5"/>
                <circle cx="155" cy="70" r="{{ $activeCase->scenario_archetype === 'EMPEORA' ? 19 : 12 }}" fill="none" stroke="#ffffff" stroke-width="2" stroke-dasharray="{{ $activeCase->scenario_archetype === 'EMPEORA' ? '3 2' : 'none' }}"/>
                <text x="14" y="22" fill="#ffffff" font-size="10" font-family="monospace">EDEMA PAPILAR (FRISÉN {{ $bp['inputs']['papilledema_frisen'] ?? 'N/E' }})</text>
                <text x="14" y="128" fill="#a1a1aa" font-size="9" font-family="monospace">PIO: {{ $bp['inputs']['iop_mmhg'] }} mmHg</text>
            </svg>
        </div>

        <div style="border:1px solid var(--border);border-radius:6px;padding:1rem;">
            <h3 style="font-size:0.9rem;font-weight:700;margin:0 0 0.5rem;">
                <i class="fa fa-align-justify"></i> 3. Espesor Medio por OCT (Capas)
            </h3>
            @if($caseEvaluation['eyes'][$simEye]['coverage_code'] === 'SIN_ESCANEOS_PREVIOS')
                <div style="height:130px;display:flex;align-items:center;justify-content:center;background:#09090b;color:#ffffff;padding:1rem;text-align:center;font-size:0.82rem;border-radius:4px;">
                    Escenario «No se escanea»: OCT no evaluable (No se dibuja como normal ni se clasifica como estable).
                </div>
            @else
                <svg viewBox="0 0 260 140" style="width:100%;height:130px;background:#09090b;border-radius:4px;">
                    <path d="M 15 55 Q 130 {{ $activeCase->scenario_archetype === 'EMPEORA' ? 28 : 48 }} 245 55" fill="none" stroke="#ffffff" stroke-width="2"/>
                    <path d="M 15 80 Q 130 78 245 80" fill="none" stroke="#a1a1aa" stroke-width="1.5" stroke-dasharray="4 2"/>
                    <path d="M 15 106 Q 130 106 245 106" fill="none" stroke="#ffffff" stroke-width="2"/>
                    <text x="14" y="22" fill="#ffffff" font-size="10" font-family="monospace">ESPESOR OCT: {{ $bp['inputs']['oct_trt_current_um'] }} µm</text>
                    <text x="14" y="128" fill="#a1a1aa" font-size="9" font-family="monospace">Cambio vs basal: {{ $bp['delta_trt_um'] !== null ? '+'.$bp['delta_trt_um'].' µm' : 'N/A' }}</text>
                </svg>
            @endif
        </div>
    </div>
</div>
@endif
@endsection
