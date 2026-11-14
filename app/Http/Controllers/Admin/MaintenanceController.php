<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Throwable;

class MaintenanceController extends Controller
{
    private function allowedCommands(): array
    {
        return [
            'migrate-status' => [
                'label' => 'Estado de migraciones (migrate:status)',
                'command' => 'migrate:status',
                'parameters' => [],
                'description' => 'Muestra qué tablas y migraciones están ejecutadas y cuáles están pendientes.',
                'badge' => 'info',
            ],
            'migrate' => [
                'label' => 'Ejecutar migraciones (migrate)',
                'command' => 'migrate',
                'parameters' => ['--force' => true],
                'description' => 'Aplica todas las migraciones de base de datos pendientes.',
                'badge' => 'primary',
            ],
            'db-seed' => [
                'label' => 'Ejecutar seeders (db:seed)',
                'command' => 'db:seed',
                'parameters' => ['--force' => true],
                'description' => 'Rellena roles, permisos y usuarios iniciales de SPACE VISION.',
                'badge' => 'success',
            ],
            'optimize-clear' => [
                'label' => 'Limpiar todos los cachés (optimize:clear)',
                'command' => 'optimize:clear',
                'parameters' => [],
                'description' => 'Limpia config, rutas, vistas, eventos y caché de compilación.',
                'badge' => 'warning',
            ],
            'config-clear' => [
                'label' => 'Limpiar caché de configuración (config:clear)',
                'command' => 'config:clear',
                'parameters' => [],
                'description' => 'Fuerza a Laravel a recargar los archivos .env y config/.',
                'badge' => 'warning',
            ],
            'scientific-engine-status' => [
                'label' => 'Estado del Motor Científico Python (scientific:engine status)',
                'command' => 'scientific:engine',
                'parameters' => ['action' => 'status'],
                'description' => 'Verifica la disponibilidad de FastAPI, NumPy, SciPy y Scikit-learn.',
                'badge' => 'info',
            ],
            'scientific-engine-test' => [
                'label' => 'Ejecutar Pytest Científico (scientific:engine test)',
                'command' => 'scientific:engine',
                'parameters' => ['action' => 'test'],
                'description' => 'Ejecuta la suite de 20 pruebas científicas automatizadas de Python.',
                'badge' => 'success',
            ],
            'route-clear' => [
                'label' => 'Limpiar caché de rutas (route:clear)',
                'command' => 'route:clear',
                'parameters' => [],
                'description' => 'Limpia las rutas compiladas en caché.',
                'badge' => 'warning',
            ],
            'view-clear' => [
                'label' => 'Limpiar vistas compiladas (view:clear)',
                'command' => 'view:clear',
                'parameters' => [],
                'description' => 'Elimina las plantillas Blade compiladas para ver cambios visuales.',
                'badge' => 'warning',
            ],
            'about' => [
                'label' => 'Diagnóstico de Laravel (about)',
                'command' => 'about',
                'parameters' => [],
                'description' => 'Detalle del entorno PHP, Laravel, base de datos y paquetes.',
                'badge' => 'info',
            ],
            'storage-link' => [
                'label' => 'Vincular almacenamiento (storage:link)',
                'command' => 'storage:link',
                'parameters' => ['--force' => true],
                'description' => 'Crea el enlace simbólico de public/storage hacia storage/app/public.',
                'badge' => 'secondary',
            ],
        ];
    }

    public function index(Request $request): View
    {
        $commands = $this->allowedCommands();
        $logPath = storage_path('logs/laravel.log');
        $recentLog = '';

        if (File::exists($logPath)) {
            // Leer las últimas 80 líneas del log de Laravel de forma segura
            $content = File::get($logPath);
            $lines = explode("\n", trim($content));
            $recentLog = implode("\n", array_slice($lines, -80));
        }

        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug') ? 'Activado' : 'Desactivado',
            'db_connection' => config('database.default'),
            'db_name' => config('database.connections.'.config('database.default').'.database'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
        ];

        return view('admin.maintenance', compact('commands', 'recentLog', 'systemInfo'));
    }

    public function run(Request $request): JsonResponse
    {
        $action = (string) $request->input('action', '');
        $commands = $this->allowedCommands();

        if (! array_key_exists($action, $commands)) {
            return response()->json([
                'success' => false,
                'output' => 'Comando no permitido o no registrado.',
            ], 422);
        }

        $definition = $commands[$action];

        try {
            $exitCode = Artisan::call($definition['command'], $definition['parameters']);
            $rawOutput = trim(Artisan::output());

            if ($rawOutput === '') {
                $rawOutput = 'Comando ejecutado exitosamente sin salida de texto.';
            }

            $output = sprintf(
                "[%s UTC] php artisan %s (Código salida: %d)\n\n%s",
                now()->format('Y-m-d H:i:s'),
                $definition['command'],
                $exitCode,
                $rawOutput
            );

            return response()->json([
                'success' => $exitCode === 0,
                'exit_code' => $exitCode,
                'command' => $definition['command'],
                'output' => $output,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'exit_code' => 1,
                'command' => $definition['command'],
                'output' => sprintf("[%s UTC] ERROR:\n%s", now()->format('Y-m-d H:i:s'), $exception->getMessage()),
            ], 500);
        }
    }
}
