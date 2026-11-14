@extends('layouts.app')

@section('title', $astronaut->name . ' · Expediente Neurooftalmológico de Tripulación')

@push('styles')
<style>
    .crew-hero {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 24px 28px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        box-shadow: var(--shadow);
    }
    .crew-meta-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        font-family: ui-monospace, monospace;
        background: var(--muted);
        color: var(--foreground);
        border: 1px solid var(--border);
    }
    .grid-2col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }
    @media (max-width: 860px) {
        .grid-2col { grid-template-columns: 1fr; }
    }
    .stat-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px;
        box-shadow: var(--shadow);
    }
</style>
@endpush

@section('content')
<div class="crew-hero">
    <div style="display:flex; align-items:center; gap:18px;">
        <img src="{{ $astronaut->avatar_url }}"
             alt="{{ $astronaut->name }}"
             style="width:72px; height:72px; border-radius:12px; object-fit:cover; border:2px solid var(--foreground);">
        <div>
            <div style="display:flex; gap:8px; align-items:center; margin-bottom:4px;">
                <span class="crew-meta-badge">{{ $astronaut->code }}</span>
                <span class="crew-meta-badge">{{ $astronaut->agency }}</span>
                <span class="crew-meta-badge">{{ $astronaut->role }}</span>
            </div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 4px; color:var(--foreground); letter-spacing:-0.02em;">{{ $astronaut->name }}</h1>
            <div style="font-size:12px; color:var(--muted-foreground);">
                Misión: <strong style="color:var(--foreground);">{{ $astronaut->mission_name }}</strong> &bull; Días en Microgravedad: <strong style="color:var(--foreground); font-family:ui-monospace, monospace;">FD{{ $astronaut->days_in_microgravity }}</strong>
            </div>
        </div>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('admin.digital-twin', ['astro' => $astronaut->code]) }}" class="btn btn-outline" style="font-size:12px;">
            <i class="fa fa-user-md"></i> Gemelo Digital
        </a>
        <a href="{{ route('admin.mission-simulator', ['astro' => $astronaut->code]) }}" class="btn btn-outline" style="font-size:12px;">
            <i class="fa fa-line-chart"></i> Simular Vuelo
        </a>
        <a href="{{ route('admin.demo-theater.report', ['code' => $astronaut->code]) }}" target="_blank" class="btn btn-primary" style="font-size:12px;">
            <i class="fa fa-print"></i> Reporte Oficial PDF
        </a>
    </div>
</div>

