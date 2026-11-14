<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ScientificEngineService;
use Illuminate\Http\Request;

class ExplainabilityCenterController extends Controller
{
    protected ScientificEngineService $engine;

    public function __construct(ScientificEngineService $engine)
    {
        $this->engine = $engine;
    }

    public function index(Request $request)
    {
        $shift = (float) $request->input('hyperopic_shift', 1.25);
        $rnfl = (float) $request->input('rnfl_delta_microns', 22.0);
        $iop = (float) $request->input('iop_mmhg', 17.5);
        $flightDay = (int) $request->input('flight_day', 90);
        $age = (int) $request->input('age', 45);
        $baseSe = (float) $request->input('baseline_se', 0.0);

        $sample = [
            'hyperopic_shift' => $shift,
            'rnfl_delta_microns' => $rnfl,
            'iop_mmhg' => $iop,
            'flight_day' => $flightDay,
            'age' => $age,
            'baseline_se' => $baseSe,
        ];

        // Estimación de probabilidad probabilística
        $prob = min(0.98, max(0.05, ($shift / 2.0) * 0.5 + ($rnfl / 40.0) * 0.35 + ($flightDay / 180.0) * 0.15));
        $label = $prob >= 0.70 ? 'Riesgo SANS Crítico' : ($prob >= 0.45 ? 'Riesgo SANS Elevado' : 'Control Normofisiológico');

        $explanation = $this->engine->explainPrediction($sample, $label, $prob);
        $isOnline = $this->engine->isOnline();

        return view('admin.explainability.index', compact(
            'explanation',
            'sample',
            'isOnline'
        ));
    }
}
