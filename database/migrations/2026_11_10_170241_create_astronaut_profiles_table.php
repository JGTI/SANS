<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('astronaut_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->string('agency', 50)->default('NASA');
            $table->string('mission_name');
            $table->string('role', 100)->default('Mission Specialist');
            $table->date('launch_date')->nullable();
            $table->date('return_date')->nullable();
            $table->unsignedSmallInteger('days_in_microgravity')->default(0);
            $table->string('status', 30)->default('active_mission')->index(); // pre_flight, active_mission, post_flight, grounded
            
            // Valores basales pre-vuelo para cálculo de desviaciones clínicas
            $table->decimal('baseline_se_od', 4, 2)->nullable();
            $table->decimal('baseline_se_os', 4, 2)->nullable();
            $table->decimal('baseline_logmar_od', 4, 3)->nullable();
            $table->decimal('baseline_logmar_os', 4, 3)->nullable();
            
            $table->text('medical_notes')->nullable();
            $table->boolean('is_simulated')->default(true); // Rigor científico
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('astronaut_profiles');
    }
};
