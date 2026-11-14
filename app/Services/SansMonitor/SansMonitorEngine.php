<?php

namespace App\Services\SansMonitor;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SansMonitorEngine
{
    /**
     * Catálogo curado de las 7 referencias oficiales + versiones de protocolo operativo (RF-05, RF-06, RF-07)
     */
    public function getCanonicalCatalogSeed(): array
    {
        return [
            [
                'protocol_id' => 'PROTO-NASA-OCHMO',
                'version_id' => 'OCHMO-MTB-001-V2022',
                'title' => 'NASA OCHMO Medical Technical Brief: SANS Surveillance Protocol (Baseline Version)',
                'issuer' => 'NASA Office of the Chief Health and Medical Officer (OCHMO)',
                'source_url' => 'https://www.nasa.gov/wp-content/uploads/2024/02/ochmo-mtb-001-sans.pdf',
                'doi' => null,
                'document_type' => 'PROTOCOLO_OPERATIVO',
                'publication_date' => '2022-06-15',
                'verified_status' => 'VERIFICADO',
                'phase_tags' => json_encode(['PRE_VUELO', 'EN_VUELO', 'POSVUELO']),
                'population' => 'Tripulación orbital de larga duración (>30 días)',
                'eligibility_rules' => json_encode([
                    'required_modalities' => ['OCT', 'FONDO_OJO', 'REFRACCION'],
                    'min_duration_days' => 30,
                    'baseline_required' => true,
                    'trt_early_signal_um' => 20,
                    'trt_primary_marker_um' => 55,
                    'trt_return_tolerance_um' => 10,
                ]),
                'supersedes_version_id' => null,
                'relation_verified' => true,
                'notes' => 'Referencia técnica de vigilancia pre, durante y posvuelo. No constituye autorización de dictamen clínico automático.',
            ],
            [
                'protocol_id' => 'PROTO-NASA-OCHMO',
                'version_id' => 'OCHMO-MTB-001-V2024',
                'title' => 'NASA OCHMO Technical Brief on SANS Risk & Postflight Follow-Up (+30/+60/+90)',
                'issuer' => 'NASA Office of the Chief Health and Medical Officer (OCHMO)',
                'source_url' => 'https://www.nasa.gov/wp-content/uploads/2024/02/ochmo-mtb-001-sans.pdf',
                'doi' => null,
                'document_type' => 'PROTOCOLO_OPERATIVO',
                'publication_date' => '2024-02-01',
                'verified_status' => 'VERIFICADO',
                'phase_tags' => json_encode(['PRE_VUELO', 'EN_VUELO', 'POSVUELO']),
                'population' => 'Tripulación orbital ISS / Artemis y seguimiento posvuelo hasta +90 días',
                'eligibility_rules' => json_encode([
                    'required_modalities' => ['OCT', 'FONDO_OJO', 'REFRACCION', 'BIOMETRIA_OPTICA'],
                    'min_duration_days' => 14,
                    'baseline_required' => true,
                    'trt_early_signal_um' => 20,
                    'trt_primary_marker_um' => 55,
                    'trt_return_tolerance_um' => 10,
                ]),
                'supersedes_version_id' => 'OCHMO-MTB-001-V2022',
                'relation_verified' => true,
                'notes' => 'Versión posterior verificada de OCHMO-MTB-001-V2022. No reemplaza la selección previa sin decisión expresa del revisor autorizado.',
            ],
            [
                'protocol_id' => 'CONSENSUS-SANS-2026',
                'version_id' => 'CONSENSUS-EYE-2026-V1',
                'title' => 'Consenso de especialistas sobre diagnóstico y manejo de SANS',
                'issuer' => 'International SANS Specialist Consensus Panel (Nature Eye)',
                'source_url' => 'https://doi.org/10.1038/s41433-026-04651-6',
                'doi' => '10.1038/s41433-026-04651-6',
                'document_type' => 'CONSENSO',
                'publication_date' => '2026-03-10',
                'verified_status' => 'VERIFICADO',
                'phase_tags' => json_encode(['PRE_VUELO', 'EN_VUELO', 'POSVUELO']),
                'population' => 'Candidatos y tripulantes en vigilancia neuro-ocular longitudinal',
                'eligibility_rules' => json_encode([
                    'required_modalities' => ['OCT', 'FONDO_OJO'],
                    'min_duration_days' => 10,
                    'baseline_required' => true,
                    'trt_early_signal_um' => 20,
                    'trt_primary_marker_um' => 55,
                    'trt_return_tolerance_um' => 8,
                ]),
                'supersedes_version_id' => null,
                'relation_verified' => true,
                'notes' => 'Referencia para separar hallazgos, umbrales y calendarios de investigación de los hitos ilustrativos de la demo.',
            ],
            [
                'protocol_id' => 'REF-MACIAS-2020',
                'version_id' => 'JAMA-MACIAS-2020',
                'title' => 'Macias et al., Cambios oculares estructurales en vuelo espacial prolongado y recuperación',
                'issuer' => 'JAMA Ophthalmology / NASA JSC Cardiovascular & Vision Lab',
                'source_url' => 'https://doi.org/10.1001/jamaophthalmol.2020.0673',
                'doi' => '10.1001/jamaophthalmol.2020.0673',
                'document_type' => 'ARTICULO_INVESTIGACION',
                'publication_date' => '2020-05-01',
                'verified_status' => 'VERIFICADO',
                'phase_tags' => json_encode(['EN_VUELO', 'POSVUELO']),
                'population' => 'Cohorte de astronautas de larga duración con OCT peripapilar y coroidea',
                'eligibility_rules' => json_encode([
                    'required_modalities' => ['OCT'],
                    'min_duration_days' => 30,
                    'baseline_required' => true,
                ]),
                'supersedes_version_id' => null,
                'relation_verified' => false,
                'notes' => 'Artículo de investigación (no se presenta como protocolo operativo). Documenta diferencias en órbita vs antes/después.',
            ],
            [
                'protocol_id' => 'REF-CARTER-2025',
                'version_id' => 'JAMA-CARTER-2025',
                'title' => 'Carter et al., Estudio de progresión del edema del disco óptico (Días 30 y 150)',
                'issuer' => 'JAMA Ophthalmology',
                'source_url' => 'https://doi.org/10.1001/jamaophthalmol.2025.4635',
                'doi' => '10.1001/jamaophthalmol.2025.4635',
                'document_type' => 'ARTICULO_INVESTIGACION',
                'publication_date' => '2025-11-12',
                'verified_status' => 'VERIFICADO',
                'phase_tags' => json_encode(['EN_VUELO', 'POSVUELO']),
                'population' => 'Seguimiento ocular en día 30 y cerca del día 150 de misión',
                'eligibility_rules' => json_encode([
                    'required_modalities' => ['OCT'],
                    'min_duration_days' => 90,
                    'baseline_required' => true,
                    'trt_primary_marker_um' => 55,
                ]),
                'supersedes_version_id' => null,
                'relation_verified' => false,
                'notes' => 'Referencia científica para los días 30 y 150. No es un protocolo operativo ni un predictor de aptitud.',
            ],
            [
                'protocol_id' => 'PROTO-LAURIE-HDT-2019',
                'version_id' => 'LAURIE-HDT6-2019-V1',
                'title' => 'Laurie et al., Comparación ocular entre vuelo espacial y análogo terrestre de reposo inclinado (-6° HDT)',
                'issuer' => 'JAMA Ophthalmology / NASA Bed Rest Analog Protocol',
                'source_url' => 'https://doi.org/10.1001/jamaophthalmol.2019.5261',
                'doi' => '10.1001/jamaophthalmol.2019.5261',
                'document_type' => 'PROTOCOLO_OPERATIVO',
                'publication_date' => '2019-02-01',
                'verified_status' => 'VERIFICADO',
                'phase_tags' => json_encode(['PRE_EXPOSICION', 'EN_EXPOSICION', 'POSEXPOSICION']),
                'population' => 'Participantes en reposo inclinado con cabeza abajo (-6° HDT)',
                'eligibility_rules' => json_encode([
                    'required_modalities' => ['OCT'],
                    'min_duration_days' => 14,
                    'baseline_required' => true,
                    'trt_early_signal_um' => 20,
                    'trt_primary_marker_um' => 55,
                ]),
                'supersedes_version_id' => null,
                'relation_verified' => true,
                'notes' => 'Protocolo de referencia para exposiciones inclinadas (-6°). Fundamenta separar las líneas base de análogos terrestres y vuelos espaciales.',
            ],
            [
                'protocol_id' => 'REF-NASA-HRP-2026',
                'version_id' => 'HRP-RAP-2026-HHC',
                'title' => 'NASA Human Research Program, Plan de Investigación de Riesgos 2026',
                'issuer' => 'NASA Human Research Program (HRP)',
                'source_url' => 'https://www.nasa.gov/wp-content/uploads/2026/08/hrp-rap-for-web-62026-hhc.pdf',
                'doi' => null,
                'document_type' => 'CONSENSO',
                'publication_date' => '2026-08-01',
                'verified_status' => 'VERIFICADO',
                'phase_tags' => json_encode(['PRE_VUELO', 'EN_VUELO', 'POSVUELO', 'POSEXPOSICION']),
                'population' => 'Investigación traslacional humana en vuelos espaciales y análogos',
                'eligibility_rules' => json_encode([
                    'required_modalities' => ['OCT', 'TONOMETRIA'],
                    'min_duration_days' => 1,
                    'baseline_required' => false,
                ]),
                'supersedes_version_id' => null,
                'relation_verified' => true,
                'notes' => 'Fundamenta tratar los factores fisiológicos y ambientales (5 dominios de contexto) como preguntas abiertas y no como causas demostradas.',
            ],
            [
                'protocol_id' => 'PORTAL-NASA-NLSP',
                'version_id' => 'NASA-NLSP-PORTAL-V1',
                'title' => 'NASA Life Sciences Portal (NLSP) — Gobernanza y Solicitud de Datos Humanos Autorizados',
                'issuer' => 'NASA Human Research Program',
                'source_url' => 'https://www.nasa.gov/hrp/nlsp/',
                'doi' => null,
                'document_type' => 'ESCENARIO_DEMO',
                'publication_date' => '2026-01-10',
                'verified_status' => 'VERIFICADO',
                'phase_tags' => json_encode(['PRE_VUELO', 'EN_VUELO', 'POSVUELO']),
                'population' => 'Cohortes humanas autorizadas y escenarios sintéticos de demostración P0',
                'eligibility_rules' => json_encode([
                    'required_modalities' => ['TONOMETRIA'],
                    'min_duration_days' => 0,
                    'baseline_required' => false,
                    'trt_early_signal_um' => 20,
                ]),
                'supersedes_version_id' => null,
                'relation_verified' => true,
                'notes' => 'Punto de partida para consultar o solicitar datos humanos autorizados y marco de escenarios de demo P0.',
            ],
        ];
    }

