@extends('layouts.app')
@php $isEn = app()->getLocale() === 'en'; @endphp

@section('title', $isEn ? '3-Min Demo Pitch · Guided Walkthrough for Judges' : 'Demo Theater · Recorrido Interactivo para Jueces')

@push('styles')
<style>
    .theater-hero {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 24px 28px;
        margin-bottom: 20px;
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
            NASA SPACE APPS CHALLENGE 2026 · LIVE DEMONSTRATION THEATER ({{ $linkedSansCase->case_id ?? 'CASE-PROG-02' }})
        </div>
        <h1 style="margin:4px 0 8px; font-size:24px; font-weight:800; color:var(--foreground); letter-spacing:-0.03em;">
            {{ $isEn
                ? '3-Minute Judge Walkthrough (Recorrido Guiado de Demostración)'
                : 'Recorrido Guiado de Demostración (3 Minutos)' }}
        </h1>
        <p style="margin:0; font-size:13px; color:var(--muted-foreground); max-width:680px;">
            {{ $isEn
                ? 'Guided narrative connecting the 3-Step SANS Monitor (6 Core Biomarkers, Before · During · After +30/+60/+90d cuts, and 3 Reference Scenarios) with the printable dossier — without fake physician signatures or automated fit-for-duty verdicts.'
                : 'Narrativa guiada que conecta el SANS Monitor de 3 Pasos (6 Biomarcadores, cortes Antes · Durante · Después +30/+60/+90d y 3 Escenarios Modelos) con el expediente imprimible — sin firmas médicas falsas ni dictámenes automáticos de aptitud.' }}
        </p>
    </div>
    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <a href="{{ url('/?case_id=' . ($linkedSansCase->case_id ?? 'CASE-PROG-02')) }}" class="btn btn-primary" style="font-size:13px;">
            <i class="fa fa-sitemap"></i> {{ $isEn ? 'Open 3-Step SANS Monitor' : 'Abrir SANS Monitor (Wizard 3 Pasos)' }}
        </a>
        <a href="{{ route('public.demo-theater.report', ['code' => $astronaut->code]) }}" target="_blank" class="btn btn-outline" style="font-size:13px;">
            <i class="fa fa-print"></i> {{ $isEn ? 'Printable Technical Dossier (PDF)' : 'Ver Expediente Imprimible (PDF)' }}
        </a>
    </div>
</div>

{{-- Puente de Trazabilidad Directa con los 3 Pasos del SANS Monitor --}}
<div class="card" style="padding:14px 18px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
    <div style="font-size:12px;">
        <strong><i class="fa fa-link"></i> {{ $isEn ? 'Direct Connection with Core Project (SANS Monitor):' : 'Relación Directa con el Proyecto Central (SANS Monitor):' }}</strong>
        <span style="color:var(--muted-foreground);">
            {{ $isEn
                ? "Active Subject {$astronaut->name} ({$astronaut->code}) maps to Dossier {$linkedSansCase->case_id} ({$linkedSansCase->scenario_archetype})."
                : "El sujeto activo {$astronaut->name} ({$astronaut->code}) corresponde al expediente {$linkedSansCase->case_id} ({$linkedSansCase->scenario_archetype})." }}
        </span>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ url('/?tab=biomarcadores&case_id=' . ($linkedSansCase->case_id ?? 'CASE-PROG-02')) }}" class="btn btn-outline" style="padding:4px 10px;font-size:12px;">
            {{ $isEn ? 'Step 1: 6 Biomarkers' : 'Paso 1: 6 Biomarcadores' }}
        </a>
        <a href="{{ url('/?tab=seguimiento&case_id=' . ($linkedSansCase->case_id ?? 'CASE-PROG-02')) }}" class="btn btn-outline" style="padding:4px 10px;font-size:12px;">
            {{ $isEn ? 'Step 2: +30/+60/+90d Cuts' : 'Paso 2: Cortes +30/+60/+90d' }}
        </a>
        <a href="{{ url('/?tab=escenarios&case_id=' . ($linkedSansCase->case_id ?? 'CASE-PROG-02')) }}" class="btn btn-outline" style="padding:4px 10px;font-size:12px;">
            {{ $isEn ? 'Step 3: 3 Scenarios' : 'Paso 3: 3 Escenarios' }}
        </a>
    </div>
