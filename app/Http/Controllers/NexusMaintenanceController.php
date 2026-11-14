<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Throwable;

class NexusMaintenanceController extends Controller
{
    private function commands(): array
    {
        return [
            'about' => [
                'label' => 'Ver información de Laravel',
                'command' => 'about',
                'parameters' => [],
            ],
            'migrate-status' => [
                'label' => 'Ver estado de migraciones',
                'command' => 'migrate:status',
                'parameters' => [],
            ],
            'migrate' => [
                'label' => 'Ejecutar migraciones',
                'command' => 'migrate',
                'parameters' => ['--force' => true],
            ],
            'optimize-clear' => [
                'label' => 'Limpiar todos los caches',
                'command' => 'optimize:clear',
                'parameters' => [],
            ],
            'config-clear' => [
                'label' => 'Limpiar cache de configuración',
                'command' => 'config:clear',
                'parameters' => [],
            ],
            'route-clear' => [
                'label' => 'Limpiar cache de rutas',
                'command' => 'route:clear',
                'parameters' => [],
            ],
            'view-clear' => [
                'label' => 'Limpiar vistas compiladas',
                'command' => 'view:clear',
                'parameters' => [],
            ],
            'cache-clear' => [
                'label' => 'Limpiar cache de aplicación',
                'command' => 'cache:clear',
                'parameters' => [],
            ],
            'storage-link' => [
                'label' => 'Intentar storage:link',
                'command' => 'storage:link',
                'parameters' => ['--force' => true],
            ],
            'queue-restart' => [
                'label' => 'Reiniciar workers de queue',
                'command' => 'queue:restart',
                'parameters' => [],
            ],
        ];
    }

    public function index(): View
    {
        abort_unless((bool) config('nexus.maintenance.enabled'), 404);

        return view('nexus.maintenance', [
            'commands' => $this->commands(),
            'output' => null,
            'success' => null,
        ]);
    }

    public function run(Request $request): View
    {
        abort_unless((bool) config('nexus.maintenance.enabled'), 404);

        $configuredKey = (string) config('nexus.maintenance.key');

        if ($configuredKey === '') {
            abort(503, 'NEXUS_MAINTENANCE_KEY no está configurada.');
        }

        $providedKey = (string) $request->input('key', '');

        if ($providedKey === '' || ! hash_equals($configuredKey, $providedKey)) {
            abort(403, 'Clave de mantenimiento incorrecta.');
        }

        $commands = $this->commands();
        $action = (string) $request->input('action', '');

        if (! array_key_exists($action, $commands)) {
            abort(422, 'Comando no permitido.');
        }

        $definition = $commands[$action];

        try {
            $exitCode = Artisan::call(
                $definition['command'],
                $definition['parameters']
            );

            $output = trim(Artisan::output());

            if ($output === '') {
                $output = 'Comando ejecutado. Artisan no devolvió texto.';
            }

            $success = $exitCode === 0;

            $output = sprintf(
                "[%s]\nComando: php artisan %s\nCódigo de salida: %d\n\n%s",
                now()->format('Y-m-d H:i:s'),
                $definition['command'],
                $exitCode,
                $output
            );
        } catch (Throwable $exception) {
            report($exception);

            $success = false;
            $output = sprintf(
                "[%s]\nERROR\n%s",
                now()->format('Y-m-d H:i:s'),
                $exception->getMessage()
            );
        }

        return view('nexus.maintenance', [
            'commands' => $commands,
            'output' => $output,
            'success' => $success,
        ]);
    }
}
