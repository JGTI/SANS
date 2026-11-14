<!DOCTYPE html>
@php $isEn = app()->getLocale() === 'en'; @endphp
<html lang="{{ $isEn ? 'en' : 'es' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPACE VISION — SANS Monitor · NASA Flight Medical Evaluation Simulation [{{ $astronaut->code }}]</title>
    <style>
        :root {
            --primary: #09090b;
            --text: #0f172a;
            --muted: #475569;
            --border: #cbd5e1;
            --bg-subtle: #f8fafc;
        }
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #e2e8f0;
            color: var(--text);
            margin: 0;
            padding: 24px;
            font-size: 13px;
            line-height: 1.5;
        }
        .report-page {
            max-width: 840px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
            padding: 36px 42px;
        }
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .agency-logo-text {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: .06em;
            color: #0f172a;
        }
        .sub-header {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: .05em;
            color: var(--muted);
        }
        .report-title {
            font-size: 18px;
            font-weight: 800;
            margin: 0 0 4px;
            color: #0f172a;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            background: var(--bg-subtle);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }
        .meta-cell {
            display: flex;
            flex-direction: column;
        }
        .meta-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--muted);
        }
        .meta-val {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            font-family: monospace;
        }
        h3.section-header {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .05em;
            border-bottom: 1px solid var(--border);
            padding-bottom: 4px;
            margin: 20px 0 10px;
            color: #0f172a;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        table.clinical-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 12px;
        }
        table.clinical-table th {
            background: var(--bg-subtle);
            border: 1px solid var(--border);
            padding: 6px 10px;
            text-align: left;
            font-weight: 700;
            color: #334155;
            font-size: 11px;
        }
        table.clinical-table td {
            border: 1px solid var(--border);
            padding: 6px 10px;
        }
        .badge-risk {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 800;
            font-size: 11px;
            text-transform: uppercase;
        }
        .risk-high { background: #fee2e2; color: #b91c1c; border: 1px solid #f87171; }
        .risk-medium { background: #fef3c7; color: #b45309; border: 1px solid #fbbf24; }
        .risk-low { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }

        .disclaimer-box {
            font-size: 10px;
            line-height: 1.4;
            color: var(--muted);
            border-top: 1px dashed var(--border);
            padding-top: 10px;
            margin-top: 24px;
        }
        .print-btn-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 840px;
            margin: 0 auto 16px;
        }
        .btn-print {
            background: #09090b;
            color: white;
            padding: 8px 16px;
            font-weight: 700;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 13px;
        }
        @media print {
            body { background: #ffffff; padding: 0; }
            .report-page { border: none; box-shadow: none; padding: 0; }
            .print-btn-bar { display: none !important; }
        }
    </style>
</head>
<body>

<div class="print-btn-bar">
    <div style="display:flex;gap:8px;">
        <a href="{{ url('/demo-theater') }}" class="btn-print" style="background:#ffffff;color:#09090b;border:1px solid #cbd5e1;">
            &larr; {{ $isEn ? 'Back to Demo Pitch' : 'Volver al Demo Pitch' }}
        </a>
        <a href="{{ url('/sans-monitor?case_id=' . ($linkedSansCase->case_id ?? 'CASE-PROG-02')) }}" class="btn-print" style="background:#ffffff;color:#09090b;border:1px solid #cbd5e1;">
            {{ $isEn ? 'Open in 3-Step SANS Monitor' : 'Abrir en SANS Monitor (3 Pasos)' }}
        </a>
    </div>
    <button onclick="window.print()" class="btn-print">
        {{ $isEn ? 'Print / Save as PDF' : 'Imprimir / Guardar como PDF' }}
    </button>
</div>

<div class="report-page">
    <div class="header-bar">
        <div>
            <div class="agency-logo-text">SPACE VISION · SANS MONITOR</div>
            <div class="sub-header">NEXUS &times; Visual Rehab Center · NASA Space Apps Challenge 2026</div>
        </div>
        <div style="text-align: right;">
            <div style="font-family: monospace; font-weight: 800; font-size: 13px;">
                DOSSIER: {{ $linkedSansCase->case_id ?? 'CASE-PROG-02' }} ({{ $astronaut->code }})
            </div>
            <div style="font-size: 11px; color: var(--muted);">
                {{ $isEn ? 'Generated:' : 'Emisión:' }} {{ now()->format('Y-m-d H:i') }} UTC · {{ $isEn ? 'Synthetic Demo Record' : 'Registro Demo Sintético' }}
            </div>
        </div>
    </div>

    <h1 class="report-title">
        {{ $isEn
            ? 'Longitudinal Neuro-Ocular & SANS Telemetry Dossier (Expediente de Evaluación Neurooftalmológica)'
            : 'Expediente de Evaluación Neurooftalmológica y Telemetría Longitudinal SANS' }}
    </h1>
    <div style="font-size: 12px; color: var(--muted); margin-bottom: 16px;">
        {{ $isEn
            ? 'Traceable ocular surveillance across 6 core biomarkers, Before · During · After (+30, +60, +90 days) cuts, and 3 reference scenarios — without issuing an automated fit-for-duty verdict.'
            : 'Vigilancia ocular trazable en 6 biomarcadores centrales, cortes Antes · Durante · Después (+30, +60, +90 días) y 3 escenarios de referencia — sin emitir dictamen automático de aptitud.' }}
    </div>

    {{-- Cuadrícula de metadatos del sujeto con retrato IA --}}
    <div style="display:flex;align-items:center;gap:16px;background:var(--bg-subtle);border:1px solid var(--border);border-radius:6px;padding:12px 16px;margin-bottom:20px;">
        <img src="{{ $astronaut->avatar_url }}"
             alt="{{ $astronaut->name }}"
             style="width:64px;height:64px;border-radius:8px;object-fit:cover;border:2px solid #0f172a;filter:grayscale(100%) contrast(1.08);flex-shrink:0;">
        <div class="meta-grid" style="margin:0;border:none;padding:0;background:transparent;flex:1;">
            <div class="meta-cell">
                <span class="meta-label">{{ $isEn ? 'Subject / Crew' : 'Tripulante / Sujeto' }}</span>
                <span class="meta-val">{{ $astronaut->name }} ({{ $linkedSansCase->case_id ?? 'CASE-PROG-02' }})</span>
            </div>
            <div class="meta-cell">
                <span class="meta-label">{{ $isEn ? 'Scenario Archetype' : 'Escenario Modelo' }}</span>
                <span class="meta-val">{{ $linkedSansCase->scenario_archetype ?? 'EMPEORA' }} · {{ $astronaut->code }}</span>
            </div>
            <div class="meta-cell">
                <span class="meta-label">{{ $isEn ? 'Mission / Context' : 'Misión / Contexto' }}</span>
                <span class="meta-val">{{ $astronaut->mission_name }} (FD{{ $astronaut->days_in_space }})</span>
            </div>
            <div class="meta-cell">
                <span class="meta-label">{{ $isEn ? 'Fit-for-Duty Verdict' : 'Dictamen de Aptitud' }}</span>
                <span class="meta-val">{{ $isEn ? 'NOT EVALUATED' : 'APTITUD NO EVALUADA' }}</span>
            </div>
        </div>
    </div>

    {{-- Tabla de refracción comparativa --}}
    <h3 class="section-header">
        <span>1. {{ $isEn ? 'Refractive Analysis & Hyperopic Shift (Shift Hipermetrópico OD / OS)' : 'Análisis Refractivo y Shift Hipermetrópico (OD / OS)' }}</span>
        <span style="font-size:11px; font-weight:normal; text-transform:none; color:var(--muted);">SE = SPH + CYL / 2</span>
    </h3>
    <table class="clinical-table">
        <thead>
            <tr>
                <th>{{ $isEn ? 'Eye' : 'Ojo' }}</th>
                <th>{{ $isEn ? 'Phase' : 'Fase' }}</th>
                <th>SPH</th>
                <th>CYL</th>
                <th>{{ $isEn ? 'Axis' : 'Eje' }}</th>
                <th>SE</th>
                <th>Shift Hipermetrópico (&Delta;SE)</th>
                <th>Snellen (LogMAR)</th>
                <th>IOP (PIO)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>OD</strong></td>
                <td>{{ $isEn ? 'Before Flight (FD0)' : 'Antes del Vuelo (FD0)' }}</td>
                <td>{{ number_format($baselineEvalOD->sphere ?? 0, 2) }} D</td>
                <td>{{ number_format($baselineEvalOD->cylinder ?? 0, 2) }} D</td>
                <td>{{ $baselineEvalOD->axis ?? 0 }}°</td>
                <td><strong>{{ number_format($baselineEvalOD->spherical_equivalent ?? 0, 2) }} D</strong></td>
                <td>0.00 D ({{ $isEn ? 'Baseline' : 'Basal' }})</td>
                <td>{{ $baselineEvalOD->snellen_fraction ?? '20/20' }} (0.000)</td>
                <td>15.0 mmHg</td>
            </tr>
            <tr style="background:#fffbeb;">
                <td><strong>OD</strong></td>
                <td><strong>{{ $isEn ? 'During Flight' : 'Durante el Vuelo' }} (FD{{ $latestEvalOD->mission_day ?? $astronaut->days_in_space }})</strong></td>
                <td>{{ number_format($latestEvalOD->sphere ?? 0, 2) }} D</td>
                <td>{{ number_format($latestEvalOD->cylinder ?? 0, 2) }} D</td>
                <td>{{ $latestEvalOD->axis ?? 0 }}°</td>
                <td><strong>{{ number_format($latestEvalOD->spherical_equivalent ?? 0, 2) }} D</strong></td>
                <td style="color:#b91c1c; font-weight:800;">
                    +{{ number_format($shiftOD, 2) }} D
                </td>
                <td>{{ $latestEvalOD->snellen_fraction ?? '20/20' }} ({{ number_format($latestEvalOD->logmar ?? 0, 3) }})</td>
                <td>{{ number_format($latestEvalOD->intraocular_pressure ?? 15, 1) }} mmHg</td>
            </tr>
            <tr>
                <td><strong>OS</strong></td>
                <td>{{ $isEn ? 'Before Flight (FD0)' : 'Antes del Vuelo (FD0)' }}</td>
                <td>{{ number_format($baselineEvalOS->sphere ?? 0, 2) }} D</td>
                <td>{{ number_format($baselineEvalOS->cylinder ?? 0, 2) }} D</td>
                <td>{{ $baselineEvalOS->axis ?? 0 }}°</td>
                <td><strong>{{ number_format($baselineEvalOS->spherical_equivalent ?? 0, 2) }} D</strong></td>
                <td>0.00 D ({{ $isEn ? 'Baseline' : 'Basal' }})</td>
                <td>{{ $baselineEvalOS->snellen_fraction ?? '20/20' }} (0.000)</td>
                <td>15.0 mmHg</td>
            </tr>
            <tr>
                <td><strong>OS</strong></td>
                <td>{{ $isEn ? 'During Flight' : 'Durante el Vuelo' }} (FD{{ $latestEvalOS->mission_day ?? $astronaut->days_in_space }})</td>
                <td>{{ number_format($latestEvalOS->sphere ?? 0, 2) }} D</td>
                <td>{{ number_format($latestEvalOS->cylinder ?? 0, 2) }} D</td>
                <td>{{ $latestEvalOS->axis ?? 0 }}°</td>
                <td><strong>{{ number_format($latestEvalOS->spherical_equivalent ?? 0, 2) }} D</strong></td>
                <td>+{{ number_format(abs(($latestEvalOS->spherical_equivalent ?? 0) - ($baselineEvalOS->spherical_equivalent ?? 0)), 2) }} D</td>
                <td>{{ $latestEvalOS->snellen_fraction ?? '20/20' }} ({{ number_format($latestEvalOS->logmar ?? 0, 3) }})</td>
                <td>{{ number_format($latestEvalOS->intraocular_pressure ?? 15, 1) }} mmHg</td>
            </tr>
        </tbody>
    </table>

    {{-- Hallazgos Estructurales de SANS y Cortes +30/+60/+90 --}}
    <h3 class="section-header">
        <span>2. {{ $isEn ? '6 Core Ocular Biomarkers & Post-Flight Cuts (+30, +60, +90 Days)' : '6 Biomarcadores Oculares y Cortes Posvuelo (+30, +60, +90 Días)' }}</span>
    </h3>
    <table class="clinical-table">
        <thead>
            <tr>
                <th>{{ $isEn ? 'Biomarker / Milestone' : 'Biomarcador / Hito' }}</th>
                <th>{{ $isEn ? 'Observed Value' : 'Valor Observado' }}</th>
                <th>{{ $isEn ? 'Reference Threshold' : 'Umbral de Referencia' }}</th>
                <th>{{ $isEn ? 'Operational Significance' : 'Significancia Operativa' }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>1. {{ $isEn ? 'Mean OCT Thickness / RNFL' : 'Espesor OCT / RNFL Peripapilar' }}</strong></td>
                <td><strong>{{ $latestSans->rnfl_thickness_microns ?? 95 }} &mu;m (OCT &Delta;TRT +26 &mu;m)</strong></td>
                <td>&Delta;TRT &ge; +20 &mu;m</td>
                <td>{{ $isEn ? 'Triggers Prioritize Review in SANS Monitor.' : 'Dispara estado Priorizar Revisión en SANS Monitor.' }}</td>
            </tr>
            <tr>
                <td><strong>2. {{ $isEn ? 'Posterior Globe Flattening & Axial Length' : 'Aplanamiento Posterior y Longitud Axial' }}</strong></td>
                <td><strong>{{ ($latestSans->posterior_globe_flattening ?? false) ? ($isEn ? 'DETECTED (23.82 mm)' : 'POSITIVO (23.82 mm)') : ($isEn ? 'NOT DETECTED' : 'NEGATIVO') }}</strong></td>
                <td>{{ $isEn ? 'Baseline 24.08 mm' : 'Basal 24.08 mm' }}</td>
                <td>{{ $isEn ? 'Anterior-posterior axial shortening under cephalic fluid shift.' : 'Acortamiento del eje axial anteroposterior por congestión cefálica.' }}</td>
            </tr>
            <tr>
                <td><strong>3. {{ $isEn ? 'Papilledema (Frisén Scale 0–5)' : 'Edema Papilar (Escala de Frisén 0–5)' }}</strong></td>
                <td><strong>{{ $isEn ? 'Grade' : 'Grado' }} {{ $latestSans->frisen_scale_grade ?? 0 }} / 5</strong></td>
                <td>{{ $isEn ? 'Grade 0' : 'Grado 0' }}</td>
                <td>{{ $isEn ? 'Primary neuro-ocular prognostic target.' : 'Objetivo primario de seguimiento neuro-ocular.' }}</td>
            </tr>
            <tr>
                <td><strong>4. {{ $isEn ? 'Post-Flight Cut (+30 / +60 / +90 Days)' : 'Corte Posvuelo (+30 / +60 / +90 Días)' }}</strong></td>
                <td colspan="3">
                    @if(isset($linkedFollowups) && $linkedFollowups->count() > 0)
                        @foreach($linkedFollowups as $fVisit)
                            <span style="margin-right:12px;">
                                <strong>+{{ $fVisit->target_milestone_day }}d:</strong>
                                {{ $fVisit->status === 'SIN_MEDICION' ? ($isEn ? 'Unmeasured (Never interpolated)' : 'Sin medición (No se interpola)') : $fVisit->status }}
                            </span>
                        @endforeach
                    @else
                        <span>+30d: REALIZADA · +60d: SIN_MEDICION ({{ $isEn ? 'Never interpolated' : 'No se interpola' }}) · +90d: PENDIENTE</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Estratificación y Diagnóstico del Algoritmo --}}
    <h3 class="section-header">3. {{ $isEn ? 'Explainable SANS Operational Stratification' : 'Estratificación Operativa Explicable de SANS' }}</h3>
    <div style="background:var(--bg-subtle); border:1px solid var(--border); border-radius:6px; padding:14px; margin-bottom:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="font-weight:700;">{{ $isEn ? 'Operational Surveillance State (SPACE VISION Engine):' : 'Estado Operativo Determinado por SPACE VISION:' }}</div>
            <div>
                @if (in_array($riskAssessment['tier'], ['severe', 'high']))
                    <span class="badge-risk risk-high">RIESGO ALTO (TIER 3) · PRIORIZE REVIEW</span>
                @elseif (in_array($riskAssessment['tier'], ['moderate', 'medium']))
                    <span class="badge-risk risk-medium">RIESGO MODERADO (TIER 2)</span>
                @else
                    <span class="badge-risk risk-low">RIESGO BAJO (TIER 1) · READY FOR REVIEW</span>
                @endif
            </div>
        </div>
        <p style="margin:0 0 8px; font-size:12px; color:#334155;">
            <strong>{{ $isEn ? 'Algorithmic Criteria' : 'Criterio Algorítmico' }} ({{ $riskAssessment['score'] }}/100):</strong>
            {{ !empty($riskAssessment['factors']) ? implode('; ', $riskAssessment['factors']) : 'Sin factores de riesgo críticos identificados en telemetría.' }}
        </p>
        <div style="font-size:12px; color:#1e293b;">
            <strong>{{ $isEn ? 'Suggested Follow-up Action:' : 'Acción Sugerida de Seguimiento:' }}</strong> {{ $riskAssessment['recommendation'] }}
        </div>
    </div>

    {{-- Evidencia Científica Cruzada NASA --}}
    <h3 class="section-header">4. {{ $isEn ? 'Indexed NASA Scientific Literature' : 'Literatura Científica NASA Indexada' }}</h3>
    <div style="font-size:11px; color:#334155;">
        @foreach ($relevantPubs as $pub)
            <div style="margin-bottom:6px;">
                &bull; <strong>NASA NTRS #{{ $pub['id'] }}:</strong> {{ $pub['authors'] }} ({{ $pub['year'] }}). <em>{{ $pub['title'] }}</em>. {{ $pub['center'] }}.
            </div>
        @endforeach
        @foreach ($relevantStudies as $st)
            <div style="margin-bottom:6px;">
                &bull; <strong>NASA OSDR {{ $st['accession'] }}:</strong> <em>{{ $st['title'] }}</em> ({{ $st['mission'] }}).
            </div>
        @endforeach
    </div>

    {{-- Bloque de Trazabilidad Científica y Ausencia de Firmas Médicas Falsas --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:28px; padding-top:16px; border-top:1px solid var(--border);">
        <div style="padding:12px; border:1px dashed var(--border); border-radius:6px; background:var(--bg-subtle);">
            <div style="font-size:10px; font-weight:800; text-transform:uppercase; color:var(--muted);">
                {{ $isEn ? 'HUMAN SPECIALIST REVIEW STATUS' : 'ESTADO DE REVISIÓN POR ESPECIALISTA HUMANO' }}
            </div>
            <div style="font-size:12px; font-weight:800; margin-top:4px; color:#0f172a;">
                {{ $isEn ? 'UNSIGNED — FIT-FOR-DUTY NOT EVALUATED' : 'SIN FIRMA MÉDICA — APTITUD NO EVALUADA' }}
            </div>
            <div style="font-size:10px; color:var(--muted); margin-top:4px;">
                {{ $isEn
                    ? 'In compliance with NASA Space Apps scientific honesty rules, this system does not generate fictitious physician signatures or automated fit-for-duty verdicts.'
                    : 'Por honestidad científica y reglas de NASA Space Apps, este sistema no genera firmas médicas ficticias ni emite dictámenes automáticos de aptitud para el servicio.' }}
            </div>
        </div>
        <div style="padding:12px; border:1px solid var(--border); border-radius:6px; background:var(--bg-subtle);">
            <div style="font-size:10px; font-weight:800; text-transform:uppercase; color:var(--muted);">
                {{ $isEn ? 'COMPUTATIONAL TRACEABILITY & AUTHORSHIP' : 'TRAZABILIDAD COMPUTACIONAL Y AUTORÍA' }}
            </div>
            <div style="font-size:12px; font-weight:800; margin-top:4px; color:#0f172a;">
                SPACE VISION — SANS Monitor (NEXUS &times; Visual Rehab Center)
            </div>
            <div style="font-size:10px; color:var(--muted); margin-top:4px;">
                {{ $isEn
                    ? 'Deterministic Rule Engine · Data Provenance: FICTICIO_DEMO (Calibrated Synthetic Cohort) · NASA Space Apps Challenge 2026.'
                    : 'Motor Determinista de Reglas · Origen de Datos: FICTICIO_DEMO (Cohorte Sintética Calibrada) · NASA Space Apps Challenge 2026.' }}
            </div>
        </div>
    </div>

    <div class="disclaimer-box">
        <strong>{{ $isEn ? 'Scientific Demonstration & Branding Notice:' : 'Aviso de Demostración Científica y Uso de Marca:' }}</strong>
        {{ $isEn
            ? 'Developed by NEXUS × Visual Rehab Center for the NASA International Space Apps Challenge 2026. SPACE VISION is an independent participant project and is not an official NASA product or clinical medical device.'
            : 'Desarrollado por NEXUS × Visual Rehab Center para el NASA International Space Apps Challenge 2026. SPACE VISION es un proyecto participante independiente y no constituye un producto oficial de NASA ni un dispositivo de diagnóstico médico definitivo.' }}
    </div>
</div>

</body>
</html>
