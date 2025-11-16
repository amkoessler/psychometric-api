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
        Schema::create('dimensions', function (Blueprint $table) {
            $table->id(); // Cria id (BIGINT - PK - Auto Incremento)
            
            // Campos Funcionais
            $table->string('code', 10)->unique(); // VARCHAR(10) - Código único e CRUCIAL
            $table->string('name', 100);         // VARCHAR(100) - Nome completo
            $table->text('description')->nullable(); // TEXT - Descrição detalhada
            
            // Status
            $table->boolean('is_active')->default(true); // BOOLEAN - Ativo/Inativo
            
            // Campos de Auditoria
            $table->timestamps(); // Cria created_at e updated_at (TIMESTAMP)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dimensions');
    }
};
