@extends('layouts.app')

@section('title', 'Vision Lab · Laboratorio Oftalmológico Espacial')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .lab-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .lab-table th { padding: 10px 12px; text-align: left; color: var(--muted-foreground); border-bottom: 1px solid var(--border); font-size: 11px; text-transform: uppercase; letter-spacing: .05em; }
    .lab-table td { padding: 12px; border-bottom: 1px solid var(--border); }
    .lab-table tr:hover td { background: var(--accent); }
    .badge-eye-od { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); padding: 2px 6px; border-radius: 4px; font-weight: 700; }
    .badge-eye-os { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); padding: 2px 6px; border-radius: 4px; font-weight: 700; }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div>
        <div class="eyebrow">SPACE VISION · BIOMEDICAL INSTRUMENTATION</div>
        <h1>Vision Lab (Laboratorio Oftalmológico)</h1>
        <p>Registro refractivo por ojo, conversión científica Snellen &rarr; LogMAR y análisis longitudinal de SANS.</p>
    </div>
    <div style="display:flex; gap:10px; align-items:center;">
        <a class="btn btn-outline" href="{{ route('public.vision-lab.export', request()->query()) }}" style="font-size:13px;">
            <i class="fa fa-download"></i> Exportar CSV
        </a>
        @auth
        <a class="btn btn-primary" href="{{ route('admin.vision-lab.create') }}">
            <i class="fa fa-plus" style="margin-right:6px;"></i>
            Registrar Evaluación
        </a>
        @endauth
    </div>
</div>

@if (session('status'))
    <div style="background: rgba(34, 197, 94, 0.12); border: 1px solid rgba(34, 197, 94, 0.3); color: #4ade80; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('status') }}
    </div>
@endif

{{-- Filtro por astronauta --}}
<div class="card" style="margin-bottom: 20px; padding: 16px;">
    <form method="GET" action="{{ url()->current() }}" style="display:flex; gap:16px; align-items:center; flex-wrap:wrap;">
        <label for="astronaut-filter" style="font-weight:600; font-size:13px;">Filtrar por Astronauta:</label>
        <select name="astronaut_id" id="astronaut-filter" class="form-control" style="max-width:320px; padding:8px 12px; border-radius:6px; background:var(--muted); color:var(--foreground); border:1px solid var(--border);" onchange="this.form.submit()">
            <option value="">-- Todos los tripulantes --</option>
            @foreach ($astronauts as $astro)
                <option value="{{ $astro->id }}" @selected(request('astronaut_id') == $astro->id)>
                    {{ $astro->code }} - {{ $astro->name }} ({{ $astro->agency }})
                </option>
            @endforeach
        </select>
        @if (request('astronaut_id'))
            <a href="{{ url()->current() }}" class="btn btn-outline" style="font-size:12px;">Limpiar filtro</a>
        @endif
    </form>
</div>

{{-- Si hay astronauta seleccionado, mostrar su gráfica y expediente --}}
@if ($selectedAstronaut && $longitudinalData)
<section class="card" style="margin-bottom: 24px;">
    <header class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h2 class="card-title">Perfil Longitudinal: {{ $selectedAstronaut->name }} ({{ $selectedAstronaut->code }})</h2>
            <p class="card-description">{{ $selectedAstronaut->agency }} · {{ $selectedAstronaut->mission_name }} · {{ $selectedAstronaut->days_in_microgravity }} días en microgravedad</p>
        </div>
        <div>
            <span class="mission-badge" style="background:var(--muted); color:var(--foreground);">Línea Base SE: OD {{ $selectedAstronaut->baseline_se_od }} D / OS {{ $selectedAstronaut->baseline_se_os }} D</span>
        </div>
    </header>
    <div class="card-content" style="padding: 16px;">
        <div style="position: relative; height: 260px; width: 100%;">
            <canvas id="astronautLongitudinalChart"></canvas>
        </div>
    </div>
</section>
@endif

