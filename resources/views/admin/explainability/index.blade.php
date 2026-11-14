@extends('layouts.app')

@section('title', 'SPACE VISION — AI Explainability Center')

@section('content')
<div class="page-header" style="margin-bottom:24px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px;">
        <div>
            <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--muted-foreground); margin-bottom:4px;">
                Inteligencia Artificial Explicable (XAI)
            </div>
            <h1 style="font-size:24px; font-weight:800; letter-spacing:-0.03em; margin:0 0 6px; color:var(--foreground);">
                AI Explainability Center
            </h1>
            <p style="font-size:13px; color:var(--muted-foreground); margin:0;">
                Desglose explicable de factores determinantes, atribución de pesos matemáticos, intervalos de incertidumbre y correlación biomecánica.
            </p>
        </div>

        <div style="display:flex; align-items:center; gap:10px;">
            <a href="{{ route('admin.model-lab') }}" class="btn btn-secondary" style="padding:7px 14px; font-size:12px;">
                &larr; Laboratorio de Modelos
            </a>
            <a href="{{ route('admin.model-registry') }}" class="btn btn-primary" style="padding:7px 14px; font-size:12px;">
                Registro de Modelos &rarr;
            </a>
        </div>
    </div>
</div>

{{-- Simulador Interactivo de Caso para Evaluación XAI --}}
<div class="card" style="padding:20px; margin-bottom:24px;">
    <div style="font-size:13px; font-weight:700; color:var(--foreground); margin-bottom:12px;">
        Ajuste Interactivo de Parámetros Clínicos del Astronauta
    </div>

    <form method="GET" action="{{ route('admin.explainability') }}" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:14px; align-items:flex-end;">
        <div>
            <label style="font-size:11px; font-weight:600; color:var(--muted-foreground); display:block; margin-bottom:4px;">
                Desplazamiento Hipermetrópico (D):
            </label>
            <input type="number" name="hyperopic_shift" value="{{ $sample['hyperopic_shift'] ?? 1.25 }}" step="0.25" min="-1.0" max="4.0" class="form-control" style="font-family:ui-monospace, monospace; font-size:12px;">
        </div>

        <div>
            <label style="font-size:11px; font-weight:600; color:var(--muted-foreground); display:block; margin-bottom:4px;">
                Delta RNFL OCT (µm):
            </label>
            <input type="number" name="rnfl_delta_microns" value="{{ $sample['rnfl_delta_microns'] ?? 22.0 }}" step="1.0" min="0" max="100" class="form-control" style="font-family:ui-monospace, monospace; font-size:12px;">
        </div>

        <div>
            <label style="font-size:11px; font-weight:600; color:var(--muted-foreground); display:block; margin-bottom:4px;">
                Presión Intraocular (mmHg):
            </label>
            <input type="number" name="iop_mmhg" value="{{ $sample['iop_mmhg'] ?? 17.5 }}" step="0.5" min="8" max="40" class="form-control" style="font-family:ui-monospace, monospace; font-size:12px;">
        </div>

        <div>
            <label style="font-size:11px; font-weight:600; color:var(--muted-foreground); display:block; margin-bottom:4px;">
                Día de Vuelo (FD):
            </label>
            <input type="number" name="flight_day" value="{{ $sample['flight_day'] ?? 90 }}" step="15" min="0" max="720" class="form-control" style="font-family:ui-monospace, monospace; font-size:12px;">
        </div>

        <div>
            <button type="submit" class="btn btn-primary" style="width:100%; font-size:12px; height:34px;">
                Recalcular Explicabilidad
            </button>
        </div>
    </form>
</div>

