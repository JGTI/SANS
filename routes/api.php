<?php

use App\Http\Controllers\Api\V1\SpaceVisionApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Versionada de SPACE VISION v1
Route::prefix('space-vision/v1')->group(function () {
    Route::get('/missions', [SpaceVisionApiController::class, 'missions']);
    Route::get('/crew', [SpaceVisionApiController::class, 'crew']);
    Route::get('/ocular-exams', [SpaceVisionApiController::class, 'ocularExams']);
    Route::get('/simulator/scenarios', [SpaceVisionApiController::class, 'simulatorScenarios']);
    Route::post('/calculator/evaluate', [SpaceVisionApiController::class, 'evaluateCalculator']);
    Route::get('/nasa/osdr', [SpaceVisionApiController::class, 'nasaOsdr']);
    Route::get('/nasa/ntrs', [SpaceVisionApiController::class, 'nasaNtrs']);
    Route::get('/nasa/donki', [SpaceVisionApiController::class, 'nasaDonki']);
});

require __DIR__.'/maintenance.php';
