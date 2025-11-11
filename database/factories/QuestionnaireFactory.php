<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Questionnaire>
 */
class QuestionnaireFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        
            return [
            // Geração de Código Alpha-numérico (Ex: XA9R7T)

            'code' => Str::upper(Str::random(6)), 
            
            // Título e Descrição aleatórios
            'title' => $this->faker->sentence(3), 
            'description' => $this->faker->paragraph(5),
            'edition' => $this->faker->numberBetween(1, 5) . 'ª Edição',
            'is_active' => $this->faker->boolean(80),
        ];
        
    }
}
