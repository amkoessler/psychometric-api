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
        Schema::create('factors', function (Blueprint $table) {
            $table->id(); // Coluna da chave primária (ID)
            
            // Campos que você definiu:
            $table->string('code')->unique(); // Código conciso e único do Fator
            $table->string('name'); // Nome completo e legível
            $table->text('description')->nullable(); // Definição teórica e clínica
            $table->boolean('is_active')->default(true); // Status (ativo por padrão)

            // Relacionamento 1:N com Questões (Chave Estrangeira que a Questão usará)
            // Não incluímos aqui pois a Questão pertencerá ao Fator (factor_id estará na tabela questions)
            
            // Relacionamentos N:M (usarão tabelas pivô separadas, não nesta migration)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factors');
    }
};