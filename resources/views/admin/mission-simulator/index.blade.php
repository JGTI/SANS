@extends('layouts.app')

@section('title', 'Mission Simulator · Simulador de Alteraciones Neurooculares en Microgravedad')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<style>
    .simulator-hero {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 24px 28px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        box-shadow: var(--shadow);
    }
    .sim-controls-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: var(--shadow);
    }
    .sim-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 24px;
    }
    @media (max-width: 992px) {
        .sim-grid { grid-template-columns: 1fr; }
    }
    .gauge-pill {
        background: var(--background);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .gauge-val {
        font-size: 18px;
        font-weight: 800;
        font-family: ui-monospace, monospace;
        color: var(--foreground);
    }
    .sim-disclaimer {
        background: var(--muted);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 20px;
        font-size: 12px;
        color: var(--foreground);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .speed-btn {
        padding: 4px 10px;
        font-size: 11px;
        background: var(--card);
        color: var(--muted-foreground);
        border: 1px solid var(--border);
        cursor: pointer;
        font-weight: 600;
        transition: all 0.15s ease;
    }
    .speed-btn.active {
        background: var(--foreground);
        color: var(--background);
        border-color: var(--foreground);
    }
</style>
@endpush

@section('content')
<div class="simulator-hero">
    <div>
        <div class="eyebrow">
            SPACE VISION · ASTRONAUT PHYSIOLOGICAL MISSION SIMULATOR
        </div>
        <h1 style="margin:4px 0 8px; font-size:24px; font-weight:800; color:var(--foreground); letter-spacing:-0.03em;">
            Simulador de Misiones & Escenarios SANS
        </h1>
        <p style="margin:0; font-size:13px; color:var(--muted-foreground); max-width:680px;">
            Simulación temporal acelerada de respuestas neurooftálmicas bajo microgravedad prolongada. Permite evaluar hipótesis clínicas, aceleración de aplanamiento posterior y contramedidas con semillas reproducibles.
        </p>
    </div>
    <div>
        <span style="display:inline-block; font-size:11px; font-family:ui-monospace, monospace; color:var(--muted-foreground); margin-bottom:4px;">ESTADO DEL SIMULADOR</span>
        <div>
            <span class="badge-subtle" style="font-weight:700;">
                <i class="fa fa-microchip"></i> MOTOR DETERMINISTA ACTIVO
            </span>
        </div>
    </div>
</div>

{{-- Banner de rigor científico obligatorio --}}
<div class="sim-disclaimer">
    <i class="fa fa-info-circle" style="font-size:16px;"></i>
    <div>
        <strong>AVISO DE MODELADO CIENTÍFICO:</strong> Todos los datos de este módulo son sintéticos y generados mediante fórmulas fisiológicas explicables con semilla fija para investigación y pruebas de estrés biomédico. No reemplazan el diagnóstico clínico.
    </div>
</div>

{{-- Controles de Simulación (Escenario, Semilla, Astronauta) --}}
<div class="sim-controls-card">
    <form method="GET" action="{{ route('admin.mission-simulator') }}" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)) auto; gap:16px; align-items:flex-end;">
        <div>
            <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted-foreground);">Escenario de Vuelo:</label>
            <select name="scenario" class="form-control" style="width:100%; height:38px;">
                <option value="scenario-1-stable" @selected(in_array($selectedScenario, ['scenario-A', 'scenario-1-stable', 'A']))>1. Control Estable (Normofisiológico) · Escenario A</option>
                <option value="scenario-2-gradual" @selected(in_array($selectedScenario, ['scenario-B', 'scenario-2-gradual', 'B']))>2. Hiperopía Gradual (+0.75 a +1.50 D) · Escenario B</option>
                <option value="scenario-C" @selected(in_array($selectedScenario, ['scenario-C', 'C']))>3. Cambio Transitorio (Pico agudo en FD45) · Escenario C</option>
                <option value="scenario-D" @selected(in_array($selectedScenario, ['scenario-D', 'D']))>4. Cambios Visuales Persistentes · Escenario D</option>
                <option value="scenario-3-sans-critical" @selected(in_array($selectedScenario, ['scenario-E', 'scenario-3-sans-critical', 'E']))>5. SANS Crítico (+1.75 D / Artemis III) · Escenario E</option>
            </select>
        </div>

        <div>
            <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted-foreground);">Tripulante:</label>
            <select name="astro" class="form-control" style="width:100%; height:38px;">
                @foreach ($astronauts as $astro)
                    <option value="{{ $astro->code }}" @selected($selectedAstro === $astro->code)>
                        {{ $astro->code }} - {{ $astro->name }} ({{ $astro->agency }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted-foreground);">Semilla Determinista:</label>
            <input type="number" name="seed" value="{{ $seed }}" class="form-control" style="width:100%; height:38px;">
        </div>

        <div>
            <button type="submit" class="btn btn-primary" style="padding:9px 18px; font-size:13px; font-weight:700;">
                <i class="fa fa-refresh"></i> Cargar Escenario
            </button>
        </div>
    </form>
</div>

{{-- Barra Deslizante y Play/Pause de Línea Temporal --}}
<div class="sim-controls-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <button type="button" class="btn btn-outline" id="sim-play-btn" style="font-size:13px;">
                <i class="fa fa-play" id="sim-play-icon"></i> <span id="sim-play-text">Iniciar Simulación</span>
            </button>
            <div style="display:inline-flex; border:1px solid var(--border); border-radius:6px; overflow:hidden;">
                <button type="button" class="speed-btn active" data-speed="1500">1x</button>
                <button type="button" class="speed-btn" data-speed="800">2x</button>
                <button type="button" class="speed-btn" data-speed="400">5x</button>
            </div>
        </div>
        <div style="font-size:18px; font-weight:800; font-family:ui-monospace, monospace; color:var(--foreground);" id="sim-day-badge">
            DÍA DE VUELO: FD0
        </div>
    </div>
    <input type="range" min="0" max="{{ count($days) - 1 }}" value="0" step="1" class="slider-rail" id="sim-slider" style="width:100%; height:8px; accent-color:var(--foreground); cursor:pointer;">
    <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--muted-foreground); margin-top:8px; font-family:ui-monospace, monospace;">
        @foreach ($days as $idx => $d)
            <span style="cursor:pointer;" onclick="setSimIndex({{ $idx }})">FD{{ $d }}</span>
        @endforeach
    </div>
