<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela transacional (pivô com contexto) para rastrear o preenchimento de um Questionário por um Paciente.
     */
    public function up(): void
    {
        Schema::create('questionnaire_sessions', function (Blueprint $table) {
            $table->id();

            // 1. Chaves Estrangeiras (O pivô N:M)

            // Quem preencheu (Paciente)
            $table->foreignId('patient_id') 
                  ->constrained('patients')
                  ->onDelete('cascade'); // Deleta as sessões se o paciente for deletado.

            // Qual questionário mestre foi preenchido
            $table->foreignId('questionnaire_id')
                  ->constrained('questionnaires')
                  ->onDelete('restrict'); // Impede a exclusão do questionário mestre se houver sessões ativas.

            // 2. Campos Transacionais (Contexto da Avaliação)
            
            // Status da avaliação (ex: 'STARTED', 'COMPLETED', 'CANCELLED')
            $table->string('status', 20)->default('STARTED')->index();

            // Rastreamento de tempo
            $table->dateTime('started_at')->useCurrent(); // Quando o preenchimento começou
            $table->dateTime('completed_at')->nullable(); // Quando o preenchimento terminou

            // Score geral final (pode ser calculado mais tarde)
            $table->decimal('total_score', 8, 2)->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaire_sessions');
    }
};