{{-- Fichas Comparativas Bilaterales (OD vs OS) --}}
<div class="grid-2col">
    {{-- Ojo Derecho (OD) --}}
    <div class="stat-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <span style="font-size:13px; font-weight:700; color:var(--foreground); text-transform:uppercase;">
                Ojo Derecho (OD - Oculus Dexter)
            </span>
            <span style="font-size:11px; font-family:ui-monospace, monospace; color:var(--muted-foreground);">
                FD{{ $latestEvalOD->mission_day ?? 0 }}
            </span>
        </div>
        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:10px; margin-bottom:14px;">
            <div style="background:var(--muted); border:1px solid var(--border); padding:10px; border-radius:6px;">
                <div style="font-size:10px; color:var(--muted-foreground); font-weight:700;">SE Basal</div>
                <div style="font-size:16px; font-weight:800; font-family:ui-monospace, monospace; color:var(--foreground);">
                    {{ number_format($baselineEvalOD->spherical_equivalent ?? 0, 2) }} D
                </div>
            </div>
            <div style="background:var(--muted); border:1px solid var(--border); padding:10px; border-radius:6px;">
                <div style="font-size:10px; color:var(--muted-foreground); font-weight:700;">SE Actual</div>
                <div style="font-size:16px; font-weight:800; font-family:ui-monospace, monospace; color:var(--foreground);">
                    {{ number_format($latestEvalOD->spherical_equivalent ?? 0, 2) }} D
                </div>
            </div>
            <div style="background:var(--muted); border:1px solid var(--border); padding:10px; border-radius:6px;">
                <div style="font-size:10px; color:var(--muted-foreground); font-weight:700;">Shift (&Delta;SE)</div>
                <div style="font-size:16px; font-weight:800; font-family:ui-monospace, monospace; color:var(--foreground);">
                    +{{ number_format($currentShiftOD, 2) }} D
                </div>
            </div>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:12px; color:var(--muted-foreground);">
            <span>Agudeza Snellen: <strong style="color:var(--foreground);">{{ $latestEvalOD->snellen_fraction ?? '20/20' }}</strong> (LogMAR {{ number_format($latestEvalOD->logmar ?? 0, 3) }})</span>
            <span>IOP: <strong style="color:var(--foreground);">{{ number_format($latestEvalOD->intraocular_pressure ?? 15, 1) }} mmHg</strong></span>
        </div>
    </div>

    {{-- Ojo Izquierdo (OS) --}}
    <div class="stat-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <span style="font-size:13px; font-weight:700; color:var(--foreground); text-transform:uppercase;">
                Ojo Izquierdo (OS - Oculus Sinister)
            </span>
            <span style="font-size:11px; font-family:ui-monospace, monospace; color:var(--muted-foreground);">
                FD{{ $latestEvalOS->mission_day ?? 0 }}
            </span>
        </div>
        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:10px; margin-bottom:14px;">
            <div style="background:var(--muted); border:1px solid var(--border); padding:10px; border-radius:6px;">
                <div style="font-size:10px; color:var(--muted-foreground); font-weight:700;">SE Basal</div>
                <div style="font-size:16px; font-weight:800; font-family:ui-monospace, monospace; color:var(--foreground);">
                    {{ number_format($baselineEvalOS->spherical_equivalent ?? 0, 2) }} D
                </div>
            </div>
            <div style="background:var(--muted); border:1px solid var(--border); padding:10px; border-radius:6px;">
                <div style="font-size:10px; color:var(--muted-foreground); font-weight:700;">SE Actual</div>
                <div style="font-size:16px; font-weight:800; font-family:ui-monospace, monospace; color:var(--foreground);">
                    {{ number_format($latestEvalOS->spherical_equivalent ?? 0, 2) }} D
                </div>
            </div>
            <div style="background:var(--muted); border:1px solid var(--border); padding:10px; border-radius:6px;">
                <div style="font-size:10px; color:var(--muted-foreground); font-weight:700;">Shift (&Delta;SE)</div>
                <div style="font-size:16px; font-weight:800; font-family:ui-monospace, monospace; color:var(--foreground);">
                    +{{ number_format(abs(($latestEvalOS->spherical_equivalent ?? 0) - ($baselineEvalOS->spherical_equivalent ?? 0)), 2) }} D
                </div>
            </div>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:12px; color:var(--muted-foreground);">
            <span>Agudeza Snellen: <strong style="color:var(--foreground);">{{ $latestEvalOS->snellen_fraction ?? '20/20' }}</strong> (LogMAR {{ number_format($latestEvalOS->logmar ?? 0, 3) }})</span>
            <span>IOP: <strong style="color:var(--foreground);">{{ number_format($latestEvalOS->intraocular_pressure ?? 15, 1) }} mmHg</strong></span>
        </div>
    </div>
</div>

