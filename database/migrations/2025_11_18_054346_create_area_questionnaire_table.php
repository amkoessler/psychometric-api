<?php
// database/migrations/XXXX_XX_XX_XXXXXX_create_area_questionnaire_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_questionnaire', function (Blueprint $table) {
            // Chave estrangeira para Questionário
            $table->foreignId('questionnaire_id')
                  ->constrained('questionnaires') // Cria a constraint de chave estrangeira (assumindo a tabela 'questionnaires')
                  ->onDelete('cascade'); // Se o questionário for deletado, as associações dele são deletadas.
            
            // Chave estrangeira para Área de Avaliação
            $table->foreignId('area_id')
                  ->constrained('areas') // (assumindo a tabela 'area')
                  ->onDelete('cascade'); // Se a área for deletada, as associações dela são deletadas.
            
            // Define uma chave primária composta para garantir a unicidade da associação.
            $table->primary(['questionnaire_id', 'area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_questionnaire');
    }
};