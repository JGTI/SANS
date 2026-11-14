@extends('layouts.app')

@section('title', 'Demo Theater · Recorrido Interactivo para Jueces')

@push('styles')
<style>
    .theater-hero {
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
    .step-progress-bar {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        margin-bottom: 24px;
    }
    @media (max-width: 900px) {
        .step-progress-bar {
            grid-template-columns: 1fr;
        }
    }
    .progress-step-btn {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 14px 16px;
        text-align: left;
        cursor: pointer;
        transition: all 0.15s ease;
        box-shadow: var(--shadow);
    }
    .progress-step-btn:hover {
        background: var(--accent);
        border-color: var(--foreground);
    }
    .progress-step-btn.active {
        background: var(--foreground);
        color: var(--background);
        border-color: var(--foreground);
    }
    .step-number {
        font-family: ui-monospace, SFMono-Regular, monospace;
        font-size: 11px;
        font-weight: 700;
        color: var(--muted-foreground);
        display: block;
        margin-bottom: 4px;
    }
    .progress-step-btn.active .step-number {
        color: color-mix(in srgb, var(--background) 75%, transparent);
    }
    .step-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--foreground);
        display: block;
    }
    .progress-step-btn.active .step-title {
        color: var(--background);
    }
    .stage-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 28px;
        min-height: 420px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: var(--shadow);
    }
    .metric-bubble {
        background: var(--background);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 18px;
    }
    .bubble-val {
        font-size: 24px;
        font-weight: 800;
        font-family: ui-monospace, monospace;
        color: var(--foreground);
    }
    .bubble-label {
        font-size: 11px;
        text-transform: uppercase;
        color: var(--muted-foreground);
        font-weight: 700;
        letter-spacing: 0.05em;
    }
</style>
@endpush

@section('content')
<div class="theater-hero">
    <div>
        <div class="eyebrow">
            NASA SPACE APPS CHALLENGE 2026 · LIVE DEMONSTRATION THEATER
        </div>
        <h1 style="margin:4px 0 8px; font-size:24px; font-weight:800; color:var(--foreground); letter-spacing:-0.03em;">
            Recorrido Guiado de Demostración (3 Minutos)
        </h1>
        <p style="margin:0; font-size:13px; color:var(--muted-foreground); max-width:640px;">
            Experiencia estructurada para jueces de hackatón. Recorre la hipótesis científica, evolución fisiológica del astronauta, algoritmo explicable de SANS y generación del reporte clínico oficial de misión.
        </p>
    </div>
    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <a href="{{ route('admin.sans-monitor.index', ['tab' => 'biomarcadores']) }}" class="btn btn-primary" style="font-size:13px;">
            <i class="fa fa-heartbeat"></i> Abrir Predictor SANS (3 Pestañas)
        </a>
        <a href="{{ route('admin.demo-theater.report', ['code' => $astronaut->code]) }}" target="_blank" class="btn btn-outline" style="font-size:13px;">
            <i class="fa fa-print"></i> Ver Reporte Oficial Imprimible
        </a>
    </div>
</div>

{{-- Barra de Progreso de 5 Pasos del Pitch --}}
<div class="step-progress-bar">
    <div class="progress-step-btn active" id="p-btn-1" onclick="goToStep(1)">
        <span class="step-number">01 // MIN 0:00 - 0:30</span>
        <span class="step-title">Misión & Problema SANS</span>
    </div>
    <div class="progress-step-btn" id="p-btn-2" onclick="goToStep(2)">
        <span class="step-number">02 // MIN 0:30 - 1:00</span>
        <span class="step-title">Línea Base del Astronauta</span>
    </div>
    <div class="progress-step-btn" id="p-btn-3" onclick="goToStep(3)">
        <span class="step-number">03 // MIN 1:00 - 1:45</span>
        <span class="step-title">Simulación Microgravedad</span>
    </div>
    <div class="progress-step-btn" id="p-btn-4" onclick="goToStep(4)">
        <span class="step-number">04 // MIN 1:45 - 2:30</span>
        <span class="step-title">Detección Algorítmica</span>
    </div>
    <div class="progress-step-btn" id="p-btn-5" onclick="goToStep(5)">
        <span class="step-number">05 // MIN 2:30 - 3:00</span>
        <span class="step-title">Evidencia NASA & Reporte</span>
    </div>