</div>

{{-- Barra de Progreso de 5 Pasos del Pitch --}}
<div class="step-progress-bar">
    <div class="progress-step-btn active" id="p-btn-1" onclick="goToStep(1)">
        <span class="step-number">01 // MIN 0:00 - 0:30</span>
        <span class="step-title">{{ $isEn ? 'Mission & SANS Problem' : 'Misión & Problema SANS' }}</span>
    </div>
    <div class="progress-step-btn" id="p-btn-2" onclick="goToStep(2)">
        <span class="step-number">02 // MIN 0:30 - 1:00</span>
        <span class="step-title">{{ $isEn ? 'Pre-Flight Baseline (Step 1)' : 'Línea Base del Astronauta' }}</span>
    </div>
    <div class="progress-step-btn" id="p-btn-3" onclick="goToStep(3)">
        <span class="step-number">03 // MIN 1:00 - 1:45</span>
        <span class="step-title">{{ $isEn ? 'Longitudinal Cuts (Step 2)' : 'Simulación Microgravedad' }}</span>
    </div>
    <div class="progress-step-btn" id="p-btn-4" onclick="goToStep(4)">
        <span class="step-number">04 // MIN 1:45 - 2:30</span>
        <span class="step-title">{{ $isEn ? '6 Biomarkers & 3 Scenarios (Step 3)' : 'Detección Algorítmica' }}</span>
    </div>
    <div class="progress-step-btn" id="p-btn-5" onclick="goToStep(5)">
        <span class="step-number">05 // MIN 2:30 - 3:00</span>
        <span class="step-title">{{ $isEn ? 'NASA Evidence & Honest Dossier' : 'Evidencia NASA & Reporte' }}</span>
    </div>
</div>

{{-- PASO 1: Misión y Problema --}}
<div class="stage-card" id="step-panel-1">
    <div>
        <div class="badge-subtle" style="margin-bottom:12px; font-weight:700;">
            {{ $isEn ? 'STEP 1 OF 5 · SPACEFLIGHT CLINICAL CONTEXT' : 'PASO 1 DE 5 · CONTEXTO CLÍNICO ESPACIAL' }}
        </div>
        <h2 style="font-size:22px; font-weight:800; color:var(--foreground); margin:0 0 12px; letter-spacing:-0.02em;">
            {{ $isEn
                ? 'The Challenge: Spaceflight-Associated Neuro-ocular Syndrome (El Desafío: Síndrome Neuroocular Asociado al Vuelo Espacial - SANS)'
                : 'El Desafío: Síndrome Neuroocular Asociado al Vuelo Espacial (SANS)' }}
        </h2>
        <p style="font-size:14px; color:var(--muted-foreground); line-height:1.7; max-width:850px; margin-bottom:24px;">
            {{ $isEn
                ? 'In long-duration microgravity missions (ISS, Artemis lunar missions, Mars transit), loss of the hydrostatic gradient shifts nearly 2 liters of fluid toward the head, inducing posterior globe flattening, optic disc edema (papilledema), and hyperopic refractive shift.'
                : 'En misiones de microgravedad prolongada (ISS, Artemis lunar, futura misión a Marte), la ausencia de gradiente hidrostático terrestre desplaza cerca de 2 litros de fluidos hacia la región cefálica, induciendo aplanamiento posterior del globo ocular, edema del disco óptico (papiledema) y un progresivo desplazamiento refractivo hipermetrópico.' }}
        </p>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px; margin-top:20px;">
            <div class="metric-bubble">
                <div class="bubble-label">{{ $isEn ? 'Affected ISS Crew' : 'Población Afectada en ISS' }}</div>
                <div class="bubble-val">~70%</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:4px;">
                    {{ $isEn ? 'Astronauts on >6-month missions show structural signs of SANS.' : 'Astronautas en misiones de más de 6 meses presentan algún signo estructural de SANS.' }}
                </div>
            </div>
            <div class="metric-bubble">
                <div class="bubble-label">{{ $isEn ? 'Hyperopic Shift' : 'Desplazamiento Hipermetrópico' }}</div>
                <div class="bubble-val">+0.50 a +1.75 D</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:4px;">
                    {{ $isEn ? 'Near-focus shift caused by axial length shortening.' : 'Pérdida de foco cercano en el visor del casco y displays de cabina.' }}
                </div>
            </div>
            <div class="metric-bubble">
                <div class="bubble-label">{{ $isEn ? 'Core SANS Monitor Architecture' : 'Propósito de SPACE VISION' }}</div>
                <div class="bubble-val" style="font-size:20px;">{{ $isEn ? '3-Step Wizard' : 'Monitoreo & SANS' }}</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:4px;">
                    {{ $isEn ? '6 Biomarkers · +30/+60/+90d Cuts · 3 Scenarios (Zero automated verdict).' : '6 Biomarcadores · Cortes +30/+60/+90d · 3 Escenarios (Sin dictamen automático).' }}
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:16px; margin-top:24px;">
        <span style="font-size:12px; color:var(--muted-foreground);">Space Apps Challenge 2026 · NEXUS &times; Visual Rehab Center</span>
        <button type="button" class="btn btn-primary" onclick="goToStep(2)">
            {{ $isEn ? 'Next: Load Subject Baseline' : 'Siguiente: Cargar Perfil de Tripulante' }} <i class="fa fa-arrow-right"></i>
        </button>
    </div>
