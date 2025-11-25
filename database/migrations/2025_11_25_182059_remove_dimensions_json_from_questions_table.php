<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove a coluna 'dimensions_json'.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Remove a coluna que ligava indiretamente a questão à dimensão.
            // A verificação é uma boa prática para evitar erros em ambientes migrados.
            if (Schema::hasColumn('questions', 'dimensions_json')) {
                $table->dropColumn('dimensions_json');
            }
        });
    }

    /**
     * Adiciona a coluna de volta (em caso de rollback).
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->json('dimensions_json')
                  ->nullable()
                  ->comment('Array JSON com as dimensões/subescalas que a questão avalia.');
        });
    }
};