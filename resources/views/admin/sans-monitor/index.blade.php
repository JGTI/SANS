@extends('layouts.app')

@section('title', 'SANS Monitor — Revisión Longitudinal Trazable')

@section('content')
<div class="page-header" style="margin-bottom:1.25rem;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
        <div>
            <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--muted-foreground);margin-bottom:0.25rem;">
                <i class="fa fa-eye"></i> SPACE VISION · SANS MONITOR · ESPECIFICACIÓN CANÓNICA P0/P1/P2
            </div>
            <h1 style="font-size:1.5rem;font-weight:700;margin:0;">
                SANS Monitor — Línea de Tiempo Ocular y Trazabilidad de Protocolos
            </h1>
            <p style="margin:0.35rem 0 0;color:var(--muted-foreground);font-size:0.875rem;">
                Reúne las revisiones oculares en una sola línea de tiempo (Antes, Durante días 10 &rarr; 15 &rarr; 30, Fin +0 y controles posteriores +30, +60 y +90). Prepara la vigilancia sin emitir un veredicto automático de aptitud.
            </p>
        </div>
        <div style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap;">
            <span class="badge" style="padding:0.4rem 0.75rem;border:1px solid var(--border);font-weight:600;">
                <i class="fa fa-shield"></i> Salida Operativa ({{ $activeCase->case_id }}):
                <strong>{{ $caseEvaluation['case_operational_text'] }}</strong>
            </span>
            <span class="badge" style="padding:0.4rem 0.75rem;border:1px solid var(--border);">
                <i class="fa fa-user-md"></i> Dictamen Médico: <strong>{{ $caseEvaluation['fitness_display'] }}</strong>
            </span>
        </div>
    </div>
</div>

@if(session('status'))
    <div class="card" style="padding:0.85rem 1rem;margin-bottom:1rem;border-left:4px solid var(--foreground);">
        <i class="fa fa-check-circle"></i> {{ session('status') }}
    </div>
@endif

@if(session('import_report'))
    @php $rep = session('import_report'); @endphp
    <div class="card" style="padding:0.85rem 1rem;margin-bottom:1rem;border-left:4px solid var(--foreground);">
        <div><strong><i class="fa fa-upload"></i> Reporte de Importación por Fila:</strong> Aceptadas: {{ $rep['accepted_count'] }} · Observadas/Rechazadas: {{ $rep['observed_count'] }}</div>
        @foreach($rep['observed_rows'] as $warn)
            <div style="font-size:0.82rem;margin-top:0.25rem;color:var(--muted-foreground);">
                <i class="fa fa-exclamation-triangle"></i> [{{ $warn['status'] }}] {{ $warn['reason'] }}
            </div>
        @endforeach
    </div>
@endif

{{-- Barra Global de Estado Compartido entre Pestañas (RF-31, RNF-03) --}}
<div class="card" style="padding:1rem;margin-bottom:1rem;">
    <form method="GET" action="{{ route('admin.sans-monitor.index') }}" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:0.75rem;align-items:end;">
        <input type="hidden" name="tab" value="{{ $activeTab }}">
        <div>
            <label for="global-case-select" style="display:block;font-size:0.75rem;font-weight:600;margin-bottom:0.25rem;">
                <i class="fa fa-folder-open"></i> Ficha / Participante
            </label>
            <select id="global-case-select" name="case_id" class="form-control" onchange="this.form.submit()">
                @foreach($cases as $c)
                    <option value="{{ $c->case_id }}" {{ $c->case_id === $activeCase->case_id ? 'selected' : '' }}>
                        {{ $c->case_id }} ({{ $c->scenario_archetype }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="global-eye-select" style="display:block;font-size:0.75rem;font-weight:600;margin-bottom:0.25rem;">
                <i class="fa fa-eye"></i> Selección de Ojo
            </label>
            <select id="global-eye-select" name="eye" class="form-control" onchange="this.form.submit()">
                <option value="OD" {{ $selectedEye === 'OD' ? 'selected' : '' }}>Ojo Derecho (OD)</option>
                <option value="OS" {{ $selectedEye === 'OS' ? 'selected' : '' }}>Ojo Izquierdo (OS)</option>
                <option value="BILATERAL" {{ $selectedEye === 'BILATERAL' ? 'selected' : '' }}>Comparación Bilateral (OD vs OS)</option>
            </select>
        </div>

        <div>
            <label for="global-day-select" style="display:block;font-size:0.75rem;font-weight:600;margin-bottom:0.25rem;">
                <i class="fa fa-calendar"></i> Hito / Día (10 &rarr; 15 &rarr; 30)
            </label>
            <select id="global-day-select" name="day" class="form-control" onchange="this.form.submit()">
                @foreach([0, 10, 15, 20, 30, 60, 90, 150, 180] as $d)
                    <option value="{{ $d }}" {{ (int)$selectedDay === $d ? 'selected' : '' }}>
                        Día {{ $d }} {{ in_array($d, [10,15,30]) ? '(Hito Demo)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="global-range-select" style="display:block;font-size:0.75rem;font-weight:600;margin-bottom:0.25rem;">
                <i class="fa fa-arrows-h"></i> Rango Temporal
            </label>
            <select id="global-range-select" name="range" class="form-control" onchange="this.form.submit()">
                <option value="0-30" {{ $selectedRange === '0-30' ? 'selected' : '' }}>0–30 días</option>
                <option value="0-90" {{ $selectedRange === '0-90' ? 'selected' : '' }}>0–90 días</option>
                <option value="0-150" {{ $selectedRange === '0-150' ? 'selected' : '' }}>0–150 días</option>
            </select>
        </div>

        <div>
            <label for="global-mode-select" style="display:block;font-size:0.75rem;font-weight:600;margin-bottom:0.25rem;">
                <i class="fa fa-sliders"></i> Modo Temporal (RF-21)
            </label>
            <select id="global-mode-select" name="mode" class="form-control" onchange="this.form.submit()">
                <option value="HISTORICO" {{ $timelineMode === 'HISTORICO' ? 'selected' : '' }}>Reproducción Histórica</option>
                <option value="SIMULACION" {{ $timelineMode === 'SIMULACION' ? 'selected' : '' }}>Simulación Ilustrativa</option>
            </select>
        </div>

        <div>
            <label for="global-context-select" style="display:block;font-size:0.75rem;font-weight:600;margin-bottom:0.25rem;">
                <i class="fa fa-layers"></i> Panel Contexto (5 Dominios)
            </label>
            <select id="global-context-select" name="context" class="form-control" onchange="this.form.submit()">
                <option value="0" {{ !$contextOpen ? 'selected' : '' }}>Plegado (Oculto)</option>
                <option value="1" {{ $contextOpen ? 'selected' : '' }}>Desplegado (Visible)</option>
            </select>
        </div>
    </form>
</div>

{{-- Navegación de las 5 Pestañas Oficiales de SANS Monitor (conservando estado en query string RF-31) --}}
@php
    $sharedParams = [
        'case_id' => $activeCase->case_id,
        'eye' => $selectedEye,
        'day' => $selectedDay,
        'range' => $selectedRange,
        'mode' => $timelineMode,
        'context' => $contextOpen ? '1' : '0',
    ];
@endphp
<div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1.25rem;border-bottom:1px solid var(--border);padding-bottom:0.75rem;" role="tablist" aria-label="Pestañas de SANS Monitor">
    <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'ficha'])) }}"
       class="btn {{ $activeTab === 'ficha' ? 'btn-primary' : 'btn-outline' }}" role="tab">
        <i class="fa fa-file-text-o"></i> 1. Ficha y protocolos
    </a>
    <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'seguimiento'])) }}"
       class="btn {{ $activeTab === 'seguimiento' ? 'btn-primary' : 'btn-outline' }}" role="tab">
        <i class="fa fa-clock-o"></i> 2. Seguimiento del ojo (+30/+60/+90)
    </a>
    <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'simulador'])) }}"
       class="btn {{ $activeTab === 'simulador' ? 'btn-primary' : 'btn-outline' }}" role="tab">
        <i class="fa fa-eye"></i> 3. Simulador
    </a>
    <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'estudios'])) }}"
       class="btn {{ $activeTab === 'estudios' ? 'btn-primary' : 'btn-outline' }}" role="tab">
        <i class="fa fa-bar-chart"></i> 4. Estudios y tendencias
    </a>
    <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'inclinacion'])) }}"
       class="btn {{ $activeTab === 'inclinacion' ? 'btn-primary' : 'btn-outline' }}" role="tab">
        <i class="fa fa-level-down"></i> 5. Inclinación y seguimiento
    </a>
