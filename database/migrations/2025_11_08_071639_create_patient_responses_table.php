<?php
// FECTH: database/migrations/2025_11_29_000000_create_patient_responses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela para armazenar as respostas individuais do paciente.
     * Ela liga uma Sessão, uma Questão e a Opção de Resposta escolhida.
     */
    public function up(): void
    {
        Schema::create('patient_responses', function (Blueprint $table) {
            // Não precisa de ID próprio, usaremos uma chave composta.
            // $table->id(); 

            // 1. Chave Estrangeira para a Sessão (QuestionnaireSession)
            // CRÍTICO: Liga a resposta à instância de preenchimento.
            $table->foreignId('questionnaire_session_id')
                  ->constrained('questionnaire_sessions')
                  ->onDelete('cascade'); // Se a sessão for deletada, as respostas devem ser deletadas.

            // 2. Chave Estrangeira para a Questão (Questions)
            // CRÍTICO: Liga a resposta à questão que foi feita.
            $table->foreignId('question_id')
                  ->constrained('questions')
                  ->onDelete('restrict'); // Se houver respostas, a questão não deve ser deletada.

            // 3. Chave Estrangeira para a Opção de Resposta (ResponseOption)
            // CRÍTICO: Liga a resposta à opção específica que foi escolhida.
            $table->foreignId('response_option_id')
                  ->constrained('response_options')
                  ->onDelete('restrict'); // Se a opção de resposta estiver em uso, não deve ser deletada.

            // Define a chave primária composta (garante que uma questão só é respondida uma vez por sessão)
            $table->primary(['questionnaire_session_id', 'question_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_responses');
    }
};