</div>

{{-- PASO 2: Línea Base del Astronauta --}}
<div class="stage-card" id="step-panel-2" style="display:none;">
    <div>
        <div class="badge-subtle" style="margin-bottom:12px; font-weight:700;">
            {{ $isEn ? 'STEP 2 OF 5 · PRE-FLIGHT BASELINE TELEMETRY (FD0)' : 'PASO 2 DE 5 · TELEMETRÍA BASAL PRE-VUELO (FD0)' }}
        </div>
        <h2 style="font-size:22px; font-weight:800; color:var(--foreground); margin:0 0 12px; letter-spacing:-0.02em;">
            {{ $isEn ? 'Selected Subject:' : 'Sujeto Seleccionado:' }} {{ $astronaut->name }} ({{ $astronaut->code }} &rarr; {{ $linkedSansCase->case_id ?? 'CASE-PROG-02' }})
        </h2>
        <p style="font-size:14px; color:var(--muted-foreground); line-height:1.7; margin-bottom:20px;">
            {{ $isEn
                ? 'Pre-launch baseline recorded at Day 0: 20/20 bilateral visual acuity, emmetropic spherical equivalent, OCT baseline 280 µm, and Frisén Grade 0.'
                : 'Línea base pre-lanzamiento registrada en el Día 0: visión basal 20/20 bilateral, equivalente esférico emétrope, OCT basal 280 µm y Frisén Grado 0.' }}
        </p>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:20px;">
            <div class="metric-bubble">
                <div class="bubble-label">{{ $isEn ? 'Active Mission' : 'Misión Activa' }}</div>
                <div class="bubble-val" style="font-size:18px;">{{ $astronaut->mission_name }}</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:2px;">{{ $isEn ? 'Linked Case:' : 'Expediente:' }} {{ $linkedSansCase->case_id ?? 'CASE-PROG-02' }}</div>
            </div>
            <div class="metric-bubble">
                <div class="bubble-label">{{ $isEn ? 'Baseline Refraction (OD)' : 'Refracción Basal (OD)' }}</div>
                <div class="bubble-val" style="font-size:18px;">{{ number_format($astronaut->baseline_spherical_equivalent_od, 2) }} D</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:2px;">Snellen: 20/20 (LogMAR 0.00)</div>
            </div>
            <div class="metric-bubble">
                <div class="bubble-label">{{ $isEn ? 'Baseline IOP' : 'Presión Intraocular (Basal)' }}</div>
                <div class="bubble-val" style="font-size:18px;">15.0 mmHg</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:2px;">{{ $isEn ? 'Normal terrestrial range' : 'Dentro de rango fisiológico nominal' }}</div>
            </div>
            <div class="metric-bubble">
                <div class="bubble-label">{{ $isEn ? 'Baseline Optic Disc' : 'Papila Óptica (Basal)' }}</div>
                <div class="bubble-val" style="font-size:18px;">Frisén 0</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:2px;">{{ $isEn ? 'Baseline OCT: 280 µm' : 'OCT basal: 280 µm' }}</div>
            </div>
        </div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:16px; margin-top:24px;">
        <button type="button" class="btn btn-outline" onclick="goToStep(1)">
            <i class="fa fa-arrow-left"></i> {{ $isEn ? 'Previous' : 'Anterior' }}
        </button>
        <button type="button" class="btn btn-primary" onclick="goToStep(3)">
            {{ $isEn ? 'Next: Advance Mission Timeline' : 'Siguiente: Avanzar Tiempo de Misión' }} <i class="fa fa-arrow-right"></i>
        </button>
    </div>
