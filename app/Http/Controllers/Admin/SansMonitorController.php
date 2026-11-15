<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SansMonitor\SansMonitorEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SansMonitorController extends Controller
{
    public function __construct(
        private readonly SansMonitorEngine $engine
    ) {}

    /**
     * Public, task-first astronaut experience. The scientific workspace remains
     * available through index(); this entry point only selects the crew view.
     */
    public function astronaut(Request $request)
    {
        $request->query->set('tab', 'astronauta');

        return $this->index($request);
    }

    /**
     * Public high-impact experience ("Through the Astronaut's Eyes + Mission & Earth ROI")
     * designed so non-medical judges feel the problem in 15 seconds.
     */
    public function impact()
    {
        if (! Schema::hasTable('sans_cases')) {
            Artisan::call('migrate', ['--force' => true]);
        }
        if (DB::table('sans_cases')->count() === 0) {
            app(\Database\Seeders\SansMonitorSeeder::class)->run();
        }

        $cases = DB::table('sans_cases')->orderBy('case_id')->get();
        $evaluations = [];
        foreach ($cases as $c) {
            $evaluations[$c->case_id] = $this->engine->evaluateCaseAtDay($c, null);
        }

        return view('admin.sans-monitor.impact', compact('cases', 'evaluations'));
    }

    /**
     * Workspace unificado de 3 pestañas principales de SANS Monitor:
     * 1. Predictor de 6 Biomarcadores
     * 2. Antes · Durante · Después (+30, +60, +90)
     * 3. 3 Modelos de Escenario (Estable, Empeora, Sin escanear)
     */
    public function index(Request $request)
    {
        if (! Schema::hasTable('sans_cases')) {
            Artisan::call('migrate', ['--force' => true]);
        }

        // Si la tabla está vacía (ej. en entorno de test RefreshDatabase), sembrar catálogo y casos base
        if (DB::table('sans_cases')->count() === 0) {
            app(\Database\Seeders\SansMonitorSeeder::class)->run();
        }

        $cases = DB::table('sans_cases')->orderBy('case_id')->get();
        $selectedCaseId = $request->query('case_id', 'CASE-PROG-02');
        $activeCase = $cases->firstWhere('case_id', $selectedCaseId) ?? $cases->first();

        $activeTab = $request->query('tab', 'hub'); // hub, biomarcadores, seguimiento, escenarios (soporta ficha, simulador, estudios, inclinacion)
        $selectedEye = strtoupper($request->query('eye', 'OD')); // OD, OS, BILATERAL
        $simEye = $selectedEye === 'BILATERAL' ? 'OD' : $selectedEye;
        $selectedDay = (int) $request->query('day', 30);
        $selectedRange = $request->query('range', '0-30'); // 0-30, 0-90, 0-150
        $timelineMode = strtoupper($request->query('mode', 'HISTORICO')); // HISTORICO, SIMULACION
        $timeAxis = strtoupper($request->query('time_axis', 'MISION')); // MISION, DESDE_FIN
        $contextOpen = $request->query('context', '0') === '1';
        $newerVersionsFilter = $request->query('newer_versions', '1') === '1';

        // Modalidades disponibles en la ficha actual (para buscador de catálogo RF-06)
        $availableModalities = DB::table('sans_metric_observations')
            ->where('case_id', $activeCase->case_id)
            ->where('status', 'MEDIDO')
            ->pluck('modality')
            ->unique()
            ->values()
            ->all();

        // 1. Catálogo y coincidencias para la ficha activa (Pestaña 1)
        $catalogSearch = $this->engine->searchProtocolsForCase($activeCase, $availableModalities, $newerVersionsFilter);

        // 2. Evaluación operativa del expediente al día seleccionado (Pestaña 2 y cabecera)
        $asOfDayForEval = $timelineMode === 'HISTORICO' ? $selectedDay : null;
        $caseEvaluation = $this->engine->evaluateCaseAtDay($activeCase, $asOfDayForEval);

        // Observaciones de misión de la ficha activa
        $missionObservations = DB::table('sans_metric_observations')
            ->where('case_id', $activeCase->case_id)
            ->whereNull('exposure_id')
            ->orderBy('mission_day')
            ->orderBy('days_post_end')
            ->get();

        // Tarjetas de seguimiento +30, +60 y +90 días y acciones sugeridas (RF-31 a RF-34)
        $followupVisits = DB::table('sans_followup_visits')
            ->where('case_id', $activeCase->case_id)
            ->orderBy('target_milestone_day')
            ->get();

        $followupActions = DB::table('sans_followup_actions')
            ->where('case_id', $activeCase->case_id)
            ->orderBy('milestone_day')
            ->get();

        // Contexto del caso en 5 dominios (RF-35, RF-36)
        $contextRecords = DB::table('sans_context_records')
            ->where('case_id', $activeCase->case_id)
            ->get()
            ->groupBy('domain');

        // 3. Estado del Simulador Temporal (Pestaña 3)
        $simulatorState = $this->engine->getTimelineStateAtDay(
            $activeCase->case_id,
            $simEye,
            $selectedDay,
            $timelineMode
        );

        // 4. Estudios y tendencias con 4 controles de filtro (Pestaña 4)
        $studiesFilterResult = $this->engine->filterStudiesAndTrends([
            'dataset' => $request->query('dataset', 'ALL'),
            'evolution' => $request->query('evolution', 'ALL'),
            'coverage' => $request->query('coverage', 'ALL'),
            'engine' => $request->query('engine', 'DEMO_RULE_V1'),
            'max_day' => $request->query('max_day', ''),
        ]);

        // 5. Inclinación y seguimiento (Pestaña 5)
        $exposures = DB::table('sans_exposure_sessions')->orderBy('exposure_id')->get();
        $selectedExposureId = $request->query('exposure_id', 'EXP-HDT6-01');
        $activeExposure = $exposures->firstWhere('exposure_id', $selectedExposureId) ?? $exposures->first();

        $exposureObservations = $activeExposure
            ? DB::table('sans_metric_observations')
                ->where('exposure_id', $activeExposure->exposure_id)
                ->orderBy('days_post_end')
                ->get()
            : collect();

        $initialObsId = $request->query('initial_obs_id', 'OBS-TILT04-EXP1-OD-PRE');
        $compareObsId = $request->query('compare_obs_id', 'OBS-TILT04-EXP1-OD-POST30');
        $exposureComparison = $activeExposure
            ? $this->engine->compareExposureVisits($activeExposure->exposure_id, $initialObsId, $compareObsId)
            : null;

        // 6. Predictor basado en los 6 Biomarcadores Clave (Pestaña 1)
        $biomarkerPredictor = $this->engine->evaluateBiomarkerPredictor($activeCase, [
            'oct_trt_baseline_um' => $request->query('oct_trt_baseline_um'),
            'oct_trt_current_um' => $request->query('oct_trt_current_um'),
            'axial_length_mm' => $request->query('axial_length_mm'),
            'hyperopia_sim_d' => $request->query('hyperopia_sim_d'),
            'papilledema_frisen' => $request->query('papilledema_frisen'),
            'iop_mmhg' => $request->query('iop_mmhg'),
            'prior_hdt6_exposure' => $request->query('prior_hdt6_exposure'),
        ]);

        $defaultFundusByCase = [
            'CASE-STABLE-01' => ['od_image' => 'images/sans/fundus/fundus-normal.jpg', 'os_image' => 'images/sans/fundus/fundus-normal.jpg', 'od_preset' => 'normal', 'os_preset' => 'normal', 'iop_od' => 14.5, 'iop_os' => 14.2],
            'CASE-PROG-02' => ['od_image' => 'images/sans/fundus/fundus-edema.png', 'os_image' => 'images/sans/fundus/fundus-folds.jpg', 'od_preset' => 'edema', 'os_preset' => 'folds', 'iop_od' => 16.4, 'iop_os' => 15.1],
            'CASE-ASO-05' => ['od_image' => 'images/sans/fundus/fundus-edema.png', 'os_image' => 'images/sans/fundus/fundus-normal.jpg', 'od_preset' => 'edema', 'os_preset' => 'normal', 'iop_od' => 17.2, 'iop_os' => 15.0],
            'CASE-TILT-04' => ['od_image' => 'images/sans/fundus/fundus-folds.jpg', 'os_image' => 'images/sans/fundus/fundus-normal.jpg', 'od_preset' => 'folds', 'os_preset' => 'normal', 'iop_od' => 15.8, 'iop_os' => 15.4],
        ];
        $orbitalTransmission = session("orbital_fundus_transmissions.{$activeCase->case_id}") ?? array_merge(
            ['case_id' => $activeCase->case_id, 'transmitted_at' => now()->subHours(2)->toIso8601String(), 'source' => 'DEMO_ORBITAL_ARCHIVE'],
            $defaultFundusByCase[$activeCase->case_id] ?? ['od_image' => 'images/sans/fundus/fundus-normal.jpg', 'os_image' => 'images/sans/fundus/fundus-normal.jpg', 'od_preset' => 'normal', 'os_preset' => 'normal', 'iop_od' => 15.5, 'iop_os' => 15.0]
        );

        $fundusGallery = [
            ['id' => 'fd0-od', 'eye' => 'OD', 'day' => 'FD0', 'label_en' => 'FD0 Baseline · Normal Disc', 'label_es' => 'FD0 Basal · Papila Normal', 'preset' => 'normal', 'iop' => 14.5, 'image' => 'images/sans/fundus/fundus-normal.jpg', 'filename' => 'fundus-normal.jpg'],
            ['id' => 'fd0-os', 'eye' => 'OS', 'day' => 'FD0', 'label_en' => 'FD0 Baseline · Left Eye', 'label_es' => 'FD0 Basal · Ojo Izquierdo', 'preset' => 'os_baseline', 'iop' => 14.2, 'image' => 'images/sans/fundus/fundus-os-baseline.jpg', 'filename' => 'fundus-os-baseline.jpg'],
            ['id' => 'fd15-od', 'eye' => 'OD', 'day' => 'FD15', 'label_en' => 'FD15 Early Nasal Blur', 'label_es' => 'FD15 Borramiento Nasal', 'preset' => 'grade1', 'iop' => 15.6, 'image' => 'images/sans/fundus/fundus-grade1.jpg', 'filename' => 'fundus-grade1.jpg'],
            ['id' => 'fd30-os', 'eye' => 'OS', 'day' => 'FD30', 'label_en' => 'FD30 Choroidal Folds', 'label_es' => 'FD30 Pliegues Coroideos', 'preset' => 'folds', 'iop' => 15.1, 'image' => 'images/sans/fundus/fundus-folds.jpg', 'filename' => 'fundus-folds.jpg'],
            ['id' => 'fd30-od', 'eye' => 'OD', 'day' => 'FD30', 'label_en' => 'FD30 Papilledema Edema', 'label_es' => 'FD30 Edema Papilar', 'preset' => 'edema', 'iop' => 16.4, 'image' => 'images/sans/fundus/fundus-edema.png', 'filename' => 'fundus-edema.png'],
            ['id' => 'fd45-os', 'eye' => 'OS', 'day' => 'FD45', 'label_en' => 'FD45 Venous Engorgement', 'label_es' => 'FD45 Ingurgitación Venosa', 'preset' => 'venous', 'iop' => 17.1, 'image' => 'images/sans/fundus/fundus-venous.jpg', 'filename' => 'fundus-venous.jpg'],
        ];

        $nasaSnapshot = app(\App\Services\NasaDataService::class)->getSansOpenScienceSnapshot(! app()->runningUnitTests());
        $populationDashboard = $this->buildPopulationDashboard($cases, $exposures);

        return view('admin.sans-monitor.index', compact(
            'cases',
            'activeCase',
            'activeTab',
            'selectedEye',
            'simEye',
            'selectedDay',
            'selectedRange',
            'timelineMode',
            'timeAxis',
            'contextOpen',
            'newerVersionsFilter',
            'availableModalities',
            'catalogSearch',
            'caseEvaluation',
            'missionObservations',
            'followupVisits',
            'followupActions',
            'contextRecords',
            'simulatorState',
            'studiesFilterResult',
            'exposures',
            'activeExposure',
            'exposureObservations',
            'initialObsId',
            'compareObsId',
            'exposureComparison',
            'biomarkerPredictor',
            'orbitalTransmission',
            'fundusGallery',
            'nasaSnapshot',
            'populationDashboard'
        ));
    }

    /**
     * Consulta en vivo NASA OSDR + NASA NTRS para el Módulo Científico.
     */
    public function nasaSearch(Request $request)
    {
        $query = trim((string) $request->query('q', 'SANS ocular microgravity'));
        if ($query === '') {
            $query = 'SANS ocular microgravity';
        }

        $nasa = app(\App\Services\NasaDataService::class);

        return response()->json([
            'ok' => true,
            'query' => $query,
            'osdr' => array_slice($nasa->getOsdrStudies($query), 0, 4),
            'ntrs' => array_slice($nasa->getNtrsPublications($query), 0, 4),
        ]);
    }

    /**
     * Predicción de susceptibilidad al SANS en 4 perfiles para futuros candidatos espaciales (Scikit-learn).
     */
    public function predictCandidate(Request $request)
    {
        $payload = [
            'axial_length_mm' => (float) $request->input('axial_length_mm', 23.6),
            'mission_duration_days' => (int) $request->input('mission_duration_days', 180),
            'hdt6_delta_trt_um' => (float) $request->input('hdt6_delta_trt_um', 14.0),
            'baseline_trt_um' => (float) $request->input('baseline_trt_um', 280.0),
            'baseline_iop_mmhg' => (float) $request->input('baseline_iop_mmhg', 15.5),
            'hyperopic_reserve_d' => (float) $request->input('hyperopic_reserve_d', 0.5),
            'onsd_mm' => (float) $request->input('onsd_mm', 5.4),
            'prior_flights' => (int) $request->input('prior_flights', 1),
            'one_carbon_risk' => (string) $request->input('one_carbon_risk', 'moderate'),
            'co2_sensitivity' => (string) $request->input('co2_sensitivity', 'nominal'),
        ];

        $result = app(\App\Services\ScientificEngineService::class)->predictCandidateSusceptibility($payload);

        return response()->json($result);
    }

    /**
     * Recibe fotografías de fondo de ojo (subidas o demo) + PIO del tonómetro desde /astronaut-check y las envía a Tierra.
     */
    public function transmitFundus(Request $request)
    {
        $caseId = strtoupper((string) $request->input('case_id', 'CASE-PROG-02'));
        $iopOd = (float) $request->input('iop_od', 16.0);
        $iopOs = (float) $request->input('iop_os', 15.0);
        $presetOd = (string) $request->input('preset_od', 'edema');
        $presetOs = (string) $request->input('preset_os', 'normal');

        $defaultMap = [
            'normal' => 'images/sans/fundus/fundus-normal.jpg',
            'os_baseline' => 'images/sans/fundus/fundus-os-baseline.jpg',
            'grade1' => 'images/sans/fundus/fundus-grade1.jpg',
            'folds' => 'images/sans/fundus/fundus-folds.jpg',
            'edema' => 'images/sans/fundus/fundus-edema.png',
            'venous' => 'images/sans/fundus/fundus-venous.jpg',
        ];

        $odPath = $defaultMap[$presetOd] ?? $defaultMap['edema'];
        $osPath = $defaultMap[$presetOs] ?? $defaultMap['normal'];

        $uploadDir = public_path('uploads/fundus');
        if (! is_dir($uploadDir)) {
            @mkdir($uploadDir, 0775, true);
        }

        if ($request->hasFile('fundus_od_file') && $request->file('fundus_od_file')->isValid()) {
            $ext = $request->file('fundus_od_file')->getClientOriginalExtension() ?: 'png';
            $fname = strtolower($caseId) . '-od-' . time() . '.' . $ext;
            $request->file('fundus_od_file')->move($uploadDir, $fname);
            $odPath = 'uploads/fundus/' . $fname;
            $presetOd = 'edema';
        }

        if ($request->hasFile('fundus_os_file') && $request->file('fundus_os_file')->isValid()) {
            $ext = $request->file('fundus_os_file')->getClientOriginalExtension() ?: 'png';
            $fname = strtolower($caseId) . '-os-' . time() . '.' . $ext;
            $request->file('fundus_os_file')->move($uploadDir, $fname);
            $osPath = 'uploads/fundus/' . $fname;
            $presetOs = 'folds';
        }

        $payload = [
            'case_id' => $caseId,
            'transmitted_at' => now()->toIso8601String(),
            'iop_od' => $iopOd,
            'iop_os' => $iopOs,
            'od_image' => $odPath,
            'os_image' => $osPath,
            'od_preset' => $presetOd,
            'os_preset' => $presetOs,
            'source' => 'ORBITAL_CREW_DOWNLINK',
        ];

        session()->put("orbital_fundus_transmissions.{$caseId}", $payload);

        return response()->json([
            'ok' => true,
            'message' => 'Transmisión orbital recibida en Tierra.',
            'transmission' => $payload,
        ]);
    }

    /**
     * Escáner del Módulo Científico: procesa la fotografía de fondo de ojo en Python (FastAPI CV) para edema papilar + PIO.
     */
    public function scanFundus(Request $request)
    {
        $imageFilename = $request->input('image_filename');
        $imageDataUrl = $request->input('image_data_url');

        if (! $imageDataUrl && is_string($imageFilename) && str_contains($imageFilename, 'uploads/fundus/')) {
            $safeName = basename($imageFilename);
            $uploadedPath = public_path('uploads/fundus/' . $safeName);
            if (is_file($uploadedPath)) {
                $mime = mime_content_type($uploadedPath) ?: 'image/png';
                $imageDataUrl = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($uploadedPath));
            }
        }

        $service = app(\App\Services\ScientificEngineService::class);
        $result = $service->scanFundusPhoto([
            'case_id' => (string) $request->input('case_id', 'CASE-PROG-02'),
            'eye' => (string) $request->input('eye', 'OD'),
            'iop_mmhg' => (float) $request->input('iop_mmhg', 16.0),
            'demo_preset' => (string) $request->input('demo_preset', 'edema'),
            'image_filename' => $imageFilename ? basename((string) $imageFilename) : null,
            'image_data_url' => $imageDataUrl,
        ]);

        return response()->json($result);
    }

    /**
     * Crear ficha con ID pseudónimo o ficticio sin exigir OCT ni protocolo previo (RF-01, AT-01)
     */
    public function storeCase(Request $request)
    {
        $validated = $request->validate([
            'case_id' => 'required|string|max:64|unique:sans_cases,case_id',
            'mission_id' => 'nullable|string|max:64',
            'source_type' => 'required|in:FICTICIO_DEMO,ANALOGO_TERRESTRE,ORBITAL_AUTORIZADO',
            'mission_phase' => 'required|in:PRE_VUELO,EN_VUELO,POSVUELO,PRE_EXPOSICION,EN_EXPOSICION,POSEXPOSICION',
            'planned_duration_days' => 'nullable|integer|min:0',
            'planned_duration_state' => 'required|in:VALOR,DESCONOCIDO,NO_APLICA',
            'previous_flights' => 'nullable|integer|min:0',
            'previous_flights_state' => 'required|in:VALOR,DESCONOCIDO,NO_APLICA',
            'reason_for_review' => 'required|string|max:255',
            'applied_protocol_version_id' => 'nullable|string|max:64',
            'applied_protocol_state' => 'required|in:CONOCIDO,DESCONOCIDO,NO_APLICA',
        ]);

        DB::table('sans_cases')->insert([
            'case_id' => strtoupper(trim($validated['case_id'])),
            'mission_id' => $validated['mission_id'] ?? null,
            'source_type' => $validated['source_type'],
            'mission_phase' => $validated['mission_phase'],
            'planned_duration_days' => $validated['planned_duration_state'] === 'VALOR' ? $validated['planned_duration_days'] : null,
            'planned_duration_state' => $validated['planned_duration_state'],
            'previous_flights' => $validated['previous_flights_state'] === 'VALOR' ? $validated['previous_flights'] : null,
            'previous_flights_state' => $validated['previous_flights_state'],
            'reason_for_review' => $validated['reason_for_review'],
            'baseline_status' => 'SIN_LINEA_BASE',
            'applied_protocol_version_id' => $validated['applied_protocol_state'] === 'CONOCIDO' ? $validated['applied_protocol_version_id'] : null,
            'applied_protocol_state' => $validated['applied_protocol_state'],
            'scenario_archetype' => 'SIN_ESCANEO',
            'eye_history' => json_encode(['OD' => 'Ficha abierta sin OCT basal registrada.', 'OS' => 'Ficha abierta sin OCT basal registrada.']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sans_followup_actions')->insert([
            'case_id' => strtoupper(trim($validated['case_id'])),
            'milestone_day' => null,
            'suggested_action' => 'Completar línea base',
            'reason' => 'Ficha creada sin OCT basal; capacidad de evaluar cambio ocular marcada como insuficiente.',
            'review_status' => 'SUGERIDA_DEMO',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.sans-monitor.index', ['case_id' => strtoupper(trim($validated['case_id'])), 'tab' => 'ficha'])
            ->with('status', 'Ficha creada correctamente. Capacidad de evaluar el ojo marcada como insuficiente (Faltan datos); sin dictamen de aptitud.');
    }

    /**
     * Importar lote de registros y reportar duplicados u OCT no comparables (RF-03, AT-09)
     */
    public function importObservations(Request $request)
    {
        $validated = $request->validate([
            'case_id' => 'required|string|exists:sans_cases,case_id',
            'rows' => 'required|array|min:1',
            'rows.*.eye' => 'required|in:OD,OS',
            'rows.*.mission_day' => 'required|integer',
            'rows.*.metric_code' => 'required|string',
            'rows.*.value' => 'required|numeric',
            'rows.*.modality' => 'required|string',
            'rows.*.region' => 'required|string',
        ]);

        $report = $this->engine->importObservationsRows($validated['case_id'], $validated['rows']);

        if ($request->wantsJson()) {
            return response()->json($report);
        }

        return redirect()
            ->route('admin.sans-monitor.index', ['case_id' => $validated['case_id'], 'tab' => 'ficha'])
            ->with('import_report', $report);
    }

    /**
     * Adoptar un protocolo con decisión auditada del revisor sin sobrescribir el protocolo previamente aplicado (RF-08, AT-02)
     */
    public function adoptProtocol(Request $request)
    {
        $validated = $request->validate([
            'case_id' => 'required|string|exists:sans_cases,case_id',
            'version_id' => 'required|string|exists:sans_protocol_versions,version_id',
            'decision_reason' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        DB::table('sans_case_protocols')->insert([
            'case_id' => $validated['case_id'],
            'version_id' => $validated['version_id'],
            'relation' => 'ADOPTADO_REVISOR',
            'reviewer_id' => $user?->id,
            'reviewer_name' => $user?->name ?? 'Revisor Autorizado',
            'decision_reason' => $validated['decision_reason'],
            'decision_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.sans-monitor.index', ['case_id' => $validated['case_id'], 'tab' => 'ficha'])
            ->with('status', "Protocolo {$validated['version_id']} registrado para seguimiento futuro con auditoría de revisor (el protocolo aplicado previamente se conserva intacto).");
    }

    /**
     * Registrar el dictamen humano emitido exclusivamente por el especialista médico autorizado (RF-11)
     */
    public function storeClinicianDecision(Request $request)
    {
        $user = Auth::user();
        if (! $user || (! $user->hasRole('flight-surgeon') && ! $user->hasRole('super-admin'))) {
            abort(403, 'Solo el especialista médico autorizado puede registrar un dictamen humano.');
        }

        $validated = $request->validate([
            'case_id' => 'required|string|exists:sans_cases,case_id',
            'evaluation_scope' => 'required|string|max:180',
            'protocol_version_id' => 'nullable|string|max:64',
            'documented_followup' => 'required|string',
            'fitness_statement' => 'required|string|max:180',
        ]);

        DB::table('sans_clinician_decisions')->insert([
            'case_id' => $validated['case_id'],
            'clinician_user_id' => $user->id,
            'clinician_name' => $user->name,
            'clinician_role' => 'Especialista médico autorizado',
            'evaluation_scope' => $validated['evaluation_scope'],
            'protocol_version_id' => $validated['protocol_version_id'] ?? null,
            'documented_followup' => $validated['documented_followup'],
            'fitness_statement' => $validated['fitness_statement'],
            'decided_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.sans-monitor.index', ['case_id' => $validated['case_id'], 'tab' => 'seguimiento'])
            ->with('status', 'Dictamen clínico del especialista autorizado registrado y auditado fuera del motor de reglas.');
    }

    /**
     * Construye el Dashboard Integral de Poblaciones (Astronautas, Terrestres 0° y Terrestres -6° HDT)
     * para evaluar el valor predictivo comparativo de los 6 biomarcadores.
     */
    private function buildPopulationDashboard($cases, $exposures): array
    {
        $obsByCase = DB::table('sans_metric_observations')
            ->where('status', 'MEDIDO')
            ->get()
            ->groupBy('case_id');

        $rows = [];
        $astroDeltas = [];
        $astroIops = [];
        $hdtDeltas = [];
        $hdtIops = [];

        foreach ($cases as $c) {
            $caseObs = $obsByCase->get($c->case_id, collect());
            $octObs = $caseObs->where('modality', 'OCT');
            $iopObs = $caseObs->where('modality', 'PIO');

            $baselineOct = $octObs->where('mission_day', 0)->first();
            $peakOct = $octObs->sortByDesc('value')->first();
            $deltaTrt = ($baselineOct && $peakOct && $baselineOct->value !== null && $peakOct->value !== null)
                ? round((float) $peakOct->value - (float) $baselineOct->value, 1)
                : null;
            $peakIop = $iopObs->max('value');

            $isHdt = $c->source_type === 'ANALOGO_TERRESTRE';
            $cohortKey = $isHdt ? 'TERRESTRE_HDT6' : 'ASTRONAUTA';

            if ($isHdt) {
                if ($deltaTrt !== null) {
                    $hdtDeltas[] = $deltaTrt;
                }
                if ($peakIop !== null) {
                    $hdtIops[] = (float) $peakIop;
                }
            } else {
                if ($deltaTrt !== null) {
                    $astroDeltas[] = $deltaTrt;
                }
                if ($peakIop !== null) {
                    $astroIops[] = (float) $peakIop;
                }
            }

            $rows[] = [
                'id' => $c->case_id,
                'cohort_key' => $cohortKey,
                'cohort_en' => $isHdt ? 'Terrestrial (-6° HDT)' : 'Astronaut (Orbital)',
                'cohort_es' => $isHdt ? 'Terrestre (-6° HDT)' : 'Astronauta (Orbital)',
                'archetype' => $c->scenario_archetype,
                'baseline_oct' => $baselineOct?->value !== null ? round((float) $baselineOct->value, 0) : null,
                'peak_oct' => $peakOct?->value !== null ? round((float) $peakOct->value, 0) : null,
                'delta_trt' => $deltaTrt,
                'peak_iop' => $peakIop !== null ? round((float) $peakIop, 1) : null,
                'axial_mm' => $c->scenario_archetype === 'EMPEORA' ? 23.4 : ($isHdt ? 23.6 : 24.1),
                'frisen' => $c->scenario_archetype === 'EMPEORA' ? 2 : ($isHdt ? 1 : 0),
                'hyperopic_d' => $c->scenario_archetype === 'EMPEORA' ? 0.75 : ($isHdt ? 0.25 : 0.0),
            ];
        }

        // Añadir cohorte normativa terrestre (controles 0° en Tierra) para contraste completo de 3 poblaciones
        $terrestrialControls = [
            ['id' => 'CTRL-GND-01', 'cohort_key' => 'TERRESTRE_CTRL', 'cohort_en' => 'Terrestrial Control (0°)', 'cohort_es' => 'Terrestre Control (0°)', 'archetype' => 'ESTABLE', 'baseline_oct' => 278, 'peak_oct' => 279, 'delta_trt' => 1.0, 'peak_iop' => 14.6, 'axial_mm' => 23.9, 'frisen' => 0, 'hyperopic_d' => 0.0],
            ['id' => 'CTRL-GND-02', 'cohort_key' => 'TERRESTRE_CTRL', 'cohort_en' => 'Terrestrial Control (0°)', 'cohort_es' => 'Terrestre Control (0°)', 'archetype' => 'ESTABLE', 'baseline_oct' => 281, 'peak_oct' => 282, 'delta_trt' => 1.0, 'peak_iop' => 15.2, 'axial_mm' => 24.0, 'frisen' => 0, 'hyperopic_d' => 0.0],
            ['id' => 'CTRL-GND-03', 'cohort_key' => 'TERRESTRE_CTRL', 'cohort_en' => 'Terrestrial Control (0°)', 'cohort_es' => 'Terrestre Control (0°)', 'archetype' => 'ESTABLE', 'baseline_oct' => 276, 'peak_oct' => 278, 'delta_trt' => 2.0, 'peak_iop' => 14.9, 'axial_mm' => 23.8, 'frisen' => 0, 'hyperopic_d' => 0.0],
        ];
        $allRows = array_merge($rows, $terrestrialControls);

        // Incluir sesión -6° HDT si existe en sans_exposure_sessions
        $hdtMeanDelta = count($hdtDeltas) > 0 ? round(array_sum($hdtDeltas) / count($hdtDeltas), 1) : 24.0;
        $hdtMeanIop = count($hdtIops) > 0 ? round(array_sum($hdtIops) / count($hdtIops), 1) : 16.8;
        $astroMeanDelta = count($astroDeltas) > 0 ? round(array_sum($astroDeltas) / count($astroDeltas), 1) : 14.0;
        $astroMaxDelta = count($astroDeltas) > 0 ? max($astroDeltas) : 26.0;
        $astroMeanIop = count($astroIops) > 0 ? round(array_sum($astroIops) / count($astroIops), 1) : 16.4;

        return [
            'cohorts' => [
                [
                    'key' => 'ASTRONAUTA',
                    'label_en' => 'Orbital Astronauts',
                    'label_es' => 'Astronautas Orbitales',
                    'count' => collect($allRows)->where('cohort_key', 'ASTRONAUTA')->count(),
                    'mean_delta_trt' => $astroMeanDelta,
                    'peak_delta_trt' => $astroMaxDelta,
                    'mean_iop' => $astroMeanIop,
                    'mean_axial' => 23.7,
                    'mean_hyperopic_d' => 0.35,
                    'accent' => '#2563eb',
                ],
                [
                    'key' => 'TERRESTRE_CTRL',
                    'label_en' => 'Terrestrial Controls (0°)',
                    'label_es' => 'Personas Terrestres (0°)',
                    'count' => count($terrestrialControls),
                    'mean_delta_trt' => 1.3,
                    'peak_delta_trt' => 2.0,
                    'mean_iop' => 14.9,
                    'mean_axial' => 23.9,
                    'mean_hyperopic_d' => 0.0,
                    'accent' => '#16a34a',
                ],
                [
                    'key' => 'TERRESTRE_HDT6',
                    'label_en' => 'Terrestrial -6° HDT Analog',
                    'label_es' => 'Terrestres a -6° HDT',
                    'count' => max(1, collect($allRows)->where('cohort_key', 'TERRESTRE_HDT6')->count()) + ($exposures->count() > 0 ? 1 : 0),
                    'mean_delta_trt' => $hdtMeanDelta,
                    'peak_delta_trt' => max(24.0, $hdtMeanDelta),
                    'mean_iop' => $hdtMeanIop,
                    'mean_axial' => 23.6,
                    'mean_hyperopic_d' => 0.25,
                    'accent' => '#d97706',
                ],
            ],
            'biomarker_predictive_matrix' => [
                [
                    'name_en' => '1. OCT Total Retinal Thickness (ΔTRT)',
                    'name_es' => '1. Espesor Retiniano OCT (ΔTRT)',
                    'astro_val' => "+{$astroMaxDelta} µm (SANS+)",
                    'ctrl_val' => '+1.3 µm',
                    'hdt6_val' => "+{$hdtMeanDelta} µm",
                    'power' => 'HIGH',
                    'power_en' => 'HIGH PREDICTIVE VALUE',
                    'power_es' => 'ALTO VALOR PREDICTIVO',
                    'note_en' => 'Earliest structural separator: +24 µm in -6° HDT mirrors +26 µm orbital SANS+ progression vs +1.3 µm in 0° terrestrial controls.',
                    'note_es' => 'Principal marcador temprano: +24 µm en -6° HDT replica la progresión orbital (+26 µm) frente a +1.3 µm en controles terrestres.',
                ],
                [
                    'name_en' => '2. -6° Head-Down Tilt Analog Response',
                    'name_es' => '2. Respuesta a Análogo -6° HDT',
                    'astro_val' => '+26 µm orbital',
                    'ctrl_val' => '0 µm (0°)',
                    'hdt6_val' => "+{$hdtMeanDelta} µm (-6°)",
                    'power' => 'HIGH',
                    'power_en' => 'HIGH PREDICTIVE VALUE',
                    'power_es' => 'ALTO VALOR PREDICTIVO',
                    'note_en' => 'Reproduces cephalic fluid shift on Earth, identifying susceptible candidates pre-flight.',
                    'note_es' => 'Reproduce el desplazamiento cefálico de fluidos en Tierra para tamizaje previo al lanzamiento.',
                ],
                [
                    'name_en' => '3. Short Axial Length (mm)',
                    'name_es' => '3. Longitud Axial Corta (mm)',
                    'astro_val' => '23.4 mm (SANS+)',
                    'ctrl_val' => '23.9 mm',
                    'hdt6_val' => '23.6 mm',
                    'power' => 'MODERATE',
                    'power_en' => 'MODERATE-HIGH',
                    'power_es' => 'MODERADO-ALTO',
                    'note_en' => 'Shorter baseline globes exhibit stronger hyperopic & posterior flattening vulnerability.',
                    'note_es' => 'Globos oculares más cortos muestran mayor vulnerabilidad a hipermetropización y aplanamiento posterior.',
                ],
                [
                    'name_en' => '4. Hyperopic Shift (Diopters)',
                    'name_es' => '4. Cambio Hipermetrópico (D)',
                    'astro_val' => '+0.75 D',
                    'ctrl_val' => '0.00 D',
                    'hdt6_val' => '+0.25 D',
                    'power' => 'MODERATE',
                    'power_en' => 'MODERATE',
                    'power_es' => 'MODERADO',
                    'note_en' => 'Tracks axial shortening secondary to choroidal thickening and posterior flattening.',
                    'note_es' => 'Refleja acortamiento axial secundario al engrosamiento coroideo y aplanamiento posterior.',
                ],
                [
                    'name_en' => '5. Optic Disc Edema (Frisén Grade)',
                    'name_es' => '5. Edema Papilar (Grado Frisén)',
                    'astro_val' => 'Grade 2 (SANS+)',
                    'ctrl_val' => 'Grade 0',
                    'hdt6_val' => 'Grade 1',
                    'power' => 'HIGH',
                    'power_en' => 'CONFIRMATORY HIGH',
                    'power_es' => 'ALTO (CONFIRMATORIO)',
                    'note_en' => 'Confirms overt neuro-ocular remodeling when paired with peripapillary halo & choroidal folds.',
                    'note_es' => 'Confirma remodelado neuro-ocular manifiesto al asociarse con halo peripapilar y pliegues coroideos.',
                ],
                [
                    'name_en' => '6. Intraocular Pressure (IOP)',
                    'name_es' => '6. Presión Intraocular (PIO)',
                    'astro_val' => "{$astroMeanIop} mmHg",
                    'ctrl_val' => '14.9 mmHg',
                    'hdt6_val' => "{$hdtMeanIop} mmHg",
                    'power' => 'LOW',
                    'power_en' => 'LOW (NOT PREDICTIVE ALONE)',
                    'power_es' => 'BAJO (NO PREDICTIVO SOLO)',
                    'note_en' => 'Overlaps across all 3 cohorts (14.9–18.5 mmHg). Normal IOP NEVER rules out SANS without OCT.',
                    'note_es' => 'Se solapa en las 3 poblaciones (14.9–18.5 mmHg). Una PIO normal JAMÁS descarta SANS sin OCT.',
                ],
            ],
            'rows' => $allRows,
        ];
    }
}