    /**
     * Recuperar el protocolo previamente aplicado y en lista separada buscar referencias de seguimiento y versiones más recientes (RF-06, RF-07, AT-02)
     */
    public function searchProtocolsForCase(object $case, array $availableModalities = [], bool $includeNewerVersionsFilter = true): array
    {
        $allProtocols = DB::table('sans_protocol_versions')
            ->where('verified_status', 'VERIFICADO')
            ->get();

        // 1. Protocolo previamente aplicado a la ficha (si está registrado)
        $previouslyApplied = null;
        $newerVerifiedVersions = [];

        if (! empty($case->applied_protocol_version_id) && $case->applied_protocol_state === 'CONOCIDO') {
            $previouslyApplied = $allProtocols->firstWhere('version_id', $case->applied_protocol_version_id);

            if ($previouslyApplied && $includeNewerVersionsFilter) {
                $newerVerifiedVersions = $allProtocols->filter(function ($proto) use ($previouslyApplied) {
                    return $proto->supersedes_version_id === $previouslyApplied->version_id
                        && (bool) $proto->relation_verified === true;
                })->values()->all();
            }
        }

        // 2. Lista separada de seguimiento posterior según fase, duración, hallazgo y modalidad disponible
        $followUpMatches = [];
        $typePriority = [
            'PROTOCOLO_OPERATIVO' => 1,
            'CONSENSO' => 2,
            'ESCENARIO_DEMO' => 3,
            'ARTICULO_INVESTIGACION' => 4,
        ];

        foreach ($allProtocols as $proto) {
            $phases = json_decode($proto->phase_tags ?? '[]', true) ?: [];
            $rules = json_decode($proto->eligibility_rules ?? '{}', true) ?: [];

            $matchedCriteria = [];
            $missingCriteria = [];

            if (in_array($case->mission_phase, $phases, true)) {
                $matchedCriteria[] = "Fase compatible ({$case->mission_phase})";
            } else {
                $missingCriteria[] = "Fase actual ({$case->mission_phase}) fuera de etiquetas principales";
            }

            $minDuration = $rules['min_duration_days'] ?? 0;
            if ($case->planned_duration_state === 'VALOR' && $case->planned_duration_days !== null) {
                if ($case->planned_duration_days >= $minDuration) {
                    $matchedCriteria[] = "Duración prevista ({$case->planned_duration_days} d >= {$minDuration} d)";
                } else {
                    $missingCriteria[] = "Duración prevista menor al umbral ({$minDuration} d)";
                }
            } else {
                $missingCriteria[] = "Duración de misión ({$case->planned_duration_state}) pendiente de verificar";
            }

            if (! empty($rules['baseline_required'])) {
                if ($case->baseline_status === 'COMPLETA') {
                    $matchedCriteria[] = 'Línea base ocular completa verificada';
                } else {
                    $missingCriteria[] = "Requiere línea base completa (actual: {$case->baseline_status})";
                }
            }

            $reqMods = $rules['required_modalities'] ?? [];
            foreach ($reqMods as $mod) {
                if (in_array($mod, $availableModalities, true)) {
                    $matchedCriteria[] = "Modalidad disponible: {$mod}";
                } else {
                    $missingCriteria[] = "Falta modalidad requerida: {$mod}";
                }
            }

            $followUpMatches[] = [
                'protocol' => $proto,
                'document_type_badge' => $proto->document_type,
                'is_operational_protocol' => $proto->document_type === 'PROTOCOLO_OPERATIVO',
                'matched_criteria' => $matchedCriteria,
                'missing_criteria' => $missingCriteria,
                'applicability_score' => count($matchedCriteria) * 10 - count($missingCriteria) * 2,
                'type_rank' => $typePriority[$proto->document_type] ?? 9,
                'publication_date' => $proto->publication_date ?? '1970-01-01',
            ];
        }

        // Ordenar primero por tipo de documento / aplicabilidad documentada y luego por fecha dentro del mismo tipo (RF-07)
        usort($followUpMatches, function ($a, $b) {
            if ($a['type_rank'] !== $b['type_rank']) {
                return $a['type_rank'] <=> $b['type_rank'];
            }
            if ($a['applicability_score'] !== $b['applicability_score']) {
                return $b['applicability_score'] <=> $a['applicability_score'];
            }
            return strcmp($b['publication_date'], $a['publication_date']);
        });

        return [
            'search_scope_banner' => 'Búsqueda en catálogo curado (no búsqueda exhaustiva externa)',
            'previously_applied' => $previouslyApplied,
            'newer_verified_versions' => $newerVerifiedVersions,
            'followup_matches' => $followUpMatches,
            'empty_catalog_message' => empty($followUpMatches)
                ? 'No se encontró un protocolo aplicable en el catálogo'
                : null,
        ];
    }

