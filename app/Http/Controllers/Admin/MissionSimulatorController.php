<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AstronautProfile;
use App\Services\OphthalmologyCalculator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MissionSimulatorController extends Controller
{
    public function __construct(
        protected OphthalmologyCalculator $calculator
    ) {}

    public function index(Request $request): View
    {
        $astronauts = AstronautProfile::all();
        $selectedAstro = $request->query('astro', 'ASTRO-01');
        $selectedScenario = $request->query('scenario', 'scenario-3-sans-critical');
        $seed = (int) $request->query('seed', 42);

        // Generar matriz de simulación determinista para 180 días de vuelo
        $days = [0, 15, 30, 45, 60, 75, 90, 105, 120, 150, 180];
        $frames = [];

        // Generador pseudoaleatorio determinista con semilla
        $rng = function (int $step, int $s) {
            $val = sin($step * 12.9898 + $s * 78.233) * 43758.5453;
            return $val - floor($val); // número entre 0 y 1
        };

        foreach ($days as $day) {
            $noise = ($rng($day, $seed) - 0.5) * 0.08; // fluctuación biológica ±0.04 D

            if (in_array($selectedScenario, ['scenario-A', 'scenario-1-stable', 'A'])) {
                // Escenario A: Estabilidad visual
                $shiftOD = max(0.00, round(($day / 180) * 0.15 + $noise, 2));
                $shiftOS = max(0.00, round(($day / 180) * 0.10 + $noise, 2));
                $frisen = 0;
                $flattening = false;
                $folds = false;
                $iop = round(15.0 + ($rng($day + 1, $seed) * 1.5), 1);
            } elseif (in_array($selectedScenario, ['scenario-C', 'C'])) {
                // Escenario C: Transitorio (Pico en FD45 y meseta posterior)
                $peak = exp(-0.5 * pow(($day - 45) / 30.0, 2));
                $shiftOD = max(0.00, round(0.75 * $peak + $noise, 2));
                $shiftOS = max(0.00, round(0.50 * $peak + $noise, 2));
                $frisen = ($day >= 30 && $day <= 60) ? 1 : 0;
                $flattening = false;
                $folds = false;
                $iop = round(16.0 + 2.5 * $peak + $noise, 1);
            } elseif (in_array($selectedScenario, ['scenario-D', 'D'])) {
                // Escenario D: Persistente (Aplanamiento y pliegues sostenidos)
                $t = $day / 180.0;
                $shiftOD = max(0.00, round(1.50 * (1.0 / (1.0 + exp(-8.0 * ($t - 0.3)))) + $noise, 2));
                $shiftOS = max(0.00, round(1.20 * (1.0 / (1.0 + exp(-8.0 * ($t - 0.3)))) + $noise, 2));
                $frisen = $day >= 90 ? 1 : 0;
                $flattening = $day >= 60;
                $folds = $day >= 75;
                $iop = round(16.0 + ($day / 180) * 2.8 + $noise, 1);
            } elseif (in_array($selectedScenario, ['scenario-E', 'scenario-3-sans-critical', 'E'])) {
                // Escenario E: Combinación funcional y estructural severa (SANS Crítico)
                $t = $day / 180.0;
                $shiftOD = max(0.00, round(2.00 * (1.0 / (1.0 + exp(-10.0 * ($t - 0.25)))) + $noise, 2));
                $shiftOS = max(0.00, round(1.50 * (1.0 / (1.0 + exp(-10.0 * ($t - 0.25)))) + $noise, 2));
                $frisen = $day >= 105 ? 3 : ($day >= 60 ? 2 : ($day >= 30 ? 1 : 0));
                $flattening = $day >= 45;
                $folds = $day >= 45;
                $iop = round(17.0 + ($day / 180) * 4.5 + $noise, 1);
            } else {
                // Escenario B: Desplazamiento progresivo estándar
                $shiftOD = max(0.00, round(($day / 180) * 1.50 + $noise, 2));
                $shiftOS = max(0.00, round(($day / 180) * 1.10 + $noise, 2));
                $frisen = $day >= 90 ? 2 : ($day >= 45 ? 1 : 0);
                $flattening = $day >= 60;
                $folds = $day >= 75;
                $iop = round(16.0 + ($day / 180) * 3.0 + $noise, 1);
            }

            // Agudeza visual simulada
            $logmarOD = round(max(0.00, $shiftOD * 0.12), 3);
            $snellenOD = $logmarOD <= 0.05 ? '20/20' : ($logmarOD <= 0.15 ? '20/25' : ($logmarOD <= 0.25 ? '20/32' : '20/40'));

            $risk = $this->calculator->evaluateSansRisk([
                'hyperopic_shift' => $shiftOD,
                'frisen_grade' => $frisen,
                'globe_flattening' => $flattening,
                'choroidal_folds' => $folds,
                'rnfl_thickness' => round(95 + ($day / 180) * ($frisen * 25)),
            ]);

            $frames[$day] = [
                'day' => $day,
                'od_shift' => $shiftOD,
                'os_shift' => $shiftOS,
                'frisen' => $frisen,
                'flattening' => $flattening,
                'folds' => $folds,
                'iop' => $iop,
                'snellen' => $snellenOD,
                'logmar' => $logmarOD,
                'risk_tier' => $risk['tier'],
                'risk_score' => $risk['score'],
                'risk_factors' => $risk['factors'],
                'recommendation' => $risk['recommendation'],
            ];
        }

        return view('admin.mission-simulator.index', compact(
            'astronauts',
            'selectedAstro',
            'selectedScenario',
            'seed',
            'days',
            'frames'
        ));
    }
}
