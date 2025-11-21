<?php 
// database/migrations/*_update_questions_table_for_response_scales.php

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
        Schema::table('questions', function (Blueprint $table) {
            // Remove a coluna antiga que armazenava as opções em JSON
            $table->dropColumn('options_json');
            
            // Adiciona a nova coluna que fará a ligação com a tabela response_options
            // Usaremos a coluna após a response_type para organização
            $table->string('scale_code', 50)->nullable()->after('response_type');
            
            // Adiciona um índice para otimizar a busca por escala
            $table->index('scale_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Desfazendo as alterações: 
            $table->dropIndex(['scale_code']); // Remove o índice
            $table->dropColumn('scale_code'); // Remove a coluna de código
            
            // Recria a coluna JSON (se necessário para rollback, mas vamos deixá-la aqui)
            // $table->json('options_json')->nullable()->after('response_type'); 
        });
    }
};