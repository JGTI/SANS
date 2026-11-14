<?php

use App\Http\Controllers\NexusMaintenanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Nexus Maintenance Routes
|--------------------------------------------------------------------------
|
| Estas rutas se cargan desde routes/api.php para ser STATELESS.
| Esto es intencional: deben funcionar incluso antes de crear las tablas
| de sessions/cache durante la primera migración en cPanel.
|
*/

Route::prefix('_nexus/maintenance')
    ->middleware('throttle:10,1')
    ->group(function (): void {
    Route::get('/', [NexusMaintenanceController::class, 'index'])
        ->name('nexus.maintenance');

    Route::post('/run', [NexusMaintenanceController::class, 'run'])
        ->name('nexus.maintenance.run');
});
