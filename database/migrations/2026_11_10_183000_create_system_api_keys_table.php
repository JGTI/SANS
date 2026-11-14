<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key_name')->unique();          // ej: NASA_API_KEY, SCIENTIFIC_ENGINE_TOKEN
            $table->string('service_label');               // ej: NASA Open Data & APIs
            $table->text('key_value')->nullable();         // Valor de la clave
            $table->string('category')->default('General'); // Categoría de servicio
            $table->string('endpoint_url')->nullable();    // Endpoint asociado
            $table->text('description')->nullable();
            $table->boolean('is_secret')->default(true);   // Ocultar caracteres por defecto
            $table->string('status')->default('ACTIVE');   // ACTIVE, OPTIONAL, DISABLED
            $table->timestamp('last_tested_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_api_keys');
    }
};
