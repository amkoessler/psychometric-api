<?php

namespace Database\Seeders;

use App\Models\Patient; // Não se esqueça deste use
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cria 50 pacientes usando a PatientFactory
        Patient::factory()->count(50)->create();
    }
}