{{-- Panel de Resultado de Inferencia y Probabilidad con Incertidumbre --}}
<div style="display:grid; grid-template-columns:1fr 2fr; gap:20px; margin-bottom:24px;">
    <div class="card" style="padding:22px; margin:0;">
        <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted-foreground); margin-bottom:6px;">
            Resultado de la Inferencia
        </div>
        <h2 style="font-size:18px; font-weight:800; color:var(--foreground); margin:0 0 14px;">
            {{ $explanation['prediction']['label'] ?? 'Riesgo SANS Elevado' }}
        </h2>

        <div style="margin-bottom:16px;">
            <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px;">
                <span style="color:var(--muted-foreground);">Probabilidad Estimada:</span>
                <span style="font-weight:800; font-family:ui-monospace, monospace;">
                    {{ number_format(($explanation['prediction']['probability'] ?? 0.85) * 100, 1) }}%
                </span>
            </div>
            <div style="height:8px; background:var(--muted); border-radius:4px; overflow:hidden;">
                <div style="height:100%; width:{{ ($explanation['prediction']['probability'] ?? 0.85) * 100 }}%; background:var(--foreground);"></div>
            </div>
        </div>

        <div style="background:var(--muted); border:1px solid var(--border); padding:10px 12px; border-radius:6px; margin-bottom:14px;">
            <div style="font-size:10px; text-transform:uppercase; color:var(--muted-foreground); font-weight:700; margin-bottom:2px;">
                Intervalo de Incertidumbre (95% CI)
            </div>
            <div style="font-size:13px; font-weight:700; color:var(--foreground); font-family:ui-monospace, monospace;">
                [{{ number_format(($explanation['prediction']['uncertainty_interval_95'][0] ?? 0.77) * 100, 1) }}% — {{ number_format(($explanation['prediction']['uncertainty_interval_95'][1] ?? 0.93) * 100, 1) }}%]
            </div>
        </div>

        <div style="font-size:11px; color:var(--muted-foreground); line-height:1.5;">
            <strong>Factor Primario Dominante:</strong><br>
            {{ $explanation['primary_driving_factor'] ?? 'Desplazamiento Hipermetrópico' }}
        </div>
    </div>

    {{-- Interpretación Técnica y Síntesis --}}
    <div class="card" style="padding:22px; margin:0;">
        <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted-foreground); margin-bottom:6px;">
            Interpretación Clínica & Técnica del Algoritmo
        </div>
        <h2 style="font-size:15px; font-weight:700; color:var(--foreground); margin:0 0 10px;">
            Síntesis Explicable del Modelo ({{ $explanation['algorithm_id'] ?? 'SpaceVision-RandomForest' }})
        </h2>

        <p style="font-size:13px; color:var(--foreground); line-height:1.6; margin-bottom:16px;">
            {{ $explanation['technical_interpretation'] ?? '' }}
        </p>

        <div style="border-top:1px solid var(--border); padding-top:12px;">
            <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted-foreground); margin-bottom:6px;">
                Limitaciones Técnicas Documentadas
            </div>
            <ul style="font-size:12px; color:var(--muted-foreground); line-height:1.5; margin:0; padding-left:18px;">
                @foreach ($explanation['model_limitations'] ?? [] as $limitation)
                    <li>{{ $limitation }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

{{-- Tabla de Atribución de Variables (Feature Attribution Table) --}}
<div class="card" style="padding:22px; margin:0 0 24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <div>
            <h2 style="font-size:15px; font-weight:700; margin:0 0 4px; color:var(--foreground);">
                Descomposición de Atribución de Variables (Pesos SHAP-Equivalentes)
            </h2>
            <p style="font-size:12px; color:var(--muted-foreground); margin:0;">
                Contribución local de cada parámetro biomecánico al vector final de predicción.
            </p>
        </div>
        <span style="font-size:11px; color:var(--muted-foreground); font-family:ui-monospace, monospace;">
            Total Factores: {{ count($explanation['feature_attributions'] ?? []) }}
        </span>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
                <tr style="border-bottom:1px solid var(--border); text-align:left; color:var(--muted-foreground); font-size:11px; text-transform:uppercase;">
                    <th style="padding:8px 10px;">Variable / Biomarcador</th>
                    <th style="padding:8px 10px;">Valor Registrado</th>
                    <th style="padding:8px 10px; width:180px;">Peso de Importancia</th>
                    <th style="padding:8px 10px;">Dirección del Impacto</th>
                    <th style="padding:8px 10px;">Mecanismo Biomecánico Espacial</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($explanation['feature_attributions'] ?? [] as $feat)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 10px; font-weight:700; color:var(--foreground);">
                            {{ $feat['label'] }}
                        </td>
                        <td style="padding:12px 10px; font-family:ui-monospace, monospace; font-weight:700;">
                            {{ $feat['value'] }}
                        </td>
                        <td style="padding:12px 10px;">
                            <div style="display:flex; justify-content:space-between; font-size:11px; margin-bottom:3px; font-family:ui-monospace, monospace;">
                                <span>{{ $feat['importance_percentage'] }}%</span>
                            </div>
                            <div style="height:6px; background:var(--muted); border-radius:3px; overflow:hidden;">
                                <div style="height:100%; width:{{ $feat['importance_percentage'] }}%; background:var(--foreground);"></div>
                            </div>
                        </td>
                        <td style="padding:12px 10px;">
                            <span style="background:var(--muted); border:1px solid var(--border); padding:2px 8px; border-radius:4px; font-size:11px;">
                                {{ $feat['direction'] }}
                            </span>
                        </td>
                        <td style="padding:12px 10px; color:var(--muted-foreground); font-size:11px; line-height:1.4;">
                            {{ $feat['clinical_rationale'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Declaración de Causalidad Obligatoria --}}
<div class="card" style="padding:18px 22px; margin:0; border-left:4px solid var(--foreground);">
    <div style="font-size:12px; font-weight:700; text-transform:uppercase; color:var(--foreground); margin-bottom:4px;">
        <i class="fa fa-exclamation-triangle" style="margin-right:6px;"></i> Cláusula de Responsabilidad Científica sobre Explicabilidad
    </div>
    <p style="font-size:12px; color:var(--muted-foreground); margin:0; line-height:1.5;">
        Las atribuciones de variables en este centro representan correlaciones matemáticas y pesos relativos calculados por árboles de decisión y métodos basados en SHAP. <strong>No constituyen una demostración formal de causalidad fisiológica</strong>, la cual requiere validación histopatológica y ensayos de contramedidas en microgravedad.
    </p>
</div>
@endsection
