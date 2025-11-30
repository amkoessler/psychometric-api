<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            // Adicione a chamada para o PatientSeeder
            PatientSeeder::class,
            AreaSeeder::class,
            DimensionSeeder::class,
            FactorSeeder::class,
            ScaleSeeder::class,
            ResponseOptionSeeder::class,
            AreaDimensionLinkerSeeder::class,
            QuestionnaireSeeder::class,
            QuestionnaireAreaLinkerSeeder::class,
            QuestionSeeder::class,
            DimensionFactorLinkerSeeder::class,
            
        ]);
    }
}
