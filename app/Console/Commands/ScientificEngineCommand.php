<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ScientificEngineService;
use Symfony\Component\Process\Process;

class ScientificEngineCommand extends Command
{
    protected $signature = 'scientific:engine {action=status : status, start, stop, test} {--port=8001}';
    protected $description = 'Gestiona y verifica el microservicio de computación científica Python y FastAPI';

    public function handle(ScientificEngineService $service): int
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'status':
                $this->info("Verificando estado del motor científico SPACE VISION...");
                $online = $service->isOnline();
                if ($online) {
                    $this->info("✅ Motor Científico Python: ONLINE (http://127.0.0.1:8001)");
                    $health = $service->getHealth();
                    $this->table(
                        ['Componente', 'Versión / Detalle'],
                        [
                            ['Servicio', $health['service'] ?? 'N/A'],
                            ['Versión', $health['version'] ?? 'N/A'],
                            ['Python', $health['system']['python_version'] ?? 'N/A'],
                            ['NumPy', $health['scientific_stack']['numpy'] ?? 'N/A'],
                            ['SciPy', $health['scientific_stack']['scipy'] ?? 'N/A'],
                            ['Scikit-learn', $health['scientific_stack']['scikit_learn'] ?? 'N/A'],
                            ['Pandas', $health['scientific_stack']['pandas'] ?? 'N/A'],
                        ]
                    );
                } else {
                    $this->warn("⚠️  Motor Científico Python: OFFLINE");
                    $this->line("El sistema está operando en modo Fallback Local PHP (100% funcional).");
                    $this->line("Para iniciar el motor Python ejecuta: php artisan scientific:engine start");
                }
                return Command::SUCCESS;

            case 'test':
                $this->info("Ejecutando suite de pruebas científicas de Python (pytest)...");
                $process = new Process([
                    base_path('scientific_engine/.venv/bin/pytest'),
                    '-v',
                    '-c',
                    base_path('scientific_engine/pytest.ini')
                ], base_path('scientific_engine'));
                $process->setTimeout(60);
                $process->run(function ($type, $buffer) {
                    $this->output->write($buffer);
                });
                return $process->isSuccessful() ? Command::SUCCESS : Command::FAILURE;

            case 'start':
                $port = $this->option('port') ?: 8001;
                $this->info("Iniciando FastAPI Scientific Engine en puerto {$port}...");
                $cmd = sprintf(
                    'cd %s && .venv/bin/uvicorn app.main:app --host 127.0.0.1 --port %d > %s 2>&1 &',
                    escapeshellarg(base_path('scientific_engine')),
                    (int) $port,
                    escapeshellarg(storage_path('logs/scientific_engine.log'))
                );
                exec($cmd);
                sleep(2);
                if ($service->isOnline()) {
                    $this->info("🚀 Motor científico iniciado exitosamente en http://127.0.0.1:{$port}");
                    return Command::SUCCESS;
                } else {
                    $this->warn("El comando fue enviado al fondo. Verifica con 'php artisan scientific:engine status'.");
                    return Command::SUCCESS;
                }

            case 'stop':
                $this->info("Deteniendo instancias de FastAPI uvicorn en puerto 8001...");
                exec("fuser -k 8001/tcp 2>/dev/null || true");
                $this->info("Proceso detenido.");
                return Command::SUCCESS;

            default:
                $this->error("Acción desconocida: {$action}. Usa: status, start, stop o test.");
                return Command::FAILURE;
        }
    }
}