</div>

{{-- Escenarios del Demo Theater --}}

{{-- PASO 1: Misión y Problema --}}
<div class="stage-card" id="step-panel-1">
    <div>
        <div class="badge-subtle" style="margin-bottom:12px; font-weight:700;">
            PASO 1 DE 5 · CONTEXTO CLÍNICO ESPACIAL
        </div>
        <h2 style="font-size:22px; font-weight:800; color:var(--foreground); margin:0 0 12px; letter-spacing:-0.02em;">
            El Desafío: Síndrome Neuroocular Asociado al Vuelo Espacial (SANS)
        </h2>
        <p style="font-size:14px; color:var(--muted-foreground); line-height:1.7; max-width:850px; margin-bottom:24px;">
            En misiones de microgravedad prolongada (ISS, Artemis lunar, futura misión a Marte), la ausencia de gradiente hidrostático terrestre desplaza cerca de <strong>2 litros de fluidos hacia la región cefálica</strong>. Esto incrementa la presión venosa yugular y retrobulbar, generando aplanamiento posterior del globo ocular, edema del disco óptico (papiledema) y un progresivo <strong>desplazamiento refractivo hipermetrópico</strong>.
        </p>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px; margin-top:20px;">
            <div class="metric-bubble">
                <div class="bubble-label">Población Afectada en ISS</div>
                <div class="bubble-val">~70%</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:4px;">Astronautas en misiones de más de 6 meses presentan algún signo estructural de SANS.</div>
            </div>
            <div class="metric-bubble">
                <div class="bubble-label">Desplazamiento Hipermetrópico</div>
                <div class="bubble-val">+0.50 a +1.75 D</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:4px;">Pérdida de foco cercano en el visor del casco y displays de cabina.</div>
            </div>
            <div class="metric-bubble">
                <div class="bubble-label">Propósito de SPACE VISION</div>
                <div class="bubble-val" style="font-size:20px;">Monitoreo & SANS</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:4px;">Vigilancia médica longitudinal, simulación anatómica y cruce de datos NASA.</div>
            </div>
        </div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:16px; margin-top:24px;">
        <span style="font-size:12px; color:var(--muted-foreground);">Demostración para jueces · Space Apps Challenge 2026</span>
        <button type="button" class="btn btn-primary" onclick="goToStep(2)">
            Siguiente: Cargar Perfil de Tripulante <i class="fa fa-arrow-right"></i>
        </button>
    </div>
</div>