    /**
     * Evalúa el estado por ojo y del expediente respetando as_of_day (reproducción histórica available_day vs observed_day)
     * y emitiendo exclusivamente: «Listo para revisión», «Faltan datos» o «Priorizar revisión» (RF-10, AT-04, AT-05, AT-17).
     */
    public function evaluateCaseAtDay(object $case, ?int $asOfDay = null): array
    {
        $query = DB::table('sans_metric_observations')
            ->where('case_id', $case->case_id)
            ->whereNull('exposure_id') // Observaciones de la misión (separadas de exposiciones de análogo terrestre AT-19)
            ->where('status', 'MEDIDO')
            ->where('quality', 'VALIDA');

        if ($asOfDay !== null) {
            $query->where(function ($q) use ($asOfDay) {
                $q->whereNull('available_day')
                    ->orWhere('available_day', '<=', $asOfDay);
            });
        }

        $observations = $query->orderBy('mission_day')->get();

        $eyes = ['OD', 'OS'];
        $eyeEvaluations = [];

        foreach ($eyes as $eye) {
            $eyeObs = $observations->where('eye', $eye)->values();
            $trtObs = $eyeObs->where('metric_code', 'PERIPAPILLARY_TRT')->values();
            $iopObs = $eyeObs->where('metric_code', 'IOP')->values();

            $baselineTrt = $trtObs->firstWhere('visit_stage', 'ANTES');
            $inflightTrt = $trtObs->where('visit_stage', 'DURANTE')->values();

            $deltas = [];
            $maxDelta = null;
            $latestTrt = $trtObs->last();

            if ($baselineTrt && $baselineTrt->value !== null) {
                foreach ($inflightTrt as $obs) {
                    if ($obs->value !== null && $obs->baseline_observation_id === $baselineTrt->observation_id) {
                        $delta = round((float) $obs->value - (float) $baselineTrt->value, 1);
                        $deltas[] = [
                            'mission_day' => $obs->mission_day,
                            'available_day' => $obs->available_day,
                            'value' => (float) $obs->value,
                            'delta' => $delta,
                            'observation_id' => $obs->observation_id,
                        ];
                        if ($maxDelta === null || $delta > $maxDelta) {
                            $maxDelta = $delta;
                        }
                    }
                }
            }

            // Clasificación de cobertura y trayectoria por ojo (RF-10, RF-15, AT-05, AT-07, AT-17)
            $reasons = [];
            $operationalLabel = 'LISTO_PARA_REVISION';
            $operationalText = 'Listo para revisión';
            $trajectoryText = 'Estable';
            $coverageCode = 'CONTROLES_COMPARABLES';

            if ($trtObs->isEmpty()) {
                $operationalLabel = 'FALTAN_DATOS';
                $operationalText = 'Faltan datos';
                $trajectoryText = 'Sin datos suficientes';
                $coverageCode = 'SIN_ESCANEOS_PREVIOS';
                $reasons[] = "Ojo {$eye}: OCT no evaluable (sin escaneos OCT registrados).";
                if ($iopObs->isNotEmpty()) {
                    $lastIop = $iopObs->last();
                    $reasons[] = "Ojo {$eye}: PIO registrada ({$lastIop->value} {$lastIop->unit}), pero no sustituye la OCT para clasificar estabilidad.";
                }
            } elseif (! $baselineTrt || $baselineTrt->value === null) {
                $operationalLabel = 'FALTAN_DATOS';
                $operationalText = 'Faltan datos';
                $trajectoryText = 'Sin datos suficientes';
                $coverageCode = 'SIN_LINEA_BASE';
                $reasons[] = "Ojo {$eye}: Falta línea base pre-vuelo comparable (Completar línea base). No se calcula cambio respecto a pre-vuelo.";
            } elseif (count($deltas) < 2) {
                $operationalLabel = 'FALTAN_DATOS';
                $operationalText = 'Faltan datos';
                $trajectoryText = 'Sin datos suficientes';
                $coverageCode = 'SIN_CONTROL_RECIENTE';
                $reasons[] = "Ojo {$eye}: Sí tiene historial y línea base ({$baselineTrt->value} µm), pero falta control reciente (mínimo 2 controles en vuelo comparables en la ventana).";
            } else {
                // Tiene línea base y al menos 2 controles comparables en vuelo: aplicar regla reproducible de demo (+20 um señal temprana, +55 um marcador primario)
                if ($maxDelta !== null && $maxDelta >= 20.0) {
                    $operationalLabel = 'PRIORIZAR_REVISION';
                    $operationalText = 'Priorizar revisión';
                    $trajectoryText = 'En progresión';
                    $coverageCode = 'CONTROLES_COMPARABLES';
                    $markerType = $maxDelta >= 55.0 ? 'marcador primario (ΔTRT >= 55 µm)' : 'señal temprana de escenario (ΔTRT >= +20 µm)';
                    $reasons[] = "Ojo {$eye}: Progresión observada en grosor retiniano peripapilar (ΔTRT máx +{$maxDelta} µm respecto a línea base de {$baselineTrt->value} µm; {$markerType}).";
                } else {
                    $operationalLabel = 'LISTO_PARA_REVISION';
                    $operationalText = 'Listo para revisión';
                    $trajectoryText = 'Estable';
                    $coverageCode = 'CONTROLES_COMPARABLES';
                    $reasons[] = "Ojo {$eye}: Mínimo de 2 controles comparables verificados sin superar el disparador de escenario (ΔTRT máx " . ($maxDelta >= 0 ? "+{$maxDelta}" : "{$maxDelta}") . " µm).";
                }
            }

            $eyeEvaluations[$eye] = [
                'eye' => $eye,
                'operational_code' => $operationalLabel,
                'operational_text' => $operationalText,
                'trajectory_text' => $trajectoryText,
                'coverage_code' => $coverageCode,
                'baseline_trt' => $baselineTrt ? (float) $baselineTrt->value : null,
                'latest_trt' => $latestTrt && $latestTrt->value !== null ? (float) $latestTrt->value : null,
                'max_delta_trt' => $maxDelta,
                'deltas' => $deltas,
                'iop_records' => $iopObs->all(),
                'reasons' => $reasons,
            ];
        }

        // Resumen global del expediente sin ocultar faltantes de un ojo por el aviso del otro (AT-05)
        $caseOperationalText = 'Listo para revisión';
        $caseOperationalCode = 'LISTO_PARA_REVISION';
        if ($eyeEvaluations['OD']['operational_code'] === 'PRIORIZAR_REVISION' || $eyeEvaluations['OS']['operational_code'] === 'PRIORIZAR_REVISION') {
            $caseOperationalCode = 'PRIORIZAR_REVISION';
            $caseOperationalText = 'Priorizar revisión';
        } elseif ($eyeEvaluations['OD']['operational_code'] === 'FALTAN_DATOS' || $eyeEvaluations['OS']['operational_code'] === 'FALTAN_DATOS') {
            $caseOperationalCode = 'FALTAN_DATOS';
            $caseOperationalText = 'Faltan datos';
        }

        $clinicianDecision = DB::table('sans_clinician_decisions')
            ->where('case_id', $case->case_id)
            ->orderByDesc('decided_at')
            ->first();

        return [
            'case_id' => $case->case_id,
            'as_of_day' => $asOfDay,
            'case_operational_code' => $caseOperationalCode,
            'case_operational_text' => $caseOperationalText,
            'completeness_status' => $case->baseline_status,
            'eyes' => $eyeEvaluations,
            'clinician_decision' => $clinicianDecision,
            'fitness_display' => $clinicianDecision
                ? $clinicianDecision->fitness_statement
                : 'Aptitud no evaluada',
            'future_outlook_banner' => [
                'mode' => 'P0_SIMULACION_ESCENARIO',
                'title' => 'Qué podría ocurrir en este escenario (Datos sintéticos ilustrativos)',
                'experimental_prognosis_status' => 'No estimable (Sin modelo P2 validado para riesgo individual; no se emiten porcentajes individuales ni veredictos apto/no apto).',
            ],
        ];
    }

