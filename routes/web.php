<?php

use App\Http\Controllers\Admin\AutomationCenterController;
use App\Http\Controllers\Admin\AiCommandCenterController;
use App\Http\Controllers\Admin\ModelLabController;
use App\Http\Controllers\Admin\ExplainabilityCenterController;
use App\Http\Controllers\Admin\DataQualityObservatoryController;
use App\Http\Controllers\Admin\ModelRegistryController;
use App\Http\Controllers\Admin\ApiKeyArchitectureController;
use App\Http\Controllers\Admin\CrewDossierController;
use App\Http\Controllers\Admin\DemoTheaterController;
use App\Http\Controllers\Admin\DigitalTwinController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\MissionControlController;
use App\Http\Controllers\Admin\MissionSimulatorController;
use App\Http\Controllers\Admin\NasaObservatoryController;
use App\Http\Controllers\Admin\ResearchIntelligenceController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SansMonitorController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VisionLabController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;

// Ruta Raíz: Abre directamente el proyecto (Predictor SANS de 3 Pestañas sin login ni landing intermedia)
Route::get('/', [SansMonitorController::class, 'index'])->name('home');

// Selector Multilenguaje (Inglés idioma principal / Español)
Route::get('/locale/{locale}', function (string $locale) {
    $norm = strtolower($locale) === 'es' ? 'es' : 'en';
    session(['locale' => $norm]);
    return redirect()->back()->withCookie(cookie('spacevision_locale', $norm, 60 * 24 * 365));
})->name('locale.switch');

// Rutas Públicas de Demostración Directa (REGLA P0: Acceso inmediato sin requerir login para el jurado)
Route::get('/demo', [SansMonitorController::class, 'index'])->name('public.demo');
Route::get('/sans-monitor', [SansMonitorController::class, 'index'])->name('public.sans-monitor');
Route::get('/astronaut-check', [SansMonitorController::class, 'astronaut'])->name('public.astronaut');
Route::get('/impact', fn () => redirect()->route('public.astronaut'))->name('public.impact');
Route::post('/astronaut-check/transmit', [SansMonitorController::class, 'transmitFundus'])->name('public.astronaut.transmit');
Route::post('/sans-monitor/fundus-scan', [SansMonitorController::class, 'scanFundus'])->name('public.sans-monitor.fundus-scan');
Route::get('/admin/sans-monitor', [SansMonitorController::class, 'index'])->name('admin.sans-monitor.index');
Route::post('/admin/sans-monitor/cases', [SansMonitorController::class, 'storeCase'])->name('admin.sans-monitor.cases.store');
Route::post('/admin/sans-monitor/import', [SansMonitorController::class, 'importObservations'])->name('admin.sans-monitor.import');
Route::post('/admin/sans-monitor/adopt-protocol', [SansMonitorController::class, 'adoptProtocol'])->name('admin.sans-monitor.adopt-protocol');

// Espejos públicos sin login para módulos científicos y de demostración (preservando /admin/* para tests de RBAC)
Route::get('/digital-twin', [DigitalTwinController::class, 'index'])->name('public.digital-twin');
Route::get('/digital-twin/oct-telemetry', [DigitalTwinController::class, 'octTelemetry'])->name('public.digital-twin.oct-telemetry');
Route::get('/oct-analysis', [DigitalTwinController::class, 'octWorkstation'])->name('public.oct-analysis');
Route::get('/demo-theater', fn () => redirect()->route('home'))->name('public.demo-theater');
Route::get('/demo-theater/report/{code}', fn () => redirect()->route('home'))->name('public.demo-theater.report');
Route::get('/vision-lab', [VisionLabController::class, 'index'])->name('public.vision-lab');
Route::get('/vision-lab/export', [VisionLabController::class, 'exportCsv'])->name('public.vision-lab.export');
Route::get('/nasa-data', [NasaObservatoryController::class, 'index'])->name('public.nasa-data');
Route::get('/model-lab', [ModelLabController::class, 'index'])->name('public.model-lab');
Route::get('/model-lab/export', [ModelLabController::class, 'exportBenchmark'])->name('public.model-lab.export');
Route::get('/explainability', [ExplainabilityCenterController::class, 'index'])->name('public.explainability');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])
        ->middleware('throttle:10,1')
        ->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
        ->middleware('throttle:10,1')
        ->name('auth.google.callback');
});

