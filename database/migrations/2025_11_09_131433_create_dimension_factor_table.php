<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Esta tabela implementa o relacionamento N:M entre Dimension e Factor.
     */
    public function up(): void
    {
        Schema::create('dimension_factor', function (Blueprint $table) {
            
            // 1. Chaves Estrangeiras
            
            // Chave Estrangeira para Dimension
            $table->foreignId('dimension_id') 
                ->constrained('dimensions')
                ->onDelete('cascade'); // Se a dimensão for deletada, as ligações são deletadas
            
            // Chave Estrangeira para Factor
            $table->foreignId('factor_id')
                ->constrained('factors')
                ->onDelete('cascade'); // Se o fator for deletado, as ligações são deletadas

            // 2. Chave Primária
            
            // Define a chave primária composta (garante a unicidade da ligação)
            $table->primary(['dimension_id', 'factor_id']);

            // 3. Opcional: Campos de rastreamento
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dimension_factor');
    }
};