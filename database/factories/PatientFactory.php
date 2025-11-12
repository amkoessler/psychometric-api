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



            // NOVOS CAMPOS ADICIONADOS COM DADOS FICTÍCIOS
            'gender' => $this->faker->randomElement(['Masculino', 'Feminino', 'Outro']),
            'cpf' => $this->faker->unique()->numerify('###########'), // 11 dígitos numéricos
            'marital_status' => $this->faker->randomElement(['Solteiro', 'Casado', 'Divorciado', 'Viúvo']),
            
            'nationality' => 'Brasileira', // Definindo um valor padrão simples
            'birth_city' => $this->faker->city,
            'profession' => $this->faker->jobTitle,
            'current_occupation' => $this->faker->jobTitle,

            'birth_order' => $this->faker->numberBetween(1, 5),
            'family_members' => $this->faker->numberBetween(2, 8),
            
            'has_addiction' => $this->faker->boolean(30), // 30% de chance de ser true
            // Preenche o detalhe apenas se 'has_addiction' for true
            'addiction_details' => $this->faker->optional(0.5)->sentence(4), // 50% de chance de preencher

            'socioeconomic_level' => $this->faker->randomElement(['A', 'B', 'C', 'D']),
            'education_level' => $this->faker->randomElement(['Fundamental Incompleto', 'Médio Completo', 'Superior Incompleto', 'Pós-graduado']),

            'referral_reason' => $this->faker->paragraph(2),
            'referred_by' => $this->faker->name,
        ];
    }
}