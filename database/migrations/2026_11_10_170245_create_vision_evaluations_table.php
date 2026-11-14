<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vision_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astronaut_id')->constrained('astronaut_profiles')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->dateTime('evaluated_at');
            $table->smallInteger('mission_day')->default(0); // 0 = Basal pre-vuelo, 30 = FD30, etc.
            $table->string('flight_phase', 30)->default('in_flight'); // pre_flight, in_flight, post_flight
            $table->string('eye', 2); // 'OD' (derecho), 'OS' (izquierdo)
            
            // Refracción Clínica
            $table->decimal('sphere', 4, 2); // Esfera (Dioptrías)
            $table->decimal('cylinder', 4, 2)->default(0.00); // Cilindro (Dioptrías)
            $table->unsignedSmallInteger('axis')->default(0); // Eje (0° - 180°)
            $table->decimal('spherical_equivalent', 4, 2); // SE = SPH + CYL/2
            $table->decimal('hyperopic_shift', 4, 2)->default(0.00); // Variación delta vs basal
            
            // Agudeza Visual
            $table->unsignedSmallInteger('snellen_numerator')->default(20);
            $table->unsignedSmallInteger('snellen_denominator')->default(20); // 20/20, 20/40, etc.
            $table->decimal('logmar', 4, 3); // log10(denominator/numerator)
            $table->boolean('with_correction')->default(false);
            
            // Presión y Síntomas
            $table->decimal('intraocular_pressure', 4, 1)->nullable(); // mmHg (normal 10-21)
            $table->json('reported_symptoms')->nullable();
            $table->text('clinical_notes')->nullable();
            $table->boolean('is_simulated')->default(true);
            
            $table->timestamps();
            
            // Índices para consultas longitudinales rápidas
            $table->index(['astronaut_id', 'eye', 'mission_day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vision_evaluations');
    }
};