{{-- PASO 2: Línea Base del Astronauta --}}
<div class="stage-card" id="step-panel-2" style="display:none;">
    <div>
        <div class="badge-subtle" style="margin-bottom:12px; font-weight:700;">
            PASO 2 DE 5 · TELEMETRÍA BASAL PRE-VUELO (FD0)
        </div>
        <h2 style="font-size:22px; font-weight:800; color:var(--foreground); margin:0 0 12px; letter-spacing:-0.02em;">
            Sujeto Seleccionado: {{ $astronaut->name }} ({{ $astronaut->code }})
        </h2>
        <p style="font-size:14px; color:var(--muted-foreground); line-height:1.7; margin-bottom:20px;">
            Línea base pre-lanzamiento registrada en el Centro Espacial Johnson. Visión basal 20/20 bilateral, equivalente esférico emétrope y fondo de ojo con disco óptico de bordes nítidos (Frisén 0).
        </p>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:20px;">
            <div class="metric-bubble">
                <div class="bubble-label">Misión Activa</div>
                <div class="bubble-val" style="font-size:18px;">{{ $astronaut->mission_name }}</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:2px;">Agencia: {{ $astronaut->agency }}</div>
            </div>
            <div class="metric-bubble">
                <div class="bubble-label">Refracción Basal (OD)</div>
                <div class="bubble-val" style="font-size:18px;">{{ number_format($astronaut->baseline_spherical_equivalent_od, 2) }} D</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:2px;">Snellen: 20/20 (LogMAR 0.00)</div>
            </div>
            <div class="metric-bubble">
                <div class="bubble-label">Presión Intraocular (Basal)</div>
                <div class="bubble-val" style="font-size:18px;">15.0 mmHg</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:2px;">Dentro de rango fisiológico nominal</div>
            </div>
            <div class="metric-bubble">
                <div class="bubble-label">Papila Óptica (Basal)</div>
                <div class="bubble-val" style="font-size:18px;">Frisén 0</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:2px;">Sin edema peripapilar visible</div>
            </div>
        </div>

        <div style="background:var(--muted); padding:14px 18px; border-radius:8px; border-left:4px solid var(--foreground);">
            <strong style="color:var(--foreground); font-size:13px;">Nota Clínica de Vuelo:</strong>
            <span style="color:var(--muted-foreground); font-size:13px;">
                El astronauta se encuentra en condiciones óptimas previas al desacoplamiento orbital. Comenzamos la simulación temporal en el entorno de microgravedad.
            </span>
        </div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:16px; margin-top:24px;">
        <button type="button" class="btn btn-outline" onclick="goToStep(1)">
            <i class="fa fa-arrow-left"></i> Anterior
        </button>
        <button type="button" class="btn btn-primary" onclick="goToStep(3)">
            Siguiente: Avanzar Tiempo de Misión <i class="fa fa-arrow-right"></i>
        </button>
    </div>
</div>

{{-- PASO 3: Simulación Microgravedad Prolongada --}}
<div class="stage-card" id="step-panel-3" style="display:none;">
    <div>
        <div class="badge-subtle" style="margin-bottom:12px; font-weight:700;">
            PASO 3 DE 5 · EVOLUCIÓN LONGITUDINAL EN MICROGRAVEDAD
        </div>
        <h2 style="font-size:22px; font-weight:800; color:var(--foreground); margin:0 0 12px; letter-spacing:-0.02em;">
            Avanzando Misión: Día de Vuelo FD0 &rarr; FD30 &rarr; FD60 &rarr; FD120
        </h2>
        <p style="font-size:14px; color:var(--muted-foreground); line-height:1.7; margin-bottom:20px;">
            A medida que transcurren las semanas en órbita, el registro oftálmico en el <strong>Vision Lab</strong> detecta un incremento constante en el hiperopic shift del ojo derecho (OD), acompañado de quejas clínicas de visión borrosa en distancia de lectura.
        </p>

        <div class="table-responsive">
            <table class="table" style="width:100%; font-size:13px;">
                <thead>
                    <tr>
                        <th>Día de Vuelo</th>
                        <th>Esfera (SPH)</th>
                        <th>Cilindro (CYL)</th>
                        <th>Equivalente Esférico (SE)</th>
                        <th>Shift Hipermetrópico</th>
                        <th>Agudeza Snellen</th>
                        <th>Presión (IOP)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($evals->where('eye', 'OD') as $ev)
                        <tr>
                            <td style="font-family:ui-monospace, monospace; font-weight:700;">FD{{ $ev->mission_day }}</td>
                            <td>{{ number_format($ev->sphere, 2) }} D</td>
                            <td>{{ number_format($ev->cylinder, 2) }} D</td>
                            <td style="font-weight:700;">{{ number_format($ev->spherical_equivalent, 2) }} D</td>
                            <td style="font-weight:700;">
                                +{{ number_format($ev->hyperopic_shift, 2) }} D
                            </td>
                            <td>{{ $ev->snellen_fraction }}</td>
                            <td>{{ number_format($ev->intraocular_pressure, 1) }} mmHg</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:16px; margin-top:24px;">
        <button type="button" class="btn btn-outline" onclick="goToStep(2)">
            <i class="fa fa-arrow-left"></i> Anterior
        </button>
        <button type="button" class="btn btn-primary" onclick="goToStep(4)">
            Siguiente: Ejecutar Algoritmo SANS <i class="fa fa-arrow-right"></i>
        </button>
    </div>
