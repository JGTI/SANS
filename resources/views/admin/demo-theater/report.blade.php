<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NASA Flight Medical Evaluation · SANS Telemetry Report [{{ $astronaut->code }}]</title>
    <style>
        :root {
            --primary: #0284c7;
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
            max-width: 820px;
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
            font-size: 22px;
            font-weight: 900;
            letter-spacing: .08em;
            color: #0f172a;
        }
        .sub-header {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: .06em;
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
            text-align: right;
            max-width: 820px;
            margin: 0 auto 16px;
        }
        .btn-print {
            background: #0284c7;
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
    <button onclick="window.print()" class="btn-print">Imprimir / Guardar como PDF</button>
</div>

<div class="report-page">
    <div class="header-bar">
        <div>
            <div class="agency-logo-text">SPACE VISION · NASA HRP</div>
            <div class="sub-header">Human Research Program · Spaceflight Ophthalmology Division</div>
        </div>
        <div style="text-align: right;">
            <div style="font-family: monospace; font-weight: 800; font-size: 14px;">DOC-ID: NASA-SANS-{{ $astronaut->code }}</div>
            <div style="font-size: 11px; color: var(--muted);">Emisión: {{ now()->format('Y-m-d H:i') }} UTC</div>
        </div>
    </div>

    <h1 class="report-title">Expediente de Evaluación Neurooftalmológica y Telemetría SANS</h1>
    <div style="font-size: 12px; color: var(--muted); margin-bottom: 16px;">
        Monitoreo longitudinal de microgravedad para misiones de exploración del espacio profundo (Artemis / ISS).
    </div>

    {{-- Cuadrícula de metadatos del sujeto --}}
    <div class="meta-grid">
        <div class="meta-cell">
            <span class="meta-label">Tripulante</span>
            <span class="meta-val">{{ $astronaut->name }}</span>
        </div>
        <div class="meta-cell">
            <span class="meta-label">ID / Agencia</span>
            <span class="meta-val">{{ $astronaut->code }} · {{ $astronaut->agency }}</span>
        </div>
        <div class="meta-cell">
            <span class="meta-label">Misión Activa</span>
            <span class="meta-val">{{ $astronaut->mission_name }}</span>
        </div>
        <div class="meta-cell">
            <span class="meta-label">Días en Microgravedad</span>
            <span class="meta-val">FD{{ $astronaut->days_in_space }}</span>
        </div>
    </div>

    {{-- Tabla de refracción comparativa --}}
    <h3 class="section-header">
        <span>1. Análisis Refractivo y Agudeza Visual (OD / OS)</span>
        <span style="font-size:11px; font-weight:normal; text-transform:none; color:var(--muted);">Fórmula: SE = SPH + CYL / 2</span>
    </h3>
    <table class="clinical-table">
        <thead>
            <tr>
                <th>Ojo</th>
                <th>Estado</th>
                <th>Esfera (SPH)</th>
                <th>Cilindro (CYL)</th>
                <th>Eje</th>
                <th>Equivalente Esférico (SE)</th>
                <th>Shift Hipermetrópico (&Delta;SE)</th>
                <th>Agudeza Snellen (LogMAR)</th>
                <th>Presión (IOP)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>OD</strong></td>
                <td>Pre-Vuelo (FD0)</td>
                <td>{{ number_format($baselineEvalOD->sphere ?? 0, 2) }} D</td>
                <td>{{ number_format($baselineEvalOD->cylinder ?? 0, 2) }} D</td>
                <td>{{ $baselineEvalOD->axis ?? 0 }}°</td>
                <td><strong>{{ number_format($baselineEvalOD->spherical_equivalent ?? 0, 2) }} D</strong></td>
                <td>0.00 D (Basal)</td>
                <td>{{ $baselineEvalOD->snellen_fraction ?? '20/20' }} (0.000)</td>
                <td>15.0 mmHg</td>
            </tr>
            <tr style="background:#fffbeb;">
                <td><strong>OD</strong></td>
                <td><strong>Actual (FD{{ $latestEvalOD->mission_day ?? $astronaut->days_in_space }})</strong></td>
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
                <td>Pre-Vuelo (FD0)</td>
                <td>{{ number_format($baselineEvalOS->sphere ?? 0, 2) }} D</td>
                <td>{{ number_format($baselineEvalOS->cylinder ?? 0, 2) }} D</td>
                <td>{{ $baselineEvalOS->axis ?? 0 }}°</td>
                <td><strong>{{ number_format($baselineEvalOS->spherical_equivalent ?? 0, 2) }} D</strong></td>
                <td>0.00 D (Basal)</td>
                <td>{{ $baselineEvalOS->snellen_fraction ?? '20/20' }} (0.000)</td>
                <td>15.0 mmHg</td>
            </tr>
            <tr>
                <td><strong>OS</strong></td>
                <td>Actual (FD{{ $latestEvalOS->mission_day ?? $astronaut->days_in_space }})</td>
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

    {{-- Hallazgos Estructurales de SANS --}}
    <h3 class="section-header">2. Biomarcadores Estructurales y Fondo de Ojo (OCT)</h3>
    <table class="clinical-table">
        <thead>
            <tr>
                <th>Biomarcador Oftálmico</th>
                <th>Valor Observado</th>
                <th>Umbral Normal</th>
                <th>Significancia Clínica</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Escala de Frisén (Edema de Disco)</strong></td>
                <td><strong>Grado {{ $latestSans->frisen_scale_grade ?? 0 }}</strong> / 5</td>
                <td>Grado 0 (Sin edema)</td>
                <td>Edema peripapilar circunferencial asociado a congestión cefálica.</td>
            </tr>
            <tr>
                <td><strong>Aplanamiento Posterior del Globo</strong></td>
                <td><strong>{{ ($latestSans->posterior_globe_flattening ?? false) ? 'POSITIVO (Detectado)' : 'NEGATIVO' }}</strong></td>
                <td>Negativo</td>
                <td>Acortamiento del eje axial anteroposterior por presión retrobulbar.</td>
            </tr>
            <tr>
                <td><strong>Pliegues Coroideos</strong></td>
                <td><strong>{{ ($latestSans->choroidal_folds ?? false) ? 'PRESENTES' : 'AUSENTES' }}</strong></td>
                <td>Ausentes</td>
                <td>Ondulaciones en la membrana de Bruch y coroides.</td>
            </tr>
            <tr>
                <td><strong>Grosor RNFL Peripapilar (OCT)</strong></td>
                <td><strong>{{ $latestSans->rnfl_thickness_microns ?? 95 }} &mu;m</strong></td>
                <td>&le; 100 &mu;m</td>
                <td>Engrosamiento por acumulación de axoplasma en la cabeza del nervio.</td>
            </tr>
        </tbody>
    </table>

    {{-- Estratificación y Diagnóstico del Algoritmo --}}
    <h3 class="section-header">3. Estratificación Explicable de Riesgo SANS</h3>
    <div style="background:var(--bg-subtle); border:1px solid var(--border); border-radius:6px; padding:14px; margin-bottom:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="font-weight:700;">Nivel de Severidad Determinado por SPACE VISION:</div>
            <div>
                @if (in_array($riskAssessment['tier'], ['severe', 'high']))
                    <span class="badge-risk risk-high">RIESGO ALTO (TIER 3)</span>
                @elseif (in_array($riskAssessment['tier'], ['moderate', 'medium']))
                    <span class="badge-risk risk-medium">RIESGO MODERADO (TIER 2)</span>
                @else
                    <span class="badge-risk risk-low">RIESGO BAJO (TIER 1)</span>
                @endif
            </div>
        </div>
        <p style="margin:0 0 8px; font-size:12px; color:#334155;">
            <strong>Criterio Algorítmico (Puntaje {{ $riskAssessment['score'] }}/100):</strong>
            {{ !empty($riskAssessment['factors']) ? implode('; ', $riskAssessment['factors']) : 'Sin factores de riesgo críticos identificados en telemetría.' }}
        </p>
        <div style="font-size:12px; color:#1e293b;">
            <strong>Protocolo de Intervención Recomendado:</strong> {{ $riskAssessment['recommendation'] }}
        </div>
    </div>

    {{-- Evidencia Científica Cruzada NASA --}}
    <h3 class="section-header">4. Literatura Científica NASA Indexada</h3>
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

    {{-- Firmas Oficiales --}}
    <div style="display:flex; justify-content:space-between; margin-top:36px; padding-top:20px; border-top:1px solid var(--border);">
        <div style="text-align:center; width:220px;">
            <div style="border-bottom:1px solid #0f172a; height:32px;"></div>
            <div style="font-size:11px; font-weight:700; margin-top:4px;">Dr. Marcus Vance, MD</div>
            <div style="font-size:10px; color:var(--muted);">Flight Surgeon · NASA Space Vision</div>
        </div>
        <div style="text-align:center; width:220px;">
            <div style="border-bottom:1px solid #0f172a; height:32px; display:flex; align-items:flex-end; justify-content:center; font-family:monospace; font-size:10px; color:#0284c7;">
                [VALIDADO DIGITALMENTE]
            </div>
            <div style="font-size:11px; font-weight:700; margin-top:4px;">SPACE VISION TELEMETRY SYSTEM</div>
            <div style="font-size:10px; color:var(--muted);">Algoritmo Clínico de Neurooftalmología</div>
        </div>
    </div>

    <div class="disclaimer-box">
        <strong>Aviso de Demostración Científica:</strong> Este documento ha sido generado por la plataforma SPACE VISION para fines de investigación y demostración en el marco del NASA Space Apps Challenge 2026. Los datos simulados se identifican expresamente y complementan el modelado biomédico sin reemplazar el juicio diagnóstico definitivo de la Dirección de Salud de la Tripulación de NASA.
    </div>
</div>

</body>
</html>
