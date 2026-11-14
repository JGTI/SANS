<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ScientificEngineService;
use App\Models\VisionEvaluation;
use App\Models\AstronautProfile;
use Illuminate\Http\Request;

class AiCommandCenterController extends Controller
{
    protected ScientificEngineService $engine;

    public function __construct(ScientificEngineService $engine)
    {
        $this->engine = $engine;
    }

    public function index()
    {
        $health = $this->engine->getHealth();
        $isOnline = $this->engine->isOnline();
        $models = $this->engine->getModelRegistry()['models'] ?? [];

        // Telemetría de la base de datos
        $totalEvaluations = VisionEvaluation::count();
        $totalAstronauts = AstronautProfile::count();
        $recentEvaluations = VisionEvaluation::with('astronaut')
            ->latest('evaluation_date')
            ->take(8)
            ->get();

        // Análisis de anomalías sobre las evaluaciones registradas
        $anomalyData = [];
        foreach ($recentEvaluations as $ev) {
            $shift = (float) $ev->hyperopic_shift;
            $iop = (float) $ev->iop_mmhg;
            $anomalyData[] = [
                'flight_day' => (int) $ev->flight_day,
                'hyperopic_shift' => $shift,
                'iop_mmhg' => $iop,
                'rnfl_microns' => (float) ($ev->rnfl_thickness_microns ?: 95.0),
            ];
        }
        $anomalyReport = $this->engine->detectAnomalies($anomalyData, 'isolation_forest', 0.15);

        return view('admin.ai-command.index', compact(
            'health',
            'isOnline',
            'models',
            'totalEvaluations',
            'totalAstronauts',
            'recentEvaluations',
            'anomalyReport'
        ));
    }
}