</div>

{{-- PASO 4: Detección Algorítmica de SANS --}}
<div class="stage-card" id="step-panel-4" style="display:none;">
    <div>
        <div class="badge-subtle" style="margin-bottom:12px; font-weight:700;">
            PASO 4 DE 5 · ALGORITMO EXPLICABLE DE ESTRATIFICACIÓN
        </div>
        <h2 style="font-size:22px; font-weight:800; color:var(--foreground); margin:0 0 12px; letter-spacing:-0.02em;">
            Detección Multivariable de SANS: RIESGO ALTO (Tier 3)
        </h2>
        <p style="font-size:14px; color:var(--muted-foreground); line-height:1.7; margin-bottom:20px;">
            El motor analítico de SPACE VISION combina variaciones refractivas y signos estructurales para clasificar de manera explicable la severidad del cuadro clínico:
        </p>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:20px;">
            <div style="background:var(--card); border:1px solid var(--border); border-left:4px solid var(--foreground); padding:16px; border-radius:8px;">
                <div style="font-size:11px; color:var(--muted-foreground); font-weight:700; text-transform:uppercase;">Shift Hipermetrópico</div>
                <div style="font-size:20px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">+1.50 D</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:4px;">Supera el umbral crítico de significancia clínica (&ge; +0.75 D).</div>
            </div>
            <div style="background:var(--card); border:1px solid var(--border); border-left:4px solid var(--foreground); padding:16px; border-radius:8px;">
                <div style="font-size:11px; color:var(--muted-foreground); font-weight:700; text-transform:uppercase;">Edema de Papila (Frisén)</div>
                <div style="font-size:20px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">Grado 2</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:4px;">Halo circunferencial de elevación del disco óptico.</div>
            </div>
            <div style="background:var(--card); border:1px solid var(--border); border-left:4px solid var(--foreground); padding:16px; border-radius:8px;">
                <div style="font-size:11px; color:var(--muted-foreground); font-weight:700; text-transform:uppercase;">Aplanamiento Posterior</div>
                <div style="font-size:20px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">POSITIVO</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:4px;">Deformación observable del polo posterior y acortamiento axial.</div>
            </div>
            <div style="background:var(--card); border:1px solid var(--border); border-left:4px solid var(--foreground); padding:16px; border-radius:8px;">
                <div style="font-size:11px; color:var(--muted-foreground); font-weight:700; text-transform:uppercase;">Pliegues Coroideos</div>
                <div style="font-size:20px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">PRESENTES</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:4px;">Líneas paralelas de tensión biomecánica en la coroides.</div>
            </div>
        </div>

        <div style="background:var(--muted); border:1px solid var(--border); padding:14px 18px; border-radius:8px;">
            <strong style="color:var(--foreground); font-size:13px;"><i class="fa fa-stethoscope"></i> Recomendación de Medicina Aeroespacial:</strong>
            <span style="color:var(--muted-foreground); font-size:13px; display:block; margin-top:4px;">
                Iniciar protocolo de contramedidas mecánicas (Charly / LBNP nocturno), prescripción de lentes de ajuste hipermetrópico para lecturas de navegación y seguimiento semanal mediante OCT.
            </span>
        </div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:16px; margin-top:24px;">
        <button type="button" class="btn btn-outline" onclick="goToStep(3)">
            <i class="fa fa-arrow-left"></i> Anterior
        </button>
        <button type="button" class="btn btn-primary" onclick="goToStep(5)">
            Siguiente: Evidencia NASA & Reporte <i class="fa fa-arrow-right"></i>
        </button>
    </div>