</div>

{{-- Resumen Permanente por Ojo y Separación de Completitud vs Señales Oculares (RF-10, AT-05, AT-08) --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1rem;margin-bottom:1.25rem;">
    @foreach(['OD' => 'Ojo Derecho (OD)', 'OS' => 'Ojo Izquierdo (OS)'] as $eyeKey => $eyeTitle)
        @php $ev = $caseEvaluation['eyes'][$eyeKey]; @endphp
        <div class="card" style="padding:1rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
                <h3 style="font-size:1rem;font-weight:700;margin:0;">
                    <i class="fa fa-eye"></i> {{ $eyeTitle }}
                </h3>
                <span class="badge" style="border:1px solid var(--border);padding:0.25rem 0.6rem;font-weight:700;">
                    {{ $ev['operational_text'] }} · {{ $ev['trajectory_text'] }}
                </span>
            </div>
            <div style="font-size:0.82rem;color:var(--muted-foreground);margin-bottom:0.5rem;">
                Cobertura: <code>{{ $ev['coverage_code'] }}</code> ·
                Línea Base TRT: <strong>{{ $ev['baseline_trt'] !== null ? $ev['baseline_trt'].' µm' : 'Sin línea base' }}</strong> ·
                Máx &Delta;TRT: <strong>{{ $ev['max_delta_trt'] !== null ? ($ev['max_delta_trt'] >= 0 ? '+'.$ev['max_delta_trt'] : $ev['max_delta_trt']).' µm' : 'No calculable' }}</strong>
            </div>
            <ul style="margin:0;padding-left:1.2rem;font-size:0.82rem;">
                @foreach($ev['reasons'] as $r)
                    <li>{{ $r }}</li>
                @endforeach
            </ul>
        </div>
    @endforeach

    <div class="card" style="padding:1rem;">
        <h3 style="font-size:1rem;font-weight:700;margin:0 0 0.5rem;">
            <i class="fa fa-info-circle"></i> Alcance Preventivo y Afectaciones Futuras (RF-10 a RF-12)
        </h3>
        <div style="font-size:0.82rem;margin-bottom:0.35rem;">
            <strong>Completitud de Ficha:</strong> <code>{{ $caseEvaluation['completeness_status'] }}</code> (Separada de señales oculares)
        </div>
        <div style="font-size:0.82rem;margin-bottom:0.35rem;">
            <strong>Dictamen de Aptitud (RF-11):</strong> <span>{{ $caseEvaluation['fitness_display'] }}</span>
        </div>
        <div style="font-size:0.8rem;color:var(--muted-foreground);">
            <strong>{{ $caseEvaluation['future_outlook_banner']['title'] }}:</strong>
            {{ $caseEvaluation['future_outlook_banner']['experimental_prognosis_status'] }}
        </div>
    </div>
</div>

{{-- Panel Plegable Secundario: «Contexto del Caso» en 5 Dominios (RF-35, RF-36, AT-18) --}}
@if($contextOpen)
<div class="card" style="padding:1rem;margin-bottom:1.25rem;border:1px dashed var(--border);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">
        <h3 style="font-size:1rem;font-weight:700;margin:0;">
            <i class="fa fa-list-alt"></i> Contexto del Caso (5 Dominios Secundarios — No modifica el estado ocular ni la acción sugerida)
        </h3>
        <span style="font-size:0.75rem;color:var(--muted-foreground);">RF-36 · AT-18 Verificado</span>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:0.75rem;">
        @foreach(['NEUROLOGICO' => 'Neurológico', 'METABOLICO' => 'Metabólico / Nutricional', 'CARDIOVASCULAR' => 'Cardiovascular', 'AMBIENTAL' => 'Ambiental', 'CONDUCTUAL' => 'Conductual'] as $domCode => $domLabel)
            @php $domItems = $contextRecords->get($domCode, collect()); @endphp
            <div style="padding:0.75rem;border:1px solid var(--border);border-radius:6px;">
                <div style="font-weight:700;font-size:0.85rem;margin-bottom:0.35rem;">{{ $domLabel }}</div>
                @if($domItems->isEmpty())
                    <div style="font-size:0.8rem;color:var(--muted-foreground);">No medido / No disponible (No genera alerta)</div>
                @else
                    @foreach($domItems as $item)
                        <div style="font-size:0.8rem;margin-bottom:0.25rem;">
                            <strong>{{ $item->variable_name }}:</strong> {{ $item->value_or_category }} {{ $item->unit }}
                            <span style="font-size:0.72rem;color:var(--muted-foreground);">[{{ $item->availability_status }} · {{ $item->recorded_window }}]</span>
                        </div>
                        <div style="font-size:0.74rem;color:var(--muted-foreground);">{{ $item->method_or_posture }}</div>
                    @endforeach
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- ===================================================================== --}}
{{-- PESTAÑA 1: FICHA Y PROTOCOLOS (RF-01 a RF-09, AT-01, AT-02, AT-09)    --}}
{{-- ===================================================================== --}}
@if($activeTab === 'ficha')
<div style="display:grid;grid-template-columns:1fr 1.35fr;gap:1.25rem;">
    <div>
        <div class="card" style="padding:1.25rem;margin-bottom:1.25rem;">
            <h2 style="font-size:1.1rem;font-weight:700;margin:0 0 0.75rem;">
                <i class="fa fa-id-card-o"></i> Ficha Activa: {{ $activeCase->case_id }}
            </h2>
            <table class="table" style="font-size:0.85rem;margin-bottom:0;">
                <tbody>
                    <tr>
                        <th>ID Pseudónimo / Ficticio</th>
                        <td><code>{{ $activeCase->case_id }}</code></td>
                    </tr>
                    <tr>
                        <th>Misión / Conjunto de Datos</th>
                        <td>{{ $activeCase->mission_id ?? 'No aplica' }} · <code>{{ $activeCase->source_type }}</code></td>
                    </tr>
                    <tr>
                        <th>Fase de Misión</th>
                        <td><code>{{ $activeCase->mission_phase }}</code></td>
                    </tr>
                    <tr>
                        <th>Duración Prevista</th>
                        <td>
                            @if($activeCase->planned_duration_state === 'VALOR')
                                {{ $activeCase->planned_duration_days }} días
                            @else
                                <code>{{ $activeCase->planned_duration_state }}</code>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Historial de Vuelos Previos</th>
                        <td>
                            @if($activeCase->previous_flights_state === 'VALOR')
                                {{ $activeCase->previous_flights }} vuelo(s)
                            @else
                                <code>{{ $activeCase->previous_flights_state }}</code>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Motivo de Revisión</th>
                        <td>{{ $activeCase->reason_for_review }}</td>
                    </tr>
                    <tr>
                        <th>Estado de Línea Base</th>
                        <td><code>{{ $activeCase->baseline_status }}</code></td>
                    </tr>
                    <tr>
                        <th>Protocolo Aplicado Previamente</th>
                        <td>
                            @if($activeCase->applied_protocol_state === 'CONOCIDO' && $activeCase->applied_protocol_version_id)
                                <code>{{ $activeCase->applied_protocol_version_id }}</code>
                            @else
                                <code>{{ $activeCase->applied_protocol_state }}</code>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Formulario para Crear Ficha Nueva sin exigir OCT ni protocolo previo (RF-01, AT-01) --}}
        <div class="card" style="padding:1.25rem;">
            <h3 style="font-size:1rem;font-weight:700;margin:0 0 0.75rem;">
                <i class="fa fa-plus-square-o"></i> Abrir Nueva Ficha (RF-01 · Distingue «Desconocido» de «No aplica»)
            </h3>
            <form method="POST" action="{{ route('admin.sans-monitor.cases.store') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:0.75rem;">
                    <div>
                        <label style="font-size:0.75rem;font-weight:600;">ID Pseudónimo (Obligatorio)</label>
                        <input type="text" name="case_id" class="form-control" placeholder="CASE-DEMO-99" required>
                    </div>
                    <div>
                        <label style="font-size:0.75rem;font-weight:600;">ID Misión (Opcional)</label>
                        <input type="text" name="mission_id" class="form-control" placeholder="MSN-DEMO-99">
                    </div>
                    <div>
                        <label style="font-size:0.75rem;font-weight:600;">Conjunto de Datos</label>
                        <select name="source_type" class="form-control">
                            <option value="FICTICIO_DEMO">Ficticio (Demo P0)</option>
                            <option value="ANALOGO_TERRESTRE">Análogo Terrestre (-6° HDT)</option>
                            <option value="ORBITAL_AUTORIZADO">Orbital Autorizado (P1)</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:0.75rem;font-weight:600;">Fase de Misión</label>
                        <select name="mission_phase" class="form-control">
                            <option value="PRE_VUELO">PRE_VUELO</option>
                            <option value="EN_VUELO">EN_VUELO</option>
                            <option value="POSVUELO">POSVUELO</option>
                            <option value="POSEXPOSICION">POSEXPOSICION</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:0.75rem;font-weight:600;">Estado Duración Prevista</label>
                        <select name="planned_duration_state" class="form-control">
                            <option value="VALOR">Valor conocido</option>
                            <option value="DESCONOCIDO">Desconocido</option>
                            <option value="NO_APLICA">No aplica</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:0.75rem;font-weight:600;">Días Duración Prevista</label>
                        <input type="number" name="planned_duration_days" class="form-control" value="90">
                    </div>
                    <div>
                        <label style="font-size:0.75rem;font-weight:600;">Estado Vuelos Previos</label>
                        <select name="previous_flights_state" class="form-control">
                            <option value="VALOR">Valor conocido</option>
                            <option value="DESCONOCIDO">Desconocido</option>
                            <option value="NO_APLICA">No aplica</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:0.75rem;font-weight:600;">Protocolo Previo</label>
                        <select name="applied_protocol_state" class="form-control">
                            <option value="DESCONOCIDO">Desconocido</option>
                            <option value="NO_APLICA">No aplica</option>
                            <option value="CONOCIDO">Conocido</option>
                        </select>
                    </div>
                </div>
                <div style="margin-bottom:0.75rem;">
                    <label style="font-size:0.75rem;font-weight:600;">Motivo de Revisión (Obligatorio)</label>
                    <input type="text" name="reason_for_review" class="form-control" value="Apertura de ficha sin OCT previa para preparación de vigilancia" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Guardar Ficha (Sin exigir OCT ni emitir dictamen)
                </button>
            </form>
        </div>
    </div>

    {{-- Columna Derecha: Catálogo Curado y Búsqueda Separada de Protocolos (RF-05 a RF-09, AT-02) --}}
    <div>
        <div class="card" style="padding:1.25rem;margin-bottom:1.25rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">
                <h2 style="font-size:1.1rem;font-weight:700;margin:0;">
                    <i class="fa fa-book"></i> Catálogo y Búsqueda de Protocolos (RF-05 a RF-09)
                </h2>
                <span class="badge" style="border:1px solid var(--border);font-size:0.75rem;">
                    {{ $catalogSearch['search_scope_banner'] }}
                </span>
            </div>

            {{-- Bloque 1: Protocolo Previamente Aplicado --}}
            <div style="padding:0.85rem;border:1px solid var(--border);border-radius:6px;margin-bottom:1rem;">
                <h3 style="font-size:0.9rem;font-weight:700;margin:0 0 0.4rem;">
                    <i class="fa fa-history"></i> 1. Protocolo Previamente Aplicado a la Ficha
                </h3>
                @if($catalogSearch['previously_applied'])
                    @php $prev = $catalogSearch['previously_applied']; @endphp
                    <div style="font-size:0.85rem;">
                        <strong>{{ $prev->title }}</strong> (<code>{{ $prev->version_id }}</code> · {{ $prev->document_type }})
                        <div style="font-size:0.78rem;color:var(--muted-foreground);">
                            Emisor: {{ $prev->issuer }} · Publicación: {{ $prev->publication_date }} ·
                            <a href="{{ $prev->source_url }}" target="_blank" rel="noopener noreferrer">Enlace / DOI</a>
                        </div>
                    </div>
                @else
                    <div style="font-size:0.82rem;color:var(--muted-foreground);">
                        Sin protocolo previo registrado ({{ $activeCase->applied_protocol_state }}). No se inventa un protocolo previo.
                    </div>
                @endif

                {{-- Filtro separado de versiones posteriores verificadas (RF-06, AT-02) --}}
                @if(!empty($catalogSearch['newer_verified_versions']))
                    <div style="margin-top:0.65rem;padding-top:0.65rem;border-top:1px dashed var(--border);">
                        <div style="font-size:0.8rem;font-weight:700;">
                            <i class="fa fa-code-fork"></i> Versión Posterior Verificada Disponible (Filtro Separado — No sustituye la selección previa sin decisión del revisor):
                        </div>
                        @foreach($catalogSearch['newer_verified_versions'] as $newer)
                            <div style="font-size:0.8rem;margin-top:0.25rem;">
                                <code>{{ $newer->version_id }}</code> — {{ $newer->title }} (Reemplaza de forma verificada a <code>{{ $newer->supersedes_version_id }}</code>)
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Bloque 2: Lista Separada de Referencias Pertinentes al Seguimiento Posterior (RF-06, RF-07) --}}
            <h3 style="font-size:0.95rem;font-weight:700;margin:0 0 0.5rem;">
                <i class="fa fa-search"></i> 2. Entradas Pertinentes al Seguimiento Posterior (Ordenadas por Aplicabilidad y Fecha)
            </h3>
            @if($catalogSearch['empty_catalog_message'])
                <div style="padding:0.75rem;border:1px solid var(--border);">
                    {{ $catalogSearch['empty_catalog_message'] }}
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:0.75rem;">
                    @foreach($catalogSearch['followup_matches'] as $match)
                        @php $p = $match['protocol']; @endphp
                        <div style="padding:0.85rem;border:1px solid var(--border);border-radius:6px;">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.5rem;">
                                <div>
                                    <strong>{{ $p->title }}</strong>
                                    <div style="font-size:0.76rem;color:var(--muted-foreground);">
                                        Versión: <code>{{ $p->version_id }}</code> · Emisor: {{ $p->issuer }} · Fecha: {{ $p->publication_date }}
                                    </div>
                                </div>
                                <span class="badge" style="border:1px solid var(--border);font-size:0.72rem;">
                                    {{ $p->document_type }}
                                </span>
                            </div>
                            <div style="font-size:0.78rem;margin-top:0.45rem;">
                                <strong>Criterios cumplidos:</strong> {{ implode(' · ', $match['matched_criteria']) ?: 'Ninguno' }}
                            </div>
                            @if(!empty($match['missing_criteria']))
                                <div style="font-size:0.78rem;color:var(--muted-foreground);margin-top:0.2rem;">
                                    <strong>Requisitos pendientes de verificar:</strong> {{ implode(' · ', $match['missing_criteria']) }}
                                </div>
                            @endif
                            <div style="font-size:0.75rem;color:var(--muted-foreground);margin-top:0.3rem;">
                                {{ $p->notes }} · <a href="{{ $p->source_url }}" target="_blank" rel="noopener noreferrer">Consultar fuente oficial</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endif

