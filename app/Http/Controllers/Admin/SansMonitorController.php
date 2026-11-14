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

        $activeTab = $request->query('tab', 'biomarcadores'); // biomarcadores, seguimiento, escenarios (soporta ficha, simulador, estudios, inclinacion)
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
            'biomarkerPredictor'
        ));
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
}
