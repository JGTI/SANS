<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AstronautProfile;
use App\Models\SansObservation;
use App\Models\VisionEvaluation;
use App\Services\ScientificEngineService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DigitalTwinController extends Controller
{
    protected ScientificEngineService $engine;

    public function __construct(ScientificEngineService $engine)
    {
        $this->engine = $engine;
    }

    public function index(Request $request): View
    {
        if (AstronautProfile::count() === 0) {
            app(\Database\Seeders\OphthalmologySeeder::class)->run();
        }
        $astronauts = AstronautProfile::with(['evaluations', 'sansObservations'])->get();
        $selectedCode = $request->query('astro', 'ASTRO-01');
        
        $astronaut = AstronautProfile::where('code', $selectedCode)
            ->with(['evaluations' => fn($q) => $q->orderBy('mission_day'), 'sansObservations'])
            ->first() ?? $astronauts->first();

        // Extraer cronología longitudinal para el Gemelo Digital
        $evals = $astronaut ? $astronaut->evaluations : collect();
        $sans = $astronaut ? $astronaut->sansObservations : collect();

        $timelineDays = $evals->pluck('mission_day')->unique()->sort()->values()->all();
        if (empty($timelineDays)) {
            $timelineDays = [0, 30, 60, 90, 120];
        }

        // Construir modelo de datos por día de vuelo para la simulación interactiva
        $simulationData = [];
        foreach ($timelineDays as $day) {
            $odEval = $evals->where('mission_day', $day)->where('eye', 'OD')->first();
            $osEval = $evals->where('mission_day', $day)->where('eye', 'OS')->first();
            $sansObs = $sans->where('mission_day', $day)->first();

            $simulationData[$day] = [
                'day' => $day,
                'od_se' => $odEval ? (float) $odEval->spherical_equivalent : 0.00,
                'os_se' => $osEval ? (float) $osEval->spherical_equivalent : 0.00,
                'od_shift' => $odEval ? (float) $odEval->hyperopic_shift : 0.00,
                'od_snellen' => $odEval ? $odEval->snellen_fraction : '20/20',
                'od_logmar' => $odEval ? (float) $odEval->logmar : 0.000,
                'iop' => $odEval ? (float) $odEval->intraocular_pressure : 15.0,
                'frisen_grade' => $sansObs ? $sansObs->frisen_scale_grade : 0,
                'globe_flattening' => $sansObs ? (bool) $sansObs->posterior_globe_flattening : false,
                'choroidal_folds' => $sansObs ? (bool) $sansObs->choroidal_folds : false,
                'rnfl' => $sansObs ? ($sansObs->rnfl_thickness_microns ?? 95) : 95,
                'risk_tier' => $sansObs ? $sansObs->sans_risk_tier : 'low',
                'symptoms' => $odEval ? ($odEval->reported_symptoms ?? []) : [],
            ];
        }

        // Obtener datos de la estación Cirrus HD-OCT para el astronauta seleccionado
        $initialDay = $timelineDays[0] ?? 0;
        $scenario = match($astronaut?->code) {
            'ASTRO-01' => 'B',
            'ASTRO-02' => 'A',
            'ASTRO-03' => 'D',
            'ASTRO-04' => 'E',
            default => 'B'
        };

        $cirrusOct = $this->engine->getCirrusOctWorkstationData(
            astronautCode: $astronaut?->code ?? 'ASTRO-01',
            astronautName: $astronaut?->name ?? 'CDR Alex Mercer',
            flightDay: $initialDay,
            scenario: $scenario
        );

        $initialMode = $request->query('mode', '3d');

        return view('admin.digital-twin.index', compact('astronauts', 'astronaut', 'timelineDays', 'simulationData', 'cirrusOct', 'scenario', 'initialMode'));
    }

    public function octWorkstation(Request $request): View
    {
        if (!$request->has('mode')) {
            $request->merge(['mode' => 'oct']);
        }
        return $this->index($request);
    }

    public function octTelemetry(Request $request): JsonResponse
    {
        $code = $request->query('code', 'ASTRO-01');
        $name = $request->query('name', 'CDR Alex Mercer');
        $flightDay = (int) $request->query('day', 45);
        $scenario = $request->query('scenario', 'B');

        $data = $this->engine->getCirrusOctWorkstationData($code, $name, $flightDay, $scenario);

        return response()->json($data);
    }
}
