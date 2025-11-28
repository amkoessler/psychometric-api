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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            
            //  Chave Estrangeira 1:N para o Questionário
            // Esta linha resolve o erro SQL original de "column questionnaire_id does not exist".
            $table->foreignId('questionnaire_id')->constrained()->onDelete('cascade');
            
            // Chave Estrangeira 1:N para scale_code (Escala de Resposta)
            $table->foreignId('scale_code')->constrained('answers')->onDelete('restrict');
            
            // 1. Campos Funcionais
            // Removida a unicidade 'question_identifier' para permitir identificadores como '01' em diferentes questionários.
            $table->string('question_identifier')->comment('O rótulo oficial da questão (ex: Q1, Q2).'); 
            
            // 2. Ordem Padrão
            $table->integer('display_order')->default(0); 

            // 3. Conteúdo
            $table->text('question_text');
            
            // response_type e scale_code removidos (substituídos por Answer.php)

            $table->timestamps();

            // Garante que o par (Questionário ID, Identificador da Questão) é único.
            $table->unique(['questionnaire_id', 'question_identifier']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};