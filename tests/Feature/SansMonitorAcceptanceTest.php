<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SansMonitor\SansMonitorEngine;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SansMonitorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SansMonitorAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $surgeon;
    protected SansMonitorEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(SansMonitorSeeder::class);

        $this->surgeon = User::factory()->create([
            'name' => 'Dr. Evelyn Vance',
            'email' => 'surgeon.test@spacevision.nasa',
        ]);
        $this->surgeon->assignRole('flight-surgeon');

        $this->engine = app(SansMonitorEngine::class);
    }

    /**
     * AT-01: Crear ficha sin OCT ni protocolo previo
     */
    public function test_at01_create_case_without_oct_or_previous_protocol(): void
    {
        $response = $this->actingAs($this->surgeon)->post(route('admin.sans-monitor.cases.store'), [
            'case_id' => 'CASE-AT01-NEW',
            'mission_id' => 'MSN-AT01',
            'source_type' => 'FICTICIO_DEMO',
            'mission_phase' => 'PRE_VUELO',
            'planned_duration_days' => 90,
            'planned_duration_state' => 'VALOR',
            'previous_flights' => null,
            'previous_flights_state' => 'NO_APLICA',
            'reason_for_review' => 'Evaluación inicial sin OCT previa',
            'applied_protocol_version_id' => null,
            'applied_protocol_state' => 'DESCONOCIDO',
        ]);

        $response->assertRedirect();

        $case = DB::table('sans_cases')->where('case_id', 'CASE-AT01-NEW')->first();
        $this->assertNotNull($case);
        $this->assertEquals('SIN_LINEA_BASE', $case->baseline_status);

        $eval = $this->engine->evaluateCaseAtDay($case);
        $this->assertEquals('Faltan datos', $eval['case_operational_text']);
        $this->assertEquals('Sin datos suficientes', $eval['eyes']['OD']['trajectory_text']);
        $this->assertEquals('Aptitud no evaluada', $eval['fitness_display']);
    }

    /**
     * AT-02: Hallar un protocolo previo y un documento más reciente sin modificar la selección anterior
     */
    public function test_at02_distinguish_previous_protocol_newer_verified_version_and_research_article(): void
    {
        $case = DB::table('sans_cases')->where('case_id', 'CASE-PROG-02')->first();
        $search = $this->engine->searchProtocolsForCase($case, ['OCT']);

        // Protocolo aplicado previamente
        $this->assertNotNull($search['previously_applied']);
        $this->assertEquals('OCHMO-MTB-001-V2022', $search['previously_applied']->version_id);

        // Versión posterior verificada separada
        $this->assertNotEmpty($search['newer_verified_versions']);
        $this->assertEquals('OCHMO-MTB-001-V2024', $search['newer_verified_versions'][0]->version_id);

        // Artículo de investigación no se presenta como protocolo operativo y la selección previa en la ficha sigue intacta
        $articles = array_filter($search['followup_matches'], fn ($m) => $m['document_type_badge'] === 'ARTICULO_INVESTIGACION');
        $this->assertNotEmpty($articles);
        $firstArticle = array_values($articles)[0];
        $this->assertFalse($firstArticle['is_operational_protocol']);
        $this->assertEquals('OCHMO-MTB-001-V2022', DB::table('sans_cases')->where('case_id', 'CASE-PROG-02')->value('applied_protocol_version_id'));
    }

    /**
     * AT-03 y AT-04: Avanzar de día 10 a 15 y luego a 30; resultados del día 15 disponibles recién el día 20
     */
    public function test_at03_and_at04_historical_playback_hides_day_15_until_day_20_and_marks_synthetic_interpolation(): void
    {
        // En reproducción histórica al día 15, el resultado tomado el día 15 (available_day = 20) permanece oculto
        $histDay15 = $this->engine->getTimelineStateAtDay('CASE-PROG-02', 'OD', 15, 'HISTORICO');
        $this->assertFalse($histDay15['has_measurement_on_day']);
        $this->assertStringContainsString('Sin medición en este día', $histDay15['status_banner']);

        // Al evaluar el caso en el día 15, solo existe 1 control disponible (Día 10), por lo que no puede disparar aviso con el dato del día 15
        $case = DB::table('sans_cases')->where('case_id', 'CASE-PROG-02')->first();
        $evalAtDay15 = $this->engine->evaluateCaseAtDay($case, 15);
        $this->assertCount(1, $evalAtDay15['eyes']['OD']['deltas']);
        $this->assertNotEquals('PRIORIZAR_REVISION', $evalAtDay15['eyes']['OD']['operational_code']);

        // Al día 20 ya está disponible el resultado del día 15 (+14 µm), y al día 30 (+26 µm) se activa Priorizar revisión
        $evalAtDay20 = $this->engine->evaluateCaseAtDay($case, 20);
        $this->assertCount(2, $evalAtDay20['eyes']['OD']['deltas']);

        $evalAtDay30 = $this->engine->evaluateCaseAtDay($case, 30);
        $this->assertEquals('Priorizar revisión', $evalAtDay30['eyes']['OD']['operational_text']);
        $this->assertEquals(26.0, $evalAtDay30['eyes']['OD']['max_delta_trt']);

        // En modo SIMULACION, el día 15 queda marcado como fotograma ilustrativo interpolado y el día 180 como «Fuera del rango»
        $simDay15 = $this->engine->getTimelineStateAtDay('CASE-PROG-02', 'OD', 15, 'SIMULACION');
        $this->assertTrue($simDay15['is_illustrative']);
        $this->assertTrue($simDay15['is_interpolated']);

        $simDay180 = $this->engine->getTimelineStateAtDay('CASE-PROG-02', 'OD', 180, 'SIMULACION');
        $this->assertTrue($simDay180['out_of_range']);
        $this->assertStringContainsString('Fuera del rango', $simDay180['status_banner']);
    }

    /**
     * AT-05: Ojo derecho en progresión, ojo izquierdo sin línea base (una condición no oculta la otra)
     */
    public function test_at05_right_eye_in_progression_and_left_eye_missing_baseline_coexist(): void
    {
        $case = DB::table('sans_cases')->where('case_id', 'CASE-ASO-05')->first();
        $eval = $this->engine->evaluateCaseAtDay($case, 30);

        $this->assertEquals('Priorizar revisión', $eval['eyes']['OD']['operational_text']);
        $this->assertEquals('En progresión', $eval['eyes']['OD']['trajectory_text']);

        $this->assertEquals('Faltan datos', $eval['eyes']['OS']['operational_text']);
        $this->assertEquals('SIN_LINEA_BASE', $eval['eyes']['OS']['coverage_code']);
    }

    /**
     * AT-06 y AT-07: Filtros combinables y distinción entre «sin escaneos previos» y «sin control reciente»
     */
    public function test_at06_and_at07_combined_filters_and_coverage_distinction_never_label_missing_as_stable(): void
    {
        // Sin escaneos previos devuelve CASE-NOSCAN-03A
        $noPreviousScans = $this->engine->filterStudiesAndTrends(['coverage' => 'SIN_ESCANEOS_PREVIOS']);
        $idsNoScan = array_map(fn ($r) => $r['case']->case_id, $noPreviousScans['results']);
        $this->assertContains('CASE-NOSCAN-03A', $idsNoScan);
        $this->assertNotContains('CASE-NORECENT-03B', $idsNoScan);

        // Sin control reciente devuelve CASE-NORECENT-03B
        $noRecentControl = $this->engine->filterStudiesAndTrends(['coverage' => 'SIN_CONTROL_RECIENTE']);
        $idsNoRecent = array_map(fn ($r) => $r['case']->case_id, $noRecentControl['results']);
        $this->assertContains('CASE-NORECENT-03B', $idsNoRecent);
        $this->assertNotContains('CASE-NOSCAN-03A', $idsNoRecent);

        // Ninguno de los dos figura como Estable
        foreach (array_merge($noPreviousScans['results'], $noRecentControl['results']) as $res) {
            $this->assertNotEquals('Estable', $res['evaluation']['eyes']['OD']['trajectory_text']);
            $this->assertNotEquals('Estable', $res['evaluation']['eyes']['OS']['trajectory_text']);
        }

        // Combinación sin coincidencias muestra mensaje vacío sin error
        $emptyCombo = $this->engine->filterStudiesAndTrends([
            'dataset' => 'ORBITAL_AUTORIZADO',
            'evolution' => 'EN_PROGRESION',
        ]);
        $this->assertEquals(0, $emptyCombo['matched_count']);
        $this->assertNotNull($emptyCombo['empty_state_message']);
    }

    /**
     * AT-08 y AT-09: Afectaciones futuras sin modelo evaluado e importación de duplicados u OCT no comparable
     */
    public function test_at08_and_at09_future_outlook_without_validated_model_and_import_duplicates_or_incompatible_oct(): void
    {
        $case = DB::table('sans_cases')->where('case_id', 'CASE-PROG-02')->first();
        $eval = $this->engine->evaluateCaseAtDay($case);
        $this->assertStringContainsString('No estimable', $eval['future_outlook_banner']['experimental_prognosis_status']);
        $this->assertEquals('Aptitud no evaluada', $eval['fitness_display']);

        // Importar dos registros duplicados y una OCT con región no comparable
        $report = $this->engine->importObservationsRows('CASE-PROG-02', [
            ['eye' => 'OD', 'mission_day' => 10, 'metric_code' => 'PERIPAPILLARY_TRT', 'value' => 288.0, 'modality' => 'OCT', 'region' => 'PERIPAPILAR'],
            ['eye' => 'OD', 'mission_day' => 10, 'metric_code' => 'PERIPAPILLARY_TRT', 'value' => 288.0, 'modality' => 'OCT', 'region' => 'PERIPAPILAR'],
            ['eye' => 'OD', 'mission_day' => 25, 'metric_code' => 'PERIPAPILLARY_TRT', 'value' => 410.0, 'modality' => 'OCT', 'region' => 'MACULAR_INCOMPATIBLE'],
        ]);

        $this->assertEquals(0, $report['accepted_count']);
        $this->assertEquals(3, $report['observed_count']);

        // La OCT no comparable de 410 µm no alteró el ΔTRT máximo (sigue siendo +26 µm)
        $evalAfter = $this->engine->evaluateCaseAtDay($case, 30);
        $this->assertEquals(26.0, $evalAfter['eyes']['OD']['max_delta_trt']);
    }

    /**
     * AT-10, AT-11, AT-12, AT-13, AT-14 y AT-19: Pestaña «Inclinación y seguimiento»
     */
    public function test_at10_to_at14_and_at19_tilt_exposure_comparison_segments_and_separated_orbital_baseline(): void
    {
        // AT-10: 280 µm (antes) -> 304 µm (primer control, +24 µm) -> 284 µm (+30 días, +4 µm) sin etiquetar «Retorno al rango basal» si no hay tolerancia
        $firstPost = $this->engine->compareExposureVisits('EXP-HDT6-01', 'OBS-TILT04-EXP1-OD-PRE', 'OBS-TILT04-EXP1-OD-FIRST-POST');
        $this->assertTrue($firstPost['comparable']);
        $this->assertEquals('+24 µm', $firstPost['signed_difference']);

        $day30Post = $this->engine->compareExposureVisits('EXP-HDT6-01', 'OBS-TILT04-EXP1-OD-PRE', 'OBS-TILT04-EXP1-OD-POST30');
        $this->assertTrue($day30Post['comparable']);
        $this->assertEquals('+4 µm', $day30Post['signed_difference']);
        $this->assertNotEquals('Retorno al rango basal', $day30Post['finding_classification']);

        // AT-14: Dos tramos de distinta inclinación (-6° y -4°) registrados por separado y duración derivada de 14 días
        $this->assertCount(2, $day30Post['segments']);
        $this->assertEquals(6.0, (float) $day30Post['segments'][0]->tilt_angle_deg);
        $this->assertEquals(4.0, (float) $day30Post['segments'][1]->tilt_angle_deg);
        $this->assertEquals(14, $day30Post['derived_duration_days']);

        // AT-13: Dos visitas de ojos o procedimientos incompatibles no calculan diferencia
        $incompatible = $this->engine->compareExposureVisits('EXP-HDT6-01', 'OBS-TILT04-EXP1-OD-PRE', 'OBS-NS03A-OD-IOP');
        $this->assertFalse($incompatible['comparable']);

        // AT-19: La misión orbital de CASE-TILT-04 usa su propia línea base (282 µm) y no la del análogo de -6° (280 µm)
        $tiltCase = DB::table('sans_cases')->where('case_id', 'CASE-TILT-04')->first();
        $orbEval = $this->engine->evaluateCaseAtDay($tiltCase, 30);
        $this->assertEquals(282.0, $orbEval['eyes']['OD']['baseline_trt']);
        $this->assertEquals(13.0, $orbEval['eyes']['OD']['max_delta_trt']);
    }

    /**
     * AT-15, AT-16, AT-17 y AT-18: Hitos +30/+60/+90 sin interpolar +60, PIO sin OCT y panel de contexto
     */
    public function test_at15_to_at18_postflight_milestones_missing_plus60_iop_without_oct_and_context_toggle(): void
    {
        // AT-16: En CASE-PROG-02 existen +30 y +90 pero +60 figura como SIN_MEDICION sin interpolar
        $visit60 = DB::table('sans_followup_visits')
            ->where('case_id', 'CASE-PROG-02')
            ->where('target_milestone_day', 60)
            ->first();
        $this->assertEquals('SIN_MEDICION', $visit60->status);
        $this->assertNull($visit60->actual_date);

        // AT-17: En CASE-NOSCAN-03A la OCT queda no evaluable, la PIO sigue visible (16 mmHg) y no se asigna escenario estable
        $noScanCase = DB::table('sans_cases')->where('case_id', 'CASE-NOSCAN-03A')->first();
        $noScanEval = $this->engine->evaluateCaseAtDay($noScanCase);
        $this->assertEquals('SIN_ESCANEOS_PREVIOS', $noScanEval['eyes']['OD']['coverage_code']);
        $this->assertNotEmpty($noScanEval['eyes']['OD']['iop_records']);
        $this->assertNotEquals('Estable', $noScanEval['eyes']['OD']['trajectory_text']);

        // AT-18: Activar o desactivar el panel de contexto en la vista HTTP no altera el estado ocular ni la acción sugerida
        $resClosed = $this->actingAs($this->surgeon)->get(route('admin.sans-monitor.index', [
            'case_id' => 'CASE-PROG-02',
            'tab' => 'seguimiento',
            'context' => '0',
        ]));
        $resClosed->assertOk();
        $resClosed->assertSee('Priorizar revisión');
        $resClosed->assertSee('Sin medición');

        $resOpen = $this->actingAs($this->surgeon)->get(route('admin.sans-monitor.index', [
            'case_id' => 'CASE-PROG-02',
            'tab' => 'seguimiento',
            'context' => '1',
        ]));
        $resOpen->assertOk();
        $resOpen->assertSee('Contexto del Caso');
        $resOpen->assertSee('Priorizar revisión');
    }

    /**
     * Regla P0 del Concurso: Un juez o visitante sin login (guest) puede abrir y probar la demostración principal sin autenticarse
     */
    public function test_public_judge_can_access_and_run_demo_without_login(): void
    {
        $this->assertGuest();

        $resDemo = $this->get('/demo');
        $resDemo->assertOk();
        $resDemo->assertSee('Predictor SANS por Biomarcadores');
        $resDemo->assertSee('Developed by NEXUS × Visual Rehab Center for the NASA International Space Apps Challenge 2026.');

        $resTab2 = $this->get('/sans-monitor?tab=seguimiento&case_id=CASE-PROG-02');
        $resTab2->assertOk();
        $resTab2->assertSee('Corte +30 Días');
        $resTab2->assertSee('Sin medición');

        $resTab3 = $this->get('/admin/sans-monitor?tab=escenarios&case_id=CASE-NOSCAN-03A');
        $resTab3->assertOk();
        $resTab3->assertSee('Escenario No se Escanea');
        $resTab3->assertSee('Faltan datos');
    }
}
