<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ScientificEngineService;
use App\Models\VisionEvaluation;
use App\Models\AstronautProfile;
use Illuminate\Http\Request;

class DataQualityObservatoryController extends Controller
{
    protected ScientificEngineService $engine;

    public function __construct(ScientificEngineService $engine)
    {
        $this->engine = $engine;
    }

    public function index()
    {
        $evaluations = VisionEvaluation::with('astronaut')->get();

        $records = [];
        foreach ($evaluations as $ev) {
            $records[] = [
                'sphere' => (float) $ev->sphere_od,
                'cylinder' => (float) $ev->cylinder_od,
                'axis' => (float) $ev->axis_od,
                'iop_mmhg' => (float) $ev->iop_mmhg,
                'rnfl_microns' => (float) ($ev->rnfl_thickness_microns ?: 95.0),
                'flight_day' => (int) $ev->flight_day,
            ];
        }

        // Si hay pocos registros en BD, complementar con cohorte sintética para auditoría robusta
        if (count($records) < 15) {
            $synth = $this->engine->runDigitalTwin('B', 42, 120, 15);
            foreach ($synth['timeline'] ?? [] as $pt) {
                $records[] = [
                    'sphere' => (float) $pt['refraction_od']['sphere'],
                    'cylinder' => (float) $pt['refraction_od']['cylinder'],
                    'axis' => (float) $pt['refraction_od']['axis'],
                    'iop_mmhg' => (float) $pt['biometrics']['iop_mmhg'],
                    'rnfl_microns' => (float) $pt['biometrics']['rnfl_microns'],
                    'flight_day' => (int) $pt['flight_day'],
                ];
            }
        }

        $audit = $this->engine->auditDataQuality($records);
        $isOnline = $this->engine->isOnline();

        return view('admin.data-quality.index', compact(
            'audit',
            'isOnline',
            'records'
        ));
    }
}
