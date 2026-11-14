@extends('layouts.app')

@section('title', 'SPACE VISION — Model Laboratory')

@section('content')
<div class="page-header" style="margin-bottom:24px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px;">
        <div>
            <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--muted-foreground); margin-bottom:4px;">
                Laboratorio Experimental de Inteligencia Artificial
            </div>
            <h1 style="font-size:24px; font-weight:800; letter-spacing:-0.03em; margin:0 0 6px; color:var(--foreground);">
                Model Laboratory & Benchmark
            </h1>
            <p style="font-size:13px; color:var(--muted-foreground); margin:0;">
                Comparación cruzada estratificada entre clasificadores supervisados: Random Forest, Gradient Boosting y Logistic Regression.
            </p>
        </div>

        <div style="display:flex; align-items:center; gap:10px;">
            <a href="{{ route('admin.model-lab.export', ['n_astronauts' => $nAstronauts, 'n_splits' => $nSplits, 'seed' => $seed]) }}" class="btn btn-outline" style="padding:7px 14px; font-size:12px;" title="Descargar informe de métricas en JSON reproducible">
                <i class="fa fa-download" style="margin-right:4px;"></i> Exportar Benchmark (JSON)
            </a>
            <a href="{{ route('admin.explainability') }}" class="btn btn-secondary" style="padding:7px 14px; font-size:12px;">
                Explainability Center &rarr;
            </a>
        </div>
    </div>
</div>

{{-- Barra de Parámetros del Experimento --}}
<div class="card" style="padding:16px 20px; margin-bottom:24px;">
    <form method="GET" action="{{ route('admin.model-lab') }}" style="display:flex; flex-wrap:wrap; align-items:center; gap:16px;">
        <div style="display:flex; align-items:center; gap:8px;">
            <label style="font-size:12px; font-weight:600; color:var(--foreground); margin:0;">Cohorte de Astronautas:</label>
            <input type="number" name="n_astronauts" value="{{ $nAstronauts }}" min="20" max="200" step="10" class="form-control" style="width:80px; height:32px; font-size:12px; font-family:ui-monospace, monospace;">
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
            <label style="font-size:12px; font-weight:600; color:var(--foreground); margin:0;">Folds de Validación (K):</label>
            <input type="number" name="n_splits" value="{{ $nSplits }}" min="2" max="10" class="form-control" style="width:70px; height:32px; font-size:12px; font-family:ui-monospace, monospace;">
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
            <label style="font-size:12px; font-weight:600; color:var(--foreground); margin:0;">Semilla (Seed):</label>
            <input type="number" name="seed" value="{{ $seed }}" class="form-control" style="width:80px; height:32px; font-size:12px; font-family:ui-monospace, monospace;">
        </div>

        <button type="submit" class="btn btn-primary" style="padding:6px 16px; font-size:12px; min-height:32px;">
            Ejecutar Benchmark Experimental
        </button>

        <span style="font-size:11px; color:var(--muted-foreground); margin-left:auto; font-family:ui-monospace, monospace;">
            Total Muestras: {{ $benchmark['dataset_summary']['total_samples'] ?? 300 }} · SANS+: {{ $benchmark['dataset_summary']['positive_sans_samples'] ?? 0 }} · Control: {{ $benchmark['dataset_summary']['negative_control_samples'] ?? 0 }}
        </span>
    </form>
</div>