</div>

{{-- PASO 3: Simulación Microgravedad Prolongada --}}
<div class="stage-card" id="step-panel-3" style="display:none;">
    <div>
        <div class="badge-subtle" style="margin-bottom:12px; font-weight:700;">
            {{ $isEn ? 'STEP 3 OF 5 · BEFORE · DURING · AFTER (+30, +60, +90 DAYS)' : 'PASO 3 DE 5 · EVOLUCIÓN LONGITUDINAL EN MICROGRAVEDAD' }}
        </div>
        <h2 style="font-size:22px; font-weight:800; color:var(--foreground); margin:0 0 12px; letter-spacing:-0.02em;">
            {{ $isEn ? 'Mission Timeline: FD0 → FD30 → FD60 → FD120 & Post-Flight Cuts (+30/+60/+90d)' : 'Avanzando Misión: Día de Vuelo FD0 → FD30 → FD60 → FD120 y Cortes Posvuelo' }}
        </h2>

        <div class="table-responsive">
            <table class="table" style="width:100%; font-size:13px;">
                <thead>
                    <tr>
                        <th>{{ $isEn ? 'Flight Day' : 'Día de Vuelo' }}</th>
                        <th>SPH</th>
                        <th>CYL</th>
                        <th>SE</th>
                        <th>{{ $isEn ? 'Hyperopic Shift' : 'Shift Hipermetrópico' }}</th>
                        <th>Snellen</th>
                        <th>IOP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($evals->where('eye', 'OD') as $ev)
                        <tr>
                            <td style="font-family:ui-monospace, monospace; font-weight:700;">FD{{ $ev->mission_day }}</td>
                            <td>{{ number_format($ev->sphere, 2) }} D</td>
                            <td>{{ number_format($ev->cylinder, 2) }} D</td>
                            <td style="font-weight:700;">{{ number_format($ev->spherical_equivalent, 2) }} D</td>
                            <td style="font-weight:700;">+{{ number_format($ev->hyperopic_shift, 2) }} D</td>
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
            <i class="fa fa-arrow-left"></i> {{ $isEn ? 'Previous' : 'Anterior' }}
        </button>
        <button type="button" class="btn btn-primary" onclick="goToStep(4)">
            {{ $isEn ? 'Next: Run SANS Biomarker Engine' : 'Siguiente: Ejecutar Algoritmo SANS' }} <i class="fa fa-arrow-right"></i>
        </button>
    </div>
</div>

