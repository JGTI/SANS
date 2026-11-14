<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AstronautProfile;
use App\Services\OphthalmologyCalculator;
use App\Services\ScientificEngineService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrewDossierController extends Controller
{
    public function __construct(
        protected OphthalmologyCalculator $calculator,
        protected ScientificEngineService $scientificEngine
    ) {}

    public function show(string $code): View
    {
        $astronaut = AstronautProfile::where('code', $code)
            ->with([
                'evaluations' => fn($q) => $q->orderBy('mission_day'),
                'sansObservations' => fn($q) => $q->orderBy('mission_day'),
            ])
            ->firstOrFail();

        $evalsOD = $astronaut->evaluations->where('eye', 'OD')->values();
        $evalsOS = $astronaut->evaluations->where('eye', 'OS')->values();

        $latestEvalOD = $evalsOD->last();
        $latestEvalOS = $evalsOS->last();
        $latestSans = $astronaut->sansObservations->last();

        $baselineEvalOD = $evalsOD->where('mission_day', 0)->first();
        $baselineEvalOS = $evalsOS->where('mission_day', 0)->first();

        $currentShiftOD = $latestEvalOD && $baselineEvalOD
            ? $this->calculator->calculateHyperopicShift((float) $latestEvalOD->spherical_equivalent, (float) $baselineEvalOD->spherical_equivalent)
            : 0.0;

        $riskAssessment = $this->calculator->evaluateSansRisk([
            'hyperopic_shift' => $currentShiftOD,
            'frisen_grade' => $latestSans ? $latestSans->frisen_scale_grade : 0,
            'globe_flattening' => $latestSans ? (bool) $latestSans->posterior_globe_flattening : false,
            'choroidal_folds' => $latestSans ? (bool) $latestSans->choroidal_folds : false,
            'rnfl_thickness' => $latestSans ? $latestSans->rnfl_thickness_microns : null,
        ]);

        $timelineDays = $astronaut->evaluations->pluck('mission_day')->unique()->sort()->values()->all();

        // Análisis de tendencia longitudinal asistido por SciPy
        $trendAnalysis = null;
        if ($evalsOD->count() >= 3) {
            $days = $evalsOD->pluck('mission_day')->map(fn($d) => (int)$d)->all();
            $seValues = $evalsOD->pluck('spherical_equivalent')->map(fn($v) => (float)$v)->all();
            $forecastTargetDays = [140, 180];
            $trendAnalysis = $this->scientificEngine->calculateLongitudinalTrend($days, $seValues, $forecastTargetDays, 2);
        }

        return view('admin.crew.show', compact(
            'astronaut',
            'evalsOD',
            'evalsOS',
            'latestEvalOD',
            'latestEvalOS',
            'baselineEvalOD',
            'baselineEvalOS',
            'latestSans',
            'currentShiftOD',
            'riskAssessment',
            'timelineDays',
            'trendAnalysis'
        ));
    }
}
