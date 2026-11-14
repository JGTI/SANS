@extends('layouts.app')

@section('title', 'SPACE VISION — Model Registry')

@section('content')
<div class="page-header" style="margin-bottom:24px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px;">
        <div>
            <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--muted-foreground); margin-bottom:4px;">
                MLOps & Control de Versiones Científicas
            </div>
            <h1 style="font-size:24px; font-weight:800; letter-spacing:-0.03em; margin:0 0 6px; color:var(--foreground);">
                Model Registry & Linaje
            </h1>
            <p style="font-size:13px; color:var(--muted-foreground); margin:0;">
                Inventario formal de modelos registrados, versiones, linaje de entrenamiento, artefactos y estado de validación para misión aeroespacial.
            </p>
        </div>

        <div style="display:flex; align-items:center; gap:10px;">
            <a href="{{ route('admin.model-lab') }}" class="btn btn-primary" style="padding:7px 14px; font-size:12px;">
                Nuevo Experimento de Modelos &rarr;
            </a>
        </div>
    </div>
</div>

{{-- Lista de Modelos Registrados --}}
<div style="display:flex; flex-direction:column; gap:18px; margin-bottom:24px;">
    @foreach ($models as $m)
        @php
            $isValidated = ($m['status'] ?? '') === 'VALIDATED';
            $isCandidate = ($m['status'] ?? '') === 'CANDIDATE';
        @endphp
        <div class="card" style="padding:22px; margin:0; border:{{ $isValidated ? '2px solid var(--foreground)' : '1px solid var(--border)' }};">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px; margin-bottom:12px;">
                <div>
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                        <span style="font-size:11px; font-family:ui-monospace, monospace; background:var(--muted); border:1px solid var(--border); padding:2px 6px; border-radius:4px; font-weight:700;">
                            {{ $m['model_id'] }}
                        </span>
                        <span style="font-size:11px; color:var(--muted-foreground); font-family:ui-monospace, monospace;">
                            {{ $m['version'] }}
                        </span>
                        <span style="font-size:11px; color:var(--muted-foreground);">·</span>
                        <span style="font-size:11px; color:var(--muted-foreground);">
                            {{ $m['category'] }}
                        </span>
                    </div>
                    <h2 style="font-size:17px; font-weight:800; color:var(--foreground); margin:0;">
                        {{ $m['name'] }}
                    </h2>
                </div>

                <div>
                    @if ($isValidated)
                        <span style="background:var(--foreground); color:var(--background); border:1px solid var(--foreground); padding:4px 10px; border-radius:4px; font-size:11px; font-weight:700; text-transform:uppercase;">
                            <i class="fa fa-check" style="margin-right:4px;"></i> VALIDADO PARA PRODUCCIÓN
                        </span>
                    @elseif ($isCandidate)
                        <span style="background:var(--muted); color:var(--foreground); border:1px solid var(--border); padding:4px 10px; border-radius:4px; font-size:11px; font-weight:700; text-transform:uppercase;">
                            <i class="fa fa-flask" style="margin-right:4px;"></i> CANDIDATO EXPERIMENTAL
                        </span>
                    @else
                        <span style="background:var(--muted); color:var(--muted-foreground); border:1px solid var(--border); padding:4px 10px; border-radius:4px; font-size:11px; font-weight:700; text-transform:uppercase;">
                            <i class="fa fa-code-fork" style="margin-right:4px;"></i> EXPERIMENTAL / PROTOTIPO
                        </span>
                    @endif
                </div>
            </div>

            <p style="font-size:13px; color:var(--muted-foreground); line-height:1.5; margin-bottom:16px;">
                {{ $m['description'] }}
            </p>

            {{-- Métricas y Parámetros --}}
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:10px; margin-bottom:16px;">
                <div style="background:var(--muted); border:1px solid var(--border); padding:10px 12px; border-radius:6px;">
                    <div style="font-size:10px; text-transform:uppercase; color:var(--muted-foreground); font-weight:700;">Framework</div>
                    <div style="font-size:12px; font-weight:700; color:var(--foreground); font-family:ui-monospace, monospace; margin-top:2px;">
                        {{ $m['framework'] }}
                    </div>
                </div>

                <div style="background:var(--muted); border:1px solid var(--border); padding:10px 12px; border-radius:6px;">
                    <div style="font-size:10px; text-transform:uppercase; color:var(--muted-foreground); font-weight:700;">Autor / Grupo</div>
                    <div style="font-size:12px; font-weight:700; color:var(--foreground); margin-top:2px;">
                        {{ $m['author'] }}
                    </div>
                </div>

                <div style="background:var(--muted); border:1px solid var(--border); padding:10px 12px; border-radius:6px;">
                    <div style="font-size:10px; text-transform:uppercase; color:var(--muted-foreground); font-weight:700;">Métricas Principales</div>
                    <div style="font-size:12px; font-weight:700; color:var(--foreground); font-family:ui-monospace, monospace; margin-top:2px;">
                        @if (isset($m['metrics']['f1_score']))
                            F1: {{ number_format($m['metrics']['f1_score'] * 100, 1) }}% · AUC: {{ $m['metrics']['roc_auc'] }}
                        @elseif (isset($m['metrics']['determinism']))
                            Determinismo: {{ $m['metrics']['determinism'] }}
                        @elseif (isset($m['metrics']['mean_r_squared']))
                            R²: {{ $m['metrics']['mean_r_squared'] }} (95% CI)
                        @else
                            Contaminación: {{ $m['metrics']['contamination_rate'] ?? 'N/A' }}
                        @endif
                    </div>
                </div>
            </div>

            {{-- Variables de Entrada --}}
            @if (isset($m['input_features']))
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; font-size:11px; padding-top:10px; border-top:1px solid var(--border);">
                    <span style="color:var(--muted-foreground); font-weight:600;">Features Requeridas:</span>
                    @foreach ($m['input_features'] as $feat)
                        <span style="font-family:ui-monospace, monospace; background:var(--muted); border:1px solid var(--border); padding:1px 6px; border-radius:3px;">
                            {{ $feat }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>
@endsection
