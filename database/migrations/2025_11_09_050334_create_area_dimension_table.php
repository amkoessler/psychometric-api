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
        Schema::create('area_dimension', function (Blueprint $table) {
            
            // Chave Estrangeira para AssessmentArea
            $table->foreignId('area_id') 
                ->constrained('areas')
                ->onDelete('cascade'); // Se a área for deletada, as ligações são deletadas
            
            // Chave Estrangeira para Dimension
            $table->foreignId('dimension_id')
                ->constrained('dimensions')
                ->onDelete('cascade'); // Se a dimensão for deletada, as ligações são deletadas

            // Define a chave primária composta (garante que uma ligação não se repita)
            $table->primary(['area_id', 'dimension_id']);

            // Opcional: Campos de rastreamento
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_dimension');
    }
};