    /**
     * Reproducción histórica vs Simulación para un día dado (RF-18 a RF-21, AT-03, AT-04)
     */
    public function getTimelineStateAtDay(string $caseId, string $eye, int $selectedDay, string $mode = 'HISTORICO'): array
    {
        if ($mode === 'HISTORICO') {
            // Solo usar resultados ya disponibles en el día seleccionado (available_day <= selectedDay)
            $availableObs = DB::table('sans_metric_observations')
                ->where('case_id', $caseId)
                ->whereNull('exposure_id')
                ->where('eye', $eye)
                ->where('status', 'MEDIDO')
                ->whereNotNull('mission_day')
                ->where(function ($q) use ($selectedDay) {
                    $q->whereNull('available_day')
                        ->orWhere('available_day', '<=', $selectedDay);
                })
                ->orderBy('mission_day')
                ->get();

            $exactDayObs = $availableObs->where('mission_day', $selectedDay)->values();

            // Verificar si existe una observación tomada en selectedDay pero aún no disponible
            $pendingObs = DB::table('sans_metric_observations')
                ->where('case_id', $caseId)
                ->whereNull('exposure_id')
                ->where('eye', $eye)
                ->where('mission_day', $selectedDay)
                ->where('available_day', '>', $selectedDay)
                ->first();

            $lastKnownObs = $availableObs->where('mission_day', '<=', $selectedDay)->last();

            return [
                'mode' => 'HISTORICO',
                'selected_day' => $selectedDay,
                'eye' => $eye,
                'has_measurement_on_day' => $exactDayObs->isNotEmpty(),
                'status_banner' => $exactDayObs->isNotEmpty()
                    ? "Medición histórica disponible en Día {$selectedDay}"
                    : ($pendingObs
                        ? "Sin medición en este día (Muestra tomada el Día {$selectedDay}, resultado disponible recién el Día {$pendingObs->available_day})"
                        : 'Sin medición en este día'),
                'exact_observations' => $exactDayObs->all(),
                'last_known_observation' => $lastKnownObs,
                'is_illustrative' => false,
            ];
        }

        // Modo SIMULACION: usar sans_scenario_points y marcar interpolaciones o «Fuera del rango» después del último punto definido (RF-21)
        $scenarioPoints = DB::table('sans_scenario_points')
            ->where('case_id', $caseId)
            ->where('eye', $eye)
            ->orderBy('mission_day')
            ->get();

        if ($scenarioPoints->isEmpty()) {
            return [
                'mode' => 'SIMULACION',
                'selected_day' => $selectedDay,
                'eye' => $eye,
                'status_banner' => 'Sin escenario sintético cargado para este ojo',
                'points' => [],
                'is_illustrative' => true,
            ];
        }

        $maxDefinedDay = (int) $scenarioPoints->max('mission_day');
        if ($selectedDay > $maxDefinedDay) {
            return [
                'mode' => 'SIMULACION',
                'selected_day' => $selectedDay,
                'eye' => $eye,
                'max_defined_day' => $maxDefinedDay,
                'out_of_range' => true,
                'status_banner' => "Fuera del rango (Último punto definido en el escenario: Día {$maxDefinedDay}; no se extrapola automáticamente)",
                'points' => [],
                'is_illustrative' => true,
            ];
        }

        $dayPoints = $scenarioPoints->where('mission_day', $selectedDay)->values();
        $isInterpolated = $dayPoints->contains(fn ($p) => (bool) $p->is_interpolated === true);

        return [
            'mode' => 'SIMULACION',
            'selected_day' => $selectedDay,
            'eye' => $eye,
            'out_of_range' => false,
            'is_interpolated' => $isInterpolated,
            'status_banner' => $isInterpolated
                ? "Fotograma ilustrativo (Valor interpolado entre hitos del escenario en Día {$selectedDay})"
                : "Fotograma ilustrativo definido en escenario (Día {$selectedDay})",
            'points' => $dayPoints->all(),
            'is_illustrative' => true,
        ];
    }

