<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ScientificEngineService;
use Illuminate\Http\Request;

class ModelLabController extends Controller
{
    protected ScientificEngineService $engine;

    public function __construct(ScientificEngineService $engine)
    {
        $this->engine = $engine;
    }

    public function index(Request $request)
    {
        $nAstronauts = (int) $request->input('n_astronauts', 50);
        $nSplits = (int) $request->input('n_splits', 5);
        $seed = (int) $request->input('seed', 42);

        $benchmark = $this->engine->runModelBenchmark($nAstronauts, $nSplits, $seed);
        $isOnline = $this->engine->isOnline();

        return view('admin.model-lab.index', compact(
            'benchmark',
            'isOnline',
            'nAstronauts',
            'nSplits',
            'seed'
        ));
    }

    public function exportReport(Request $request)
    {
        $nAstronauts = (int) $request->input('n_astronauts', 50);
        $nSplits = (int) $request->input('n_splits', 5);
        $seed = (int) $request->input('seed', 42);

        $benchmark = $this->engine->runModelBenchmark($nAstronauts, $nSplits, $seed);

        $reportData = [
            'project' => 'SPACE VISION — NASA Space Apps Challenge 2026',
            'module' => 'Aerospace Medical Intelligence & ML Benchmark Lab',
            'exported_at' => now()->toIso8601String(),
            'exported_by' => auth()->user()?->name ?? 'Mission Specialist',
            'cohort_parameters' => [
                'n_cohort_astronauts' => $nAstronauts,
                'cv_splits' => $nSplits,
                'reproducibility_seed' => $seed,
            ],
            'benchmark_results' => $benchmark,
            'scientific_disclaimer' => 'All machine learning models presented are experimental technical demonstrations validated on synthetic microgravity cohort simulations. They do not constitute certified medical devices under FDA or CE Mark.',
        ];

        $filename = 'spacevision-ml-benchmark-' . now()->format('Ymd-His') . '.json';

        return response()->json($reportData, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
