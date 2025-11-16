<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assessment_areas', function (Blueprint $table) {
            $table->id(); // id (BIGINT - PK - Auto Incremento)
            
            // Campos Funcionais
            $table->string('code', 5)->unique(); // VARCHAR(5) - Ex: COG, PER
            $table->string('name', 50);         // VARCHAR(50) - Ex: Cognitivo
            $table->text('description')->nullable(); // TEXT
            
            // Status
            $table->boolean('is_active')->default(true);
            
            // Campos de Auditoria
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_areas');
    }
};