{{-- ================================================================================= --}}
{{-- PESTAÑA 2: SEGUIMIENTO DEL OJO (+30, +60 y +90 DÍAS) (RF-13, RF-31 a RF-34, AT-15, AT-16) --}}
{{-- ================================================================================= --}}
@if($activeTab === 'seguimiento')
<div class="card" style="padding:1.25rem;margin-bottom:1.25rem;">
    <h2 style="font-size:1.1rem;font-weight:700;margin:0 0 0.5rem;">
        <i class="fa fa-calendar-check-o"></i> Seguimiento Cronológico por Fase: Antes, Durante (10 &rarr; 15 &rarr; 30), Fin (+0) y Después (+30, +60 y +90 días)
    </h2>
    <p style="font-size:0.82rem;color:var(--muted-foreground);margin:0 0 1rem;">
        «Día 30 de vuelo» y «+30 días tras aterrizar» se etiquetan como hitos diferentes (RF-31 · AT-15). La pantalla informa hasta el último control existente sin extrapolar ni interpolar visitas faltantes.
    </p>

    <table class="table" style="font-size:0.84rem;">
        <thead>
            <tr>
                <th>ID Observación</th>
                <th>Ojo</th>
                <th>Fase / Hito Distinto</th>
                <th>Fecha Toma (`observed_at`)</th>
                <th>Disponible (`available_day`)</th>
                <th>Métrica / Modalidad</th>
                <th>Valor Observado</th>
                <th>Referencia Línea Base</th>
                <th>Tipo de Dato</th>
            </tr>
        </thead>
        <tbody>
            @foreach($missionObservations as $obs)
                @if($selectedEye === 'BILATERAL' || $obs->eye === $selectedEye)
                <tr>
                    <td><code>{{ $obs->observation_id }}</code></td>
                    <td><strong>{{ $obs->eye }}</strong></td>
                    <td>
                        @if($obs->visit_stage === 'ANTES')
                            <span class="badge" style="border:1px solid var(--border);">Antes (Línea Base Pre-vuelo)</span>
                        @elseif($obs->visit_stage === 'DURANTE')
                            <span class="badge" style="border:1px solid var(--border);">Día {{ $obs->mission_day }} de vuelo</span>
                        @elseif($obs->visit_stage === 'SEGUIMIENTO_POS')
                            <span class="badge" style="border:1px solid var(--border);">+{{ $obs->days_post_end }} días tras aterrizar</span>
                        @else
                            <span class="badge" style="border:1px solid var(--border);">{{ $obs->visit_stage }}</span>
                        @endif
                    </td>
                    <td>{{ $obs->observed_at ?? 'N/D' }}</td>
                    <td>{{ $obs->available_day !== null ? 'Día '.$obs->available_day : ($obs->available_at ?? 'Inmediato') }}</td>
                    <td><code>{{ $obs->metric_code }}</code> ({{ $obs->modality }})</td>
                    <td>
                        @if($obs->value !== null)
                            <strong>{{ $obs->value }} {{ $obs->unit }}</strong>
                        @else
                            {{ $obs->categorical_value ?? 'Sin medición' }}
                        @endif
                    </td>
                    <td><code>{{ $obs->baseline_observation_id ?? 'Línea base propia' }}</code></td>
                    <td><code>{{ $obs->source_type }}</code></td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>

