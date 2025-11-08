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
        Schema::create('patients', function (Blueprint $table) {
            // Chave primária padrão (PK)
            $table->id();

            // ID ÚNICO DO PACIENTE (Código de 6 dígitos, letras/números maiúsculos)
            $table->string('patient_id', 6)->unique();

            // Dados do Paciente
            $table->string('full_name');
            $table->date('birth_date');

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};