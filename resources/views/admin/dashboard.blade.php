@extends('layouts.app')

@section('title', 'Mission Control · Salud Visual Espacial')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .mission-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
    }
    .badge-danger { background: #09090b; color: #ffffff; border: 1px solid #ffffff; }
    .badge-warning { background: var(--muted); color: var(--foreground); border: 1px solid var(--border); font-weight: 700; }
    .badge-success { background: var(--muted); color: var(--muted-foreground); border: 1px solid var(--border); }
    .crew-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .crew-table th { padding: 10px 12px; text-align: left; color: var(--muted-foreground); border-bottom: 1px solid var(--border); font-size: 11px; text-transform: uppercase; letter-spacing: .05em; }
    .crew-table td { padding: 12px; border-bottom: 1px solid var(--border); }
    .crew-table tr:hover td { background: var(--accent); }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div>
        <div class="eyebrow">NASA SPACE APPS 2026 · QUERÉTARO · MISSION OPERATIONS</div>
        <h1>SPACE VISION Mission Control Dashboard</h1>
        <p>Telemetría clínica, monitoreo neurooftalmológico y vigilancia longitudinal de <strong>SANS</strong> en microgravedad.</p>
    </div>
    <div style="display:flex; gap:10px; align-items:center;">
        <span class="mission-badge badge-{{ $alertColor }}">
            <span style="width:8px;height:8px;border-radius:50%;background:currentColor;"></span>
            ESTADO SANS: {{ $missionAlertLevel }}
        </span>
        <a class="btn btn-primary" href="{{ route('admin.vision-lab.create') }}">
            <i class="fa fa-plus" style="margin-right:4px;"></i>
            Nueva Evaluación Oftalmológica
        </a>
    </div>
</div>

{{-- Barra de Métricas Principales --}}
<section class="metric-grid" aria-label="Indicadores de Salud Visual Espacial">
    <article class="card metric-card">
        <div class="metric-header">
            <strong>Astronautas en Órbita</strong>
            <i class="fa fa-users" style="color:var(--muted-foreground); font-size:16px;"></i>
        </div>
        <div class="metric-value">{{ $totalAstronauts }}</div>
        <div class="metric-note">{{ $activeMissions }} tripulantes en misiones activas</div>
    </article>

    <article class="card metric-card">
        <div class="metric-header">
            <strong>Evaluaciones Clínicas</strong>
            <i class="fa fa-eye" style="color:var(--muted-foreground); font-size:16px;"></i>
        </div>
        <div class="metric-value">{{ $totalEvaluations }}</div>
        <div class="metric-note">Exámenes de refracción y LogMAR</div>
    </article>

    <article class="card metric-card">
        <div class="metric-header">
            <strong>Casos con Desplazamiento Hipermetrópico</strong>
            <i class="fa fa-line-chart" style="color:var(--muted-foreground); font-size:16px;"></i>
        </div>
        <div class="metric-value">{{ $hyperopicShiftCases }}</div>
        <div class="metric-note">&Delta;SE &ge; +0.50 D documentado en vuelo</div>
    </article>

    <article class="card metric-card">
        <div class="metric-header">
            <strong>Signos Estructurales SANS</strong>
            <i class="fa fa-exclamation-triangle" style="color:var(--muted-foreground); font-size:16px;"></i>
        </div>
        <div class="metric-value">{{ $highRiskSansCount }}</div>
        <div class="metric-note">Edema de papila o pliegues coroideos</div>
    </article>
</section>

{{-- Sección central: Gráfica de evolución y Estado de la Tripulación --}}
<section class="dashboard-grid" style="grid-template-columns: 1.5fr 1fr; margin-bottom: 24px;">
    {{-- Gráfica longitudinal de CDR Alex Mercer (Caso prototipo SANS) --}}
    <article class="card">
        <header class="card-header" style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <h2 class="card-title">Curva Longitudinal de Refracción (SANS Progression)</h2>
                <p class="card-description">Evolución de Equivalente Esférico (SE) por día de vuelo (FD0 &rarr; FD120) · Caso: <strong>CDR Alex Mercer</strong></p>
            </div>
            <span class="mission-badge badge-warning">DESPLAZAMIENTO HIPERMETRÓPICO</span>
        </header>
        <div class="card-content" style="padding: 16px;">
            <div style="position: relative; height: 280px; width: 100%;">
                <canvas id="sansEvolutionChart"></canvas>
            </div>
            <div style="font-size:11px; color:var(--muted-foreground); margin-top:12px; display:flex; justify-content:space-between;">
                <span>* Datos sintéticos calibrados según estudios de microgravedad de NASA HRP.</span>
                <span>Fórmula: SE = SPH + (CYL / 2)</span>
            </div>
        </div>
    </article>

    {{-- Contexto Científico y Protocolo NASA --}}
    <article class="card">
        <header class="card-header">
            <h2 class="card-title">Criterios de Vigilancia SANS</h2>
            <p class="card-description">Parámetros neurooftalmológicos estandarizados.</p>
        </header>
        <div class="card-content">
            <div style="display:flex; flex-direction:column; gap:14px; font-size:13px;">
                <div style="padding:10px 12px; border-radius:6px; background:var(--muted); border-left:3px solid var(--foreground);">
                    <strong>Equivalente Esférico (SE)</strong>
                    <p style="margin:4px 0 0; color:var(--muted-foreground); font-size:12px;">Desviaciones &Delta;SE &gt; +0.50 D alertan aplanamiento posterior del globo ocular.</p>
                </div>
                <div style="padding:10px 12px; border-radius:6px; background:var(--muted); border-left:3px solid var(--border);">
                    <strong>Agudeza Visual LogMAR</strong>
                    <p style="margin:4px 0 0; color:var(--muted-foreground); font-size:12px;">Conversión continua Snellen &rarr; LogMAR para trazabilidad estadística sin sesgo de notación.</p>
                </div>
                <div style="padding:10px 12px; border-radius:6px; background:var(--muted); border-left:3px solid var(--foreground);">
                    <strong>Escala Frisen (Edema de Disco)</strong>
                    <p style="margin:4px 0 0; color:var(--muted-foreground); font-size:12px;">Grados 1 a 5 para cuantificar inflamación de papila inducida por redistribución de fluidos cefálicos.</p>
                </div>
            </div>
        </div>
    </article>
</section>

{{-- Tabla de Tripulantes Monitoreados --}}
<section class="card" style="margin-bottom: 24px;">
    <header class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h2 class="card-title">Telemetría de Salud Visual por Tripulante</h2>
            <p class="card-description">Estado oftalmológico de astronautas en misiones de microgravedad.</p>
        </div>
        <a href="{{ route('admin.vision-lab.index') }}" class="btn btn-outline" style="font-size:12px;">Ver en Vision Lab &rarr;</a>
    </header>
    <div class="card-content" style="padding:0; overflow-x:auto;">
        <table class="crew-table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Astronauta</th>
                    <th>Agencia / Misión</th>
                    <th>Días en Microgravedad</th>
                    <th>Última Refracción (OD)</th>
                    <th>&Delta; Hipermetropía</th>
                    <th>Riesgo SANS</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($crew as $member)
                    @php
                        $latest = $member->latestEvaluation;
                        $sans = $member->latestSansObservation;
                        $shift = $latest ? $latest->hyperopic_shift : 0;
                        $riskTier = $sans ? $sans->sans_risk_tier : 'low';
                        $riskClass = match($riskTier) {
                            'severe', 'high' => 'badge-danger',
                            'moderate' => 'badge-warning',
                            default => 'badge-success'
                        };
                    @endphp
                    <tr>
                        <td><code>{{ $member->code }}</code></td>
                        <td>
                            <strong>{{ $member->name }}</strong>
                            <div style="font-size:11px; color:var(--muted-foreground);">{{ $member->role }}</div>
                        </td>
                        <td>
                            <span style="font-weight:600;">{{ $member->agency }}</span> · {{ $member->mission_name }}
                        </td>
                        <td>
                            <span style="font-family:monospace; font-weight:700;">{{ $member->days_in_microgravity }} días</span>
                        </td>
                        <td>
                            @if ($latest)
                                {{ $latest->refraction_formula }} (SE: {{ $latest->spherical_equivalent }} D)
                            @else
                                <span style="color:var(--muted-foreground);">Pendiente</span>
                            @endif
                        </td>
                        <td>
                            @if ($shift > 0)
                                <span style="color:#fbbf24; font-weight:700;">+{{ number_format($shift, 2) }} D</span>
                            @else
                                <span style="color:#4ade80;">{{ number_format($shift, 2) }} D</span>
                            @endif
                        </td>
                        <td>
                            <span class="mission-badge {{ $riskClass }}">{{ strtoupper($riskTier) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.crew.show', ['code' => $member->code]) }}" class="btn btn-outline" style="padding:4px 8px; font-size:11px;">
                                Ver Expediente
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('sansEvolutionChart');
    if (!ctx) return;

    const chartData = @json($chartData);
    if (!chartData || !chartData.labels) return;

    const isDark = document.documentElement.dataset.theme === 'dark';
    const textColor = isDark ? '#f4f4f5' : '#09090b';
    const textMuted = isDark ? '#a1a1aa' : '#3f3f46';
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.08)';

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Ojo Derecho (OD) - Equivalente Esférico (D)',
                    data: chartData.od_se,
                    borderColor: isDark ? '#ffffff' : '#09090b',
                    backgroundColor: isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(9, 9, 11, 0.05)',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                },
                {
                    label: 'Ojo Izquierdo (OS) - Equivalente Esférico (D)',
                    data: chartData.os_se,
                    borderColor: isDark ? '#a1a1aa' : '#52525b',
                    backgroundColor: 'transparent',
                    tension: 0.3,
                    borderDash: [5, 5],
                    pointRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    title: { display: true, text: 'Dioptrías (D) · Equivalente Esférico', color: textColor, font: { weight: '600' } },
                    grid: { color: gridColor },
                    ticks: { color: textMuted, font: { weight: '600' } }
                },
                x: {
                    title: { display: true, text: 'Cronología de Vuelo (Días en Microgravedad)', color: textMuted, font: { weight: '600' } },
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { weight: '600' } }
                }
            },
            plugins: {
                legend: {
                    labels: { color: textColor, font: { size: 12, weight: 600 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' D';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
