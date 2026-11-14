<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AstronautProfile;
use App\Models\SansObservation;
use App\Models\VisionEvaluation;
use App\Services\NasaDataService;
use App\Services\OphthalmologyCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpaceVisionApiController extends Controller
{
    public function __construct(
        protected OphthalmologyCalculator $calculator,
        protected NasaDataService $nasaService
    ) {}

    /**
     * Misiones espaciales registradas / simuladas
     */
    public function missions(): JsonResponse
    {
        $missions = AstronautProfile::select('mission_name', 'agency')
            ->distinct()
            ->get()
            ->map(function ($item) {
                $crewCount = AstronautProfile::where('mission_name', $item->mission_name)->count();
                return [
                    'mission' => $item->mission_name,
                    'agency' => $item->agency,
                    'crew_count' => $crewCount,
                    'type' => str_contains($item->mission_name, 'Artemis') ? 'Lunar Exploration' : 'Low Earth Orbit (LEO)',
                    'environment' => 'Microgravity / Spaceflight',
                    'is_simulated' => true,
                ];
            });

        return response()->json([
            'status' => 'success',
            'version' => 'v1',
            'data' => $missions,
            'source' => 'SPACE_VISION_SIMULATION_CORE',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Catálogo de astronautas y expedientes biomédicos
     */
    public function crew(Request $request): JsonResponse
    {
        $query = AstronautProfile::query();

        if ($request->has('code')) {
            $query->where('code', $request->query('code'));
        }

        if ($request->has('agency')) {
            $query->where('agency', $request->query('agency'));
        }

        $crew = $query->with(['evaluations', 'sansObservations'])->get()->map(function ($astro) {
            return [
                'code' => $astro->code,
                'name' => $astro->name,
                'agency' => $astro->agency,
                'mission' => $astro->mission_name,
                'days_in_space' => $astro->days_in_space,
                'baseline_refraction' => [
                    'od_se' => (float) $astro->baseline_spherical_equivalent_od,
                    'os_se' => (float) $astro->baseline_spherical_equivalent_os,
                    'od_logmar' => (float) $astro->baseline_logmar_od,
                    'os_logmar' => (float) $astro->baseline_logmar_os,
                ],
                'evaluations_count' => $astro->evaluations->count(),
                'latest_sans_observation' => $astro->sansObservations->sortByDesc('mission_day')->first(),
                'is_simulated' => (bool) $astro->is_simulated,
            ];
        });

        return response()->json([
            'status' => 'success',
            'version' => 'v1',
            'count' => $crew->count(),
            'data' => $crew,
        ]);
    }

    /**
     * Evaluaciones oftálmicas longitudinales
     */
    public function ocularExams(Request $request): JsonResponse
    {
        $query = VisionEvaluation::with('astronaut');

        if ($request->has('astronaut_code')) {
            $code = $request->query('astronaut_code');
            $query->whereHas('astronaut', fn($q) => $q->where('code', $code));
        }

        if ($request->has('eye')) {
            $query->where('eye', strtoupper($request->query('eye')));
        }

        if ($request->has('max_day')) {
            $query->where('mission_day', '<=', (int) $request->query('max_day'));
        }

        $exams = $query->orderBy('mission_day')->get()->map(function ($exam) {
            return [
                'id' => $exam->id,
                'astronaut_code' => $exam->astronaut->code,
                'mission_day' => $exam->mission_day,
                'eye' => $exam->eye,
                'refraction' => [
                    'sphere' => (float) $exam->sphere,
                    'cylinder' => (float) $exam->cylinder,
                    'axis' => (int) $exam->axis,
                    'spherical_equivalent' => (float) $exam->spherical_equivalent,
                    'hyperopic_shift' => (float) $exam->hyperopic_shift,
                ],
                'visual_acuity' => [
                    'snellen' => $exam->snellen_fraction,
                    'logmar' => (float) $exam->logmar,
                    'corrected' => (bool) $exam->is_corrected,
                ],
                'intraocular_pressure' => (float) $exam->intraocular_pressure,
                'symptoms' => $exam->reported_symptoms ?? [],
                'is_simulated' => (bool) $exam->is_simulated,
            ];
        });

        return response()->json([
            'status' => 'success',
            'count' => $exams->count(),
            'data' => $exams,
        ]);
    }

    /**
     * Escenarios de simulación deterministas
     */
    public function simulatorScenarios(): JsonResponse
    {
        $scenarios = [
            [
                'id' => 'scenario-1-stable',
                'name' => 'Escenario 1: Control Estable (Normofisiológico)',
                'description' => 'Tripulante en LEO con adaptación fisiológica nominal. Variaciones refractivas menores (< +0.25 D) atribuibles a fatiga visual o fluctuación corneal transitoria, sin signos estructurales de SANS.',
                'flight_days' => [0, 30, 60, 90, 120, 180],
                'expected_outcome' => 'Riesgo Bajo (Tier 1)',
                'frisen_peak' => 0,
                'max_hyperopic_shift' => 0.15,
                'globe_flattening' => false,
                'choroidal_folds' => false,
            ],
            [
                'id' => 'scenario-2-gradual',
                'name' => 'Escenario 2: Desplazamiento Hipermetrópico Gradual',
                'description' => 'Evolución moderada con astenopia progresiva. Shift hipermetrópico progresivo de +0.75 D en OD, borrosidad leve para lectura de paneles e indicios tempranos de congestión peripapilar (Frisén Grado 1).',
                'flight_days' => [0, 30, 60, 90, 120, 180],
                'expected_outcome' => 'Riesgo Moderado (Tier 2)',
                'frisen_peak' => 1,
                'max_hyperopic_shift' => 0.75,
                'globe_flattening' => false,
                'choroidal_folds' => true,
            ],
            [
                'id' => 'scenario-3-sans-critical',
                'name' => 'Escenario 3: Síndrome Neuroocular Completo (SANS Crítico)',
                'description' => 'Cuadro severo característico de misiones de espacio profundo (Artemis III / Tránsito a Marte). Deformación por aplanamiento posterior del polo ocular, pliegues coroideos en haz retiniano y edema de disco óptico Frisén Grado 2+, requiriendo contramedidas de descompresión de miembros inferiores (LBNP).',
                'flight_days' => [0, 30, 60, 90, 120, 180],
                'expected_outcome' => 'Riesgo Alto (Tier 3)',
                'frisen_peak' => 3,
                'max_hyperopic_shift' => 1.75,
                'globe_flattening' => true,
                'choroidal_folds' => true,
            ],
        ];

        return response()->json([
            'status' => 'success',
            'data' => $scenarios,
            'scientific_disclaimer' => 'Modelos reproducibles generados para investigación y entrenamiento médico aeroespacial simulado. No constituyen diagnóstico clínico automatizado.',
        ]);
    }

    /**
     * Endpoint interactivo del calculador oftalmológico espacial
     */
    public function evaluateCalculator(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sphere' => 'required|numeric|between:-25.00,25.00',
            'cylinder' => 'required|numeric|between:-15.00,15.00',
            'axis' => 'nullable|integer|between:0,180',
            'snellen_fraction' => 'nullable|string|regex:/^20\/[0-9]{2,3}$/',
            'baseline_se' => 'nullable|numeric|between:-25.00,25.00',
            'frisen_grade' => 'nullable|integer|between:0,5',
            'globe_flattening' => 'nullable|boolean',
            'choroidal_folds' => 'nullable|boolean',
            'rnfl_thickness' => 'nullable|integer|between:50,300',
        ]);

        $se = $this->calculator->calculateSphericalEquivalent((float) $validated['sphere'], (float) $validated['cylinder']);
        
        $logmar = null;
        if (!empty($validated['snellen_fraction'])) {
            $logmar = $this->calculator->convertSnellenToLogmar($validated['snellen_fraction']);
        }

        $hyperopicShift = 0.00;
        if (isset($validated['baseline_se'])) {
            $hyperopicShift = $this->calculator->calculateHyperopicShift($se, (float) $validated['baseline_se']);
        }

        $sansRisk = $this->calculator->evaluateSansRisk([
            'frisen_grade' => $validated['frisen_grade'] ?? 0,
            'globe_flattening' => $validated['globe_flattening'] ?? false,
            'choroidal_folds' => $validated['choroidal_folds'] ?? false,
            'hyperopic_shift' => $hyperopicShift,
            'rnfl_thickness' => $validated['rnfl_thickness'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'results' => [
                'spherical_equivalent' => $se,
                'logmar' => $logmar,
                'hyperopic_shift' => $hyperopicShift,
                'sans_risk' => $sansRisk,
            ],
            'formulas' => [
                'spherical_equivalent' => 'SE = SPH + (CYL / 2)',
                'logmar' => 'LogMAR = log10(denominator / numerator)',
                'hyperopic_shift' => 'ΔSE = SE_actual - SE_baseline',
            ],
        ]);
    }

    /**
     * Datasets biológicos de NASA OSDR
     */
    public function nasaOsdr(Request $request): JsonResponse
    {
        $query = $request->query('query');
        $studies = $this->nasaService->getOsdrStudies($query);

        return response()->json([
            'status' => 'success',
            'count' => count($studies),
            'source' => 'NASA Open Science Data Repository (OSDR)',
            'data' => $studies,
        ]);
    }

    /**
     * Publicaciones técnicas de NASA NTRS
     */
    public function nasaNtrs(Request $request): JsonResponse
    {
        $query = $request->query('query');
        $pubs = $this->nasaService->getNtrsPublications($query);

        return response()->json([
            'status' => 'success',
            'count' => count($pubs),
            'source' => 'NASA Technical Reports Server (NTRS)',
            'data' => $pubs,
        ]);
    }

    /**
     * Telemetría ambiental espacial de NASA DONKI
     */
    public function nasaDonki(): JsonResponse
    {
        $alerts = $this->nasaService->getDonkiAlerts();

        return response()->json([
            'status' => 'success',
            'count' => count($alerts),
            'source' => 'NASA Space Weather Database Of Notifications, Knowledge, Information (DONKI)',
            'scientific_disclaimer' => 'Telemetría ambiental de contexto aeroespacial. La radiación ionizante espacial constituye un riesgo biomédico sistémico, pero no se asume una correlación causal directa con el aplanamiento de globo ni con el síndrome SANS sin dosimetría tisular específica.',
            'data' => $alerts,
        ]);
    }
}
