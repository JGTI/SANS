<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AstronautProfile;
use App\Models\SansObservation;
use App\Models\VisionEvaluation;
use Illuminate\View\View;

class MissionControlController extends Controller
{
    public function index(): View
    {
        $totalAstronauts = AstronautProfile::count();
        $activeMissions = AstronautProfile::where('status', 'active_mission')->count();
        $totalEvaluations = VisionEvaluation::count();
        
        // Detección de casos con desplazamiento hipermetrópico significativo (Delta SE >= +0.50 D)
        $hyperopicShiftCases = VisionEvaluation::where('hyperopic_shift', '>=', 0.50)
            ->distinct('astronaut_id')
            ->count('astronaut_id');

        // Casos con riesgo SANS moderado o alto
        $highRiskSansCount = SansObservation::whereIn('sans_risk_tier', ['high', 'severe'])->count();

        // Nivel de alerta general de la misión
        $missionAlertLevel = $highRiskSansCount > 0 ? 'ELEVADA' : ($hyperopicShiftCases > 0 ? 'VIGILANCIA' : 'NOMINAL');
        $alertColor = $highRiskSansCount > 0 ? 'danger' : ($hyperopicShiftCases > 0 ? 'warning' : 'success');

        // Tripulación con su última evaluación y signos SANS
        $crew = AstronautProfile::with(['latestEvaluation', 'latestSansObservation'])
            ->orderBy('days_in_microgravity', 'desc')
            ->get();

        // Últimas evaluaciones oftalmológicas
        $recentEvaluations = VisionEvaluation::with(['astronaut', 'evaluator'])
            ->orderBy('evaluated_at', 'desc')
            ->take(6)
            ->get();

        // Datos para la gráfica de evolución temporal de refracción (FD vs SE) de CDR Alex Mercer
        $alexMercer = AstronautProfile::where('code', 'ASTRO-01')->first();
        $chartData = [];
        if ($alexMercer) {
            $evals = VisionEvaluation::where('astronaut_id', $alexMercer->id)
                ->orderBy('mission_day')
                ->get();

            $chartData = [
                'labels' => $evals->where('eye', 'OD')->pluck('mission_day')->map(fn ($d) => "FD{$d}")->values()->all(),
                'od_se' => $evals->where('eye', 'OD')->pluck('spherical_equivalent')->values()->all(),
                'os_se' => $evals->where('eye', 'OS')->pluck('spherical_equivalent')->values()->all(),
                'od_logmar' => $evals->where('eye', 'OD')->pluck('logmar')->values()->all(),
            ];
        }

        return view('admin.dashboard', compact(
            'totalAstronauts',
            'activeMissions',
            'totalEvaluations',
            'hyperopicShiftCases',
            'highRiskSansCount',
            'missionAlertLevel',
            'alertColor',
            'crew',
            'recentEvaluations',
            'chartData'
        ));
    }
}
