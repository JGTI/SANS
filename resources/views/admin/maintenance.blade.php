@extends('layouts.app')

@section('title', 'Consola de Sistema y Comandos · SPACE VISION')

@push('styles')
<style>
    .terminal-box {
        background: #09090b;
        color: #f4f4f5;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 12px;
        line-height: 1.5;
        border-radius: 8px;
        padding: 16px;
        min-height: 240px;
        max-height: 420px;
        overflow-y: auto;
        white-space: pre-wrap;
        border: 1px solid var(--border);
    }
    .cmd-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        transition: border-color .15s ease, background .15s ease;
    }
    .cmd-card:hover {
        background: var(--accent);
        border-color: var(--foreground);
    }
    .metric-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        background: var(--muted);
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        color: var(--foreground);
    }
    .maintenance-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(320px, 0.9fr);
        gap: 20px;
    }
    @media (max-width: 960px) {
        .maintenance-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div>
        <div class="eyebrow">Operaciones de Sistema</div>
        <h1>Mantenimiento y Comandos de Misión</h1>
        <p>Consola de comandos Artisan, migraciones de base de datos y telemetría del servidor.</p>
    </div>
</div>

<div class="maintenance-grid">
    {{-- Columna izquierda: Comandos y Terminal --}}
    <div>
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Comandos de Misión y Base de Datos</h2>
                    <p class="card-description">Ejecuta tareas operativas del motor Laravel en segundo plano.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="command-list">
                    @foreach ($commands as $key => $cmd)
                        <div class="cmd-card">
                            <div style="min-width:0; flex:1;">
                                <strong style="color:var(--foreground); font-size:13px; display:block;">{{ $cmd['label'] }}</strong>
                                <span style="color:var(--muted-foreground); font-size:12px;">{{ $cmd['description'] }}</span>
                            </div>
                            <button
                                type="button"
                                class="btn btn-primary run-cmd-btn"
                                data-action="{{ $key }}"
                                data-label="{{ $cmd['command'] }}"
                                style="font-size:12px; padding:6px 12px; min-height:32px;"
                            >
                                <i class="fa fa-play" style="font-size:10px;"></i> Ejecutar
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Terminal interactivo --}}
        <div class="card">
            <div class="card-header" style="background:var(--muted);">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="width:8px; height:8px; border-radius:50%; background:#ef4444; display:inline-block;"></span>
                    <span style="width:8px; height:8px; border-radius:50%; background:#f59e0b; display:inline-block;"></span>
                    <span style="width:8px; height:8px; border-radius:50%; background:#10b981; display:inline-block;"></span>
                    <strong style="font-size:12px; font-family:monospace; margin-left:6px; color:var(--foreground);">TERMINAL ARTISAN</strong>
                </div>
                <div>
                    <button type="button" class="btn btn-outline" id="clear-terminal" title="Limpiar terminal" style="padding:4px 8px; min-height:28px; font-size:11px;">
                        <i class="fa fa-trash"></i> Limpiar
                    </button>
                </div>
            </div>
            <div class="card-body" style="background:#09090b; padding:12px;">
                <div id="artisan-output" class="terminal-box">SPACE VISION Mission Operating System [Version 2.0.26]
(c) NASA Space Apps Challenge · Querétaro

Listo para ejecutar comandos. Haz clic en 'Ejecutar' en cualquiera de las acciones superiores.</div>
            </div>
        </div>
    </div>

    {{-- Columna derecha: Telemetría y log del sistema --}}
    <div>
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Telemetría de Servidor</h2>
                    <p class="card-description">Parámetros del entorno de ejecución.</p>
                </div>
            </div>
            <div class="card-body" style="padding:0;">
                <table class="table" style="margin:0;">
                    <tbody>
                        <tr>
                            <td style="padding:12px 18px; font-weight:600;">PHP Version</td>
                            <td style="padding:12px 18px; text-align:right;"><span class="metric-badge">{{ $systemInfo['php_version'] }}</span></td>
                        </tr>
                        <tr>
                            <td style="padding:12px 18px; font-weight:600;">Laravel Framework</td>
                            <td style="padding:12px 18px; text-align:right;"><span class="metric-badge">{{ $systemInfo['laravel_version'] }}</span></td>
                        </tr>
                        <tr>
                            <td style="padding:12px 18px; font-weight:600;">Entorno (APP_ENV)</td>
                            <td style="padding:12px 18px; text-align:right;"><span class="badge-subtle" style="font-weight:700;">{{ strtoupper($systemInfo['app_env']) }}</span></td>
                        </tr>
                        <tr>
                            <td style="padding:12px 18px; font-weight:600;">Base de Datos</td>
                            <td style="padding:12px 18px; text-align:right;"><span class="metric-badge">{{ $systemInfo['db_connection'] }}: {{ $systemInfo['db_name'] }}</span></td>
                        </tr>
                        <tr>
                            <td style="padding:12px 18px; font-weight:600;">Driver de Caché</td>
                            <td style="padding:12px 18px; text-align:right;"><span class="metric-badge">{{ $systemInfo['cache_driver'] }}</span></td>
                        </tr>
                        <tr>
                            <td style="padding:12px 18px; font-weight:600;">Driver de Sesión</td>
                            <td style="padding:12px 18px; text-align:right;"><span class="metric-badge">{{ $systemInfo['session_driver'] }}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Visor de log reciente --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Log del Sistema</h2>
                    <p class="card-description">Últimos eventos registrados en laravel.log.</p>
                </div>
                <button type="button" class="btn btn-outline" onclick="location.reload();" title="Recargar log" style="padding:4px 8px; min-height:28px; font-size:11px;">
                    <i class="fa fa-refresh"></i>
                </button>
            </div>
            <div class="card-body" style="background:#09090b; padding:12px;">
                <pre style="background:transparent; color:#a1a1aa; border:none; font-family:ui-monospace, monospace; font-size:11px; max-height:280px; overflow-y:auto; margin:0; white-space:pre-wrap;">{{ $recentLog ?: 'No se registran errores o logs recientes en storage/logs/laravel.log.' }}</pre>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const $output = $('#artisan-output');
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $('#clear-terminal').on('click', function() {
        $output.text('[Terminal limpia]');
    });

    $('.run-cmd-btn').on('click', function() {
        const $btn = $(this);
        const action = $btn.data('action');
        const cmdName = $btn.data('label');

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Ejecutando...');
        $output.append('\n> Iniciando: php artisan ' + cmdName + ' ...\n');
        $output.scrollTop($output[0].scrollHeight);

        $.ajax({
            url: "{{ route('admin.maintenance.run') }}",
            type: 'POST',
            data: {
                _token: csrfToken,
                action: action
            },
            dataType: 'json',
            success: function(res) {
                $output.append(res.output + '\n----------------------------------------------------\n');
                $output.scrollTop($output[0].scrollHeight);
                Swal.fire({
                    icon: 'success',
                    title: 'Comando completado',
                    text: 'php artisan ' + cmdName + ' ejecutado correctamente.',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            },
            error: function(xhr) {
                const err = xhr.responseJSON ? xhr.responseJSON.output : 'Error desconocido al ejecutar comando.';
                $output.append('[ERROR]\n' + err + '\n----------------------------------------------------\n');
                $output.scrollTop($output[0].scrollHeight);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de ejecución',
                    text: 'Ocurrió un error al ejecutar ' + cmdName
                });
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-play"></i> Ejecutar');
            }
        });
    });
});
</script>
@endpush
