<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sans_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astronaut_id')->constrained('astronaut_profiles')->cascadeOnDelete();
            $table->foreignId('vision_evaluation_id')->nullable()->constrained('vision_evaluations')->nullOnDelete();
            
            $table->dateTime('observed_at');
            $table->smallInteger('mission_day')->default(0);
            $table->string('eye', 2); // 'OD', 'OS'
            
            // Signos estructurales neurooftalmológicos (Biomarcadores SANS)
            $table->unsignedTinyInteger('frisen_scale_grade')->default(0); // 0 = Normal, 1 = C-shaped halo, 2 = Circumferential halo, 3 = Obscuration of 1+ major vessels, 4 = Total obscuration on disc, 5 = Severe dome-shaped edema
            $table->boolean('choroidal_folds')->default(false); // Pliegues coroideos
            $table->boolean('retinal_folds')->default(false); // Pliegues retinianos
            $table->boolean('posterior_globe_flattening')->default(false); // Aplanamiento posterior del globo
            $table->boolean('cotton_wool_spots')->default(false); // Manchas algodonosas
            $table->unsignedSmallInteger('rnfl_thickness_microns')->nullable(); // OCT: Retinal Nerve Fiber Layer (µm)
            
            // Estratificación de Riesgo Explicable
            $table->string('sans_risk_tier', 20)->default('low'); // low, moderate, high, severe
            $table->json('risk_factors')->nullable(); // Factores transparentes detectados
            $table->string('clinical_action_recommended')->nullable(); // Recomendación médica de vuelo
            
            $table->boolean('is_simulated')->default(true);
            $table->timestamps();
            
            $table->index(['astronaut_id', 'eye', 'mission_day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sans_observations');
    }
};