{{-- Biomarcadores Estructurales SANS y Recomendación --}}
<div class="card" style="padding:20px; margin-bottom:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <h2 class="card-title" style="font-size:15px;">
            Biomarcadores de Fondo de Ojo y Estratificación SANS
        </h2>
        @if ($riskAssessment['tier'] === 'severe' || $riskAssessment['tier'] === 'high')
            <span class="badge-subtle" style="font-weight:800;">
                RIESGO ELEVADO (TIER 3)
            </span>
        @elseif ($riskAssessment['tier'] === 'moderate' || $riskAssessment['tier'] === 'medium')
            <span class="badge-subtle" style="font-weight:800;">
                RIESGO MODERADO (TIER 2)
            </span>
        @else
            <span class="badge-subtle" style="font-weight:800;">
                RIESGO NOMINAL (TIER 1)
            </span>
        @endif
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:12px; margin-bottom:14px;">
        <div style="background:var(--muted); border:1px solid var(--border); padding:12px; border-radius:6px;">
            <div style="font-size:11px; color:var(--muted-foreground); font-weight:700;">Escala Frisén (Papiledema)</div>
            <div style="font-size:16px; font-weight:800; color:var(--foreground);">
                Grado {{ $latestSans->frisen_scale_grade ?? 0 }} / 5
            </div>
        </div>
        <div style="background:var(--muted); border:1px solid var(--border); padding:12px; border-radius:6px;">
            <div style="font-size:11px; color:var(--muted-foreground); font-weight:700;">Aplanamiento de Polo Posterior</div>
            <div style="font-size:16px; font-weight:800; color:var(--foreground);">
                {{ ($latestSans->posterior_globe_flattening ?? false) ? 'POSITIVO' : 'NEGATIVO' }}
            </div>
        </div>
        <div style="background:var(--muted); border:1px solid var(--border); padding:12px; border-radius:6px;">
            <div style="font-size:11px; color:var(--muted-foreground); font-weight:700;">Pliegues Coroideos</div>
            <div style="font-size:16px; font-weight:800; color:var(--foreground);">
                {{ ($latestSans->choroidal_folds ?? false) ? 'PRESENTES' : 'AUSENTES' }}
            </div>
        </div>
        <div style="background:var(--muted); border:1px solid var(--border); padding:12px; border-radius:6px;">
            <div style="font-size:11px; color:var(--muted-foreground); font-weight:700;">Grosor RNFL OCT</div>
            <div style="font-size:16px; font-weight:800; color:var(--foreground);">
                {{ $latestSans->rnfl_thickness_microns ?? 95 }} &mu;m
            </div>
        </div>
    </div>

    <div style="background:var(--muted); border-left:4px solid var(--foreground); padding:12px 16px; border-radius:6px; font-size:12px; color:var(--foreground);">
        <strong>Recomendación Médica de Vuelo:</strong> <span style="color:var(--muted-foreground);">{{ $riskAssessment['recommendation'] }}</span>
    </div>
</div>

