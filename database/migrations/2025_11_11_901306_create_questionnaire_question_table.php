<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela transacional que representa uma instância de preenchimento.
     * Ela atua como a tabela pivô N:M com contexto extra.
     */
    public function up(): void
    {
        Schema::create('questionnaire_sessions', function (Blueprint $table) {
            $table->id();

            // 1. Chaves Estrangeiras (O pivô N:M)

            // Quem preencheu (Paciente)
            $table->foreignId('patient_id') 
                  ->constrained('patients')
                  ->onDelete('cascade'); // Se o paciente for deletado, a sessão deve ser deletada.

            // Qual questionário mestre foi preenchido
            $table->foreignId('questionnaire_id')
                  ->constrained('questionnaires')
                  ->onDelete('restrict'); // O questionário mestre não deve ser deletado se houver sessões ligadas a ele.

            // 2. Campos Transacionais (Contexto)
            
            // Status da avaliação (ex: 'STARTED', 'COMPLETED', 'CANCELLED')
            $table->string('status', 20)->default('STARTED')->index();

            // Rastreamento de tempo
            $table->dateTime('started_at')->useCurrent(); // Quando o preenchimento começou
            $table->dateTime('completed_at')->nullable(); // Quando o preenchimento terminou

            // Outras informações úteis
            $table->decimal('total_score', 8, 2)->nullable(); // Score geral final (pode ser calculado mais tarde)

            // Garante que um paciente só pode começar um questionário uma vez
            // Se o status permitir múltiplos, essa restrição pode ser removida ou alterada
            $table->unique(['patient_id', 'questionnaire_id']); 

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