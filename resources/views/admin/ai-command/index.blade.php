@extends('layouts.app')

@section('title', 'SPACE VISION — AI Command Center')

@section('content')
<div class="page-header" style="margin-bottom:24px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px;">
        <div>
            <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--muted-foreground); margin-bottom:4px;">
                Ecosistema de Computación Científica & Aprendizaje Automático
            </div>
            <h1 style="font-size:24px; font-weight:800; letter-spacing:-0.03em; margin:0 0 6px; color:var(--foreground);">
                AI Command Center
            </h1>
            <p style="font-size:13px; color:var(--muted-foreground); margin:0;">
                Supervisión en tiempo real del microservicio Python/FastAPI, stack científico (NumPy, SciPy, Scikit-learn, Pandas) y telemetría de inferencia.
            </p>
        </div>

        <div style="display:flex; align-items:center; gap:10px;">
            @if ($isOnline)
                <span class="badge" style="background:#09090b; color:#ffffff; border:1px solid #27272a; padding:6px 12px; font-size:12px; font-family:ui-monospace, monospace;">
                    <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#22c55e; margin-right:6px;"></span>
                    PYTHON FASTAPI ONLINE · :8001
                </span>
            @else
                <span class="badge" style="background:#09090b; color:#ffffff; border:1px solid #27272a; padding:6px 12px; font-size:12px; font-family:ui-monospace, monospace;">
                    <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#eab308; margin-right:6px;"></span>
                    LOCAL PHP FALLBACK ACTIVO
                </span>
            @endif
            <a href="{{ route('admin.model-lab') }}" class="btn btn-primary" style="padding:7px 14px; font-size:12px;">
                Laboratorio de Modelos &rarr;
            </a>
        </div>
    </div>
</div>

{{-- Tarjetas de Métricas del Motor --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:24px;">
    <div class="card" style="padding:18px; margin:0;">
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted-foreground); margin-bottom:6px;">Stack Científico</div>
        <div style="font-size:20px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">
            NumPy {{ $health['scientific_stack']['numpy'] ?? '2.5' }}
        </div>
        <div style="font-size:11px; color:var(--muted-foreground); margin-top:4px;">
            SciPy {{ $health['scientific_stack']['scipy'] ?? '1.18' }} · Scikit-learn {{ $health['scientific_stack']['scikit_learn'] ?? '1.9' }}
        </div>
    </div>

    <div class="card" style="padding:18px; margin:0;">
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted-foreground); margin-bottom:6px;">Modelos Registrados</div>
        <div style="font-size:20px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">
            {{ count($models) }} Modelos
        </div>
        <div style="font-size:11px; color:var(--muted-foreground); margin-top:4px;">
            Deterministas, Ensembles, OLS y Detección
        </div>
    </div>

    <div class="card" style="padding:18px; margin:0;">
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted-foreground); margin-bottom:6px;">Evaluaciones Procesadas</div>
        <div style="font-size:20px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">
            {{ $totalEvaluations }} Registros
        </div>
        <div style="font-size:11px; color:var(--muted-foreground); margin-top:4px;">
            {{ $totalAstronauts }} Astronautas en monitoreo orbital
        </div>
    </div>

    <div class="card" style="padding:18px; margin:0;">
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted-foreground); margin-bottom:6px;">Detección de Anomalías</div>
        <div style="font-size:20px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">
            {{ $anomalyReport['anomalies_detected'] ?? 0 }} Atípicas
        </div>
        <div style="font-size:11px; color:var(--muted-foreground); margin-top:4px;">
            Isolation Forest (contaminación: 15%)
        </div>
    </div>
</div>