</div>

{{-- Rejilla Interactiva de Gráfica y Telemetría --}}
<div class="sim-grid">
    {{-- Columna Izquierda: Curva de Evolución Refractiva en Chart.js --}}
    <div class="card" style="padding:20px; margin:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <div style="font-size:12px; font-weight:700; text-transform:uppercase; color:var(--muted-foreground);">
                Curva Longitudinal de Shift Hipermetrópico (&Delta;SE)
            </div>
            <span style="font-size:11px; color:var(--muted-foreground); font-family:ui-monospace, monospace;">UMBRAL CLÍNICO: +0.75 D</span>
        </div>
        <div style="position:relative; height:340px;">
            <canvas id="simChart"></canvas>
        </div>
    </div>

    {{-- Columna Derecha: Indicadores Biomédicos en Tiempo Real --}}
    <div style="display:flex; flex-direction:column; gap:12px;">
        <div class="gauge-pill">
            <span style="font-size:12px; color:var(--muted-foreground); font-weight:700;">Shift Hipermetrópico (OD)</span>
            <span class="gauge-val" id="val-od-shift">+0.00 D</span>
        </div>
        <div class="gauge-pill">
            <span style="font-size:12px; color:var(--muted-foreground); font-weight:700;">Agudeza Snellen (LogMAR)</span>
            <span class="gauge-val" id="val-snellen">20/20 (0.000)</span>
        </div>
        <div class="gauge-pill">
            <span style="font-size:12px; color:var(--muted-foreground); font-weight:700;">Presión Intraocular (IOP)</span>
            <span class="gauge-val" id="val-iop">15.0 mmHg</span>
        </div>
        <div class="gauge-pill">
            <span style="font-size:12px; color:var(--muted-foreground); font-weight:700;">Edema Papilar (Escala Frisén)</span>
            <span class="gauge-val" id="val-frisen">Grado 0</span>
        </div>
        <div class="gauge-pill">
            <span style="font-size:12px; color:var(--muted-foreground); font-weight:700;">Aplanamiento de Polo Posterior</span>
            <span class="gauge-val" id="val-flattening" style="font-size:14px;">NEGATIVO</span>
        </div>
        <div class="gauge-pill">
            <span style="font-size:12px; color:var(--muted-foreground); font-weight:700;">Pliegues Coroideos</span>
            <span class="gauge-val" id="val-folds" style="font-size:14px;">AUSENTES</span>
        </div>

        {{-- Tarjeta de Diagnóstico y Recomendación Médica --}}
        <div class="card" style="padding:16px; margin:0;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <span style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted-foreground);">Clasificación de Riesgo:</span>
                <span id="val-risk-tier" class="badge-subtle" style="font-size:11px; font-weight:800;">
                    RIESGO BAJO
                </span>
            </div>
            <div id="val-recommendation" style="font-size:12px; color:var(--muted-foreground); line-height:1.5;">
                Valores dentro de tolerancia operacional en microgravedad.
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const simDays = @json($days);
    const simFrames = @json($frames);

    let currentSimIndex = 0;
    let simInterval = null;
    let currentSpeed = 1500;

    // Configurar Chart.js
    const canvasEl = document.getElementById('simChart');
    if (!canvasEl) return;
    const ctx = canvasEl.getContext('2d');
    const chartDays = simDays.map(d => 'FD' + d);
    const allOdShifts = simDays.map(d => simFrames[d].od_shift);
    const allOsShifts = simDays.map(d => simFrames[d].os_shift);

    const isDark = document.documentElement.dataset.theme === 'dark';
    const textColor = isDark ? '#f4f4f5' : '#09090b';
    const textMuted = isDark ? '#a1a1aa' : '#3f3f46';
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.08)';

    const simChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartDays,
            datasets: [
                {
                    label: 'Ojo Derecho (OD)',
                    data: [allOdShifts[0]],
                    borderColor: isDark ? '#ffffff' : '#09090b',
                    backgroundColor: isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(9, 9, 11, 0.05)',
                    borderWidth: 2.5,
                    tension: 0.3,
                    fill: true,
                },
                {
                    label: 'Ojo Izquierdo (OS)',
                    data: [allOsShifts[0]],
                    borderColor: isDark ? '#a1a1aa' : '#52525b',
                    borderWidth: 1.8,
                    borderDash: [4, 4],
                    tension: 0.3,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    min: -0.2,
                    max: 2.2,
                    title: { display: true, text: 'Shift Hipermetrópico (Dioptrías)', color: textColor, font: { weight: '600' } },
                    grid: { color: gridColor },
                    ticks: { color: textMuted, font: { weight: '600' } }
                },
                x: {
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { weight: '600' } }
                }
            },
            plugins: {
                legend: { labels: { color: textColor, font: { weight: '600' } } }
            }
        }
    });

    function renderSimFrame(idx) {
        currentSimIndex = idx;
        const day = simDays[idx];
        const data = simFrames[day];
        if (!data) return;

        const dayBadge = document.getElementById('sim-day-badge');
        if (dayBadge) dayBadge.textContent = 'DÍA DE VUELO: FD' + day;

        const odShiftEl = document.getElementById('val-od-shift');
        if (odShiftEl) odShiftEl.textContent = (data.od_shift >= 0 ? '+' : '') + Number(data.od_shift).toFixed(2) + ' D';

        const snellenEl = document.getElementById('val-snellen');
        if (snellenEl) snellenEl.textContent = data.snellen + ' (' + Number(data.logmar).toFixed(3) + ')';

        const iopEl = document.getElementById('val-iop');
        if (iopEl) iopEl.textContent = Number(data.iop).toFixed(1) + ' mmHg';

        const frisenEl = document.getElementById('val-frisen');
        if (frisenEl) frisenEl.textContent = 'Grado ' + data.frisen;

        const flatEl = document.getElementById('val-flattening');
        if (flatEl) flatEl.textContent = data.flattening ? 'POSITIVO (Detectado)' : 'NEGATIVO';

        const foldsEl = document.getElementById('val-folds');
        if (foldsEl) foldsEl.textContent = data.folds ? 'PRESENTES' : 'AUSENTES';

        const tierEl = document.getElementById('val-risk-tier');
        if (tierEl) {
            if (data.risk_tier === 'severe' || data.risk_tier === 'high') {
                tierEl.textContent = 'RIESGO ALTO (TIER 3)';
            } else if (data.risk_tier === 'moderate' || data.risk_tier === 'medium') {
                tierEl.textContent = 'RIESGO MODERADO (TIER 2)';
            } else {
                tierEl.textContent = 'RIESGO BAJO (TIER 1)';
            }
        }

        const recEl = document.getElementById('val-recommendation');
        if (recEl) recEl.textContent = data.recommendation;

        // Actualizar gráfica hasta el día actual
        simChart.data.datasets[0].data = allOdShifts.slice(0, idx + 1);
        simChart.data.datasets[1].data = allOsShifts.slice(0, idx + 1);
        simChart.update();
    }

    function setSimIndex(idx) {
        const slider = document.getElementById('sim-slider');
        if (slider) slider.value = idx;
        renderSimFrame(idx);
    }
    window.setSimIndex = setSimIndex;

    const sliderEl = document.getElementById('sim-slider');
    if (sliderEl) {
        sliderEl.addEventListener('input', function() {
            renderSimFrame(parseInt(this.value));
        });
    }

    // Control Play / Pause
    const playBtn = document.getElementById('sim-play-btn');
    if (playBtn) {
        playBtn.addEventListener('click', function() {
            const playIcon = document.getElementById('sim-play-icon');
            const playText = document.getElementById('sim-play-text');
            if (simInterval) {
                clearInterval(simInterval);
                simInterval = null;
                if (playIcon) playIcon.className = 'fa fa-play';
                if (playText) playText.textContent = 'Continuar Simulación';
            } else {
                if (playIcon) playIcon.className = 'fa fa-pause';
                if (playText) playText.textContent = 'Pausar';
                simInterval = setInterval(() => {
                    let next = (currentSimIndex + 1) % simDays.length;
                    setSimIndex(next);
                }, currentSpeed);
            }
        });
    }

    // Selectores de velocidad
    document.querySelectorAll('.speed-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.speed-btn').forEach(b => {
                b.classList.remove('active');
            });
            this.classList.add('active');
            currentSpeed = parseInt(this.getAttribute('data-speed'));
            if (simInterval) {
                clearInterval(simInterval);
                simInterval = setInterval(() => {
                    let next = (currentSimIndex + 1) % simDays.length;
                    setSimIndex(next);
                }, currentSpeed);
            }
        });
    });

    renderSimFrame(0);
});
</script>
@endpush
