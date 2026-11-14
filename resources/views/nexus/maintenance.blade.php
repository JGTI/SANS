<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nexus Maintenance</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #111827;
            color: #e5e7eb;
        }
        .wrap {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }
        .card {
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 18px;
        }
        h1 { margin-top: 0; }
        label {
            display: block;
            font-weight: bold;
            margin: 16px 0 8px;
        }
        input, select, button {
            width: 100%;
            border-radius: 8px;
            border: 1px solid #4b5563;
            padding: 12px;
            font-size: 16px;
        }
        input, select {
            background: #111827;
            color: #fff;
        }
        button {
            margin-top: 18px;
            background: #2563eb;
            border: none;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover { background: #1d4ed8; }
        .warning {
            background: #422006;
            border-color: #92400e;
        }
        .ok {
            border-left: 5px solid #22c55e;
        }
        .bad {
            border-left: 5px solid #ef4444;
        }
        pre {
            overflow: auto;
            white-space: pre-wrap;
            word-break: break-word;
            background: #030712;
            border-radius: 8px;
            padding: 16px;
            color: #d1d5db;
        }
        .small {
            color: #9ca3af;
            font-size: 13px;
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>Nexus Maintenance</h1>
        <p>Panel seguro para ejecutar tareas Artisan permitidas en cPanel compartido.</p>
        <p class="small">
            No existe una opción para escribir comandos manuales. Esto evita convertir
            esta ruta en una consola remota.
        </p>
    </div>

    <div class="card warning">
        <strong>Producción:</strong>
        mantén la URL privada y usa una clave larga. Cuando no lo necesites puedes poner
        <code>NEXUS_MAINTENANCE_ENABLED=false</code>.
    </div>

    <div class="card">
        <form method="POST" action="{{ url('/api/_nexus/maintenance/run') }}" id="maintenance-form">
            <label for="key">Clave de mantenimiento</label>
            <input
                type="password"
                name="key"
                id="key"
                autocomplete="current-password"
                required
            >

            <label for="action">Acción</label>
            <select name="action" id="action" required>
                @foreach ($commands as $value => $definition)
                    <option value="{{ $value }}">{{ $definition['label'] }}</option>
                @endforeach
            </select>

            <button type="submit">Ejecutar</button>
        </form>
    </div>

    @if ($output !== null)
        <div class="card {{ $success ? 'ok' : 'bad' }}">
            <strong>{{ $success ? 'Completado' : 'Error' }}</strong>
            <pre>{{ $output }}</pre>
        </div>
    @endif
</div>

<script>
    (function () {
        const key = document.getElementById('key');
        const form = document.getElementById('maintenance-form');

        try {
            key.value = sessionStorage.getItem('nexusMaintenanceKey') || '';
            form.addEventListener('submit', function () {
                sessionStorage.setItem('nexusMaintenanceKey', key.value);
            });
        } catch (e) {
            // sessionStorage puede estar bloqueado; el panel sigue funcionando.
        }
    })();
</script>
</body>
</html>
