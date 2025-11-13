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
            
            // 1. Chave Estrangeira para Questionário
            $table->foreignId('questionnaire_id')->constrained()->onDelete('cascade');
            
            // 2. Identificador Fixo da Questão (rótulo oficial)
            $table->string('question_identifier')->comment('O rótulo oficial da questão (ex: 1, A, Q3b).');
            
            // 3. Ordem de Exibição (para UI/Drag & Drop)
            $table->integer('display_order')->default(0)->comment('Ordem de exibição da questão na UI (suporta drag&drop).');

            // 4. Conteúdo e Tipo de Resposta
            $table->text('question_text');
            $table->string('response_type')->comment('Tipo de interação (ex: LIKERT, MULTIPLE_CHOICE).');

            // 5. Opções e Pontuação (JSON)
            $table->json('options_json')->comment('Array JSON de objetos {text: string, score: integer} para opções e scoring.');

            // 6. Dimensões (JSON para múltiplas abrangências)
            $table->json('dimensions_json')->nullable()->comment('Array JSON com as dimensões/subescalas que a questão avalia (ex: ["COGNITIVO", "SOMATICO"]).');
            
            // Timestamps
            $table->timestamps();

            // Restrição de unicidade: Não pode haver dois identificadores iguais no mesmo questionário.
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