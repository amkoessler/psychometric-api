<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Scale; // Assumindo que o Model Scale foi criado

class ScaleSeeder extends Seeder
{
    /**
     * Popula a tabela 'scales' com os códigos únicos de escala.
     */
    public function run(): void
    {
        $scales = [
            [
                'code' => 'LIKERT_6_PONTOS_NORMAL',
                'name' => 'Escala Likert 6 Pontos (Normal)',
                'description' => 'Escala de 6 pontos sem ponto neutro, pontuação crescente (1=Nunca, 6=Sempre).',
            ],
            [
                'code' => 'LIKERT_6_PONTOS_INVERSO',
                'name' => 'Escala Likert 6 Pontos (Inversa)',
                'description' => 'Escala de 6 pontos sem ponto neutro, pontuação decrescente (6=Nunca, 1=Sempre).',
            ],
            [
                'code' => 'LIKERT_4',
                'name' => 'Escala Likert 4 Pontos',
                'description' => 'Escala de 4 pontos (Discordo Totalmente a Concordo Totalmente).',
            ],
            [
                'code' => 'LIKERT_5',
                'name' => 'Escala Likert 5 Pontos',
                'description' => 'Escala de 5 pontos com ponto neutro (0=Nunca, 4=Sempre).',
            ],
            [
                'code' => 'YES_NO',
                'name' => 'Escala Binária (Sim/Não)',
                'description' => 'Escala de dois pontos (0=Não, 1=Sim).',
            ],
        ];

        $totalCount = count($scales);
        echo "Iniciando o seeding de {$totalCount} Escalas...\n";

        foreach ($scales as $scale) {
            Scale::firstOrCreate(['code' => $scale['code']], $scale);
            echo "  [CRIADO]: [{$scale['code']}] {$scale['name']}\n";
        }
        
        echo "Seeding de Escalas concluído com sucesso.\n";
    }
}