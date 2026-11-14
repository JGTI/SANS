<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AstronautProfile;
use App\Models\SansObservation;
use App\Models\VisionEvaluation;
use App\Services\NasaDataService;
use App\Services\OphthalmologyCalculator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DemoTheaterController extends Controller
{
    public function __construct(
        protected OphthalmologyCalculator $calculator,
        protected NasaDataService $nasaService
    ) {}

    public function index(Request $request): View
    {
        $astronauts = AstronautProfile::with(['evaluations' => fn($q) => $q->orderBy('mission_day'), 'sansObservations'])->get();
        $selectedCode = $request->query('astro', 'ASTRO-01');

        $astronaut = AstronautProfile::where('code', $selectedCode)
            ->with(['evaluations' => fn($q) => $q->orderBy('mission_day'), 'sansObservations'])
            ->first() ?? $astronauts->first();

        $evals = $astronaut ? $astronaut->evaluations : collect();
        $sans = $astronaut ? $astronaut->sansObservations : collect();

        // Evidencia cruzada de NASA para el Demo
        $osdrTopStudies = array_slice($this->nasaService->getOsdrStudies('retina SANS'), 0, 3);
        $ntrsTopPubs = array_slice($this->nasaService->getNtrsPublications('SANS evidence'), 0, 3);

        return view('admin.demo-theater.index', compact(
            'astronauts',
            'astronaut',
            'evals',
            'sans',
            'osdrTopStudies',
            'ntrsTopPubs'
        ));
    }

    /**
     * Reporte clínico oficial imprimible/exportable de vuelo espacial
     */
    public function report(string $code): View
    {
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
            'relevantStudies',
            'relevantPubs'
        ));
    }
}
