<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            // Gera um Patient ID fictício (6 caracteres, maiúsculos)
            'patient_id' => Str::upper(Str::random(6)),
            'full_name' => fake()->name(),
            // Gera uma data de nascimento entre 18 e 80 anos atrás
            'birth_date' => fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
        ];
    }
}