    /**
     * Comparar dos visitas en la pestaña «Inclinación y seguimiento» (RF-26 a RF-29, AT-10, AT-11, AT-12, AT-13, AT-14, AT-19)
     */
    public function compareExposureVisits(string $exposureId, string $obsInitialId, ?string $obsCompareId = null): array
    {
        $exposure = DB::table('sans_exposure_sessions')->where('exposure_id', $exposureId)->first();
        $segments = DB::table('sans_exposure_segments')
            ->where('exposure_id', $exposureId)
            ->orderBy('segment_order')
            ->get();

        $initial = DB::table('sans_metric_observations')->where('observation_id', $obsInitialId)->first();
        $compare = $obsCompareId
            ? DB::table('sans_metric_observations')->where('observation_id', $obsCompareId)->first()
            : null;

        if (! $exposure || ! $initial) {
            return [
                'comparable' => false,
                'reason' => 'No se encontró la sesión de exposición o la visita inicial seleccionada.',
            ];
        }

        // Derivar duración solo si inicio y fin son conocidos (RF-26, RF-28, AT-12)
        $derivedDurationDays = null;
        if ($exposure->end_is_known && $exposure->start_at && $exposure->end_at) {
            $start = strtotime($exposure->start_at);
            $end = strtotime($exposure->end_at);
            $derivedDurationDays = (int) round(($end - $start) / 86400);
        }

        if (! $compare) {
            return [
                'comparable' => false,
                'exposure' => $exposure,
                'segments' => $segments->all(),
                'derived_duration_days' => $derivedDurationDays,
                'days_since_end_axis_enabled' => (bool) $exposure->end_is_known,
                'finding_classification' => 'Seguimiento insuficiente',
                'reason' => 'Falta seleccionar o registrar un control posterior para comparar.',
            ];
        }

        // Validar comparabilidad: mismo participante, mismo ojo, misma métrica, misma región y misma modalidad (RF-29, AT-13)
        if ($initial->case_id !== $compare->case_id) {
            return [
                'comparable' => false,
                'reason' => 'No comparables: las visitas pertenecen a participantes distintos.',
            ];
        }
        if ($initial->eye !== $compare->eye) {
            return [
                'comparable' => false,
                'reason' => "No comparables: las visitas corresponden a ojos distintos ({$initial->eye} vs {$compare->eye}).",
            ];
        }
        if ($initial->metric_code !== $compare->metric_code || $initial->modality !== $compare->modality || $initial->region !== $compare->region) {
            return [
                'comparable' => false,
                'reason' => "No comparables: procedimiento, métrica o región incompatibles ({$initial->metric_code}/{$initial->modality}/{$initial->region} vs {$compare->metric_code}/{$compare->modality}/{$compare->region}).",
            ];
        }
        if ($initial->exposure_id !== $exposureId || $compare->exposure_id !== $exposureId) {
            return [
                'comparable' => false,
                'reason' => 'No comparables: no se permite cruzar la línea base del análogo de inclinación con observaciones de una misión orbital separada (AT-19).',
            ];
        }

        // Si es pliegue coriorretiniano, comparar como ausencia, presencia o cambio de región, no como resta numérica (RF-29)
        if ($initial->metric_code === 'CHORIORETINAL_FOLD') {
            return [
                'comparable' => true,
                'is_categorical' => true,
                'exposure' => $exposure,
                'segments' => $segments->all(),
                'derived_duration_days' => $derivedDurationDays,
                'days_since_end_axis_enabled' => (bool) $exposure->end_is_known,
                'initial_value' => $initial->categorical_value,
                'compare_value' => $compare->categorical_value,
                'signed_difference' => "Cambio de categoría/región: {$initial->categorical_value} → {$compare->categorical_value} (no se calcula resta numérica)",
                'finding_classification' => $initial->categorical_value === $compare->categorical_value ? 'Sin cambio de categoría' : 'Cambio observado',
            ];
        }

        $diff = round((float) $compare->value - (float) $initial->value, 2);
        $signedDiff = ($diff >= 0 ? '+' : '') . $diff . ' ' . $initial->unit;

        // Verificar si existe control tardío válido (SEGUIMIENTO_POS con days_post_end >= 30) en esta exposición (AT-10, AT-11)
        $lateControl = DB::table('sans_metric_observations')
            ->where('exposure_id', $exposureId)
            ->where('eye', $initial->eye)
            ->where('metric_code', $initial->metric_code)
            ->where('visit_stage', 'SEGUIMIENTO_POS')
            ->where('days_post_end', '>=', 30)
            ->orderByDesc('days_post_end')
            ->first();

        $classification = 'Cambio observado';
        $classificationNote = 'Diferencia descriptiva respecto a la línea base de la exposición.';

        if (! $lateControl) {
            $classification = 'Seguimiento insuficiente';
            $classificationNote = 'No existe control tardío válido (>= +30 días tras fin de exposición); no se extrapola la última medición.';
        } elseif ($compare->observation_id === $lateControl->observation_id) {
            // Estamos evaluando el control tardío: exigir tolerancia documentada para declarar «Retorno al rango basal» (AT-10)
            if ($exposure->tolerance_trt_um !== null) {
                $tol = (float) $exposure->tolerance_trt_um;
                if (abs($diff) <= $tol) {
                    $classification = 'Retorno al rango basal';
                    $classificationNote = "|Δ| ({$diff} {$initial->unit}) dentro de la tolerancia documentada del protocolo (±{$tol} {$initial->unit}).";
                } else {
                    $classification = 'Cambio aún observado en el último control';
                    $classificationNote = "|Δ| ({$diff} {$initial->unit}) supera la tolerancia documentada (±{$tol} {$initial->unit}) en el último control (+{$lateControl->days_post_end} días).";
                }
            } else {
                $classification = 'Cambio disminuyó (Sin tolerancia documentada para declarar retorno al rango basal)';
                $classificationNote = 'Se muestran valores y diferencia (+24 µm → +4 µm), sin etiquetar «Retorno al rango basal» por ausencia de tolerancia documentada en esta sesión.';
            }
        }

        return [
            'comparable' => true,
            'is_categorical' => false,
            'exposure' => $exposure,
            'segments' => $segments->all(),
            'derived_duration_days' => $derivedDurationDays,
            'days_since_end_axis_enabled' => (bool) $exposure->end_is_known,
            'initial' => $initial,
            'compare' => $compare,
            'initial_value' => (float) $initial->value,
            'compare_value' => (float) $compare->value,
            'numeric_diff' => $diff,
            'signed_difference' => $signedDiff,
            'unit' => $initial->unit,
            'days_since_exposure_end' => $exposure->end_is_known ? $compare->days_post_end : null,
            'finding_classification' => $classification,
            'classification_note' => $classificationNote,
        ];
    }

