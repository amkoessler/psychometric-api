<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela pivô para a relação N:M entre Questionário e Questão.
     */
    public function up(): void
    {
        Schema::create('questionnaire_question', function (Blueprint $table) {
            // Chave Estrangeira para Questionário
            $table->foreignId('questionnaire_id')
                  ->constrained('questionnaires') // Assume tabela 'questionnaires'
                  ->onDelete('cascade');

            // Chave Estrangeira para Questão
            $table->foreignId('question_id')
                  ->constrained('questions') // Assume tabela 'questions'
                  ->onDelete('cascade');

            // Campo específico do relacionamento (A ORDEM DE EXIBIÇÃO VAI AQUI)
            $table->integer('display_order')->default(0)->comment('Ordem de exibição da questão dentro do questionário específico.');

            // Define a chave primária composta (para garantir unicidade e eficiência)
            $table->primary(['questionnaire_id', 'question_id']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaire_question');
    }
};