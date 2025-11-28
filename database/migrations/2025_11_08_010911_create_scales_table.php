<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scales', function (Blueprint $table) {
            $table->id(); // A PK (ID) que será referenciada
            $table->string('code', 50)->unique()->comment('Código único da escala (Ex: LIKERT_4)');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scales');
    }
};