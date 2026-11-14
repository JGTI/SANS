<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AstronautProfile;
use App\Services\NasaDataService;
use App\Services\OphthalmologyCalculator;
use App\Services\SansMonitor\SansMonitorEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DemoTheaterController extends Controller
{
    public function __construct(
        protected OphthalmologyCalculator $calculator,
        protected NasaDataService $nasaService,
        protected SansMonitorEngine $sansEngine
    ) {}

    public function index(Request $request): View
    {
        if (AstronautProfile::count() === 0) {
            app(\Database\Seeders\OphthalmologySeeder::class)->run();
        }
        if (DB::table('sans_cases')->count() === 0) {
            app(\Database\Seeders\SansMonitorSeeder::class)->run();
        }

        $astronauts = AstronautProfile::with(['evaluations' => fn($q) => $q->orderBy('mission_day'), 'sansObservations'])->get();
        $selectedCode = $request->query('astro', 'ASTRO-01');

        $astronaut = AstronautProfile::where('code', $selectedCode)
            ->with(['evaluations' => fn($q) => $q->orderBy('mission_day'), 'sansObservations'])
            ->first() ?? $astronauts->first();

        $evals = $astronaut ? $astronaut->evaluations : collect();
        $sans = $astronaut ? $astronaut->sansObservations : collect();

        // Sincronización directa con los 3 escenarios canónicos del SANS Monitor
        $sansCases = DB::table('sans_cases')->orderBy('id')->get();
        $linkedCaseId = match ($astronaut?->code) {
            'ASTRO-02' => 'CASE-STABLE-01',
            'ASTRO-03' => 'CASE-NOSCAN-03A',
            default => 'CASE-PROG-02',
        };
        $linkedSansCase = $sansCases->firstWhere('case_id', $linkedCaseId) ?? $sansCases->first();
        $linkedFollowups = $linkedSansCase
            ? DB::table('sans_followup_visits')->where('case_id', $linkedSansCase->case_id)->orderBy('target_milestone_day')->get()
            : collect();

        // Evidencia cruzada de NASA para el Demo
        $osdrTopStudies = array_slice($this->nasaService->getOsdrStudies('retina SANS'), 0, 3);
        $ntrsTopPubs = array_slice($this->nasaService->getNtrsPublications('SANS evidence'), 0, 3);

        return view('admin.demo-theater.index', compact(
            'astronauts',
            'astronaut',
            'evals',
            'sans',
            'sansCases',
            'linkedSansCase',
            'linkedFollowups',
            'osdrTopStudies',
            'ntrsTopPubs'
        ));
    }

    /**
     * Reporte técnico-científico imprimible/exportable (Sin firmas médicas falsas ni dictamen automático de aptitud)
     */
    public function report(string $code): View
    {
        if (AstronautProfile::count() === 0) {
            app(\Database\Seeders\OphthalmologySeeder::class)->run();
        }
        if (DB::table('sans_cases')->count() === 0) {
            app(\Database\Seeders\SansMonitorSeeder::class)->run();
        }

        $astronaut = AstronautProfile::where('code', $code)
            ->with(['evaluations' => fn($q) => $q->orderBy('mission_day'), 'sansObservations' => fn($q) => $q->orderBy('mission_day')])
            ->firstOrFail();

        $latestEvalOD = $astronaut->evaluations->where('eye', 'OD')->last();
        $latestEvalOS = $astronaut->evaluations->where('eye', 'OS')->last();
        $latestSans = $astronaut->sansObservations->last();

        $baselineEvalOD = $astronaut->evaluations->where('eye', 'OD')->where('mission_day', 0)->first();
        $baselineEvalOS = $astronaut->evaluations->where('eye', 'OS')->where('mission_day', 0)->first();

        $shiftOD = $latestEvalOD && $baselineEvalOD
            ? $this->calculator->calculateHyperopicShift((float) $latestEvalOD->spherical_equivalent, (float) $baselineEvalOD->spherical_equivalent)
            : 0.0;

        $riskAssessment = $this->calculator->evaluateSansRisk([
            'hyperopic_shift' => $shiftOD,
            'frisen_grade' => $latestSans ? $latestSans->frisen_scale_grade : 0,
            'globe_flattening' => $latestSans ? (bool) $latestSans->posterior_globe_flattening : false,
            'choroidal_folds' => $latestSans ? (bool) $latestSans->choroidal_folds : false,
            'rnfl_thickness' => $latestSans ? $latestSans->rnfl_thickness_microns : null,
        ]);

        $linkedCaseId = match ($astronaut->code) {
            'ASTRO-02' => 'CASE-STABLE-01',
            'ASTRO-03' => 'CASE-NOSCAN-03A',
            default => 'CASE-PROG-02',
        };
        $linkedSansCase = DB::table('sans_cases')->where('case_id', $linkedCaseId)->first();
        $linkedFollowups = DB::table('sans_followup_visits')
            ->where('case_id', $linkedCaseId)
            ->orderBy('target_milestone_day')
            ->get();

        $relevantStudies = array_slice($this->nasaService->getOsdrStudies(), 0, 2);
        $relevantPubs = array_slice($this->nasaService->getNtrsPublications(), 0, 2);

        return view('admin.demo-theater.report', compact(
            'astronaut',
            'latestEvalOD',
            'latestEvalOS',
            'baselineEvalOD',
            'baselineEvalOS',
            'latestSans',
            'shiftOD',
            'riskAssessment',
            'linkedSansCase',
            'linkedFollowups',
            'relevantStudies',
            'relevantPubs'
        ));
    }
}