Route::post('/logout', [AuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Mission Control Dashboard
    Route::get('/', [MissionControlController::class, 'index'])->name('dashboard');

    // Dictamen clínico firmado (exclusivo especialista médico autorizado P1 - RF-11)
    Route::post('/sans-monitor/clinician-decision', [SansMonitorController::class, 'storeClinicianDecision'])->name('sans-monitor.clinician-decision');

    // Vision Lab (Laboratorio oftalmológico espacial)
    Route::get('/vision-lab', [VisionLabController::class, 'index'])->name('vision-lab.index');
    Route::get('/vision-lab/export', [VisionLabController::class, 'exportCsv'])->name('vision-lab.export');
    Route::get('/vision-lab/create', [VisionLabController::class, 'create'])->name('vision-lab.create');
    Route::post('/vision-lab', [VisionLabController::class, 'store'])->name('vision-lab.store');

    // Astronaut Digital Twin (Gemelo digital neurooftalmológico)
    Route::get('/digital-twin', [DigitalTwinController::class, 'index'])->name('digital-twin');
    Route::get('/digital-twin/oct-telemetry', [DigitalTwinController::class, 'octTelemetry'])->name('digital-twin.oct-telemetry');
    Route::get('/oct-analysis', [DigitalTwinController::class, 'octWorkstation'])->name('oct-analysis');

    // Expediente Individual de Salud Visual de Tripulación
    Route::get('/crew/{code}', [CrewDossierController::class, 'show'])->name('crew.show');

    // Mission Simulator (Simulador de escenarios y misiones de microgravedad)
    Route::get('/mission-simulator', [MissionSimulatorController::class, 'index'])->name('mission-simulator');

    // NASA Data Observatory (Integración de repositorios científicos y telemetría espacial)
    Route::get('/nasa-data', [NasaObservatoryController::class, 'index'])->name('nasa-data');

    // Demo Theater (Experiencia interactiva para jueces y generación de reporte)
    Route::get('/demo-theater', [DemoTheaterController::class, 'index'])->name('demo-theater');
    Route::get('/demo-theater/report/{code}', [DemoTheaterController::class, 'report'])->name('demo-theater.report');

    // Research Intelligence (Matriz de hipótesis, literatura y citas)
    Route::get('/research-intelligence', [ResearchIntelligenceController::class, 'index'])->name('research-intelligence');

    // Advanced AI & Machine Learning Suite (NASA Space Apps Challenge 2026)
    Route::get('/ai-command', [AiCommandCenterController::class, 'index'])->name('ai-command');
    Route::get('/model-lab', [ModelLabController::class, 'index'])->name('model-lab');
    Route::get('/model-lab/export', [ModelLabController::class, 'exportReport'])->name('model-lab.export');
    Route::get('/explainability', [ExplainabilityCenterController::class, 'index'])->name('explainability');
    Route::get('/data-quality', [DataQualityObservatoryController::class, 'index'])->name('data-quality');
    Route::get('/model-registry', [ModelRegistryController::class, 'index'])->name('model-registry');

    // Gestión de Claves de APIs y Diagrama de Arquitectura
    Route::get('/api-keys', [ApiKeyArchitectureController::class, 'index'])->name('api-keys.index');
    Route::post('/api-keys', [ApiKeyArchitectureController::class, 'store'])->name('api-keys.store');
    Route::post('/api-keys/sync-env', [ApiKeyArchitectureController::class, 'syncToEnv'])->name('api-keys.sync-env');
    Route::put('/api-keys/{id}', [ApiKeyArchitectureController::class, 'update'])->name('api-keys.update');
    Route::delete('/api-keys/{id}', [ApiKeyArchitectureController::class, 'destroy'])->name('api-keys.destroy');
    Route::post('/api-keys/{id}/ping', [ApiKeyArchitectureController::class, 'testPing'])->name('api-keys.ping');

    // Automation Center (Tareas programadas, auditoría y disparador de sincronización)
    Route::get('/automations', [AutomationCenterController::class, 'index'])->name('automations');
    Route::post('/automations/run-sync', [AutomationCenterController::class, 'runSync'])->name('automations.run-sync');

    // Gestión de usuarios y roles
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('contacts', \App\Http\Controllers\Admin\ContactInquiryController::class)->only(['index', 'show', 'update']);

    // Mantenimiento y ejecución de comandos Artisan
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance');
    Route::post('/maintenance/run', [MaintenanceController::class, 'run'])->name('maintenance.run');
});

require __DIR__.'/seo.php';
