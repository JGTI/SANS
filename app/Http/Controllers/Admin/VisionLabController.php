<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AstronautProfile;
use App\Models\SansObservation;
use App\Models\VisionEvaluation;
use App\Services\OphthalmologyCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VisionLabController extends Controller
{
    public function __construct(private OphthalmologyCalculator $calculator) {}

    public function index(Request $request): View
    {
        $selectedAstronautId = $request->query('astronaut_id');
        $astronauts = AstronautProfile::orderBy('name')->get();

        $query = VisionEvaluation::with(['astronaut', 'evaluator', 'sansObservation'])
            ->orderBy('evaluated_at', 'desc');

        if ($selectedAstronautId) {
            $query->where('astronaut_id', $selectedAstronautId);
        }

        $evaluations = $query->paginate(15);
        $selectedAstronaut = $selectedAstronautId ? AstronautProfile::find($selectedAstronautId) : null;

        // Gráfica longitudinal si hay un astronauta seleccionado
        $longitudinalData = null;
        if ($selectedAstronaut) {
            $evals = VisionEvaluation::where('astronaut_id', $selectedAstronaut->id)
                ->orderBy('mission_day')
                ->get();

            $longitudinalData = [
                'labels' => $evals->where('eye', 'OD')->pluck('mission_day')->map(fn ($d) => "FD{$d}")->values()->all(),
                'od_se' => $evals->where('eye', 'OD')->pluck('spherical_equivalent')->values()->all(),
                'os_se' => $evals->where('eye', 'OS')->pluck('spherical_equivalent')->values()->all(),
                'od_logmar' => $evals->where('eye', 'OD')->pluck('logmar')->values()->all(),
                'os_logmar' => $evals->where('eye', 'OS')->pluck('logmar')->values()->all(),
            ];
        }

        return view('admin.vision-lab.index', compact('evaluations', 'astronauts', 'selectedAstronaut', 'longitudinalData'));
    }

    /**
     * Exportar telemetría clínica longitudinal en formato CSV estándar
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $selectedAstronautId = $request->query('astronaut_id');
        $query = VisionEvaluation::with(['astronaut', 'evaluator', 'sansObservation'])
            ->orderBy('evaluated_at', 'desc');

        if ($selectedAstronautId) {
            $query->where('astronaut_id', $selectedAstronautId);
        }

        $evaluations = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="space_vision_telemetry_' . date('Ymd_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($evaluations) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID Evaluacion',
                'Codigo Astronauta',
                'Nombre Astronauta',
                'Agencia',
                'Mision',
                'Dia de Mision',
                'Fase de Vuelo',
                'Fecha Evaluacion',
                'Ojo',
                'Esfera (SPH)',
                'Cilindro (CYL)',
                'Eje',
                'Equivalente Esferico (SE)',
                'Shift Hipermetropico (dSE)',
                'Agudeza Snellen',
                'LogMAR',
                'Presion Intraocular (IOP mmHg)',
                'Con Correccion',
                'Escala Frisen',
                'Aplanamiento Polo Posterior',
                'Pliegues Coroideos',
                'Estrato Riesgo SANS',
                'Es Simulado',
            ]);

            foreach ($evaluations as $e) {
                $sans = $e->sansObservation;
                fputcsv($handle, [
                    $e->id,
                    $e->astronaut->code ?? 'N/A',
                    $e->astronaut->name ?? 'N/A',
                    $e->astronaut->agency ?? 'N/A',
                    $e->astronaut->mission_name ?? 'N/A',
                    'FD' . $e->mission_day,
                    $e->flight_phase,
                    $e->evaluated_at ? $e->evaluated_at->format('Y-m-d H:i') : '',
                    $e->eye,
                    number_format($e->sphere, 2),
                    number_format($e->cylinder, 2),
                    $e->axis,
                    number_format($e->spherical_equivalent, 2),
                    number_format($e->hyperopic_shift, 2),
                    $e->snellen_fraction,
                    number_format($e->logmar, 3),
                    number_format($e->intraocular_pressure, 1),
                    $e->is_corrected ? 'SI' : 'NO',
                    $sans ? $sans->frisen_scale_grade : 0,
                    $sans && $sans->posterior_globe_flattening ? 'POSITIVO' : 'NEGATIVO',
                    $sans && $sans->choroidal_folds ? 'PRESENTES' : 'AUSENTES',
                    $sans ? strtoupper($sans->sans_risk_tier) : 'LOW',
                    $e->is_simulated ? 'SI' : 'NO',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create(): View
    {
        $astronauts = AstronautProfile::where('status', 'active_mission')
            ->orWhere('status', 'pre_flight')
            ->orderBy('name')
            ->get();

        return view('admin.vision-lab.create', compact('astronauts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'astronaut_id' => ['required', 'exists:astronaut_profiles,id'],
            'evaluated_at' => ['required', 'date'],
            'mission_day' => ['required', 'integer', 'min:0', 'max:2000'],
            'flight_phase' => ['required', 'in:pre_flight,in_flight,post_flight'],
            'eye' => ['required', 'in:OD,OS'],
            'sphere' => ['required', 'numeric', 'between:-20,20'],
            'cylinder' => ['required', 'numeric', 'between:-10,10'],
            'axis' => ['required', 'integer', 'between:0,180'],
            'snellen_numerator' => ['required', 'integer', 'min:10', 'max:20'],
            'snellen_denominator' => ['required', 'integer', 'min:10', 'max:400'],
            'with_correction' => ['nullable', 'boolean'],
            'intraocular_pressure' => ['nullable', 'numeric', 'between:5,50'],
            'reported_symptoms' => ['nullable', 'array'],
            'clinical_notes' => ['nullable', 'string', 'max:2000'],
            
            // Biomarcadores SANS opcionales
            'frisen_scale_grade' => ['nullable', 'integer', 'between:0,5'],
            'choroidal_folds' => ['nullable', 'boolean'],
            'posterior_globe_flattening' => ['nullable', 'boolean'],
            'cotton_wool_spots' => ['nullable', 'boolean'],
            'rnfl_thickness_microns' => ['nullable', 'integer', 'between:50,300'],
        ]);

        $astronaut = AstronautProfile::findOrFail($validated['astronaut_id']);
        
        // 1. Cálculos matemáticos certificados
        $se = $this->calculator->calculateSphericalEquivalent((float) $validated['sphere'], (float) $validated['cylinder']);
        $baselineSe = $validated['eye'] === 'OD' ? $astronaut->baseline_se_od : $astronaut->baseline_se_os;
        $hyperopicShift = $this->calculator->calculateHyperopicShift($se, $baselineSe);
        $logmar = $this->calculator->snellenToLogmar((int) $validated['snellen_numerator'], (int) $validated['snellen_denominator']);

        // 2. Persistir Evaluación
        $evaluation = VisionEvaluation::create([
            'astronaut_id' => $astronaut->id,
            'evaluator_id' => auth()->id(),
            'evaluated_at' => $validated['evaluated_at'],
            'mission_day' => $validated['mission_day'],
            'flight_phase' => $validated['flight_phase'],
            'eye' => $validated['eye'],
            'sphere' => $validated['sphere'],
            'cylinder' => $validated['cylinder'],
            'axis' => $validated['axis'],
            'spherical_equivalent' => $se,
            'hyperopic_shift' => $hyperopicShift,
            'snellen_numerator' => $validated['snellen_numerator'],
            'snellen_denominator' => $validated['snellen_denominator'],
            'logmar' => $logmar,
            'with_correction' => (bool) ($validated['with_correction'] ?? false),
            'intraocular_pressure' => $validated['intraocular_pressure'] ?? null,
            'reported_symptoms' => $validated['reported_symptoms'] ?? [],
            'clinical_notes' => $validated['clinical_notes'] ?? null,
            'is_simulated' => true,
        ]);

        // 3. Registrar o actualizar Observación SANS si se registraron biomarcadores
        if (isset($validated['frisen_scale_grade']) || ! empty($validated['choroidal_folds']) || ! empty($validated['posterior_globe_flattening'])) {
            $risk = $this->calculator->evaluateSansRisk([
                'frisen_grade' => $validated['frisen_scale_grade'] ?? 0,
                'globe_flattening' => (bool) ($validated['posterior_globe_flattening'] ?? false),
                'choroidal_folds' => (bool) ($validated['choroidal_folds'] ?? false),
                'cotton_wool_spots' => (bool) ($validated['cotton_wool_spots'] ?? false),
                'hyperopic_shift' => $hyperopicShift,
                'rnfl_thickness' => $validated['rnfl_thickness_microns'] ?? null,
            ]);

            SansObservation::create([
                'astronaut_id' => $astronaut->id,
                'vision_evaluation_id' => $evaluation->id,
                'observed_at' => $validated['evaluated_at'],
                'mission_day' => $validated['mission_day'],
                'eye' => $validated['eye'],
                'frisen_scale_grade' => $validated['frisen_scale_grade'] ?? 0,
                'choroidal_folds' => (bool) ($validated['choroidal_folds'] ?? false),
                'posterior_globe_flattening' => (bool) ($validated['posterior_globe_flattening'] ?? false),
                'cotton_wool_spots' => (bool) ($validated['cotton_wool_spots'] ?? false),
                'rnfl_thickness_microns' => $validated['rnfl_thickness_microns'] ?? null,
                'sans_risk_tier' => $risk['tier'],
                'risk_factors' => $risk['factors'],
                'clinical_action_recommended' => $risk['recommendation'],
                'is_simulated' => true,
            ]);
        }

        return redirect()->route('admin.vision-lab.index', ['astronaut_id' => $astronaut->id])
            ->with('status', "Evaluación oftalmológica para {$astronaut->name} (Ojo {$validated['eye']}) registrada con éxito. SE: {$se} D (Shift: +{$hyperopicShift} D).");
    }
}
