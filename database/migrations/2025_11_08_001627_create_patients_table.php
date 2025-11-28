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

        // Campos Originais
        // Chave primária padrão (PK)
        $table->id();
        // ID ÚNICO DO PACIENTE (Código de 6 dígitos, letras/números maiúsculos)
        $table->string('patient_code', 6)->unique();
        $table->string('full_name');
        $table->date('birth_date');

        // NOVOS CAMPOS ADICIONADOS (com base na sua lista)
        $table->string('gender')->nullable();
        $table->string('cpf', 11)->nullable();
        $table->string('marital_status')->nullable();
        
        $table->string('nationality')->nullable();
        $table->string('birth_city')->nullable();
        $table->string('profession')->nullable();
        $table->string('current_occupation')->nullable();

        $table->unsignedSmallInteger('birth_order')->nullable();
        $table->unsignedSmallInteger('family_members')->nullable();
        $table->boolean('has_addiction')->default(false);
        $table->text('addiction_details')->nullable();

        $table->string('socioeconomic_level')->nullable();
        $table->string('education_level')->nullable();

        $table->text('referral_reason')->nullable();
        $table->string('referred_by')->nullable();

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