{{-- Arquitectura del Sistema y Módulos Activos --}}
<div style="display:grid; grid-template-columns:2fr 1fr; gap:20px; margin-bottom:24px;">
    <div class="card" style="padding:22px; margin:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom:1px solid var(--border); padding-bottom:10px;">
            <h2 style="font-size:15px; font-weight:700; margin:0; color:var(--foreground);">Módulos de Computación Científica Activos</h2>
            <span style="font-size:11px; color:var(--muted-foreground); font-family:ui-monospace, monospace;">FastAPI v{{ $health['version'] ?? '1.0.0' }}</span>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            @foreach ($health['modules_active'] ?? [] as $module)
                <div style="background:var(--muted); border:1px solid var(--border); padding:10px 14px; border-radius:6px; display:flex; align-items:center; gap:8px;">
                    <i class="fa fa-check" style="color:var(--foreground); font-size:12px;"></i>
                    <span style="font-size:12px; font-weight:600; color:var(--foreground);">{{ $module }}</span>
                </div>
            @endforeach
        </div>

        <div style="margin-top:16px; padding-top:12px; border-top:1px solid var(--border); font-size:11px; color:var(--muted-foreground); line-height:1.5;">
            <strong>Principio de Integridad Científica:</strong> Los módulos deterministas (SE, LogMAR, vectores de Thibos) operan bajo cálculo matemático exacto sin aproximaciones estocásticas. Los algoritmos de aprendizaje automático se etiquetan estrictamente como demostraciones técnicas experimentales.
        </div>
    </div>

    <div class="card" style="padding:22px; margin:0;">
        <h2 style="font-size:15px; font-weight:700; margin:0 0 14px; color:var(--foreground);">Enlaces Rápidos del Ecosistema</h2>
        <div style="display:flex; flex-direction:column; gap:10px;">
            <a href="{{ route('admin.model-lab') }}" style="display:block; padding:10px 12px; border:1px solid var(--border); border-radius:6px; color:var(--foreground); text-decoration:none; font-size:12px; font-weight:600;">
                <i class="fa fa-flask" style="margin-right:6px;"></i> Laboratorio de Modelos &rarr;
                <div style="font-size:11px; color:var(--muted-foreground); font-weight:normal;">Benchmark Random Forest vs GBM vs OLS</div>
            </a>
            <a href="{{ route('admin.explainability') }}" style="display:block; padding:10px 12px; border:1px solid var(--border); border-radius:6px; color:var(--foreground); text-decoration:none; font-size:12px; font-weight:600;">
                <i class="fa fa-search" style="margin-right:6px;"></i> AI Explainability Center &rarr;
                <div style="font-size:11px; color:var(--muted-foreground); font-weight:normal;">Atribución de variables y pesos SHAP</div>
            </a>
            <a href="{{ route('admin.data-quality') }}" style="display:block; padding:10px 12px; border:1px solid var(--border); border-radius:6px; color:var(--foreground); text-decoration:none; font-size:12px; font-weight:600;">
                <i class="fa fa-bar-chart" style="margin-right:6px;"></i> Calidad de Datos & Observatorio &rarr;
                <div style="font-size:11px; color:var(--muted-foreground); font-weight:normal;">Auditoría de límites fisiológicos y vacíos</div>
            </a>
            <a href="{{ route('admin.model-registry') }}" style="display:block; padding:10px 12px; border:1px solid var(--border); border-radius:6px; color:var(--foreground); text-decoration:none; font-size:12px; font-weight:600;">
                <i class="fa fa-archive" style="margin-right:6px;"></i> Registro de Modelos & Linaje &rarr;
                <div style="font-size:11px; color:var(--muted-foreground); font-weight:normal;">Versiones, artefactos y estado de validación</div>
            </a>
        </div>
    </div>
</div>

{{-- Tabla de Telemetría Reciente y Detección de Anomalías --}}
<div class="card" style="padding:22px; margin:0;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <div>
            <h2 style="font-size:15px; font-weight:700; margin:0 0 4px; color:var(--foreground);">Auditoría de Anomalías en Tiempo Real (Isolation Forest)</h2>
            <p style="font-size:12px; color:var(--muted-foreground); margin:0;">Evaluación estocástica sobre las observaciones longitudinales registradas en órbita.</p>
        </div>
        <span style="font-size:11px; color:var(--muted-foreground); font-family:ui-monospace, monospace;">Algoritmo: IsolationForest (n=100)</span>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
                <tr style="border-bottom:1px solid var(--border); text-align:left; color:var(--muted-foreground); font-size:11px; text-transform:uppercase;">
                    <th style="padding:8px 10px;">Día de Vuelo</th>
                    <th style="padding:8px 10px;">Astronauta</th>
                    <th style="padding:8px 10px;">Desplazamiento Refractivo</th>
                    <th style="padding:8px 10px;">IOP</th>
                    <th style="padding:8px 10px;">RNFL (OCT)</th>
                    <th style="padding:8px 10px;">Score de Anomalía</th>
                    <th style="padding:8px 10px;">Clasificación</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($anomalyReport['results'] ?? [] as $idx => $item)
                    @php
                        $ev = $recentEvaluations[$idx] ?? null;
                    @endphp
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:10px; font-family:ui-monospace, monospace; font-weight:700;">FD{{ $item['flight_day'] }}</td>
                        <td style="padding:10px; font-weight:600;">{{ $ev && $ev->astronaut ? $ev->astronaut->name : 'Astronauta Simulado' }}</td>
                        <td style="padding:10px; font-family:ui-monospace, monospace;">{{ sprintf('%+.2f D', $item['values']['hyperopic_shift']) }}</td>
                        <td style="padding:10px; font-family:ui-monospace, monospace;">{{ $item['values']['iop_mmhg'] }} mmHg</td>
                        <td style="padding:10px; font-family:ui-monospace, monospace;">{{ $item['values']['rnfl_microns'] }} µm</td>
                        <td style="padding:10px; font-family:ui-monospace, monospace;">{{ $item['anomaly_score'] }}</td>
                        <td style="padding:10px;">
                            @if ($item['is_anomaly'])
                                <span style="background:#09090b; color:#ffffff; border:1px solid #ffffff; padding:2px 8px; border-radius:4px; font-size:10px; font-weight:700;">
                                    OBSERVACIÓN ATÍPICA
                                </span>
                            @else
                                <span style="background:var(--muted); color:var(--muted-foreground); border:1px solid var(--border); padding:2px 8px; border-radius:4px; font-size:10px;">
                                    PATRÓN NORMAL
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:20px; text-align:center; color:var(--muted-foreground);">
                            No hay observaciones suficientes para ejecutar detección de anomalías.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