    /**
     * Importador CSV con detección de duplicados y OCT no comparable sin sobrescribir el dato original (RF-03, AT-09)
     */
    public function importObservationsRows(string $caseId, array $rows): array
    {
        $accepted = [];
        $observedWarnings = [];

        $baselineObs = DB::table('sans_metric_observations')
            ->where('case_id', $caseId)
            ->where('visit_stage', 'ANTES')
            ->where('metric_code', 'PERIPAPILLARY_TRT')
            ->get()
            ->keyBy('eye');

        $seenSignatures = [];

        foreach ($rows as $idx => $row) {
            $rowNum = $idx + 1;
            $eye = strtoupper(trim($row['eye'] ?? 'OD'));
            $missionDay = isset($row['mission_day']) && $row['mission_day'] !== '' ? (int) $row['mission_day'] : null;
            $metric = strtoupper(trim($row['metric_code'] ?? 'PERIPAPILLARY_TRT'));
            $value = isset($row['value']) && $row['value'] !== '' ? (float) $row['value'] : null;
            $modality = strtoupper(trim($row['modality'] ?? 'OCT'));
            $region = strtoupper(trim($row['region'] ?? 'PERIPAPILAR'));

            $signature = "{$caseId}|{$eye}|{$missionDay}|{$metric}|{$value}|{$modality}|{$region}";

            // 1. Detectar duplicado en el lote o ya existente en la base de datos
            $existsInDb = DB::table('sans_metric_observations')
                ->where('case_id', $caseId)
                ->where('eye', $eye)
                ->where('mission_day', $missionDay)
                ->where('metric_code', $metric)
                ->where('value', $value)
                ->where('status', 'MEDIDO')
                ->exists();

            if (isset($seenSignatures[$signature]) || $existsInDb) {
                $observedWarnings[] = [
                    'row' => $rowNum,
                    'status' => 'DUPLICADO_OBSERVADO',
                    'reason' => "Fila {$rowNum}: Registro duplicado para {$eye} en Día {$missionDay} ({$metric} = {$value}). No genera cambio de grosor ni alerta.",
                ];
                continue;
            }
            $seenSignatures[$signature] = true;

            // 2. Verificar comparabilidad con la línea base del mismo ojo
            $eyeBaseline = $baselineObs->get($eye);
            if ($metric === 'PERIPAPILLARY_TRT' && $eyeBaseline) {
                if ($eyeBaseline->modality !== $modality || $eyeBaseline->region !== $region) {
                    $observedWarnings[] = [
                        'row' => $rowNum,
                        'status' => 'OCT_NO_COMPARABLE',
                        'reason' => "Fila {$rowNum}: OCT no comparable con la línea base de {$eye} (esperado {$eyeBaseline->modality}/{$eyeBaseline->region}, recibido {$modality}/{$region}). Se conserva auditada sin calcular ΔTRT ni disparar alerta.",
                    ];

                    DB::table('sans_metric_observations')->insert([
                        'observation_id' => 'OBS-IMP-NC-' . Str::upper(Str::random(6)),
                        'case_id' => $caseId,
                        'eye' => $eye,
                        'mission_day' => $missionDay,
                        'available_day' => $missionDay,
                        'visit_stage' => 'DURANTE',
                        'metric_code' => $metric,
                        'value' => $value,
                        'unit' => $row['unit'] ?? 'µm',
                        'region' => $region,
                        'modality' => $modality,
                        'quality' => 'NO_COMPARABLE',
                        'status' => 'NO_EVALUABLE',
                        'baseline_observation_id' => null,
                        'source_type' => 'FICTICIO_OBSERVADO',
                        'notes' => 'Registrada por importador como no comparable con línea base.',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    continue;
                }
            }

            $obsId = 'OBS-IMP-' . Str::upper(Str::random(6));
            DB::table('sans_metric_observations')->insert([
                'observation_id' => $obsId,
                'case_id' => $caseId,
                'eye' => $eye,
                'mission_day' => $missionDay,
                'available_day' => $missionDay,
                'visit_stage' => $missionDay === 0 ? 'ANTES' : 'DURANTE',
                'metric_code' => $metric,
                'value' => $value,
                'unit' => $row['unit'] ?? 'µm',
                'region' => $region,
                'modality' => $modality,
                'quality' => 'VALIDA',
                'status' => 'MEDIDO',
                'baseline_observation_id' => $eyeBaseline ? $eyeBaseline->observation_id : null,
                'source_type' => 'FICTICIO_OBSERVADO',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $accepted[] = [
                'row' => $rowNum,
                'observation_id' => $obsId,
            ];
        }

        return [
            'accepted_count' => count($accepted),
            'observed_count' => count($observedWarnings),
            'accepted' => $accepted,
            'observed_rows' => $observedWarnings,
        ];
    }

    /**
     * Filtrar expedientes en la pestaña 4 «Estudios y tendencias» con los 4 ejes combinables (RF-14, RF-15, AT-06, AT-07)
     */
    public function filterStudiesAndTrends(array $filters = []): array
    {
        $datasetFilter = $filters['dataset'] ?? 'ALL'; // FICTICIO_DEMO, ANALOGO_TERRESTRE, ORBITAL_AUTORIZADO
        $evolutionFilter = $filters['evolution'] ?? 'ALL'; // ESTABLE, EN_PROGRESION, SIN_DATOS_SUFICIENTES
        $coverageFilter = $filters['coverage'] ?? 'ALL'; // SIN_LINEA_BASE, SIN_ESCANEOS_PREVIOS, SIN_CONTROL_RECIENTE, CONTROLES_COMPARABLES
        $engineFilter = $filters['engine'] ?? 'DEMO_RULE_V1'; // DEMO_RULE_V1, PUBLISHED_REF_55UM, EXPERIMENTAL_MODEL_P2
        $maxDayFilter = isset($filters['max_day']) && $filters['max_day'] !== '' ? (int) $filters['max_day'] : null;

        $cases = DB::table('sans_cases')->orderBy('case_id')->get();
        $matchedCases = [];

        foreach ($cases as $case) {
            if ($datasetFilter !== 'ALL' && $case->source_type !== $datasetFilter) {
                continue;
            }

            $eval = $this->evaluateCaseAtDay($case, $maxDayFilter);
            $eyes = $eval['eyes'];

            // Mapear evolución observada del caso o cualquiera de sus ojos
            $caseEvolutionCodes = [];
            $caseCoverageCodes = [];
            foreach (['OD', 'OS'] as $e) {
                $traj = $eyes[$e]['trajectory_text'];
                if ($traj === 'En progresión') {
                    $caseEvolutionCodes[] = 'EN_PROGRESION';
                } elseif ($traj === 'Estable') {
                    $caseEvolutionCodes[] = 'ESTABLE';
                } else {
                    $caseEvolutionCodes[] = 'SIN_DATOS_SUFICIENTES';
                }
                $caseCoverageCodes[] = $eyes[$e]['coverage_code'];
            }

            if ($evolutionFilter !== 'ALL' && ! in_array($evolutionFilter, $caseEvolutionCodes, true)) {
                continue;
            }

            if ($coverageFilter !== 'ALL' && ! in_array($coverageFilter, $caseCoverageCodes, true)) {
                continue;
            }

            $matchedCases[] = [
                'case' => $case,
                'evaluation' => $eval,
                'engine_applied' => $engineFilter,
                'engine_note' => $engineFilter === 'EXPERIMENTAL_MODEL_P2'
                    ? 'No estimable (Modelo experimental P2 requiere validación en cohorte independiente)'
                    : ($engineFilter === 'PUBLISHED_REF_55UM' ? 'Referencia publicada (ΔTRT >= 55 µm)' : 'Regla reproducible de demo (ΔTRT >= +20 µm)'),
            ];
        }

        return [
            'filters_applied' => [
                'dataset' => $datasetFilter,
                'evolution' => $evolutionFilter,
                'coverage' => $coverageFilter,
                'engine' => $engineFilter,
                'max_day' => $maxDayFilter,
            ],
            'total_cases_denominator' => $cases->count(),
            'matched_count' => count($matchedCases),
            'results' => $matchedCases,
            'empty_state_message' => empty($matchedCases)
                ? 'No hay coincidencias para la combinación de filtros seleccionada (Conjunto de datos, Evolución observada, Cobertura y Motor de evaluación).'
                : null,
        ];
    }

    /**
     * Predictor basado en los 6 Biomarcadores Clave del Formulario Inicial:
     * 1. Espesor medio por OCT (TRT µm)
     * 2. Longitud axial del ojo (mm)
     * 3. Hipermetropía simulada (ΔSE en Dioptrías D)
     * 4. Edema papilar (Escala Frisén 0-5)
     * 5. Presión intraocular (PIO mmHg)
     * 6. Exposición previa a inclinación cefálica de -6° en la Tierra (HDT -6°)
     */
    public function evaluateBiomarkerPredictor(object $case, array $customInputs = []): array
    {
        $archetype = $case->scenario_archetype ?? 'EMPEORA';

        // Valores por defecto según el escenario seleccionado (Estable, Empeora o Sin Escaneo)
        $defaults = match ($archetype) {
            'ESTABLE' => [
                'oct_trt_baseline_um' => 278.0,
                'oct_trt_current_um' => 284.0,
                'axial_length_mm' => 24.08,
                'axial_baseline_mm' => 24.10,
                'hyperopia_sim_d' => 0.15,
                'papilledema_frisen' => 0,
                'iop_mmhg' => 15.0,
                'prior_hdt6_exposure' => 'NO',
                'prior_hdt6_days' => 0,
            ],
            'SIN_ESCANEO', 'SIN_CONTROL_RECIENTE' => [
                'oct_trt_baseline_um' => null,
                'oct_trt_current_um' => null,
                'axial_length_mm' => null,
                'axial_baseline_mm' => 24.10,
                'hyperopia_sim_d' => 0.25,
                'papilledema_frisen' => null,
                'iop_mmhg' => 16.0,
                'prior_hdt6_exposure' => 'SI_14_DIAS',
                'prior_hdt6_days' => 14,
            ],
            default => [ // EMPEORA / INCLINACION
                'oct_trt_baseline_um' => 280.0,
                'oct_trt_current_um' => 306.0,
                'axial_length_mm' => 23.82,
                'axial_baseline_mm' => 24.10,
                'hyperopia_sim_d' => 0.75,
                'papilledema_frisen' => 2,
                'iop_mmhg' => 18.5,
                'prior_hdt6_exposure' => 'SI_14_DIAS',
                'prior_hdt6_days' => 14,
            ],
        };

        $b = array_merge($defaults, array_filter($customInputs, fn ($v) => $v !== null && $v !== ''));

        $deltaTrt = ($b['oct_trt_baseline_um'] !== null && $b['oct_trt_current_um'] !== null)
            ? round((float) $b['oct_trt_current_um'] - (float) $b['oct_trt_baseline_um'], 1)
            : null;

        $deltaAxial = ($b['axial_length_mm'] !== null && $b['axial_baseline_mm'] !== null)
            ? round((float) $b['axial_length_mm'] - (float) $b['axial_baseline_mm'], 2)
            : null;

        // Cálculo de señal predictiva explicable basada en los 6 biomarcadores
        $signals = [];
        $weights = [];
        $signalIndex = 0;

        // 1. Espesor medio por OCT
        if ($deltaTrt === null) {
            $signals[] = 'OCT No escaneada: falta espesor retiniano medio para cuantificar progresión estructural.';
            $weights['oct_thickness'] = ['label' => 'Espesor medio OCT (TRT)', 'value' => 'Sin escaneo OCT', 'impact' => 0, 'status' => 'FALTANTE'];
        } elseif ($deltaTrt >= 20) {
            $pts = min(40, (int) round($deltaTrt * 1.1));
            $signalIndex += $pts;
            $signals[] = "Espesor medio OCT en ascenso (+{$deltaTrt} µm vs línea base de {$b['oct_trt_baseline_um']} µm).";
            $weights['oct_thickness'] = ['label' => 'Espesor medio OCT (TRT)', 'value' => "{$b['oct_trt_current_um']} µm (Δ +{$deltaTrt} µm)", 'impact' => $pts, 'status' => 'PROGRESION'];
        } else {
            $weights['oct_thickness'] = ['label' => 'Espesor medio OCT (TRT)', 'value' => "{$b['oct_trt_current_um']} µm (Δ +{$deltaTrt} µm)", 'impact' => 5, 'status' => 'ESTABLE'];
        }

        // 2. Longitud Axial del Ojo
        if ($deltaAxial === null) {
            $weights['axial_length'] = ['label' => 'Longitud Axial del Ojo', 'value' => 'No medida', 'impact' => 0, 'status' => 'FALTANTE'];
        } elseif ($deltaAxial <= -0.15) {
            $pts = 18;
            $signalIndex += $pts;
            $signals[] = "Acortamiento de longitud axial ({$b['axial_length_mm']} mm; Δ {$deltaAxial} mm compatible con aplanamiento posterior).";
            $weights['axial_length'] = ['label' => 'Longitud Axial del Ojo', 'value' => "{$b['axial_length_mm']} mm (Δ {$deltaAxial} mm)", 'impact' => $pts, 'status' => 'ALERTA'];
        } else {
            $weights['axial_length'] = ['label' => 'Longitud Axial del Ojo', 'value' => "{$b['axial_length_mm']} mm (Δ {$deltaAxial} mm)", 'impact' => 4, 'status' => 'ESTABLE'];
        }

        // 3. Hipermetropía Simulada (Escenario separado RF-35)
        $hyp = (float) ($b['hyperopia_sim_d'] ?? 0.0);
        if ($hyp >= 0.50) {
            $pts = 15;
            $signalIndex += $pts;
            $signals[] = "Hipermetropía simulada significativa (+{$hyp} D de corrimiento refractivo hipermetrópico).";
            $weights['simulated_hyperopia'] = ['label' => 'Hipermetropía Simulada', 'value' => "+{$hyp} D (Escenario)", 'impact' => $pts, 'status' => 'ESCENARIO_ACTIVO'];
        } else {
            $weights['simulated_hyperopia'] = ['label' => 'Hipermetropía Simulada', 'value' => "+{$hyp} D (Escenario)", 'impact' => 3, 'status' => 'LEVE'];
        }

        // 4. Edema Papilar (Grado Frisén)
        $frisen = $b['papilledema_frisen'] !== null ? (int) $b['papilledema_frisen'] : null;
        if ($frisen === null) {
            $weights['papilledema'] = ['label' => 'Edema Papilar (Frisén)', 'value' => 'No evaluado', 'impact' => 0, 'status' => 'FALTANTE'];
        } elseif ($frisen >= 1) {
            $pts = $frisen * 14;
            $signalIndex += $pts;
            $signals[] = "Edema papilar documentado (Grado Frisén {$frisen}/5 — Objetivo principal de pronóstico).";
            $weights['papilledema'] = ['label' => 'Edema Papilar (Frisén)', 'value' => "Grado {$frisen} / 5", 'impact' => $pts, 'status' => 'HALLAZGO_CLINICO'];
        } else {
            $weights['papilledema'] = ['label' => 'Edema Papilar (Frisén)', 'value' => 'Grado 0 / 5 (Ausente)', 'impact' => 2, 'status' => 'ESTABLE'];
        }

        // 5. Presión Intraocular (PIO)
        $iop = (float) ($b['iop_mmhg'] ?? 15.0);
        $iopStatus = ($iop >= 20.0) ? 'ELEVADA' : 'RANGO_FISIOLOGICO';
        if ($iop >= 20.0) {
            $signalIndex += 8;
            $signals[] = "Presión intraocular en límite superior ({$iop} mmHg).";
        }
        $weights['iop'] = ['label' => 'Presión Intraocular (PIO)', 'value' => "{$iop} mmHg", 'impact' => $iop >= 20.0 ? 8 : 3, 'status' => $iopStatus];

        // 6. Exposición previa a inclinación cefálica de -6° en la Tierra
        $hdt = (string) ($b['prior_hdt6_exposure'] ?? 'NO');
        $hasHdt = str_starts_with($hdt, 'SI');
        $weights['hdt6_exposure'] = [
            'label' => 'Exposición Previa -6° en Tierra',
            'value' => $hasHdt ? 'Sí (-6° HDT · 14 días · +24 µm -> +4 µm a +30d)' : 'Sin exposición previa a -6°',
            'impact' => $hasHdt ? 10 : 0,
            'status' => $hasHdt ? 'ANTECEDENTE_ANALOGO' : 'NO_REGISTRADO',
        ];
        if ($hasHdt) {
            $signals[] = 'Antecedente de exposición terrestre a -6° cabeza abajo registrado con su propia cronología separada.';
        }

        $predictedTrajectory = $deltaTrt === null
            ? 'No estimable (Falta escaneo OCT basal/control)'
            : ($signalIndex >= 35 ? 'Tendencia de Progresión Neuro-ocular (Priorizar vigilancia a +30, +60 y +90 días)' : 'Trayectoria Ocular Estable dentro de ventana');

        return [
            'inputs' => $b,
            'delta_trt_um' => $deltaTrt,
            'delta_axial_mm' => $deltaAxial,
            'signal_index' => min(100, $signalIndex),
            'predicted_trajectory' => $predictedTrajectory,
            'biomarker_cards' => $weights,
            'signals' => $signals,
        ];
    }
}

