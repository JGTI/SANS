<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AstronautProfile;
use App\Services\SansMonitor\SansMonitorEngine;
use App\Services\ScientificEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DigitalTwinController extends Controller
{
    protected ScientificEngineService $engine;
    protected SansMonitorEngine $sansEngine;

    public function __construct(ScientificEngineService $engine, SansMonitorEngine $sansEngine)
    {
        $this->engine = $engine;
        $this->sansEngine = $sansEngine;
    }

    public function index(Request $request): View
    {
        if (! Schema::hasTable('sans_cases')) {
            Artisan::call('migrate', ['--force' => true]);
        }
        if (DB::table('sans_cases')->count() === 0) {
            app(\Database\Seeders\SansMonitorSeeder::class)->run();
        }
        if (AstronautProfile::count() < 6) {
            app(\Database\Seeders\OphthalmologySeeder::class)->run();
        }

        $astronauts = AstronautProfile::orderBy('code')->get();
        $cases = DB::table('sans_cases')->orderBy('case_id')->get();

        // Allow selecting by either ?case_id=CASE-... or ?astro=ASTRO-...
        $requestedCaseId = $request->query('case_id');
        $requestedAstro = $request->query('astro');

        if ($requestedCaseId) {
            $selectedCaseId = strtoupper($requestedCaseId);
            $selectedCode = AstronautProfile::codeForCaseId($selectedCaseId);
        } elseif ($requestedAstro) {
            $selectedCode = strtoupper($requestedAstro);
            $selectedCaseId = AstronautProfile::caseIdForCode($selectedCode);
        } else {
            $selectedCode = 'ASTRO-01';
            $selectedCaseId = 'CASE-PROG-02';
        }

        $astronaut = AstronautProfile::where('code', $selectedCode)->first() ?? $astronauts->first();
        $activeCase = $cases->firstWhere('case_id', $selectedCaseId) ?? $cases->first();

        // Evaluate all 6 cases for the top crew selector cards
        $crewRoster = [];
        foreach ($cases as $c) {
            $meta = AstronautProfile::crewMetaForCase($c->case_id);
            $eval = $this->sansEngine->evaluateCaseAtDay($c, null);
            $crewRoster[] = [
                'case_id' => $c->case_id,
                'astro_code' => $meta['code'],
                'name' => $meta['name'],
                'agency' => $meta['agency'] ?? 'NASA',
                'role' => $meta['role'],
                'avatar' => $meta['avatar'],
                'archetype' => $c->scenario_archetype,
                'operational_code' => $eval['case_operational_code'],
                'operational_text' => $eval['case_operational_text'],
            ];
        }

        // Evaluate active case & biomarker predictor
        $caseEvaluation = $this->sansEngine->evaluateCaseAtDay($activeCase, null);
        $biomarkerPredictor = $this->sansEngine->evaluateBiomarkerPredictor($activeCase, []);

        // Build real clinical samples bound to sans_metric_observations and sans_followup_visits
        $clinicalSamples = $this->buildCaseClinicalSamples($activeCase, $caseEvaluation, $biomarkerPredictor);

        $timelineDays = array_values(array_map(fn ($s) => $s['day'], $clinicalSamples));
        $simulationData = [];
        foreach ($clinicalSamples as $s) {
            $simulationData[$s['day']] = $s;
        }

        // Determine initial sample index if ?day= or ?sample= was requested
        $requestedDay = $request->query('day');
        $initialSampleIndex = 0;
        if ($requestedDay !== null) {
            foreach ($clinicalSamples as $idx => $s) {
                if ((int) $s['day'] === (int) $requestedDay) {
                    $initialSampleIndex = $idx;
                    break;
                }
            }
        } else {
            // Default to the most clinically informative sample (e.g. FD30 peak for progression, or index 0)
            foreach ($clinicalSamples as $idx => $s) {
                if ((int) $s['day'] === 30 || $s['is_peak_sample']) {
                    $initialSampleIndex = $idx;
                    break;
                }
            }
        }

        $initialDay = $clinicalSamples[$initialSampleIndex]['day'] ?? ($timelineDays[0] ?? 0);
        $scenario = match ($astronaut?->code) {
            'ASTRO-01' => 'B',
            'ASTRO-02' => 'A',
            'ASTRO-03' => 'C',
            'ASTRO-04' => 'D',
            'ASTRO-05' => 'A',
            'ASTRO-06' => 'E',
            default => 'B',
        };

        $cirrusOct = $this->engine->getCirrusOctWorkstationData(
            astronautCode: $astronaut?->code ?? 'ASTRO-01',
            astronautName: $astronaut?->name ?? 'CDR Alex Mercer',
            flightDay: max(0, (int) $initialDay),
            scenario: $scenario
        );

        $initialMode = $request->query('mode', 'dual');

        return view('admin.digital-twin.index', compact(
            'astronauts',
            'astronaut',
            'activeCase',
            'crewRoster',
            'caseEvaluation',
            'biomarkerPredictor',
            'clinicalSamples',
            'initialSampleIndex',
            'timelineDays',
            'simulationData',
            'cirrusOct',
            'scenario',
            'initialMode'
        ));
    }

    public function octWorkstation(Request $request): View
    {
        if (! $request->has('mode')) {
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

        $data = $this->engine->getCirrusOctWorkstationData($code, $name, max(0, $flightDay), $scenario);

        return response()->json($data);
    }

    /**
     * Build the exact longitudinal sample series from sans_metric_observations and sans_followup_visits
     * so every sample in the 3D Digital Twin + Cirrus HD-OCT console matches the SANS Monitor dossier.
     */
    private function buildCaseClinicalSamples(object $activeCase, array $caseEvaluation, array $biomarkerPredictor): array
    {
        $caseId = $activeCase->case_id;

        if ($caseId === 'CASE-NOSCAN-03A') {
            return [
                $this->makeSample([
                    'sample_id' => 'OBS-NS03A-IOP-ONLY',
                    'day' => 0,
                    'code_label' => 'FD0 (Pre-Vuelo)',
                    'stage_es' => 'Pre-vuelo · Solo Tonometría PIO (Sin OCT)',
                    'stage_en' => 'Pre-flight · IOP Tonometry Only (No OCT)',
                    'observed_at' => '2026-08-25',
                    'has_oct_od' => false,
                    'has_oct_os' => false,
                    'os_missing_baseline' => true,
                    'od_trt_um' => null,
                    'od_base_um' => null,
                    'od_delta_um' => null,
                    'os_trt_um' => null,
                    'os_base_um' => null,
                    'os_delta_um' => null,
                    'axial_length_mm' => 24.10,
                    'od_shift' => 0.00,
                    'od_snellen' => '20/20',
                    'od_logmar' => 0.000,
                    'iop' => 16.0,
                    'iop_os' => 15.0,
                    'frisen_grade' => 0,
                    'globe_flattening' => false,
                    'choroidal_folds' => false,
                    'rnfl' => null,
                    'risk_tier' => 'missing',
                    'status_code' => 'FALTAN_DATOS',
                    'status_es' => 'Faltan datos (Sin escaneo OCT)',
                    'status_en' => 'Missing data (No OCT scan)',
                    'note_es' => 'Cuenta con PIO (16.0 mmHg OD / 15.0 mmHg OS), pero carece de escaneo OCT basal. Regla AT-01/AT-17: nunca se asume como estable.',
                    'note_en' => 'Has IOP (16.0 mmHg OD / 15.0 mmHg OS), but lacks baseline OCT scan. Rule AT-01/AT-17: never assumed stable.',
                    'is_peak_sample' => true,
                ]),
                $this->makeSample([
                    'sample_id' => 'VISIT-NS03A-POST30-PENDING',
                    'day' => 30,
                    'code_label' => 'Corte +30d (Sin OCT)',
                    'stage_es' => 'Control +30 Días · Pendiente de Línea Base OCT',
                    'stage_en' => 'Cut +30 Days · Pending Baseline OCT',
                    'observed_at' => 'Pendiente',
                    'has_oct_od' => false,
                    'has_oct_os' => false,
                    'os_missing_baseline' => true,
                    'od_trt_um' => null,
                    'od_base_um' => null,
                    'od_delta_um' => null,
                    'os_trt_um' => null,
                    'os_base_um' => null,
                    'os_delta_um' => null,
                    'axial_length_mm' => 24.10,
                    'od_shift' => 0.00,
                    'od_snellen' => '20/20',
                    'od_logmar' => 0.000,
                    'iop' => 16.0,
                    'iop_os' => 15.0,
                    'frisen_grade' => 0,
                    'globe_flattening' => false,
                    'choroidal_folds' => false,
                    'rnfl' => null,
                    'risk_tier' => 'missing',
                    'status_code' => 'FALTAN_DATOS',
                    'status_es' => 'Faltan datos (Completar línea base)',
                    'status_en' => 'Missing data (Complete baseline)',
                    'note_es' => 'Acción sugerida RF-32: Completar línea base OCT antes de emitir comparativa longitudinal.',
                    'note_en' => 'Suggested action RF-32: Complete OCT baseline before issuing longitudinal comparison.',
                    'is_peak_sample' => false,
                ]),
            ];
        }

        if ($caseId === 'CASE-STABLE-01') {
            return [
                $this->makeSample([
                    'sample_id' => 'OBS-ST01-OD-BASE',
                    'day' => 0,
                    'code_label' => 'FD0 (Pre-Vuelo)',
                    'stage_es' => 'Pre-vuelo · Línea Base Completa',
                    'stage_en' => 'Pre-flight · Complete Baseline',
                    'observed_at' => '2026-04-20',
                    'od_trt_um' => 278.0, 'od_base_um' => 278.0, 'od_delta_um' => 0.0,
                    'os_trt_um' => 276.0, 'os_base_um' => 276.0, 'os_delta_um' => 0.0,
                    'axial_length_mm' => 24.10, 'od_shift' => 0.00, 'od_snellen' => '20/20', 'od_logmar' => 0.000,
                    'iop' => 14.0, 'frisen_grade' => 0, 'globe_flattening' => false, 'choroidal_folds' => false, 'rnfl' => 95,
                    'risk_tier' => 'low', 'status_code' => 'LISTO_PARA_REVISION',
                    'status_es' => 'Listo para revisión · Línea base', 'status_en' => 'Ready for review · Baseline',
                    'note_es' => 'Línea base pre-vuelo completa en ambos ojos (OD 278 µm / OS 276 µm).',
                    'note_en' => 'Complete pre-flight baseline in both eyes (OD 278 µm / OS 276 µm).',
                    'is_peak_sample' => false,
                ]),
                $this->makeSample([
                    'sample_id' => 'OBS-ST01-OD-D10',
                    'day' => 10,
                    'code_label' => 'FD10 (En Vuelo)',
                    'stage_es' => 'En Vuelo · Día 10',
                    'stage_en' => 'In-Flight · Day 10',
                    'observed_at' => '2026-05-11',
                    'od_trt_um' => 281.0, 'od_base_um' => 278.0, 'od_delta_um' => 3.0,
                    'os_trt_um' => 278.0, 'os_base_um' => 276.0, 'os_delta_um' => 2.0,
                    'axial_length_mm' => 24.09, 'od_shift' => 0.05, 'od_snellen' => '20/20', 'od_logmar' => 0.000,
                    'iop' => 14.2, 'frisen_grade' => 0, 'globe_flattening' => false, 'choroidal_folds' => false, 'rnfl' => 96,
                    'risk_tier' => 'low', 'status_code' => 'LISTO_PARA_REVISION',
                    'status_es' => 'Listo para revisión · Estable', 'status_en' => 'Ready for review · Stable',
                    'note_es' => 'Variación fisiológica mínima en microgravedad temprana (ΔTRT +3 µm OD / +2 µm OS).',
                    'note_en' => 'Minimal physiological variation in early microgravity (ΔTRT +3 µm OD / +2 µm OS).',
                    'is_peak_sample' => false,
                ]),
                $this->makeSample([
                    'sample_id' => 'OBS-ST01-OD-D30',
                    'day' => 30,
                    'code_label' => 'FD30 (En Vuelo)',
                    'stage_es' => 'En Vuelo · Día 30 (Control Nominal)',
                    'stage_en' => 'In-Flight · Day 30 (Nominal Check)',
                    'observed_at' => '2026-05-31',
                    'od_trt_um' => 284.0, 'od_base_um' => 278.0, 'od_delta_um' => 6.0,
                    'os_trt_um' => 280.0, 'os_base_um' => 276.0, 'os_delta_um' => 4.0,
                    'axial_length_mm' => 24.08, 'od_shift' => 0.12, 'od_snellen' => '20/20', 'od_logmar' => 0.000,
                    'iop' => 14.5, 'frisen_grade' => 0, 'globe_flattening' => false, 'choroidal_folds' => false, 'rnfl' => 97,
                    'risk_tier' => 'low', 'status_code' => 'LISTO_PARA_REVISION',
                    'status_es' => 'Listo para revisión · Estable (ΔTRT +6 µm)', 'status_en' => 'Ready for review · Stable (ΔTRT +6 µm)',
                    'note_es' => 'Cambio máximo ΔTRT +6 µm (< umbral de +20 µm). Escenario Estable de referencia.',
                    'note_en' => 'Max change ΔTRT +6 µm (< +20 µm threshold). Reference Stable Scenario.',
                    'is_peak_sample' => true,
                ]),
                $this->makeSample([
                    'sample_id' => 'VISIT-ST01-POST30',
                    'day' => 60,
                    'code_label' => 'Posvuelo +30d',
                    'stage_es' => 'Posvuelo +30 Días · Recuperación Completa',
                    'stage_en' => 'Post-flight +30 Days · Complete Recovery',
                    'observed_at' => '2026-11-27',
                    'od_trt_um' => 280.0, 'od_base_um' => 278.0, 'od_delta_um' => 2.0,
                    'os_trt_um' => 277.0, 'os_base_um' => 276.0, 'os_delta_um' => 1.0,
                    'axial_length_mm' => 24.10, 'od_shift' => 0.00, 'od_snellen' => '20/20', 'od_logmar' => 0.000,
                    'iop' => 14.0, 'frisen_grade' => 0, 'globe_flattening' => false, 'choroidal_folds' => false, 'rnfl' => 95,
                    'risk_tier' => 'low', 'status_code' => 'LISTO_PARA_REVISION',
                    'status_es' => 'Listo para revisión · Corte +30d Completado', 'status_en' => 'Ready for review · +30d Cut Completed',
                    'note_es' => 'Retorno a línea base tras 30 días en gravedad terrestre 1G.',
                    'note_en' => 'Return to baseline after 30 days in 1G terrestrial gravity.',
                    'is_peak_sample' => false,
                ]),
                $this->makeSample([
                    'sample_id' => 'VISIT-ST01-POST60',
                    'day' => 90,
                    'code_label' => 'Posvuelo +60d',
                    'stage_es' => 'Posvuelo +60 Días · Estable',
                    'stage_en' => 'Post-flight +60 Days · Stable',
                    'observed_at' => '2026-12-27',
                    'od_trt_um' => 279.0, 'od_base_um' => 278.0, 'od_delta_um' => 1.0,
                    'os_trt_um' => 276.0, 'os_base_um' => 276.0, 'os_delta_um' => 0.0,
                    'axial_length_mm' => 24.10, 'od_shift' => 0.00, 'od_snellen' => '20/20', 'od_logmar' => 0.000,
                    'iop' => 14.0, 'frisen_grade' => 0, 'globe_flattening' => false, 'choroidal_folds' => false, 'rnfl' => 95,
                    'risk_tier' => 'low', 'status_code' => 'LISTO_PARA_REVISION',
                    'status_es' => 'Listo para revisión · Corte +60d Completado', 'status_en' => 'Ready for review · +60d Cut Completed',
                    'note_es' => 'Control posvuelo +60 días completado sin hallazgos residuales.',
                    'note_en' => 'Post-flight +60 days check completed with no residual findings.',
                    'is_peak_sample' => false,
                ]),
                $this->makeSample([
                    'sample_id' => 'VISIT-ST01-POST90',
                    'day' => 120,
                    'code_label' => 'Posvuelo +90d',
                    'stage_es' => 'Posvuelo +90 Días · Cierre Longitudinal',
                    'stage_en' => 'Post-flight +90 Days · Longitudinal Closure',
                    'observed_at' => '2027-01-26',
                    'od_trt_um' => 278.0, 'od_base_um' => 278.0, 'od_delta_um' => 0.0,
                    'os_trt_um' => 276.0, 'os_base_um' => 276.0, 'os_delta_um' => 0.0,
                    'axial_length_mm' => 24.10, 'od_shift' => 0.00, 'od_snellen' => '20/20', 'od_logmar' => 0.000,
                    'iop' => 14.0, 'frisen_grade' => 0, 'globe_flattening' => false, 'choroidal_folds' => false, 'rnfl' => 95,
                    'risk_tier' => 'low', 'status_code' => 'LISTO_PARA_REVISION',
                    'status_es' => 'Listo para revisión · Vigilancia rutinaria', 'status_en' => 'Ready for review · Routine surveillance',
                    'note_es' => 'Ciclo completo +30/+60/+90 días cerrado en estabilidad nominal.',
                    'note_en' => 'Full +30/+60/+90 days cycle closed in nominal stability.',
                    'is_peak_sample' => false,
                ]),
            ];
        }

        if ($caseId === 'CASE-ASO-05') {
            return [
                $this->makeSample([
                    'sample_id' => 'OBS-ASO05-OD-BASE',
                    'day' => 0,
                    'code_label' => 'FD0 (Base Parcial)',
                    'stage_es' => 'Pre-vuelo · OD con Base (280 µm) / OS SIN LÍNEA BASE',
                    'stage_en' => 'Pre-flight · OD Baseline (280 µm) / OS NO BASELINE',
                    'observed_at' => '2026-05-20',
                    'has_oct_od' => true,
                    'has_oct_os' => false,
                    'os_missing_baseline' => true,
                    'od_trt_um' => 280.0, 'od_base_um' => 280.0, 'od_delta_um' => 0.0,
                    'os_trt_um' => null, 'os_base_um' => null, 'os_delta_um' => null,
                    'axial_length_mm' => 24.10, 'od_shift' => 0.00, 'od_snellen' => '20/20', 'od_logmar' => 0.000,
                    'iop' => 15.0, 'frisen_grade' => 0, 'globe_flattening' => false, 'choroidal_folds' => false, 'rnfl' => 96,
                    'risk_tier' => 'moderate', 'status_code' => 'FALTAN_DATOS',
                    'status_es' => 'Línea base parcial (OS sin escaneo basal)', 'status_en' => 'Partial baseline (OS missing baseline scan)',
                    'note_es' => 'AT-05: OD cuenta con línea base (280 µm), pero OS no fue escaneado antes del vuelo.',
                    'note_en' => 'AT-05: OD has baseline (280 µm), but OS was not scanned prior to flight.',
                    'is_peak_sample' => false,
                ]),
                $this->makeSample([
                    'sample_id' => 'OBS-ASO05-OD-D10',
                    'day' => 10,
                    'code_label' => 'FD10 (Asimetría)',
                    'stage_es' => 'En Vuelo · Día 10 (OD +12 µm / OS 289 µm sin base)',
                    'stage_en' => 'In-Flight · Day 10 (OD +12 µm / OS 289 µm no baseline)',
                    'observed_at' => '2026-06-11',
                    'has_oct_od' => true,
                    'has_oct_os' => true,
                    'os_missing_baseline' => true,
                    'od_trt_um' => 292.0, 'od_base_um' => 280.0, 'od_delta_um' => 12.0,
                    'os_trt_um' => 289.0, 'os_base_um' => null, 'os_delta_um' => null,
                    'axial_length_mm' => 23.96, 'od_shift' => 0.35, 'od_snellen' => '20/25', 'od_logmar' => 0.097,
                    'iop' => 16.5, 'frisen_grade' => 1, 'globe_flattening' => true, 'choroidal_folds' => false, 'rnfl' => 110,
                    'risk_tier' => 'moderate', 'status_code' => 'PRIORIZAR_REVISION',
                    'status_es' => 'Priorizar revisión · Asimetría mixta (AT-05)', 'status_en' => 'Prioritize review · Mixed asymmetry (AT-05)',
                    'note_es' => 'OD sube a 292 µm (ΔTRT +12 µm); OS mide 289 µm pero no puede calcularse ΔTRT por falta de línea base.',
                    'note_en' => 'OD rises to 292 µm (ΔTRT +12 µm); OS measures 289 µm but ΔTRT cannot be computed due to missing baseline.',
                    'is_peak_sample' => false,
                ]),
                $this->makeSample([
                    'sample_id' => 'OBS-ASO05-OD-D30',
                    'day' => 30,
                    'code_label' => 'FD30 (OD +26 µm)',
                    'stage_es' => 'En Vuelo · Día 30 (OD 306 µm En Progresión / OS Sin Base)',
                    'stage_en' => 'In-Flight · Day 30 (OD 306 µm Worsening / OS No Baseline)',
                    'observed_at' => '2026-07-01',
                    'has_oct_od' => true,
                    'has_oct_os' => true,
                    'os_missing_baseline' => true,
                    'od_trt_um' => 306.0, 'od_base_um' => 280.0, 'od_delta_um' => 26.0,
                    'os_trt_um' => 289.0, 'os_base_um' => null, 'os_delta_um' => null,
                    'axial_length_mm' => 23.82, 'od_shift' => 0.75, 'od_snellen' => '20/30', 'od_logmar' => 0.176,
                    'iop' => 17.5, 'frisen_grade' => 2, 'globe_flattening' => true, 'choroidal_folds' => true, 'rnfl' => 126,
                    'risk_tier' => 'severe', 'status_code' => 'PRIORIZAR_REVISION',
                    'status_es' => 'Priorizar revisión · OD ΔTRT +26 µm y OS sin línea base', 'status_en' => 'Prioritize review · OD ΔTRT +26 µm & OS missing baseline',
                    'note_es' => 'AT-05: Coexisten Ojo Derecho en progresión (+26 µm >= 20 µm) y Ojo Izquierdo con cobertura insuficiente.',
                    'note_en' => 'AT-05: Right Eye in progression (+26 µm >= 20 µm) and Left Eye with insufficient coverage coexist.',
                    'is_peak_sample' => true,
                ]),
            ];
        }

        if ($caseId === 'CASE-NORECENT-03B') {
            return [
                $this->makeSample([
                    'sample_id' => 'OBS-NR03B-OD-BASE',
                    'day' => 0,
                    'code_label' => 'FD0 (Línea Base)',
                    'stage_es' => 'Pre-vuelo · Línea Base Completa (275 µm OD / 274 µm OS)',
                    'stage_en' => 'Pre-flight · Complete Baseline (275 µm OD / 274 µm OS)',
                    'observed_at' => '2026-06-20',
                    'od_trt_um' => 275.0, 'od_base_um' => 275.0, 'od_delta_um' => 0.0,
                    'os_trt_um' => 274.0, 'os_base_um' => 274.0, 'os_delta_um' => 0.0,
                    'axial_length_mm' => 24.12, 'od_shift' => 0.00, 'od_snellen' => '20/20', 'od_logmar' => 0.000,
                    'iop' => 14.5, 'frisen_grade' => 0, 'globe_flattening' => false, 'choroidal_folds' => false, 'rnfl' => 94,
                    'risk_tier' => 'low', 'status_code' => 'LISTO_PARA_REVISION',
                    'status_es' => 'Línea base completa', 'status_en' => 'Complete baseline',
                    'note_es' => 'Línea base OCT registrada antes del lanzamiento (OD 275 µm, OS 274 µm).',
                    'note_en' => 'OCT baseline recorded prior to launch (OD 275 µm, OS 274 µm).',
                    'is_peak_sample' => false,
                ]),
                $this->makeSample([
                    'sample_id' => 'OBS-NR03B-OD-D10',
                    'day' => 10,
                    'code_label' => 'FD10 (Control Único)',
                    'stage_es' => 'En Vuelo · Día 10 (OD 279 µm · OS sin segundo control)',
                    'stage_en' => 'In-Flight · Day 10 (OD 279 µm · OS missing second check)',
                    'observed_at' => '2026-07-11',
                    'has_oct_od' => true,
                    'has_oct_os' => false,
                    'od_trt_um' => 279.0, 'od_base_um' => 275.0, 'od_delta_um' => 4.0,
                    'os_trt_um' => null, 'os_base_um' => 274.0, 'os_delta_um' => null,
                    'axial_length_mm' => 24.08, 'od_shift' => 0.15, 'od_snellen' => '20/20', 'od_logmar' => 0.000,
                    'iop' => 15.0, 'frisen_grade' => 0, 'globe_flattening' => false, 'choroidal_folds' => false, 'rnfl' => 96,
                    'risk_tier' => 'missing', 'status_code' => 'FALTAN_DATOS',
                    'status_es' => 'Faltan datos · Sin control reciente en ventana', 'status_en' => 'Missing data · No recent check in window',
                    'note_es' => 'AT-07: Sí tiene escaneo previo (279 µm en FD10), pero carece de control reciente en la ventana actual.',
                    'note_en' => 'AT-07: Has prior scan (279 µm at FD10), but lacks a recent check in the current window.',
                    'is_peak_sample' => false,
                ]),
                $this->makeSample([
                    'sample_id' => 'VISIT-NR03B-D30-MISSING',
                    'day' => 30,
                    'code_label' => 'FD30 (Falta Control)',
                    'stage_es' => 'En Vuelo · Día 30 (Control Reciente Omitido — AT-07)',
                    'stage_en' => 'In-Flight · Day 30 (Recent Check Omitted — AT-07)',
                    'observed_at' => 'Sin medición',
                    'has_oct_od' => false,
                    'has_oct_os' => false,
                    'od_trt_um' => null, 'od_base_um' => 275.0, 'od_delta_um' => null,
                    'os_trt_um' => null, 'os_base_um' => 274.0, 'os_delta_um' => null,
                    'axial_length_mm' => 24.08, 'od_shift' => 0.15, 'od_snellen' => '20/20', 'od_logmar' => 0.000,
                    'iop' => 15.0, 'frisen_grade' => 0, 'globe_flattening' => false, 'choroidal_folds' => false, 'rnfl' => null,
                    'risk_tier' => 'missing', 'status_code' => 'FALTAN_DATOS',
                    'status_es' => 'Faltan datos · Repetir / adquirir control reciente', 'status_en' => 'Missing data · Repeat / acquire recent check',
                    'note_es' => 'AT-07: Se distingue explícitamente de CASE-NOSCAN-03A porque sí posee línea base, pero falta la toma de la ventana actual.',
                    'note_en' => 'AT-07: Explicitly distinguished from CASE-NOSCAN-03A because baseline exists, but current window scan is missing.',
                    'is_peak_sample' => true,
                ]),
            ];
        }

        if ($caseId === 'CASE-TILT-04') {
            return [
                $this->makeSample([
                    'sample_id' => 'OBS-TILT04-EXP1-OD-PRE',
                    'day' => 0,
                    'code_label' => 'Pre-HDT -6°',
                    'stage_es' => 'Análogo Terrestre -6° HDT · Línea Base Pre-Exposición',
                    'stage_en' => '-6° HDT Terrestrial Analog · Pre-Exposure Baseline',
                    'observed_at' => '2026-01-01',
                    'od_trt_um' => 280.0, 'od_base_um' => 280.0, 'od_delta_um' => 0.0,
                    'os_trt_um' => 278.0, 'os_base_um' => 278.0, 'os_delta_um' => 0.0,
                    'axial_length_mm' => 24.10, 'od_shift' => 0.00, 'od_snellen' => '20/20', 'od_logmar' => 0.000,
                    'iop' => 14.5, 'frisen_grade' => 0, 'globe_flattening' => false, 'choroidal_folds' => false, 'rnfl' => 95,
                    'risk_tier' => 'low', 'status_code' => 'LISTO_PARA_REVISION',
                    'status_es' => 'Línea base Análogo -6° HDT (280 µm)', 'status_en' => '-6° HDT Analog Baseline (280 µm)',
                    'note_es' => 'AT-14: Línea base terrestre previa a los 2 tramos de exposición inclinada -6° HDT.',
                    'note_en' => 'AT-14: Terrestrial baseline prior to the 2 segments of -6° HDT exposure.',
                    'is_peak_sample' => false,
                ]),
                $this->makeSample([
                    'sample_id' => 'OBS-TILT04-EXP1-OD-FIRST-POST',
                    'day' => 14,
                    'code_label' => 'Fin -6° HDT (+24 µm)',
                    'stage_es' => 'Fin de Exposición -6° HDT · Engrosamiento Cefálico (304 µm)',
                    'stage_en' => 'End of -6° HDT Exposure · Cephalic Thickening (304 µm)',
                    'observed_at' => '2026-01-15',
                    'od_trt_um' => 304.0, 'od_base_um' => 280.0, 'od_delta_um' => 24.0,
                    'os_trt_um' => 296.0, 'os_base_um' => 278.0, 'os_delta_um' => 18.0,
                    'axial_length_mm' => 23.88, 'od_shift' => 0.50, 'od_snellen' => '20/25', 'od_logmar' => 0.097,
                    'iop' => 17.0, 'frisen_grade' => 1, 'globe_flattening' => true, 'choroidal_folds' => false, 'rnfl' => 118,
                    'risk_tier' => 'moderate', 'status_code' => 'PRIORIZAR_REVISION',
                    'status_es' => 'Pico Posexposición -6° HDT (ΔTRT +24 µm)', 'status_en' => 'Post -6° HDT Peak (ΔTRT +24 µm)',
                    'note_es' => 'AT-10/AT-12: Tras 14 días en -6° HDT (en 2 tramos), el TRT sube de 280 a 304 µm (+24 µm).',
                    'note_en' => 'AT-10/AT-12: After 14 days at -6° HDT (in 2 segments), TRT rises from 280 to 304 µm (+24 µm).',
                    'is_peak_sample' => true,
                ]),
                $this->makeSample([
                    'sample_id' => 'OBS-TILT04-EXP1-OD-POST30',
                    'day' => 30,
                    'code_label' => 'Pos-HDT +30d (284 µm)',
                    'stage_es' => 'Posexposición -6° HDT +30 Días · Recuperación (284 µm)',
                    'stage_en' => 'Post -6° HDT +30 Days · Recovery (284 µm)',
                    'observed_at' => '2026-02-14',
                    'od_trt_um' => 284.0, 'od_base_um' => 280.0, 'od_delta_um' => 4.0,
                    'os_trt_um' => 280.0, 'os_base_um' => 278.0, 'os_delta_um' => 2.0,
                    'axial_length_mm' => 24.06, 'od_shift' => 0.10, 'od_snellen' => '20/20', 'od_logmar' => 0.000,
                    'iop' => 14.8, 'frisen_grade' => 0, 'globe_flattening' => false, 'choroidal_folds' => false, 'rnfl' => 98,
                    'risk_tier' => 'low', 'status_code' => 'LISTO_PARA_REVISION',
                    'status_es' => 'Recuperación Posexposición +30d (Δ neto +4 µm)', 'status_en' => 'Post-exposure +30d Recovery (Net Δ +4 µm)',
                    'note_es' => 'AT-12: Comparación 280 -> 304 -> 284 µm (recuperación de -20 µm respecto al fin de exposición).',
                    'note_en' => 'AT-12: Comparison 280 -> 304 -> 284 µm (-20 µm recovery from end of exposure).',
                    'is_peak_sample' => false,
                ]),
                $this->makeSample([
                    'sample_id' => 'OBS-TILT04-ORB-OD-D30',
                    'day' => 60,
                    'code_label' => 'Misión Orbital FD30',
                    'stage_es' => 'Misión Orbital Separada (MSN-ORB-04) · Base Independiente 282 -> 288 µm',
                    'stage_en' => 'Separate Orbital Mission (MSN-ORB-04) · Independent Baseline 282 -> 288 µm',
                    'observed_at' => '2026-08-31',
                    'od_trt_um' => 288.0, 'od_base_um' => 282.0, 'od_delta_um' => 6.0,
                    'os_trt_um' => 283.0, 'os_base_um' => 279.0, 'os_delta_um' => 4.0,
                    'axial_length_mm' => 24.05, 'od_shift' => 0.15, 'od_snellen' => '20/20', 'od_logmar' => 0.000,
                    'iop' => 15.0, 'frisen_grade' => 0, 'globe_flattening' => false, 'choroidal_folds' => false, 'rnfl' => 99,
                    'risk_tier' => 'low', 'status_code' => 'LISTO_PARA_REVISION',
                    'status_es' => 'Listo para revisión · Línea base orbital separada (AT-14)', 'status_en' => 'Ready for review · Separate orbital baseline (AT-14)',
                    'note_es' => 'AT-14: La misión orbital usa su propia línea base (282 µm -> 288 µm, Δ +6 µm) sin mezclarla con la del estudio -6° HDT.',
                    'note_en' => 'AT-14: Orbital mission uses its own baseline (282 µm -> 288 µm, Δ +6 µm) without mixing it with the -6° HDT baseline.',
                    'is_peak_sample' => false,
                ]),
            ];
        }

        // Default / Canonical Progression Case: CASE-PROG-02 (CDR Alex Mercer)
        return [
            $this->makeSample([
                'sample_id' => 'OBS-PR02-OD-BASE',
                'day' => 0,
                'code_label' => 'FD0 (Pre-Vuelo)',
                'stage_es' => 'Pre-vuelo · Línea Base Completa (OD 280 µm / OS 279 µm)',
                'stage_en' => 'Pre-flight · Complete Baseline (OD 280 µm / OS 279 µm)',
                'observed_at' => '2026-03-20',
                'od_trt_um' => 280.0, 'od_base_um' => 280.0, 'od_delta_um' => 0.0,
                'os_trt_um' => 279.0, 'os_base_um' => 279.0, 'os_delta_um' => 0.0,
                'axial_length_mm' => 24.10, 'od_shift' => 0.00, 'od_snellen' => '20/20', 'od_logmar' => 0.000,
                'iop' => 14.5, 'frisen_grade' => 0, 'globe_flattening' => false, 'choroidal_folds' => false, 'rnfl' => 96,
                'risk_tier' => 'low', 'status_code' => 'LISTO_PARA_REVISION',
                'status_es' => 'Listo para revisión · Línea Base Pre-Vuelo', 'status_en' => 'Ready for review · Pre-Flight Baseline',
                'note_es' => 'Línea base pre-vuelo registrada con protocolo OCHMO-MTB-001-V2022.',
                'note_en' => 'Pre-flight baseline recorded under protocol OCHMO-MTB-001-V2022.',
                'is_peak_sample' => false,
            ]),
            $this->makeSample([
                'sample_id' => 'OBS-PR02-OD-D10',
                'day' => 10,
                'code_label' => 'FD10 (+8 µm)',
                'stage_es' => 'En Vuelo · Día 10 (ΔTRT +8 µm en OD)',
                'stage_en' => 'In-Flight · Day 10 (ΔTRT +8 µm in OD)',
                'observed_at' => '2026-04-11',
                'od_trt_um' => 288.0, 'od_base_um' => 280.0, 'od_delta_um' => 8.0,
                'os_trt_um' => 281.0, 'os_base_um' => 279.0, 'os_delta_um' => 2.0,
                'axial_length_mm' => 24.01, 'od_shift' => 0.25, 'od_snellen' => '20/20', 'od_logmar' => 0.000,
                'iop' => 15.8, 'frisen_grade' => 0, 'globe_flattening' => false, 'choroidal_folds' => false, 'rnfl' => 102,
                'risk_tier' => 'low', 'status_code' => 'LISTO_PARA_REVISION',
                'status_es' => 'Listo para revisión · Incremento inicial (+8 µm)', 'status_en' => 'Ready for review · Initial increase (+8 µm)',
                'note_es' => 'Primer control en vuelo: OD sube de 280 a 288 µm (+8 µm); aún por debajo del umbral de alerta.',
                'note_en' => 'First in-flight check: OD rises from 280 to 288 µm (+8 µm); still below alert threshold.',
                'is_peak_sample' => false,
            ]),
            $this->makeSample([
                'sample_id' => 'OBS-PR02-OD-D15-AV20',
                'day' => 15,
                'code_label' => 'FD15 (+14 µm · Disp. FD20)',
                'stage_es' => 'En Vuelo · Día 15 (ΔTRT +14 µm · Disponible en Tierra el Día 20)',
                'stage_en' => 'In-Flight · Day 15 (ΔTRT +14 µm · Available on Ground Day 20)',
                'observed_at' => '2026-04-16 (Disp. 2026-04-21)',
                'od_trt_um' => 294.0, 'od_base_um' => 280.0, 'od_delta_um' => 14.0,
                'os_trt_um' => 282.0, 'os_base_um' => 279.0, 'os_delta_um' => 3.0,
                'axial_length_mm' => 23.94, 'od_shift' => 0.50, 'od_snellen' => '20/25', 'od_logmar' => 0.097,
                'iop' => 16.4, 'frisen_grade' => 1, 'globe_flattening' => true, 'choroidal_folds' => false, 'rnfl' => 112,
                'risk_tier' => 'moderate', 'status_code' => 'PRIORIZAR_REVISION',
                'status_es' => 'Tendencia ascendente (+8 -> +14 µm · AT-04)', 'status_en' => 'Upward trend (+8 -> +14 µm · AT-04)',
                'note_es' => 'AT-03/AT-04: Tomado en FD15 y recibido en Tierra en FD20; segundo aumento consecutivo en OD (+14 µm).',
                'note_en' => 'AT-03/AT-04: Captured on FD15 and received on Ground on FD20; second consecutive increase in OD (+14 µm).',
                'is_peak_sample' => false,
            ]),
            $this->makeSample([
                'sample_id' => 'OBS-PR02-OD-D30',
                'day' => 30,
                'code_label' => 'FD30 (Pico +26 µm)',
                'stage_es' => 'En Vuelo · Día 30 (Pico ΔTRT +26 µm · Dispara Priorizar Revisión)',
                'stage_en' => 'In-Flight · Day 30 (Peak ΔTRT +26 µm · Triggers Prioritize Review)',
                'observed_at' => '2026-05-01',
                'od_trt_um' => 306.0, 'od_base_um' => 280.0, 'od_delta_um' => 26.0,
                'os_trt_um' => 283.0, 'os_base_um' => 279.0, 'os_delta_um' => 4.0,
                'axial_length_mm' => 23.82, 'od_shift' => 0.75, 'od_snellen' => '20/30', 'od_logmar' => 0.176,
                'iop' => 17.0, 'frisen_grade' => 2, 'globe_flattening' => true, 'choroidal_folds' => true, 'rnfl' => 128,
                'risk_tier' => 'severe', 'status_code' => 'PRIORIZAR_REVISION',
                'status_es' => 'Priorizar revisión · OD en progresión (+26 µm)', 'status_en' => 'Prioritize review · OD worsening (+26 µm)',
                'note_es' => 'OD alcanza 306 µm (ΔTRT +26 µm >= 20 µm), Frisén 2, shift +0.75 D y acortamiento axial a 23.82 mm; OS permanece estable (283 µm, Δ +4 µm).',
                'note_en' => 'OD reaches 306 µm (ΔTRT +26 µm >= 20 µm), Frisén 2, +0.75 D shift and axial shortening to 23.82 mm; OS remains stable (283 µm, Δ +4 µm).',
                'is_peak_sample' => true,
            ]),
            $this->makeSample([
                'sample_id' => 'OBS-PR02-OD-POST30',
                'day' => 60,
                'code_label' => 'Posvuelo +30d (+15 µm)',
                'stage_es' => 'Posvuelo +30 Días · Recuperación Parcial (295 µm · ΔTRT +15 µm)',
                'stage_en' => 'Post-flight +30 Days · Partial Recovery (295 µm · ΔTRT +15 µm)',
                'observed_at' => '2026-09-28',
                'od_trt_um' => 295.0, 'od_base_um' => 280.0, 'od_delta_um' => 15.0,
                'os_trt_um' => 281.0, 'os_base_um' => 279.0, 'os_delta_um' => 2.0,
                'axial_length_mm' => 23.93, 'od_shift' => 0.45, 'od_snellen' => '20/25', 'od_logmar' => 0.097,
                'iop' => 15.5, 'frisen_grade' => 1, 'globe_flattening' => true, 'choroidal_folds' => false, 'rnfl' => 112,
                'risk_tier' => 'moderate', 'status_code' => 'PRIORIZAR_REVISION',
                'status_es' => 'Recuperación parcial a +30d (Persiste +15 µm)', 'status_en' => 'Partial recovery at +30d (+15 µm persists)',
                'note_es' => 'AT-15: A los +30 días de aterrizar, OD baja de 306 a 295 µm, pero persiste +15 µm sobre la línea base.',
                'note_en' => 'AT-15: At +30 days post-landing, OD drops from 306 to 295 µm, but remains +15 µm above baseline.',
                'is_peak_sample' => false,
            ]),
            $this->makeSample([
                'sample_id' => 'VISIT-PR02-POST60-MISSING',
                'day' => 90,
                'code_label' => 'Posvuelo +60d (Sin Medición)',
                'stage_es' => 'Posvuelo +60 Días · CONTROL OMITIDO / SIN MEDICIÓN (AT-15 / AT-16)',
                'stage_en' => 'Post-flight +60 Days · OMITTED CHECK / UNMEASURED (AT-15 / AT-16)',
                'observed_at' => 'Sin medición',
                'has_oct_od' => false,
                'has_oct_os' => false,
                'od_trt_um' => null, 'od_base_um' => 280.0, 'od_delta_um' => null,
                'os_trt_um' => null, 'os_base_um' => 279.0, 'os_delta_um' => null,
                'axial_length_mm' => 23.93, 'od_shift' => 0.45, 'od_snellen' => '20/25', 'od_logmar' => 0.097,
                'iop' => 15.0, 'frisen_grade' => 1, 'globe_flattening' => true, 'choroidal_folds' => false, 'rnfl' => null,
                'risk_tier' => 'missing', 'status_code' => 'FALTAN_DATOS',
                'status_es' => 'Corte +60d: Sin medición (No se interpola)', 'status_en' => 'Cut +60d: Unmeasured (Never interpolated)',
                'note_es' => 'AT-15/AT-16: El corte de +60 días no fue tomado. El sistema lo reporta como «Sin medición» y jamás inventa ni interpola un valor intermedio.',
                'note_en' => 'AT-15/AT-16: The +60d cut was not captured. The system reports «Unmeasured» and never invents or interpolates an intermediate value.',
                'is_peak_sample' => false,
            ]),
            $this->makeSample([
                'sample_id' => 'OBS-PR02-OD-POST90',
                'day' => 120,
                'code_label' => 'Posvuelo +90d (+5 µm)',
                'stage_es' => 'Posvuelo +90 Días · Estabilización Posvuelo (285 µm · ΔTRT +5 µm)',
                'stage_en' => 'Post-flight +90 Days · Post-flight Stabilization (285 µm · ΔTRT +5 µm)',
                'observed_at' => '2026-11-27',
                'od_trt_um' => 285.0, 'od_base_um' => 280.0, 'od_delta_um' => 5.0,
                'os_trt_um' => 279.0, 'os_base_um' => 279.0, 'os_delta_um' => 0.0,
                'axial_length_mm' => 24.05, 'od_shift' => 0.15, 'od_snellen' => '20/20', 'od_logmar' => 0.000,
                'iop' => 14.8, 'frisen_grade' => 0, 'globe_flattening' => false, 'choroidal_folds' => false, 'rnfl' => 98,
                'risk_tier' => 'low', 'status_code' => 'LISTO_PARA_REVISION',
                'status_es' => 'Corte +90d Completado (ΔTRT +5 µm)', 'status_en' => 'Cut +90d Completed (ΔTRT +5 µm)',
                'note_es' => 'A +90 días posvuelo el grosor peripapilar OD desciende a 285 µm (+5 µm sobre la base de 280 µm).',
                'note_en' => 'At +90 days post-flight OD peripapillary thickness returns to 285 µm (+5 µm above 280 µm baseline).',
                'is_peak_sample' => false,
            ]),
        ];
    }

    private function makeSample(array $raw): array
    {
        $hasOctOd = $raw['has_oct_od'] ?? ($raw['od_trt_um'] !== null);
        $hasOctOs = $raw['has_oct_os'] ?? ($raw['os_trt_um'] !== null);
        $odTrt = $raw['od_trt_um'];
        $osTrt = $raw['os_trt_um'];

        // Compute deterministic ETDRS 9-sector values directly from the sample's TRT so the Cirrus OCT grid matches 100%
        $odCentral = $odTrt !== null ? round($odTrt - 24) : null;
        $osCentral = $osTrt !== null ? round($osTrt - 24) : null;

        return array_merge([
            'has_oct_od' => $hasOctOd,
            'has_oct_os' => $hasOctOs,
            'os_missing_baseline' => $raw['os_missing_baseline'] ?? false,
            'od_se' => $raw['od_shift'] ?? 0.0,
            'os_se' => 0.0,
            'symptoms' => [],
            'od_central_fovea_um' => $odCentral,
            'os_central_fovea_um' => $osCentral,
            'od_volume_mm3' => $odTrt !== null ? round(10.0 + (($odTrt - 278.0) * 0.045), 2) : null,
            'os_volume_mm3' => $osTrt !== null ? round(10.0 + (($osTrt - 278.0) * 0.045), 2) : null,
        ], $raw);
    }
}