{{-- PASO 4: Detección Algorítmica de SANS --}}
<div class="stage-card" id="step-panel-4" style="display:none;">
    <div>
        <div class="badge-subtle" style="margin-bottom:12px; font-weight:700;">
            {{ $isEn ? 'STEP 4 OF 5 · EXPLAINABLE 6-BIOMARKER STRATIFICATION' : 'PASO 4 DE 5 · ALGORITMO EXPLICABLE DE ESTRATIFICACIÓN' }}
        </div>
        <h2 style="font-size:22px; font-weight:800; color:var(--foreground); margin:0 0 12px; letter-spacing:-0.02em;">
            {{ $isEn ? 'Multivariable SANS Evaluation: Prioritize Review (Tier 3)' : 'Detección Multivariable de SANS: Priorizar Revisión (Tier 3)' }}
        </h2>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:20px;">
            <div style="background:var(--card); border:1px solid var(--border); border-left:4px solid var(--foreground); padding:16px; border-radius:8px;">
                <div style="font-size:11px; color:var(--muted-foreground); font-weight:700; text-transform:uppercase;">{{ $isEn ? '1. OCT Thickness Shift' : '1. Espesor Medio OCT' }}</div>
                <div style="font-size:20px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">280 &rarr; 306 &mu;m (+26 &mu;m)</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:4px;">{{ $isEn ? 'Exceeds +20 µm threshold.' : 'Supera el umbral de +20 µm.' }}</div>
            </div>
            <div style="background:var(--card); border:1px solid var(--border); border-left:4px solid var(--foreground); padding:16px; border-radius:8px;">
                <div style="font-size:11px; color:var(--muted-foreground); font-weight:700; text-transform:uppercase;">{{ $isEn ? '2. Hyperopic Shift' : '2. Shift Hipermetrópico' }}</div>
                <div style="font-size:20px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">+1.50 D</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:4px;">{{ $isEn ? 'Associated with 23.82 mm axial shortening.' : 'Asociado a acortamiento axial (23.82 mm).' }}</div>
            </div>
            <div style="background:var(--card); border:1px solid var(--border); border-left:4px solid var(--foreground); padding:16px; border-radius:8px;">
                <div style="font-size:11px; color:var(--muted-foreground); font-weight:700; text-transform:uppercase;">{{ $isEn ? '3. Papilledema (Frisén)' : '3. Edema de Papila (Frisén)' }}</div>
                <div style="font-size:20px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">{{ $isEn ? 'Grade 2 / 5' : 'Grado 2 / 5' }}</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:4px;">{{ $isEn ? 'Early circumferential optic disc elevation.' : 'Halo circunferencial de elevación del disco óptico.' }}</div>
            </div>
            <div style="background:var(--card); border:1px solid var(--border); border-left:4px solid var(--foreground); padding:16px; border-radius:8px;">
                <div style="font-size:11px; color:var(--muted-foreground); font-weight:700; text-transform:uppercase;">{{ $isEn ? '4. Fit-for-Duty Verdict' : '4. Dictamen de Aptitud' }}</div>
                <div style="font-size:20px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">{{ $isEn ? 'Not Evaluated' : 'Aptitud no evaluada' }}</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:4px;">{{ $isEn ? 'Prepared for human specialist review.' : 'Preparado para revisión de especialista humano.' }}</div>
            </div>
        </div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:16px; margin-top:24px;">
        <button type="button" class="btn btn-outline" onclick="goToStep(3)">
            <i class="fa fa-arrow-left"></i> {{ $isEn ? 'Previous' : 'Anterior' }}
        </button>
        <button type="button" class="btn btn-primary" onclick="goToStep(5)">
            {{ $isEn ? 'Next: NASA Evidence & Printable Dossier' : 'Siguiente: Evidencia NASA & Reporte' }} <i class="fa fa-arrow-right"></i>
        </button>
    </div>
</div>

