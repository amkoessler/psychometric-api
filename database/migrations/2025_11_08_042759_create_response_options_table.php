<?php
// database/migrations/*_create_response_options_table.php

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
        Schema::create('response_options', function (Blueprint $table) {
            $table->id(); // ID (PK, Auto-Increment)
            
            
            // NOVO: Adicione a chave estrangeira para a nova tabela 'scales'
            $table->foreignId('scale_id')->constrained('scales')->onDelete('cascade');

            // O valor que será usado no cálculo do score
            $table->integer('score_value'); 
            
            // O texto que será exibido na interface do usuário (Ex: 'Discordo Totalmente')
            $table->string('option_text', 255); 
            
            // CRUCIAL: Impede que a mesma escala tenha dois itens com o mesmo valor de score
            $table->unique(['scale_id', 'score_value']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('response_options');
    }
};