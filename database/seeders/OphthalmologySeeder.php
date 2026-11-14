<?php

namespace Database\Seeders;

use App\Models\AstronautProfile;
use App\Models\SansObservation;
use App\Models\User;
use App\Models\VisionEvaluation;
use App\Services\OphthalmologyCalculator;
use Illuminate\Database\Seeder;

class OphthalmologySeeder extends Seeder
{
    public function run(): void
    {
        $calculator = app(OphthalmologyCalculator::class);
        $surgeonUser = User::where('email', 'surgeon@spacevision.nasa')->first();
        $astronautUser = User::where('email', 'astronaut@spacevision.nasa')->first();

        // 1. CDR Alex Mercer (Caso clínico con progresión SANS)
        $alex = AstronautProfile::updateOrCreate(
            ['code' => 'ASTRO-01'],
            [
                'user_id' => $astronautUser?->id,
                'name' => 'CDR Alex Mercer',
                'agency' => 'NASA',
                'mission_name' => 'Artemis III (Lunar Gateway)',
                'role' => 'Mission Commander',
                'launch_date' => now()->subDays(120),
                'days_in_microgravity' => 120,
                'status' => 'active_mission',
                'baseline_se_od' => 0.00,
                'baseline_se_os' => 0.00,
                'baseline_logmar_od' => 0.000, // 20/20
                'baseline_logmar_os' => 0.000,
                'medical_notes' => 'Astronauta diestro de 46 años en vuelo orbital prolongado. Monitoreo cercano por quejas de dificultad para lectura de manuales técnicos en cabina a partir de FD45.',
                'is_simulated' => true,
            ]
        );

        // Evaluaciones longitudinales para Alex Mercer (FD0, FD30, FD60, FD90, FD120)
        $alexTimeline = [
            [
                'day' => 0, 'phase' => 'pre_flight', 'date' => now()->subDays(120),
                'od' => ['sph' => 0.00, 'cyl' => 0.00, 'axis' => 0, 'denom' => 20, 'iop' => 14.5, 'symptoms' => []],
                'os' => ['sph' => 0.00, 'cyl' => 0.00, 'axis' => 0, 'denom' => 20, 'iop' => 14.2, 'symptoms' => []],
                'sans' => ['frisen' => 0, 'folds' => false, 'flattening' => false, 'rnfl' => 96],
            ],
            [
                'day' => 30, 'phase' => 'in_flight', 'date' => now()->subDays(90),
                'od' => ['sph' => 0.50, 'cyl' => -0.25, 'axis' => 180, 'denom' => 20, 'iop' => 16.0, 'symptoms' => ['mild_eyestrain']],
                'os' => ['sph' => 0.25, 'cyl' => 0.00, 'axis' => 0, 'denom' => 20, 'iop' => 15.8, 'symptoms' => []],
                'sans' => ['frisen' => 0, 'folds' => false, 'flattening' => false, 'rnfl' => 101],
            ],
            [
                'day' => 60, 'phase' => 'in_flight', 'date' => now()->subDays(60),
                'od' => ['sph' => 0.75, 'cyl' => -0.50, 'axis' => 175, 'denom' => 25, 'iop' => 17.5, 'symptoms' => ['blurred_near_vision', 'eyestrain']],
                'os' => ['sph' => 0.50, 'cyl' => -0.25, 'axis' => 180, 'denom' => 20, 'iop' => 16.9, 'symptoms' => ['mild_eyestrain']],
                'sans' => ['frisen' => 1, 'folds' => false, 'flattening' => true, 'rnfl' => 114],
            ],
            [
                'day' => 90, 'phase' => 'in_flight', 'date' => now()->subDays(30),
                'od' => ['sph' => 1.25, 'cyl' => -0.50, 'axis' => 170, 'denom' => 30, 'iop' => 18.2, 'symptoms' => ['blurred_near_vision', 'headache']],
                'os' => ['sph' => 0.75, 'cyl' => -0.25, 'axis' => 175, 'denom' => 25, 'iop' => 17.6, 'symptoms' => ['blurred_near_vision']],
                'sans' => ['frisen' => 2, 'folds' => true, 'flattening' => true, 'rnfl' => 128],
            ],
            [
                'day' => 120, 'phase' => 'in_flight', 'date' => now(),
                'od' => ['sph' => 1.50, 'cyl' => -0.50, 'axis' => 170, 'denom' => 40, 'iop' => 18.8, 'symptoms' => ['blurred_near_vision', 'headache', 'reduced_contrast']],
                'os' => ['sph' => 1.00, 'cyl' => -0.50, 'axis' => 175, 'denom' => 25, 'iop' => 18.0, 'symptoms' => ['blurred_near_vision']],
                'sans' => ['frisen' => 2, 'folds' => true, 'flattening' => true, 'rnfl' => 134],
            ],
        ];

        foreach ($alexTimeline as $step) {
            foreach (['OD', 'OS'] as $eye) {
                $eyeData = $eye === 'OD' ? $step['od'] : $step['os'];
                $se = $calculator->calculateSphericalEquivalent($eyeData['sph'], $eyeData['cyl']);
                $shift = $calculator->calculateHyperopicShift($se, 0.00);
                $logmar = $calculator->snellenToLogmar(20, $eyeData['denom']);

                $eval = VisionEvaluation::updateOrCreate(
                    [
                        'astronaut_id' => $alex->id,
                        'mission_day' => $step['day'],
                        'eye' => $eye,
                    ],
                    [
                        'evaluator_id' => $surgeonUser?->id,
                        'evaluated_at' => $step['date'],
                        'flight_phase' => $step['phase'],
                        'sphere' => $eyeData['sph'],
                        'cylinder' => $eyeData['cyl'],
                        'axis' => $eyeData['axis'],
                        'spherical_equivalent' => $se,
                        'hyperopic_shift' => $shift,
                        'snellen_numerator' => 20,
                        'snellen_denominator' => $eyeData['denom'],
                        'logmar' => $logmar,
                        'with_correction' => $eyeData['denom'] > 25,
                        'intraocular_pressure' => $eyeData['iop'],
                        'reported_symptoms' => $eyeData['symptoms'],
                        'clinical_notes' => "Evaluación programada FD{$step['day']} en órbita.",
                        'is_simulated' => true,
                    ]
                );

                if ($eye === 'OD') {
                    $sansMeta = $step['sans'];
                    $riskEval = $calculator->evaluateSansRisk([
                        'frisen_grade' => $sansMeta['frisen'],
                        'globe_flattening' => $sansMeta['flattening'],
                        'choroidal_folds' => $sansMeta['folds'],
                        'hyperopic_shift' => $shift,
                        'rnfl_thickness' => $sansMeta['rnfl'],
                    ]);

                    SansObservation::updateOrCreate(
                        [
                            'astronaut_id' => $alex->id,
                            'mission_day' => $step['day'],
                            'eye' => 'OD',
                        ],
                        [
                            'vision_evaluation_id' => $eval->id,
                            'observed_at' => $step['date'],
                            'frisen_scale_grade' => $sansMeta['frisen'],
                            'choroidal_folds' => $sansMeta['folds'],
                            'posterior_globe_flattening' => $sansMeta['flattening'],
                            'cotton_wool_spots' => $step['day'] >= 90,
                            'rnfl_thickness_microns' => $sansMeta['rnfl'],
                            'sans_risk_tier' => $riskEval['tier'],
                            'risk_factors' => $riskEval['factors'],
                            'clinical_action_recommended' => $riskEval['recommendation'],
                            'is_simulated' => true,
                        ]
                    );
                }
            }
        }

        // 2. Dr. Sara Lindqvist (ESA) - Control estable / Variaciones mínimas
        $sara = AstronautProfile::updateOrCreate(
            ['code' => 'ASTRO-02'],
            [
                'name' => 'Dr. Sara Lindqvist',
                'agency' => 'ESA',
                'mission_name' => 'ISS Expedition 71',
                'role' => 'Science Officer',
                'launch_date' => now()->subDays(75),
                'days_in_microgravity' => 75,
                'status' => 'active_mission',
                'baseline_se_od' => -0.50,
                'baseline_se_os' => -0.50,
                'baseline_logmar_od' => 0.000,
                'baseline_logmar_os' => 0.000,
                'medical_notes' => 'Astronauta de 38 años. Línea base miópica leve corregida. Sin síntomas refractivos adversos reportados a FD75.',
                'is_simulated' => true,
            ]
        );

        VisionEvaluation::updateOrCreate(
            ['astronaut_id' => $sara->id, 'mission_day' => 0, 'eye' => 'OD'],
            [
                'evaluator_id' => $surgeonUser?->id,
                'evaluated_at' => now()->subDays(75),
                'flight_phase' => 'pre_flight',
                'sphere' => -0.50, 'cylinder' => 0.00, 'axis' => 0,
                'spherical_equivalent' => -0.50, 'hyperopic_shift' => 0.00,
                'snellen_numerator' => 20, 'snellen_denominator' => 20, 'logmar' => 0.000,
                'intraocular_pressure' => 13.8, 'is_simulated' => true,
            ]
        );
        VisionEvaluation::updateOrCreate(
            ['astronaut_id' => $sara->id, 'mission_day' => 60, 'eye' => 'OD'],
            [
                'evaluator_id' => $surgeonUser?->id,
                'evaluated_at' => now()->subDays(15),
                'flight_phase' => 'in_flight',
                'sphere' => -0.25, 'cylinder' => 0.00, 'axis' => 0,
                'spherical_equivalent' => -0.25, 'hyperopic_shift' => 0.25,
                'snellen_numerator' => 20, 'snellen_denominator' => 20, 'logmar' => 0.000,
                'intraocular_pressure' => 14.5, 'is_simulated' => true,
            ]
        );

        // 3. Kenji Sato (JAXA) - Misión activa reciente
        AstronautProfile::updateOrCreate(
            ['code' => 'ASTRO-03'],
            [
                'name' => 'Kenji Sato',
                'agency' => 'JAXA',
                'mission_name' => 'ISS Expedition 72',
                'role' => 'Flight Engineer',
                'launch_date' => now()->subDays(25),
                'days_in_microgravity' => 25,
                'status' => 'active_mission',
                'baseline_se_od' => 0.00,
                'baseline_se_os' => 0.00,
                'baseline_logmar_od' => 0.000,
                'baseline_logmar_os' => 0.000,
                'medical_notes' => 'Fase temprana de adaptación a microgravedad. Monitoreo rutinario sin signos de edema o pliegues.',
                'is_simulated' => true,
            ]
        );
    }
}
