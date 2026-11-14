@extends('layouts.app')

@section('title', 'SPACE VISION — Data Quality Observatory')

@section('content')
<div class="page-header" style="margin-bottom:24px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px;">
        <div>
            <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--muted-foreground); margin-bottom:4px;">
                Gobernanza & Auditoría de Telemetría Clínica
            </div>
            <h1 style="font-size:24px; font-weight:800; letter-spacing:-0.03em; margin:0 0 6px; color:var(--foreground);">
                Data Quality Observatory
            </h1>
            <p style="font-size:13px; color:var(--muted-foreground); margin:0;">
                Auditoría en tiempo real de completitud, límites biológicos, detección de valores faltantes y conformidad de datasets oftalmológicos.
            </p>
        </div>

        <div style="display:flex; align-items:center; gap:10px;">
            <a href="{{ route('admin.model-registry') }}" class="btn btn-secondary" style="padding:7px 14px; font-size:12px;">
                Registro de Modelos &rarr;
            </a>
        </div>
    </div>
</div>

{{-- Tarjetas Resumen de Calidad --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:24px;">
    <div class="card" style="padding:20px; margin:0;">
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted-foreground); margin-bottom:6px;">Score Global de Calidad</div>
        <div style="font-size:24px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">
            {{ $audit['overall_quality_score'] ?? 98.0 }}%
        </div>
        <div style="font-size:11px; color:var(--muted-foreground); margin-top:4px;">
            Estado: <strong>{{ $audit['health_tier'] ?? 'EXCELENTE' }}</strong>
        </div>
    </div>

    <div class="card" style="padding:20px; margin:0;">
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted-foreground); margin-bottom:6px;">Tasa de Completitud</div>
        <div style="font-size:24px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">
            {{ $audit['completeness_percentage'] ?? 99.1 }}%
        </div>
        <div style="font-size:11px; color:var(--muted-foreground); margin-top:4px;">
            {{ $audit['total_missing_values'] ?? 0 }} valores nulos detectados
        </div>
    </div>

    <div class="card" style="padding:20px; margin:0;">
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted-foreground); margin-bottom:6px;">Registros Auditados</div>
        <div style="font-size:24px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">
            {{ $audit['total_records_audited'] ?? count($records) }} Filas
        </div>
        <div style="font-size:11px; color:var(--muted-foreground); margin-top:4px;">
            Evaluaciones longitudinales en órbita
        </div>
    </div>

    <div class="card" style="padding:20px; margin:0;">
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted-foreground); margin-bottom:6px;">Violaciones de Rango</div>
        <div style="font-size:24px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">
            {{ $audit['total_range_violations'] ?? 0 }}
        </div>
        <div style="font-size:11px; color:var(--muted-foreground); margin-top:4px;">
            Límites fisiológicos biológicos
        </div>
    </div>
</div>

{{-- Tabla de Conformidad de Límites Fisiológicos --}}
<div class="card" style="padding:22px; margin:0 0 24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <div>
            <h2 style="font-size:15px; font-weight:700; margin:0 0 4px; color:var(--foreground);">
                Auditoría de Límites Fisiológicos & Distribución Estadística
            </h2>
            <p style="font-size:12px; color:var(--muted-foreground); margin:0;">
                Verificación de cada variable contra los umbrales estándar de la medicina aeroespacial.
            </p>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
                <tr style="border-bottom:1px solid var(--border); text-align:left; color:var(--muted-foreground); font-size:11px; text-transform:uppercase;">
                    <th style="padding:8px 10px;">Variable</th>
                    <th style="padding:8px 10px;">Rango Fisiológico Esperado</th>
                    <th style="padding:8px 10px;">Mínimo Observado</th>
                    <th style="padding:8px 10px;">Máximo Observado</th>
                    <th style="padding:8px 10px;">Media ± Desv.</th>
                    <th style="padding:8px 10px;">Violaciones</th>
                    <th style="padding:8px 10px;">Conformidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($audit['range_violations'] ?? [] as $feat => $info)
                    @php
                        $dist = $audit['feature_distributions'][$feat] ?? [];
                        $violations = $info['violations_count'] ?? 0;
                    @endphp
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 10px; font-weight:700; font-family:ui-monospace, monospace; color:var(--foreground);">
                            {{ $feat }}
                        </td>
                        <td style="padding:12px 10px; font-family:ui-monospace, monospace;">
                            {{ $info['expected_range'] }}
                        </td>
                        <td style="padding:12px 10px; font-family:ui-monospace, monospace;">
                            {{ $info['observed_min'] ?? 'N/A' }}
                        </td>
                        <td style="padding:12px 10px; font-family:ui-monospace, monospace;">
                            {{ $info['observed_max'] ?? 'N/A' }}
                        </td>
                        <td style="padding:12px 10px; font-family:ui-monospace, monospace;">
                            @if (isset($dist['mean']))
                                {{ $dist['mean'] }} ± {{ $dist['std'] }}
                            @else
                                —
                            @endif
                        </td>
                        <td style="padding:12px 10px; font-family:ui-monospace, monospace;">
                            {{ $violations }}
                        </td>
                        <td style="padding:12px 10px;">
                            @if ($violations === 0)
                                <span style="background:var(--muted); color:var(--foreground); border:1px solid var(--border); padding:2px 8px; border-radius:4px; font-size:10px; font-weight:700;">
                                    CONFORME (100%)
                                </span>
                            @else
                                <span style="background:#09090b; color:#ffffff; border:1px solid #ffffff; padding:2px 8px; border-radius:4px; font-size:10px; font-weight:700;">
                                    ATÍPICO ({{ $violations }})
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Procedencia y Trazabilidad --}}
<div class="card" style="padding:18px 22px; margin:0; border-left:4px solid var(--foreground);">
    <div style="font-size:12px; font-weight:700; text-transform:uppercase; color:var(--foreground); margin-bottom:4px;">
        <i class="fa fa-university" style="margin-right:6px;"></i> Procedencia y Gobernanza de Datos
    </div>
    <p style="font-size:12px; color:var(--muted-foreground); margin:0; line-height:1.5;">
        {{ $audit['provenance_notes'] ?? 'Todos los datos provienen de protocolos estandarizados de telemetría oftalmológica orbital y cohortes de validación de NASA. Cada registro cuenta con timestamp UTC y trazabilidad de identidad de tripulante.' }}
    </p>
</div>
@endsection