{{-- 3. Análisis de Tendencia Longitudinal (SciPy OLS + 95% CI) --}}
@if ($trendAnalysis && isset($trendAnalysis['metrics']))
<div class="card" style="padding:22px; margin-bottom:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
        <div>
            <span style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted-foreground);">
                Análisis Estadístico Avanzado · Microservicio Python
            </span>
            <h2 class="card-title" style="font-size:15px; margin:2px 0 0;">
                Regresión Longitudinal de Refracción (Ojo Derecho OD) & Bandas de Confianza (95% CI)
            </h2>
        </div>
        <div style="font-family:ui-monospace, monospace; font-size:11px; background:var(--muted); border:1px solid var(--border); padding:3px 8px; border-radius:4px;">
            R² = {{ $trendAnalysis['metrics']['r_squared'] }} · Pendiente: {{ sprintf('%+.4f D/día', $trendAnalysis['metrics']['slope_per_day']) }}
        </div>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:10px; margin-bottom:16px;">
        <div style="background:var(--muted); border:1px solid var(--border); padding:10px 12px; border-radius:6px;">
            <div style="font-size:10px; text-transform:uppercase; color:var(--muted-foreground); font-weight:700;">Modelo</div>
            <div style="font-size:12px; font-weight:700; color:var(--foreground); font-family:ui-monospace, monospace; margin-top:2px;">
                {{ $trendAnalysis['model_type'] }}
            </div>
        </div>
        <div style="background:var(--muted); border:1px solid var(--border); padding:10px 12px; border-radius:6px;">
            <div style="font-size:10px; text-transform:uppercase; color:var(--muted-foreground); font-weight:700;">Significancia Estadística</div>
            <div style="font-size:12px; font-weight:700; color:var(--foreground); font-family:ui-monospace, monospace; margin-top:2px;">
                p = {{ $trendAnalysis['metrics']['p_value'] }} (p < 0.05)
            </div>
        </div>
        <div style="background:var(--muted); border:1px solid var(--border); padding:10px 12px; border-radius:6px;">
            <div style="font-size:10px; text-transform:uppercase; color:var(--muted-foreground); font-weight:700;">Proyección en FD180</div>
            <div style="font-size:12px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace; margin-top:2px;">
                @php
                    $lastForecast = end($trendAnalysis['forecast_points']);
                @endphp
                {{ $lastForecast ? sprintf('%+.2f D [%+.2f a %+.2f D]', $lastForecast['forecast_value'], $lastForecast['ci_lower_95'], $lastForecast['ci_upper_95']) : 'N/A' }}
            </div>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
                <tr style="border-bottom:1px solid var(--border); text-align:left; color:var(--muted-foreground); font-size:11px; text-transform:uppercase;">
                    <th style="padding:6px 8px;">Día de Vuelo</th>
                    <th style="padding:6px 8px;">Valor Observado (SE)</th>
                    <th style="padding:6px 8px;">Ajuste SciPy</th>
                    <th style="padding:6px 8px;">IC 95% Inferior</th>
                    <th style="padding:6px 8px;">IC 95% Superior</th>
                    <th style="padding:6px 8px;">Residual</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($trendAnalysis['fitted_points'] ?? [] as $pt)
                    <tr style="border-bottom:1px solid var(--border); font-family:ui-monospace, monospace;">
                        <td style="padding:6px 8px; font-weight:700;">FD{{ $pt['flight_day'] }}</td>
                        <td style="padding:6px 8px;">{{ sprintf('%+.2f D', $pt['actual_value']) }}</td>
                        <td style="padding:6px 8px; font-weight:700;">{{ sprintf('%+.2f D', $pt['fitted_value']) }}</td>
                        <td style="padding:6px 8px; color:var(--muted-foreground);">{{ sprintf('%+.2f D', $pt['ci_lower_95']) }}</td>
                        <td style="padding:6px 8px; color:var(--muted-foreground);">{{ sprintf('%+.2f D', $pt['ci_upper_95']) }}</td>
                        <td style="padding:6px 8px; color:var(--muted-foreground);">{{ sprintf('%+.3f', $pt['residual']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Historial de Evaluaciones Oftálmicas --}}
<div class="card" style="padding:20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <h2 class="card-title" style="font-size:15px;">
            Historial de Evaluaciones Oftalmológicas (FD0 &rarr; FD{{ $astronaut->days_in_microgravity }})
        </h2>
        <a href="{{ route('admin.vision-lab.create', ['astronaut_id' => $astronaut->id]) }}" class="btn btn-primary" style="font-size:12px;">
            <i class="fa fa-plus"></i> Añadir Registro
        </a>
    </div>

    <div class="table-responsive">
        <table class="table" style="width:100%; font-size:12px;">
            <thead>
                <tr>
                    <th>Día de Vuelo</th>
                    <th>Ojo</th>
                    <th>SPH</th>
                    <th>CYL</th>
                    <th>Eje</th>
                    <th>Equivalente Esférico (SE)</th>
                    <th>Shift (&Delta;SE)</th>
                    <th>Snellen</th>
                    <th>LogMAR</th>
                    <th>IOP</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($astronaut->evaluations as $ev)
                    <tr>
                        <td style="font-family:ui-monospace, monospace; font-weight:700;">FD{{ $ev->mission_day }}</td>
                        <td>
                            <span class="crew-meta-badge">
                                {{ $ev->eye }}
                            </span>
                        </td>
                        <td>{{ number_format($ev->sphere, 2) }} D</td>
                        <td>{{ number_format($ev->cylinder, 2) }} D</td>
                        <td>{{ $ev->axis }}°</td>
                        <td style="font-weight:700;">{{ number_format($ev->spherical_equivalent, 2) }} D</td>
                        <td style="font-weight:700;">
                            +{{ number_format($ev->hyperopic_shift, 2) }} D
                        </td>
                        <td>{{ $ev->snellen_fraction }}</td>
                        <td>{{ number_format($ev->logmar, 3) }}</td>
                        <td>{{ number_format($ev->intraocular_pressure, 1) }} mmHg</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