{{-- Tarjetas de Comparativa de Modelos --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:20px; margin-bottom:24px;">
    @foreach ($benchmark['models_evaluated'] ?? [] as $key => $model)
        @php
            $isBest = ($benchmark['best_performing_model'] ?? '') === $key;
        @endphp
        <div class="card" style="padding:22px; margin:0; position:relative; border:{{ $isBest ? '2px solid var(--foreground)' : '1px solid var(--border)' }};">
            @if ($isBest)
                <div style="position:absolute; top:-10px; right:16px; background:#09090b; color:#ffffff; border:1px solid #ffffff; padding:2px 8px; font-size:10px; font-weight:800; text-transform:uppercase; border-radius:4px;">
                    MEJOR F1-SCORE
                </div>
            @endif

            <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted-foreground); margin-bottom:4px;">
                {{ $model['model_type'] }}
            </div>
            <h2 style="font-size:16px; font-weight:800; color:var(--foreground); margin:0 0 16px;">
                {{ $model['model_name'] }}
            </h2>

            {{-- Métricas Clave --}}
            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:10px; margin-bottom:18px;">
                <div style="background:var(--muted); border:1px solid var(--border); padding:10px; border-radius:6px; text-align:center;">
                    <div style="font-size:10px; text-transform:uppercase; color:var(--muted-foreground); font-weight:700;">Exactitud</div>
                    <div style="font-size:16px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">
                        {{ number_format(($model['metrics']['accuracy'] ?? 0) * 100, 1) }}%
                    </div>
                </div>

                <div style="background:var(--muted); border:1px solid var(--border); padding:10px; border-radius:6px; text-align:center;">
                    <div style="font-size:10px; text-transform:uppercase; color:var(--muted-foreground); font-weight:700;">F1-Score</div>
                    <div style="font-size:16px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">
                        {{ number_format(($model['metrics']['f1_score'] ?? 0) * 100, 1) }}%
                    </div>
                </div>

                <div style="background:var(--muted); border:1px solid var(--border); padding:10px; border-radius:6px; text-align:center;">
                    <div style="font-size:10px; text-transform:uppercase; color:var(--muted-foreground); font-weight:700;">ROC-AUC</div>
                    <div style="font-size:16px; font-weight:800; color:var(--foreground); font-family:ui-monospace, monospace;">
                        {{ number_format($model['metrics']['roc_auc'] ?? 0, 3) }}
                    </div>
                </div>
            </div>

            {{-- Matriz de Confusión --}}
            @if (isset($model['confusion_matrix']))
                <div style="margin-bottom:16px;">
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted-foreground); margin-bottom:6px;">
                        Matriz de Confusión Acumulada
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px; font-family:ui-monospace, monospace; font-size:11px; text-align:center;">
                        <div style="background:var(--muted); border:1px solid var(--border); padding:6px; border-radius:4px;">
                            <span style="color:var(--muted-foreground); font-size:10px;">TN:</span> <strong>{{ $model['confusion_matrix']['true_negative'] }}</strong>
                        </div>
                        <div style="background:var(--muted); border:1px solid var(--border); padding:6px; border-radius:4px;">
                            <span style="color:var(--muted-foreground); font-size:10px;">FP:</span> <strong>{{ $model['confusion_matrix']['false_positive'] }}</strong>
                        </div>
                        <div style="background:var(--muted); border:1px solid var(--border); padding:6px; border-radius:4px;">
                            <span style="color:var(--muted-foreground); font-size:10px;">FN:</span> <strong>{{ $model['confusion_matrix']['false_negative'] }}</strong>
                        </div>
                        <div style="background:var(--muted); border:1px solid var(--border); padding:6px; border-radius:4px;">
                            <span style="color:var(--muted-foreground); font-size:10px;">TP:</span> <strong>{{ $model['confusion_matrix']['true_positive'] }}</strong>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Importancia de Características --}}
            <div>
                <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted-foreground); margin-bottom:8px;">
                    Peso Relativo de Variables
                </div>
                <div style="display:flex; flex-direction:column; gap:6px;">
                    @foreach ($model['feature_importances'] ?? [] as $feat => $val)
                        <div>
                            <div style="display:flex; justify-content:space-between; font-size:11px; margin-bottom:2px;">
                                <span style="font-family:ui-monospace, monospace; color:var(--foreground);">{{ $feat }}</span>
                                <span style="font-family:ui-monospace, monospace; font-weight:700;">{{ number_format($val * 100, 1) }}%</span>
                            </div>
                            <div style="height:4px; background:var(--muted); border-radius:2px; overflow:hidden;">
                                <div style="height:100%; width:{{ min(100, $val * 100) }}%; background:var(--foreground);"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Declaración de Rigor Científico Obligatoria --}}
<div class="card" style="padding:18px 22px; margin:0; border-left:4px solid var(--foreground);">
    <div style="font-size:12px; font-weight:700; text-transform:uppercase; color:var(--foreground); margin-bottom:4px;">
        <i class="fa fa-flask" style="margin-right:6px;"></i> Transparencia y Rigor Científico (NASA Space Apps Challenge 2026)
    </div>
    <p style="font-size:12px; color:var(--muted-foreground); margin:0; line-height:1.5;">
        {{ $benchmark['scientific_disclaimer'] ?? 'Los modelos han sido calibrados sobre cohortes sintéticas de astronautas simuladas según los lineamientos del NASA Human Research Program. No deben utilizarse como herramientas diagnósticas clínicas en pacientes terrestres sin validación clínica independiente.' }}
    </p>
</div>
@endsection