{{-- Tarjetas de Controles Posteriores +30, +60 y +90 días (RF-32, RF-34, AT-16) --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.25rem;">
    @foreach([30, 60, 90] as $milestone)
        @php $visit = $followupVisits->firstWhere('target_milestone_day', $milestone); @endphp
        <div class="card" style="padding:1rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
                <h3 style="font-size:1rem;font-weight:700;margin:0;">
                    <i class="fa fa-flag-o"></i> Control Posterior +{{ $milestone }} días
                </h3>
                <span class="badge" style="border:1px solid var(--border);font-weight:700;">
                    {{ $visit && $visit->status === 'SIN_MEDICION' ? 'Sin medición' : ($visit->status ?? 'Seguimiento insuficiente') }}
                </span>
            </div>
            @if($visit)
                <div style="font-size:0.82rem;margin-bottom:0.25rem;">
                    <strong>Ventana:</strong> {{ $visit->window_label }}
                </div>
                <div style="font-size:0.82rem;margin-bottom:0.25rem;">
                    <strong>Fecha prevista:</strong> {{ $visit->planned_date ?? 'N/D' }} ·
                    <strong>Fecha real:</strong> {{ $visit->actual_date ?? 'Sin medición registrada' }}
                </div>
                <div style="font-size:0.82rem;margin-bottom:0.25rem;">
                    <strong>Estado de hallazgo:</strong>
                    @if($visit->status === 'SIN_MEDICION')
                        <strong>Sin medición</strong> (No se interpola con +30 ni +90 · AT-16)
                    @else
                        <code>{{ $visit->finding_summary_state }}</code>
                    @endif
                </div>
                <div style="font-size:0.76rem;color:var(--muted-foreground);">
                    Fuente: <code>{{ $visit->data_source_badge }}</code> · Responsable: {{ $visit->responsible_role }}
                </div>
            @else
                <div style="font-size:0.82rem;color:var(--muted-foreground);">
                    Seguimiento insuficiente (Sin visita registrada para el hito +{{ $milestone }} días).
                </div>
            @endif
        </div>
    @endforeach
</div>

{{-- Acciones de Revisión Sugeridas (RF-32) --}}
<div class="card" style="padding:1.25rem;">
    <h3 style="font-size:1rem;font-weight:700;margin:0 0 0.75rem;">
        <i class="fa fa-tasks"></i> Acciones de Revisión Registradas (RF-32 — No se convierten en citas sin confirmación)
    </h3>
    <table class="table" style="font-size:0.84rem;margin:0;">
        <thead>
            <tr>
                <th>Hito</th>
                <th>Acción Sugerida</th>
                <th>Motivo</th>
                <th>Dato de Origen</th>
                <th>Estado Revisión</th>
                <th>Responsable</th>
            </tr>
        </thead>
        <tbody>
            @forelse($followupActions as $act)
                <tr>
                    <td>{{ $act->milestone_day ? '+'.$act->milestone_day.' días' : 'Pre/En vuelo' }}</td>
                    <td><strong>{{ $act->suggested_action }}</strong></td>
                    <td>{{ $act->reason }}</td>
                    <td><code>{{ $act->source_observation_id ?? 'Ficha' }}</code></td>
                    <td><code>{{ $act->review_status }}</code></td>
                    <td>{{ $act->responsible_name ?? 'Equipo revisor' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Sin acciones pendientes para esta ficha.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- ================================================================================= --}}
{{-- PESTAÑA 3: SIMULADOR TEMPORAL DEL OJO (RF-18 a RF-22, AT-03, AT-04, AT-17)         --}}
{{-- ================================================================================= --}}
@if($activeTab === 'simulador')
<div class="card" style="padding:1.25rem;margin-bottom:1.25rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;margin-bottom:1rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:700;margin:0;">
                <i class="fa fa-sliders"></i> Simulador Temporal Sincronizado — Ojo {{ $simEye }} · Día {{ $selectedDay }} (Rango {{ $selectedRange }})
            </h2>
            <div style="font-size:0.82rem;color:var(--muted-foreground);margin-top:0.2rem;">
                Estado en Día {{ $selectedDay }} ({{ $timelineMode }}): <strong>{{ $simulatorState['status_banner'] }}</strong>
            </div>
        </div>
        <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
            @foreach([10, 15, 20, 30, 90, 150, 180] as $stepDay)
                <a href="{{ route('admin.sans-monitor.index', array_merge($sharedParams, ['tab' => 'simulador', 'day' => $stepDay])) }}"
                   class="btn {{ (int)$selectedDay === $stepDay ? 'btn-primary' : 'btn-outline' }}" style="font-size:0.78rem;padding:0.35rem 0.65rem;">
                    Día {{ $stepDay }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Tres Vistas Esquemáticas Sincronizadas (RF-19, RF-20): 1. Sagital del globo, 2. Fondo de ojo con disco óptico, 3. Corte de capas tipo OCT --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
        {{-- Vista 1: Sagital del Globo --}}
        <div style="border:1px solid var(--border);border-radius:6px;padding:1rem;">
            <h3 style="font-size:0.92rem;font-weight:700;margin:0 0 0.5rem;">
                <i class="fa fa-circle-o"></i> 1. Vista Esquemática Sagital del Globo
            </h3>
            <svg viewBox="0 0 260 150" style="width:100%;height:140px;background:#09090b;border-radius:4px;">
                <ellipse cx="125" cy="75" rx="{{ $selectedDay >= 30 ? 52 : 56 }}" ry="55" fill="none" stroke="#ffffff" stroke-width="2"/>
                <line x1="68" y1="75" x2="182" y2="75" stroke="#a1a1aa" stroke-dasharray="4 3" stroke-width="1.5"/>
                <rect x="175" y="62" width="35" height="26" fill="none" stroke="#ffffff" stroke-width="1.5"/>
                <text x="14" y="22" fill="#ffffff" font-size="10" font-family="monospace">EJE ANTEROPOSTERIOR ({{ $simEye }})</text>
                <text x="14" y="138" fill="#a1a1aa" font-size="9" font-family="monospace">Contorno posterior · Día {{ $selectedDay }}</text>
            </svg>
            <div style="font-size:0.8rem;margin-top:0.5rem;">
                <strong>Desplazamiento hipermetrópico (&Delta;SE):</strong> Mostrado como cambio de graduación numérica (+0.50 D en escenario día 30), nunca como deformación deducida del dibujo (RF-19).
            </div>
        </div>

        {{-- Vista 2: Fondo de Ojo con Disco Óptico --}}
        <div style="border:1px solid var(--border);border-radius:6px;padding:1rem;">
            <h3 style="font-size:0.92rem;font-weight:700;margin:0 0 0.5rem;">
                <i class="fa fa-dot-circle-o"></i> 2. Vista Esquemática Fondo de Ojo y Disco Óptico
            </h3>
            <svg viewBox="0 0 260 150" style="width:100%;height:140px;background:#09090b;border-radius:4px;">
                <circle cx="130" cy="75" r="56" fill="none" stroke="#ffffff" stroke-width="1.5"/>
                <circle cx="155" cy="75" r="{{ $selectedDay >= 30 ? 18 : 13 }}" fill="none" stroke="#ffffff" stroke-width="2" stroke-dasharray="{{ $selectedDay >= 30 ? '3 2' : 'none' }}"/>
                <line x1="95" y1="55" x2="145" y2="60" stroke="#71717a" stroke-width="1"/>
                <line x1="95" y1="95" x2="145" y2="90" stroke="#71717a" stroke-width="1"/>
                <text x="14" y="22" fill="#ffffff" font-size="10" font-family="monospace">CABEZA DEL NERVIO ÓPTICO</text>
                <text x="14" y="138" fill="#a1a1aa" font-size="9" font-family="monospace">Pliegues y contorno papilar · Esquema explicativo</text>
            </svg>
            <div style="font-size:0.8rem;margin-top:0.5rem;">
                <strong>Nota RF-20:</strong> Se diferencia señal numérica, esquema explicativo e imagen clínica. Si no hay línea base de una capa, se indica en vez de dibujarla como normal.
            </div>
        </div>

        {{-- Vista 3: Corte de Capas Tipo OCT --}}
        <div style="border:1px solid var(--border);border-radius:6px;padding:1rem;">
            <h3 style="font-size:0.92rem;font-weight:700;margin:0 0 0.5rem;">
                <i class="fa fa-align-justify"></i> 3. Vista Esquemática Corte de Capas Tipo OCT
            </h3>
            @php
                $octMissing = ($caseEvaluation['eyes'][$simEye]['coverage_code'] === 'SIN_ESCANEOS_PREVIOS');
            @endphp
            @if($octMissing)
                <div style="height:140px;display:flex;align-items:center;justify-content:center;background:#09090b;color:#ffffff;padding:1rem;text-align:center;font-size:0.82rem;border-radius:4px;">
                    OCT no evaluable (Sin escaneo OCT registrado para {{ $simEye }}; no se dibuja como normal · RF-20 / AT-17)
                </div>
            @else
                <svg viewBox="0 0 260 150" style="width:100%;height:140px;background:#09090b;border-radius:4px;">
                    <path d="M 15 55 Q 130 {{ $selectedDay >= 30 ? 32 : 48 }} 245 55" fill="none" stroke="#ffffff" stroke-width="2"/>
                    <path d="M 15 80 Q 130 78 245 80" fill="none" stroke="#a1a1aa" stroke-width="1.5" stroke-dasharray="4 2"/>
                    <path d="M 15 108 Q 130 108 245 108" fill="none" stroke="#ffffff" stroke-width="2"/>
                    <text x="14" y="22" fill="#ffffff" font-size="10" font-family="monospace">CAPAS ILM / RNFL / RPE-COROIDES</text>
                    <text x="14" y="138" fill="#a1a1aa" font-size="9" font-family="monospace">Comparación pre-vuelo vs Día {{ $selectedDay }}</text>
                </svg>
            @endif
            <div style="font-size:0.8rem;margin-top:0.5rem;">
                <strong>Estado OCT ({{ $simEye }}):</strong> {{ $caseEvaluation['eyes'][$simEye]['operational_text'] }}
            </div>
        </div>
    </div>
</div>
@endif

{{-- ================================================================================= --}}
{{-- PESTAÑA 4: ESTUDIOS Y TENDENCIAS (RF-14 a RF-17, AT-06, AT-07)                    --}}
{{-- ================================================================================= --}}
@if($activeTab === 'estudios')
<div class="card" style="padding:1.25rem;margin-bottom:1.25rem;">
    <h2 style="font-size:1.1rem;font-weight:700;margin:0 0 0.75rem;">
        <i class="fa fa-filter"></i> Controles Separados de Filtro Combinables (RF-14, RF-15 · AT-06, AT-07)
    </h2>
    <form method="GET" action="{{ route('admin.sans-monitor.index') }}" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:0.75rem;align-items:end;margin-bottom:1rem;">
        <input type="hidden" name="tab" value="estudios">
        <input type="hidden" name="case_id" value="{{ $activeCase->case_id }}">
        <div>
            <label style="font-size:0.75rem;font-weight:600;">1. Conjunto de Datos</label>
            <select name="dataset" class="form-control">
                <option value="ALL" {{ $studiesFilterResult['filters_applied']['dataset'] === 'ALL' ? 'selected' : '' }}>Todos</option>
                <option value="FICTICIO_DEMO" {{ $studiesFilterResult['filters_applied']['dataset'] === 'FICTICIO_DEMO' ? 'selected' : '' }}>Ficticio (Demo)</option>
                <option value="ANALOGO_TERRESTRE" {{ $studiesFilterResult['filters_applied']['dataset'] === 'ANALOGO_TERRESTRE' ? 'selected' : '' }}>Análogo Terrestre</option>
                <option value="ORBITAL_AUTORIZADO" {{ $studiesFilterResult['filters_applied']['dataset'] === 'ORBITAL_AUTORIZADO' ? 'selected' : '' }}>Orbital Autorizado</option>
            </select>
        </div>
        <div>
            <label style="font-size:0.75rem;font-weight:600;">2. Evolución Observada</label>
            <select name="evolution" class="form-control">
                <option value="ALL" {{ $studiesFilterResult['filters_applied']['evolution'] === 'ALL' ? 'selected' : '' }}>Todas</option>
                <option value="ESTABLE" {{ $studiesFilterResult['filters_applied']['evolution'] === 'ESTABLE' ? 'selected' : '' }}>Estable</option>
                <option value="EN_PROGRESION" {{ $studiesFilterResult['filters_applied']['evolution'] === 'EN_PROGRESION' ? 'selected' : '' }}>En progresión</option>
                <option value="SIN_DATOS_SUFICIENTES" {{ $studiesFilterResult['filters_applied']['evolution'] === 'SIN_DATOS_SUFICIENTES' ? 'selected' : '' }}>Sin datos suficientes</option>
            </select>
        </div>
        <div>
            <label style="font-size:0.75rem;font-weight:600;">3. Cobertura Longitudinal</label>
            <select name="coverage" class="form-control">
                <option value="ALL" {{ $studiesFilterResult['filters_applied']['coverage'] === 'ALL' ? 'selected' : '' }}>Todas</option>
                <option value="SIN_LINEA_BASE" {{ $studiesFilterResult['filters_applied']['coverage'] === 'SIN_LINEA_BASE' ? 'selected' : '' }}>Sin línea base</option>
                <option value="SIN_ESCANEOS_PREVIOS" {{ $studiesFilterResult['filters_applied']['coverage'] === 'SIN_ESCANEOS_PREVIOS' ? 'selected' : '' }}>Sin escaneos previos</option>
                <option value="SIN_CONTROL_RECIENTE" {{ $studiesFilterResult['filters_applied']['coverage'] === 'SIN_CONTROL_RECIENTE' ? 'selected' : '' }}>Sin control reciente</option>
                <option value="CONTROLES_COMPARABLES" {{ $studiesFilterResult['filters_applied']['coverage'] === 'CONTROLES_COMPARABLES' ? 'selected' : '' }}>Con controles comparables</option>
            </select>
        </div>
        <div>
            <label style="font-size:0.75rem;font-weight:600;">4. Motor de Evaluación</label>
            <select name="engine" class="form-control">
                <option value="DEMO_RULE_V1" {{ $studiesFilterResult['filters_applied']['engine'] === 'DEMO_RULE_V1' ? 'selected' : '' }}>Regla de demo (+20 µm)</option>
                <option value="PUBLISHED_REF_55UM" {{ $studiesFilterResult['filters_applied']['engine'] === 'PUBLISHED_REF_55UM' ? 'selected' : '' }}>Referencia publicada (55 µm)</option>
                <option value="EXPERIMENTAL_MODEL_P2" {{ $studiesFilterResult['filters_applied']['engine'] === 'EXPERIMENTAL_MODEL_P2' ? 'selected' : '' }}>Modelo experimental (P2)</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="width:100%;">
                <i class="fa fa-search"></i> Aplicar Filtros
            </button>
        </div>
    </form>

    <div style="font-size:0.85rem;margin-bottom:0.75rem;">
        <strong>Recuento y Denominador:</strong> Mostrando <strong>{{ $studiesFilterResult['matched_count'] }}</strong> de <strong>{{ $studiesFilterResult['total_cases_denominator'] }}</strong> fichas registradas (Cifras de demo etiquetadas).
    </div>

    @if($studiesFilterResult['empty_state_message'])
        <div style="padding:1rem;border:1px solid var(--border);border-radius:6px;">
            <i class="fa fa-info-circle"></i> {{ $studiesFilterResult['empty_state_message'] }}
        </div>
    @else
        <table class="table" style="font-size:0.84rem;">
            <thead>
                <tr>
                    <th>Ficha</th>
                    <th>Conjunto de Datos</th>
                    <th>Estado OD (Evolución / Cobertura)</th>
                    <th>Estado OS (Evolución / Cobertura)</th>
                    <th>Salida Operativa</th>
                    <th>Motor de Evaluación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studiesFilterResult['results'] as $row)
                    <tr>
                        <td><code>{{ $row['case']->case_id }}</code></td>
                        <td><code>{{ $row['case']->source_type }}</code></td>
                        <td>
                            <strong>{{ $row['evaluation']['eyes']['OD']['trajectory_text'] }}</strong>
                            (<code>{{ $row['evaluation']['eyes']['OD']['coverage_code'] }}</code>)
                        </td>
                        <td>
                            <strong>{{ $row['evaluation']['eyes']['OS']['trajectory_text'] }}</strong>
                            (<code>{{ $row['evaluation']['eyes']['OS']['coverage_code'] }}</code>)
                        </td>
                        <td><strong>{{ $row['evaluation']['case_operational_text'] }}</strong></td>
                        <td>{{ $row['engine_note'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endif

{{-- ================================================================================= --}}
{{-- PESTAÑA 5: INCLINACIÓN Y SEGUIMIENTO (RF-26 a RF-30, AT-10 a AT-14, AT-19)         --}}
{{-- ================================================================================= --}}
@if($activeTab === 'inclinacion')
<div class="card" style="padding:1.25rem;margin-bottom:1.25rem;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:0.75rem;margin-bottom:1rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:700;margin:0;">
                <i class="fa fa-level-down"></i> Pestaña 5: Comparativa «Inclinación y seguimiento» (RF-26 a RF-30)
            </h2>
            <p style="font-size:0.82rem;color:var(--muted-foreground);margin:0.25rem 0 0;">
                El ángulo y la duración se muestran como datos de la exposición (fin de exposición = día 0 de recuperación), nunca como un valor deducido del estado del ojo. Las cronologías de análogo (-6°) y de vuelo espacial se mantienen separadas (AT-19).
            </p>
        </div>
        @if($activeExposure)
            <span class="badge" style="border:1px solid var(--border);padding:0.4rem 0.75rem;">
                Exposición: <code>{{ $activeExposure->exposure_id }}</code> ·
                Ángulo: <strong>{{ $activeExposure->primary_tilt_angle_deg }}° ({{ $activeExposure->tilt_direction }})</strong> ·
                Duración derivada: <strong>{{ $exposureComparison['derived_duration_days'] !== null ? $exposureComparison['derived_duration_days'].' días' : 'Fin desconocido (no se estima)' }}</strong>
            </span>
        @endif
    </div>

    {{-- Tramos de Inclinación Registrados por Separado sin Promedio Oculto (RF-26, AT-14) --}}
    @if(!empty($exposureComparison['segments']))
    <div style="margin-bottom:1rem;padding:0.85rem;border:1px solid var(--border);border-radius:6px;">
        <h3 style="font-size:0.9rem;font-weight:700;margin:0 0 0.5rem;">
            <i class="fa fa-random"></i> Tramos de Inclinación Registrados por Separado (Sin sustituirlos por un promedio oculto · AT-14)
        </h3>
        <div style="display:flex;gap:1rem;flex-wrap:wrap;">
            @foreach($exposureComparison['segments'] as $seg)
                <div style="font-size:0.82rem;padding:0.45rem 0.75rem;border:1px solid var(--border);border-radius:4px;">
                    <strong>Tramo #{{ $seg->segment_order }}:</strong> {{ $seg->tilt_angle_deg }}° ({{ $seg->tilt_direction }}) ·
                    Inicio: {{ $seg->start_at }} &rarr; Fin: {{ $seg->end_at }}
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Selector de Dos Visitas y Referencia Temporal del Eje (RF-27, RF-28, AT-10 a AT-13) --}}
    <form method="GET" action="{{ route('admin.sans-monitor.index') }}" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:0.75rem;align-items:end;margin-bottom:1rem;">
        <input type="hidden" name="tab" value="inclinacion">
        <input type="hidden" name="case_id" value="{{ $activeCase->case_id }}">
        <div>
            <label style="font-size:0.75rem;font-weight:600;">Exposición Registrada</label>
            <select name="exposure_id" class="form-control">
                @foreach($exposures as $exp)
                    <option value="{{ $exp->exposure_id }}" {{ $activeExposure && $exp->exposure_id === $activeExposure->exposure_id ? 'selected' : '' }}>
                        {{ $exp->exposure_id }} ({{ $exp->case_id }} · -{{ $exp->primary_tilt_angle_deg }}°)
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="font-size:0.75rem;font-weight:600;">Visita Inicial (Línea Base Análogo)</label>
            <select name="initial_obs_id" class="form-control">
                @foreach($exposureObservations as $eo)
                    <option value="{{ $eo->observation_id }}" {{ $initialObsId === $eo->observation_id ? 'selected' : '' }}>
                        {{ $eo->observation_id }} ({{ $eo->visit_stage }} · {{ $eo->value }} {{ $eo->unit }})
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="font-size:0.75rem;font-weight:600;">Visita de Comparación</label>
            <select name="compare_obs_id" class="form-control">
                @foreach($exposureObservations as $eo)
                    <option value="{{ $eo->observation_id }}" {{ $compareObsId === $eo->observation_id ? 'selected' : '' }}>
                        {{ $eo->observation_id }} ({{ $eo->visit_stage }} · +{{ $eo->days_post_end }}d · {{ $eo->value }} {{ $eo->unit }})
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="font-size:0.75rem;font-weight:600;">Eje Temporal (AT-12)</label>
            <select name="time_axis" class="form-control">
                <option value="DESDE_FIN" {{ $timeAxis === 'DESDE_FIN' ? 'selected' : '' }} {{ !($exposureComparison['days_since_end_axis_enabled'] ?? true) ? 'disabled' : '' }}>
                    Día desde fin de exposición (+0 = Fin)
                </option>
                <option value="MISION" {{ $timeAxis === 'MISION' ? 'selected' : '' }}>
                    Fecha / Día relativo de sesión
                </option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-exchange"></i> Comparar Visitas
            </button>
        </div>
    </form>

    {{-- Resultado de la Comparación (Ejemplo Demo 280 -> 304 [+24 µm] -> 284 [+4 µm] AT-10, AT-11, AT-13) --}}
    @if($exposureComparison)
        @if(!$exposureComparison['comparable'])
            <div style="padding:1rem;border:1px solid var(--border);border-radius:6px;">
                <strong><i class="fa fa-ban"></i> Comparación Bloqueada / Insuficiente:</strong> {{ $exposureComparison['reason'] }}
                @if(isset($exposureComparison['finding_classification']))
                    <div>Estado: <strong>{{ $exposureComparison['finding_classification'] }}</strong></div>
                @endif
            </div>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;">
                <div style="padding:1rem;border:1px solid var(--border);border-radius:6px;">
                    <div style="font-size:0.76rem;color:var(--muted-foreground);">Valor Inicial (Antes)</div>
                    <div style="font-size:1.35rem;font-weight:700;">{{ $exposureComparison['initial_value'] }} {{ $exposureComparison['unit'] ?? '' }}</div>
                    <div style="font-size:0.76rem;color:var(--muted-foreground);">ID: <code>{{ $exposureComparison['initial']->observation_id }}</code></div>
                </div>
                <div style="padding:1rem;border:1px solid var(--border);border-radius:6px;">
                    <div style="font-size:0.76rem;color:var(--muted-foreground);">Valor de Comparación</div>
                    <div style="font-size:1.35rem;font-weight:700;">{{ $exposureComparison['compare_value'] }} {{ $exposureComparison['unit'] ?? '' }}</div>
                    <div style="font-size:0.76rem;color:var(--muted-foreground);">
                        Día desde fin de exposición: <strong>+{{ $exposureComparison['days_since_exposure_end'] }} días</strong>
                    </div>
                </div>
                <div style="padding:1rem;border:1px solid var(--border);border-radius:6px;">
                    <div style="font-size:0.76rem;color:var(--muted-foreground);">Diferencia con Signo vs Línea Base</div>
                    <div style="font-size:1.35rem;font-weight:700;">{{ $exposureComparison['signed_difference'] }}</div>
                    <div style="font-size:0.76rem;color:var(--muted-foreground);">Ejemplo Demo: +24 µm (primer control) y +4 µm (+30 días)</div>
                </div>
                <div style="padding:1rem;border:1px solid var(--border);border-radius:6px;">
                    <div style="font-size:0.76rem;color:var(--muted-foreground);">Clasificación del Hallazgo (RF-29 · AT-10)</div>
                    <div style="font-size:1rem;font-weight:700;">{{ $exposureComparison['finding_classification'] }}</div>
                    <div style="font-size:0.76rem;color:var(--muted-foreground);margin-top:0.25rem;">
                        {{ $exposureComparison['classification_note'] }}
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
@endif
@endsection
