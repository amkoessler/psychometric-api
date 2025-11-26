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
        
        // NOVO: Chave Estrangeira 1:N para Answer (Escala de Resposta)
        $table->foreignId('answer_id')->constrained('answers')->onDelete('restrict');
        
        // 1. Campos Funcionais
        $table->string('question_identifier')->unique()->comment('O rótulo oficial da questão (ex: Q1, Q2).'); 
        
        // 2. Ordem Padrão (Manter no corpo da tabela para ordenação canônica)
        $table->integer('display_order')->default(0); 

        // 3. Conteúdo
        $table->text('question_text');
        
        // response_type e scale_code removidos (substituídos por Answer.php)

        $table->timestamps();

        // ATENÇÃO: A chave 'questionnaire_id' e a restrição de unicidade foram REMOVIDAS.
        // O N:M será tratado na tabela pivô 'questionnaire_question'.
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