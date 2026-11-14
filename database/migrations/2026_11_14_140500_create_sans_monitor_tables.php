<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. ProtocolVersion: Catálogo curado de protocolos, consensos, artículos y escenarios
        Schema::create('sans_protocol_versions', function (Blueprint $table) {
            $table->id();
            $table->string('protocol_id', 64)->index();
            $table->string('version_id', 64)->unique();
            $table->string('title');
            $table->string('issuer', 160);
            $table->string('source_url')->nullable();
            $table->string('doi', 120)->nullable();
            // PROTOCOLO_OPERATIVO, CONSENSO, ARTICULO_INVESTIGACION, ESCENARIO_DEMO
            $table->string('document_type', 48)->index();
            $table->date('publication_date')->nullable();
            // VERIFICADO, PENDIENTE_VERIFICACION, ARCHIVADO
            $table->string('verified_status', 40)->default('VERIFICADO');
            $table->json('phase_tags')->nullable(); // ["PRE_VUELO", "EN_VUELO", "POSVUELO", "ANALOGO_INCLINADO"]
            $table->string('population', 160)->nullable();
            $table->json('eligibility_rules')->nullable(); // Criterios documentados de aplicabilidad y tolerancias
            $table->string('supersedes_version_id', 64)->nullable()->index();
            $table->boolean('relation_verified')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Case y Mission: Ficha con ID pseudónimo o ficticio
        Schema::create('sans_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_id', 64)->unique();
            $table->string('mission_id', 64)->nullable()->index();
            // FICTICIO_DEMO, ANALOGO_TERRESTRE, ORBITAL_AUTORIZADO
            $table->string('source_type', 40)->default('FICTICIO_DEMO')->index();
            // PRE_VUELO, EN_VUELO, POSVUELO, PRE_EXPOSICION, EN_EXPOSICION, POSEXPOSICION
            $table->string('mission_phase', 40)->index();
            $table->integer('planned_duration_days')->nullable();
            $table->string('planned_duration_state', 24)->default('VALOR'); // VALOR, DESCONOCIDO, NO_APLICA
            $table->integer('previous_flights')->nullable();
            $table->string('previous_flights_state', 24)->default('VALOR'); // VALOR, DESCONOCIDO, NO_APLICA
            $table->string('reason_for_review');
            // COMPLETA, PARCIAL, SIN_LINEA_BASE, DESCONOCIDO, NO_APLICA
            $table->string('baseline_status', 40)->default('SIN_LINEA_BASE')->index();
            $table->string('applied_protocol_version_id', 64)->nullable();
            $table->string('applied_protocol_state', 24)->default('DESCONOCIDO'); // CONOCIDO, DESCONOCIDO, NO_APLICA
            $table->string('scenario_archetype', 48)->nullable()->index(); // ESTABLE, EMPEORA, SIN_ESCANEO, SIN_CONTROL_RECIENTE, INCLINACION
            $table->date('mission_start_date')->nullable();
            $table->date('mission_end_date')->nullable(); // Fecha de regreso / fin (nullable si desconocido)
            $table->json('eye_history')->nullable(); // Antecedentes oculares relevantes por ojo
            $table->timestamps();
        });

        // 3. CaseProtocol: Vínculo entre ficha y versión de protocolo (aplicado, sugerido, adoptado)
        Schema::create('sans_case_protocols', function (Blueprint $table) {
            $table->id();
            $table->string('case_id', 64)->index();
            $table->string('version_id', 64)->index();
            // APLICADO_PREVIO, SUGERIDO_SEGUIMIENTO, ADOPTADO_REVISOR
            $table->string('relation', 40)->index();
            $table->json('matched_criteria')->nullable();
            $table->json('missing_criteria')->nullable();
            $table->unsignedBigInteger('reviewer_id')->nullable();
            $table->string('reviewer_name')->nullable();
            $table->string('decision_reason')->nullable();
            $table->timestamp('decision_date')->nullable();
            $table->timestamps();
        });

        // 4. Observation: Mediciones por ojo y misión (valores faltantes son NULL con status explícito)
        Schema::create('sans_metric_observations', function (Blueprint $table) {
            $table->id();
            $table->string('observation_id', 64)->unique();
            $table->string('case_id', 64)->index();
            $table->string('mission_id', 64)->nullable()->index();
            $table->string('exposure_id', 64)->nullable()->index();
            $table->string('eye', 8)->index(); // OD, OS
            $table->date('observed_at')->nullable();
            $table->date('available_at')->nullable();
            $table->integer('mission_day')->nullable()->index();
            $table->integer('available_day')->nullable()->index();
            $table->integer('days_post_end')->nullable()->index(); // +0, +30, +60, +90 tras aterrizaje o fin de exposición
            // ANTES, DURANTE, FIN_TEMPRANO, SEGUIMIENTO_POS
            $table->string('visit_stage', 32)->default('DURANTE')->index();
            // AXIAL_LENGTH, POSTERIOR_FLATTENING, SPHERICAL_EQ, CHOROID_THICKNESS, CHORIORETINAL_FOLD, PERIPAPILLARY_TRT, RNFL, FRISEN_GRADE, IOP
            $table->string('metric_code', 48)->index();
            $table->decimal('value', 10, 3)->nullable(); // NULL si no medido o si es categórico
            $table->string('categorical_value', 80)->nullable(); // Ej. PRESENTE, AUSENTE, INDETERMINADO, NO_EVALUADO, REGION_TEMPORAL
            $table->string('unit', 24)->nullable(); // mm, D, um, mmHg, grade, category
            $table->string('region', 64)->default('GLOBAL'); // GLOBAL, PERIPAPILAR, MACULAR, POLO_POSTERIOR
            $table->string('modality', 64)->default('OCT'); // OCT, BIOMETRIA_OPTICA, REFRACCION, FONDO_OJO, TONOMETRIA, RM
            $table->string('device_id', 64)->nullable();
            $table->string('protocol_version_id', 64)->nullable();
            $table->string('quality', 32)->default('VALIDA'); // VALIDA, BAJA_CALIDAD, INCOMPLETA, NO_COMPARABLE
            // MEDIDO, SIN_MEDICION, NO_EVALUABLE, DUPLICADO_RECHAZADO
            $table->string('status', 32)->default('MEDIDO')->index();
            $table->string('baseline_observation_id', 64)->nullable()->index();
            // OBSERVADO, FICTICIO_OBSERVADO
            $table->string('source_type', 40)->default('FICTICIO_OBSERVADO');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. ScenarioPoint: Puntos sintéticos del simulador (nunca se guardan como observación clínica)
        Schema::create('sans_scenario_points', function (Blueprint $table) {
            $table->id();
            $table->string('scenario_id', 64)->index();
            $table->string('case_id', 64)->nullable()->index();
            $table->integer('mission_day')->index();
            $table->string('eye', 8)->index(); // OD, OS
            $table->string('metric_code', 48)->index();
            $table->decimal('scenario_value', 10, 3)->nullable();
            $table->string('categorical_value', 80)->nullable();
            $table->string('unit', 24)->nullable();
            // SAGITAL_GLOBO, FONDO_DISCO, CORTE_OCT, REFRACCION_ETIQUETA
            $table->string('visual_layer', 48)->default('CORTE_OCT');
            $table->boolean('is_interpolated')->default(false);
            $table->timestamps();
        });

        // 6. Assessment: Salidas operativas del motor de reglas (Listo para revisión, Faltan datos, Priorizar revisión)
        Schema::create('sans_assessments', function (Blueprint $table) {
            $table->id();
            $table->string('case_id', 64)->index();
            $table->string('eye', 12)->default('BILATERAL'); // OD, OS, BILATERAL
            // LISTO_PARA_REVISION, FALTAN_DATOS, PRIORIZAR_REVISION
            $table->string('assessment_state', 40)->index();
            // ESTABLE, EN_PROGRESION, SIN_DATOS_SUFICIENTES
            $table->string('trajectory_label', 40)->default('SIN_DATOS_SUFICIENTES');
            // COMPLETA, SIN_LINEA_BASE, SIN_ESCANEOS_PREVIOS, SIN_CONTROL_RECIENTE, CONTROLES_COMPARABLES
            $table->string('coverage_label', 48)->default('SIN_LINEA_BASE');
            $table->json('reason_codes')->nullable();
            $table->json('input_ids')->nullable();
            $table->string('rule_or_model_version', 64)->default('DEMO_RULE_V1');
            $table->integer('as_of_day')->nullable();
            $table->text('explanation')->nullable();
            $table->timestamps();
        });

        // 7. ClinicianDecision: Dictamen humano separado del motor de reglas (RF-11)
        Schema::create('sans_clinician_decisions', function (Blueprint $table) {
            $table->id();
            $table->string('case_id', 64)->index();
            $table->unsignedBigInteger('clinician_user_id')->nullable();
            $table->string('clinician_name');
            $table->string('clinician_role', 64)->default('flight-surgeon');
            $table->string('evaluation_scope');
            $table->string('protocol_version_id', 64)->nullable();
            $table->text('documented_followup');
            $table->string('fitness_statement')->nullable(); // Solo escrito por el médico autorizado
            $table->timestamp('decided_at');
            $table->timestamps();
        });

        // 8. ExposureSession y ExposureSegment: Exposiciones inclinadas separadas de la misión (RF-26)
        Schema::create('sans_exposure_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('exposure_id', 64)->unique();
            $table->string('case_id', 64)->index();
            $table->string('mission_id', 64)->nullable();
            $table->string('source_type', 40)->default('ANALOGO_TERRESTRE');
            $table->string('protocol_version_id', 64)->nullable();
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable(); // NULL si fin desconocido
            $table->boolean('end_is_known')->default(true);
            $table->decimal('primary_tilt_angle_deg', 5, 2)->nullable(); // Magnitud, ej. 6.00
            // CABEZA_ABAJO, CABEZA_ARRIBA, HORIZONTAL, DESCONOCIDO
            $table->string('tilt_direction', 32)->default('CABEZA_ABAJO');
            $table->string('angle_reference', 64)->default('HORIZONTAL_0_DEG');
            $table->decimal('tolerance_trt_um', 6, 2)->nullable(); // Tolerancia documentada opcional para declarar retorno al rango basal
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sans_exposure_segments', function (Blueprint $table) {
            $table->id();
            $table->string('exposure_id', 64)->index();
            $table->integer('segment_order')->default(1);
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->decimal('tilt_angle_deg', 5, 2);
            $table->string('tilt_direction', 32)->default('CABEZA_ABAJO');
            $table->string('angle_reference', 64)->default('HORIZONTAL_0_DEG');
            $table->string('provenance', 120)->default('FICTICIO_DEMO');
            $table->timestamps();
        });

        Schema::create('sans_exposure_observation_links', function (Blueprint $table) {
            $table->id();
            $table->string('exposure_id', 64)->index();
            $table->string('observation_id', 64)->index();
            // ANTES, DURANTE, PRIMER_CONTROL_POS, SEGUIMIENTO
            $table->string('exposure_phase', 40)->index();
            $table->string('baseline_observation_id', 64)->nullable();
            $table->integer('days_since_exposure_end')->nullable(); // NULL si fin desconocido
            $table->timestamps();
        });

        // 9. FollowUpPlan, FollowUpVisit y FollowUpAction: Hitos +30, +60 y +90 días (RF-31 a RF-34)
        Schema::create('sans_followup_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_id', 64)->unique();
            $table->string('case_id', 64)->index();
            $table->string('mission_or_exposure_id', 64)->index();
            $table->string('reference_type', 24)->default('MISION'); // MISION, EXPOSICION
            $table->date('end_reference_date')->nullable(); // Día +0
            $table->string('protocol_version_id', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('sans_followup_visits', function (Blueprint $table) {
            $table->id();
            $table->string('plan_id', 64)->index();
            $table->string('case_id', 64)->index();
            $table->integer('target_milestone_day'); // 30, 60, 90
            $table->string('window_label', 40)->nullable(); // Ej. "+25 a +35 días"
            $table->date('planned_date')->nullable();
            $table->date('actual_date')->nullable();
            // PENDIENTE, REALIZADO, VENCIDO_SIN_DATO, NO_EVALUABLE, SIN_MEDICION
            $table->string('status', 32)->default('PENDIENTE')->index();
            // CAMBIO_OBSERVADO, RETORNO_RANGO_BASAL, CAMBIO_AUN_OBSERVADO, SEGUIMIENTO_INSUFICIENTE, SIN_MEDICION
            $table->string('finding_summary_state', 48)->default('SEGUIMIENTO_INSUFICIENTE');
            $table->string('data_source_badge', 32)->default('OBSERVADO'); // OBSERVADO, SIMULADO, PRONOSTICO_EXPERIMENTAL
            $table->string('responsible_role', 64)->nullable();
            $table->json('linked_observation_ids')->nullable();
            $table->timestamps();
        });

        Schema::create('sans_followup_actions', function (Blueprint $table) {
            $table->id();
            $table->string('case_id', 64)->index();
            $table->integer('milestone_day')->nullable(); // NULL (pre/en vuelo) o 30, 60, 90
            $table->string('suggested_action');
            $table->string('reason');
            $table->string('source_observation_id', 64)->nullable();
            // SUGERIDA_DEMO, CONFIRMADA, MODIFICADA, DESCARTADA
            $table->string('review_status', 32)->default('SUGERIDA_DEMO');
            $table->string('responsible_name', 120)->nullable();
            $table->text('recorded_decision')->nullable();
            $table->timestamps();
        });

        // 10. ContextRecord: Los 5 dominios de contexto separados de las mediciones oculares (RF-35, RF-36)
        Schema::create('sans_context_records', function (Blueprint $table) {
            $table->id();
            $table->string('case_id', 64)->index();
            // NEUROLOGICO, METABOLICO, CARDIOVASCULAR, AMBIENTAL, CONDUCTUAL
            $table->string('domain', 40)->index();
            $table->string('variable_name', 120);
            $table->string('value_or_category', 160)->nullable();
            $table->string('unit', 40)->nullable();
            $table->string('recorded_window', 80)->nullable();
            $table->string('phase', 40)->nullable();
            $table->string('method_or_posture', 120)->nullable();
            $table->string('source', 120)->default('FICTICIO_DEMO');
            // DISPONIBLE, NO_MEDIDO, NO_DISPONIBLE
            $table->string('availability_status', 32)->default('DISPONIBLE');
            $table->text('research_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sans_context_records');
        Schema::dropIfExists('sans_followup_actions');
        Schema::dropIfExists('sans_followup_visits');
        Schema::dropIfExists('sans_followup_plans');
        Schema::dropIfExists('sans_exposure_observation_links');
        Schema::dropIfExists('sans_exposure_segments');
        Schema::dropIfExists('sans_exposure_sessions');
        Schema::dropIfExists('sans_clinician_decisions');
        Schema::dropIfExists('sans_assessments');
        Schema::dropIfExists('sans_scenario_points');
        Schema::dropIfExists('sans_metric_observations');
        Schema::dropIfExists('sans_case_protocols');
        Schema::dropIfExists('sans_cases');
        Schema::dropIfExists('sans_protocol_versions');
    }
};
