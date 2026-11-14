@extends('layouts.app')

@section('title', 'Automation Center · Centro de Automatizaciones y Sincronizaciones')

@push('styles')
<style>
    .auto-hero {
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
    .jobs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }
    .job-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: var(--shadow);
        transition: border-color 0.15s ease;
    }
    .job-card:hover {
        border-color: var(--foreground);
    }
</style>
@endpush

@section('content')
<div class="auto-hero">
    <div>
        <div class="eyebrow">
            LARAVEL SCHEDULER & BACKGROUND WORKERS · AUTOMATION CORE
        </div>
        <h1 style="margin:4px 0 8px; font-size:24px; font-weight:800; color:var(--foreground); letter-spacing:-0.03em;">
            Centro de Automatizaciones & Sincronización
        </h1>
        <p style="margin:0; font-size:13px; color:var(--muted-foreground); max-width:680px;">
            Monitoreo en tiempo real de tareas programadas: sincronizaciones periódicas de datasets biológicos de NASA OSDR, indexación bibliográfica de NTRS y verificación de integridad clínica.
        </p>
    </div>
    <form method="POST" action="{{ route('admin.automations.run-sync') }}">
        @csrf
        <button type="submit" class="btn btn-primary" style="padding:10px 20px; font-weight:700; font-size:13px;">
            <i class="fa fa-refresh"></i> Sincronizar Fuentes NASA Ahora
        </button>
    </form>
</div>

@if (session('success'))
    <div class="alert alert-success">
        <i class="fa fa-check-circle" style="margin-right:6px;"></i> {{ session('success') }}
    </div>
@endif

{{-- Rejilla de Tareas Programadas --}}
<div style="margin-bottom:16px;">
    <h2 style="font-size:16px; font-weight:700; color:var(--foreground); margin:0 0 4px;">Tareas Automáticas de Fondo (Scheduled Workers)</h2>
    <p style="font-size:12px; color:var(--muted-foreground); margin:0;">Procesos idempotentes, reintentables y auditables ejecutados por el orquestador de SPACE VISION.</p>
</div>

<div class="jobs-grid">
    @foreach ($automations as $job)
        <div class="job-card">
            <div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <span style="font-family:ui-monospace, monospace; font-size:11px; color:var(--muted-foreground);">{{ $job['id'] }}</span>
                    <span class="badge-subtle" style="font-size:10px;">
                        <i class="fa fa-circle" style="font-size:7px; margin-right:4px;"></i> {{ $job['status'] }}
                    </span>
                </div>
                <h3 style="font-size:15px; font-weight:700; color:var(--foreground); margin:0 0 8px;">{{ $job['name'] }}</h3>
                <div style="font-size:12px; color:var(--muted-foreground); margin-bottom:6px;">
                    <strong>Objetivo:</strong> {{ $job['target'] }}
                </div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-bottom:12px;">
                    <strong>Frecuencia:</strong> <span style="color:var(--foreground); font-family:ui-monospace, monospace;">{{ $job['schedule'] }}</span> ({{ $job['frequency'] }})
                </div>
            </div>
            <div style="border-top:1px solid var(--border); padding-top:10px; display:flex; justify-content:space-between; font-size:11px; color:var(--muted-foreground);">
                <span>Última ejecución:</span>
                <span style="color:var(--foreground); font-family:ui-monospace, monospace;">{{ $job['last_run'] }}</span>
            </div>
        </div>
    @endforeach
</div>

{{-- Registro de Auditoría de Sincronizaciones Recientes --}}
<div class="card" style="padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <div>
            <h2 class="card-title" style="font-size:16px;">Auditoría de Eventos de Sincronización</h2>
            <p class="card-description">Historial de ejecuciones y tiempos de respuesta de los microprocesos de background.</p>
        </div>
        <span class="badge-subtle" style="font-size:11px; font-family:ui-monospace, monospace;">TODO NOMINAL · 0 FALLOS</span>
    </div>

    <div class="table-responsive">
        <table class="table" style="width:100%; font-size:13px;">
            <thead>
                <tr>
                    <th>Hora (UTC)</th>
                    <th>Tarea / Conector</th>
                    <th>Estado de Integridad</th>
                    <th>Latencia</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($syncLogs as $log)
                    <tr>
                        <td style="font-family:ui-monospace, monospace;">{{ $log['time'] }}</td>
                        <td style="font-weight:600;">{{ $log['job'] }}</td>
                        <td>
                            <span class="badge-subtle" style="font-size:10px; padding:2px 8px;">
                                <i class="fa fa-check"></i> {{ $log['status'] }}
                            </span>
                        </td>
                        <td style="font-family:ui-monospace, monospace; color:var(--muted-foreground);">{{ $log['duration'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