{{-- PASO 5: Evidencia NASA & Generación de Reporte --}}
<div class="stage-card" id="step-panel-5" style="display:none;">
    <div>
        <div class="badge-subtle" style="margin-bottom:12px; font-weight:700;">
            {{ $isEn ? 'STEP 5 OF 5 · SCIENTIFIC TRACEABILITY & PRINTABLE DOSSIER' : 'PASO 5 DE 5 · VALIDACIÓN CIENTÍFICA & EXPEDIENTE IMPRIMIBLE' }}
        </div>
        <h2 style="font-size:22px; font-weight:800; color:var(--foreground); margin:0 0 12px; letter-spacing:-0.02em;">
            {{ $isEn ? 'NASA Literature Cross-Reference & Honest Technical Dossier' : 'Cruce de Literatura NASA y Expediente Técnico Sin Firmas Falsas' }}
        </h2>
        <p style="font-size:14px; color:var(--muted-foreground); line-height:1.7; margin-bottom:20px;">
            {{ $isEn
                ? 'Every finding is linked to NASA OSDR and NASA NTRS references. The printable PDF dossier explicitly marks Fit-for-Duty as "Not Evaluated" and omits fictitious physician signatures.'
                : 'Cada hallazgo se vincula con referencias de NASA OSDR y NASA NTRS. El expediente imprimible/PDF declara explícitamente "Aptitud no evaluada" y no incluye firmas médicas ficticias.' }}
        </p>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px;">
            <div style="background:var(--background); border:1px solid var(--border); padding:16px; border-radius:8px;">
                <div style="font-size:11px; font-weight:700; color:var(--muted-foreground); text-transform:uppercase; margin-bottom:6px;">
                    <i class="fa fa-flask"></i> NASA OSDR
                </div>
                <div style="font-size:13px; font-weight:700; color:var(--foreground);">OSD-137 / OSD-255: Retinal & Choroidal Alterations</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:4px;">
                    {{ $isEn ? 'Biological repository reference for spaceflight retinal remodeling.' : 'Referencia biológica de remodelación retiniana en vuelo espacial.' }}
                </div>
            </div>
            <div style="background:var(--background); border:1px solid var(--border); padding:16px; border-radius:8px;">
                <div style="font-size:11px; font-weight:700; color:var(--muted-foreground); text-transform:uppercase; margin-bottom:6px;">
                    <i class="fa fa-book"></i> NASA NTRS & HRP
                </div>
                <div style="font-size:13px; font-weight:700; color:var(--foreground);">NTRS #20200002341 · SPD-MED-2026-V2</div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-top:4px;">
                    {{ $isEn ? 'NASA HRP Evidence Report on SANS surveillance and -6° HDT analogs.' : 'Evidencia documental NASA HRP sobre vigilancia SANS y análogos -6° HDT.' }}
                </div>
            </div>
        </div>

        <div style="text-align:center; padding:24px; background:var(--muted); border-radius:10px; border:1px solid var(--border);">
            <h3 style="font-size:18px; font-weight:800; color:var(--foreground); margin:0 0 8px;">
                {{ $isEn ? 'Printable Longitudinal Dossier Ready (Unsigned / No Automated Verdict)' : 'Expediente Longitudinal Imprimible Listo (Sin Firma Falsa ni Dictamen Automático)' }}
            </h3>
            <p style="font-size:13px; color:var(--muted-foreground); margin:0 0 16px;">
                {{ $isEn
                    ? 'Generates a clean PDF summary of the 6 core biomarkers, +30/+60/+90d cuts, and NASA references, explicitly preserving human specialist authority.'
                    : 'Genera el resumen PDF de los 6 biomarcadores, cortes +30/+60/+90 días y referencias NASA, preservando explícitamente la revisión del especialista humano.' }}
            </p>
            <a href="{{ route('public.demo-theater.report', ['code' => $astronaut->code]) }}" target="_blank" class="btn btn-primary" style="padding:10px 24px; font-size:14px; font-weight:700;">
                <i class="fa fa-file-text-o"></i> {{ $isEn ? 'Open Printable Dossier / PDF' : 'Abrir Expediente para Impresión / PDF' }}
            </a>
        </div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:16px; margin-top:24px;">
        <button type="button" class="btn btn-outline" onclick="goToStep(4)">
            <i class="fa fa-arrow-left"></i> {{ $isEn ? 'Previous' : 'Anterior' }}
        </button>
        <button type="button" class="btn btn-outline" onclick="goToStep(1)">
            <i class="fa fa-refresh"></i> {{ $isEn ? 'Restart Walkthrough' : 'Reiniciar Recorrido' }}
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