{{-- Tabla de Evaluaciones --}}
<section class="card">
    <header class="card-header">
        <h2 class="card-title">Historial de Evaluaciones Registradas ({{ $evaluations->total() }})</h2>
        <p class="card-description">Parámetros de refracción clínica (SPH, CYL, Axis), Equivalente Esférico (SE) y agudeza visual.</p>
    </header>
    <div class="card-content" style="padding:0; overflow-x:auto;">
        <table class="lab-table">
            <thead>
                <tr>
                    <th>Fecha / FD</th>
                    <th>Astronauta</th>
                    <th>Ojo</th>
                    <th>Fórmula Refracción</th>
                    <th>Equiv. Esférico (SE)</th>
                    <th>&Delta; Hipermetropía</th>
                    <th>Agudeza Snellen</th>
                    <th>LogMAR</th>
                    <th>Presión (IOP)</th>
                    <th>Signos SANS</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($evaluations as $eval)
                    @php
                        $sans = $eval->sansObservation;
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $eval->evaluated_at->format('d/m/Y') }}</strong>
                            <div style="font-size:11px; color:var(--muted-foreground);">FD{{ $eval->mission_day }} ({{ $eval->flight_phase }})</div>
                        </td>
                        <td>
                            <a href="{{ url()->current() }}?astronaut_id={{ $eval->astronaut_id }}" style="color:#58a6ff; font-weight:600;">
                                {{ $eval->astronaut->name }}
                            </a>
                            <div style="font-size:11px; color:var(--muted-foreground);">{{ $eval->astronaut->agency }}</div>
                        </td>
                        <td>
                            <span class="badge-eye-{{ strtolower($eval->eye) }}">{{ $eval->eye }}</span>
                        </td>
                        <td style="font-family:monospace;">
                            {{ $eval->refraction_formula }}
                        </td>
                        <td style="font-family:monospace; font-weight:700;">
                            {{ number_format($eval->spherical_equivalent, 2) }} D
                        </td>
                        <td>
                            @if ($eval->hyperopic_shift > 0)
                                <span style="color:#fbbf24; font-weight:700;">+{{ number_format($eval->hyperopic_shift, 2) }} D</span>
                            @else
                                <span style="color:#4ade80;">{{ number_format($eval->hyperopic_shift, 2) }} D</span>
                            @endif
                        </td>
                        <td>
                            {{ $eval->snellen_fraction }}
                        </td>
                        <td style="font-family:monospace; font-weight:600;">
                            {{ number_format($eval->logmar, 3) }}
                        </td>
                        <td>
                            {{ $eval->intraocular_pressure ? $eval->intraocular_pressure.' mmHg' : 'N/A' }}
                        </td>
                        <td>
                            @if ($sans)
                                @php
                                    $class = match($sans->sans_risk_tier) {
                                        'severe', 'high' => 'color:#f87171',
                                        'moderate' => 'color:#fbbf24',
                                        default => 'color:#4ade80',
                                    };
                                @endphp
                                <span style="{{ $class }}; font-weight:700; font-size:11px;">
                                    Frisen {{ $sans->frisen_scale_grade }} ({{ strtoupper($sans->sans_risk_tier) }})
                                </span>
                            @else
                                <span style="color:var(--muted-foreground); font-size:11px;">Normal</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align:center; padding:32px; color:var(--muted-foreground);">
                            No hay evaluaciones registradas con los filtros actuales.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($evaluations->hasPages())
        <div style="padding:16px;">
            {{ $evaluations->links() }}
        </div>
    @endif
</section>

@if ($selectedAstronaut && $longitudinalData)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('astronautLongitudinalChart');
    if (!ctx) return;

    const data = @json($longitudinalData);
    const isDark = document.documentElement.dataset.theme === 'dark';
    const textColor = isDark ? '#f4f4f5' : '#09090b';
    const textMuted = isDark ? '#a1a1aa' : '#27272a';
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.08)';

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'OD Equivalente Esférico (SE)',
                    data: data.od_se,
                    borderColor: '#d97706',
                    backgroundColor: 'rgba(217, 119, 6, 0.15)',
                    tension: 0.25,
                    fill: false,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    borderWidth: 2.5,
                },
                {
                    label: 'OS Equivalente Esférico (SE)',
                    data: data.os_se,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.15)',
                    tension: 0.25,
                    fill: false,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    borderWidth: 2.5,
                },
                {
                    label: 'OD Agudeza Visual LogMAR (Escala invertida)',
                    data: data.od_logmar,
                    borderColor: '#db2777',
                    borderDash: [5, 4],
                    tension: 0.25,
                    fill: false,
                    pointRadius: 5,
                    borderWidth: 2.2,
                    yAxisID: 'yLogmar',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    title: { display: true, text: 'Equivalente Esférico (Dioptrías D)', color: textColor, font: { weight: '700', size: 12 } },
                    grid: { color: gridColor },
                    ticks: { color: textMuted, font: { weight: '600', size: 11 } }
                },
                yLogmar: {
                    position: 'right',
                    title: { display: true, text: 'LogMAR (Invertido)', color: '#db2777', font: { weight: '700', size: 12 } },
                    grid: { drawOnChartArea: false },
                    ticks: { color: '#db2777', font: { weight: '700', size: 11 } }
                },
                x: {
                    title: { display: true, text: 'Cronología de Vuelo (Flight Day)', color: textMuted, font: { weight: '600', size: 11 } },
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { weight: '700', size: 11 } }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: textColor,
                        font: { size: 12, weight: '700' },
                        padding: 16,
                        usePointStyle: true,
                    }
                },
                tooltip: {
                    backgroundColor: isDark ? '#18181b' : '#09090b',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#27272a',
                    borderWidth: 1,
                    padding: 10,
                }
            }
        }
    });
});
</script>
@endpush
@endif
@endsection