</div>

{{-- PASO 5: Evidencia NASA & Generación de Reporte --}}
<div class="stage-card" id="step-panel-5" style="display:none;">
    <div>
        <div class="badge-subtle" style="margin-bottom:12px; font-weight:700;">
            PASO 5 DE 5 · VALIDACIÓN CIENTÍFICA & GENERACIÓN DE REPORTE
        </div>
        <h2 style="font-size:22px; font-weight:800; color:var(--foreground); margin:0 0 12px; letter-spacing:-0.02em;">
            Cruce de Literatura NASA y Reporte Oficial de Misión
        </h2>
        <p style="font-size:14px; color:var(--muted-foreground); line-height:1.7; margin-bottom:20px;">
            SPACE VISION respalda cada hallazgo clínico vinculándolo con estudios del repositorio biológico <strong>NASA OSDR</strong> y publicaciones técnicas del <strong>NASA Human Research Program</strong>.
        </p>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px;">
            <div style="background:var(--background); border:1px solid var(--border); padding:16px; border-radius:8px;">
                <div style="font-size:11px; font-weight:700; color:var(--muted-foreground); text-transform:uppercase; margin-bottom:6px;">
                    <i class="fa fa-flask"></i> Evidencia en NASA OSDR
                </div>
                <div style="font-size:13px; font-weight:700; color:var(--foreground);">OSD-137: Retinal & Choroidal Alterations</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:4px;">
                    Comprobación histopatológica en modelos espaciales de congestión venosa y pérdida de elasticidad de la esclera posterior.
                </div>
            </div>
            <div style="background:var(--background); border:1px solid var(--border); padding:16px; border-radius:8px;">
                <div style="font-size:11px; font-weight:700; color:var(--muted-foreground); text-transform:uppercase; margin-bottom:6px;">
                    <i class="fa fa-book"></i> Publicación NASA NTRS
                </div>
                <div style="font-size:13px; font-weight:700; color:var(--foreground);">NTRS #20200002341 (Stenger et al.)</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:4px;">
                    NASA HRP Evidence Report oficial sobre incidencia, factores genéticos y contramedidas de SANS en tripulaciones de larga duración.
                </div>
            </div>
        </div>

        <div style="text-align:center; padding:28px; background:var(--muted); border-radius:10px; border:1px solid var(--border);">
            <h3 style="font-size:18px; font-weight:800; color:var(--foreground); margin:0 0 8px;">
                Reporte Médico Aeroespacial Oficial Listo
            </h3>
            <p style="font-size:13px; color:var(--muted-foreground); margin:0 0 16px;">
                Genera el expediente clínico de vuelo con formato estándar NASA / Artemis, tablas de refracción, análisis de agudeza visual y firmas médicas.
            </p>
            <a href="{{ route('admin.demo-theater.report', ['code' => $astronaut->code]) }}" target="_blank" class="btn btn-primary" style="padding:10px 24px; font-size:14px; font-weight:700;">
                <i class="fa fa-file-text-o"></i> Abrir Expediente Oficial para Impresión / PDF
            </a>
        </div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:16px; margin-top:24px;">
        <button type="button" class="btn btn-outline" onclick="goToStep(4)">
            <i class="fa fa-arrow-left"></i> Anterior
        </button>
        <button type="button" class="btn btn-outline" onclick="goToStep(1)">
            <i class="fa fa-refresh"></i> Reiniciar Recorrido
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
function goToStep(step) {
    for (let i = 1; i <= 5; i++) {
        const panel = document.getElementById('step-panel-' + i);
        const btn = document.getElementById('p-btn-' + i);
        if (panel) {
            panel.style.display = (i === step) ? 'flex' : 'none';
        }
        if (btn) {
            if (i === step) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        }
    }
    window.scrollTo({ top: 120, behavior: 'smooth' });
}
</script>
@endpush
