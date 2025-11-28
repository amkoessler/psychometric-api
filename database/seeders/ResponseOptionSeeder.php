<?php

namespace Database\Seeders;

use App\Models\ResponseOption;
use App\Models\Scale; // NECESSÁRIO para buscar o ID da escala mestre
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResponseOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Limpa a tabela antes de popular
        DB::table('response_options')->truncate();
        
        // 2. Carrega todas as escalas para mapeamento rápido (code => id)
        $scalesMap = Scale::all()->keyBy('code');

        if ($scalesMap->isEmpty()) {
            echo "ERRO: O ScaleSeeder deve ser executado primeiro. As escalas mestre não foram encontradas na tabela 'scales'. Abortando ResponseOptionSeeder.\n";
            return;
        }

        $options = $this->getResponseOptionsData();
        $totalCount = count($options);
        $count = 0;
        
        echo "Iniciando o seeding de {$totalCount} Opções de Resposta...\n";

        foreach ($options as $data) {
            
            // A. Encontra o ID da Escala usando o 'scale_code'
            $scaleCode = $data['scale_code'];
            $scale = $scalesMap->get($scaleCode);

            if (!$scale) {
                echo "AVISO: Escala com código '{$scaleCode}' não encontrada. Opção '{$data['option_text']}' ignorada.\n";
                continue;
            }

            // B. Prepara os dados finais para ResponseOption (agora usando scale_id)
            $responseOptionData = [
                'scale_id' => $scale->id, // Usa a chave estrangeira (FK)
                'score_value' => $data['score_value'],
                'option_text' => $data['option_text'],
            ];
            
            // C. updateOrCreate para garantir a unicidade de (scale_id, score_value)
            $responseOption = ResponseOption::updateOrCreate([
                'scale_id' => $scale->id, // CRITÉRIO AGORA USA FK ID
                'score_value' => $data['score_value'],
            ], $responseOptionData);
            
            $count++;
            
            // D. LOGGING DETALHADO
            $action = $responseOption->wasRecentlyCreated ? 'CRIADO' : 'ATUALIZADO';
            
            echo "  [{$count}/{$totalCount}] {$action}: [ID: {$scale->id} | {$scaleCode}] {$data['option_text']} (Valor: {$data['score_value']})\n";
        }
        
        echo "Seeding de Opções de Resposta concluído com sucesso.\n";
    }

    /**
     * Retorna o array de dados estáticos para as Opções de Resposta (23 itens).
     * O array MANTÉM 'scale_code' para facilitar a leitura e o mapeamento interno.
     */
    private function getResponseOptionsData(): array
    {
        return [
            // =================================================================
            // INÍCIO DOS VALORES ESPECÍFICOS PARA ETDAH-II (6 PONTOS)
            // =================================================================
            
            // 1. ESCALA LIKERT_6_PONTOS_NORMAL (6 PONTOS) - ETDAH-II:
            // O maior valor (6) resulta no score mais alto (6).
            [
                'scale_code' => 'LIKERT_6_PONTOS_NORMAL',
                'score_value' => 1,
                'option_text' => 'Nunca',
            ],
            [
                'scale_code' => 'LIKERT_6_PONTOS_NORMAL',
                'score_value' => 2,
                'option_text' => 'Muito Raramente',
            ],
            [
                'scale_code' => 'LIKERT_6_PONTOS_NORMAL',
                'score_value' => 3,
                'option_text' => 'Raramente',
            ],
            [
                'scale_code' => 'LIKERT_6_PONTOS_NORMAL',
                'score_value' => 4,
                'option_text' => 'Geralmente',
            ],
            [
                'scale_code' => 'LIKERT_6_PONTOS_NORMAL',
                'score_value' => 5,
                'option_text' => 'Frequentemente',
            ],
            [
                'scale_code' => 'LIKERT_6_PONTOS_NORMAL',
                'score_value' => 6,
                'option_text' => 'Muito Frequentemente',
            ],

            // 2. ESCALA LIKERT_6_PONTOS_INVERSO (6 PONTOS) - ETDAH-II:
            // O maior valor (6) resulta no score mais baixo (1).
            [
                'scale_code' => 'LIKERT_6_PONTOS_INVERSO',
                'score_value' => 6,
                'option_text' => 'Nunca', // Score 1 (Inverso)
            ],
            [
                'scale_code' => 'LIKERT_6_PONTOS_INVERSO',
                'score_value' => 5,
                'option_text' => 'Muito Raramente', // Score 2
            ],
            [
            'scale_code' => 'LIKERT_6_PONTOS_INVERSO',
                'score_value' => 4,
                'option_text' => 'Raramente', // Score 3
            ],
            [
                'scale_code' => 'LIKERT_6_PONTOS_INVERSO',
                'score_value' => 3,
                'option_text' => 'Geralmente', // Score 4
            ],
            [
                'scale_code' => 'LIKERT_6_PONTOS_INVERSO',
                'score_value' => 2,
                'option_text' => 'Frequentemente', // Score 5
            ],
            [
                'scale_code' => 'LIKERT_6_PONTOS_INVERSO',
                'score_value' => 1,
                'option_text' => 'Muito Frequentemente', // Score 6 (Inverso)
            ],
            
            // =================================================================
            // FIM DOS VALORES ESPECÍFICOS PARA ETDAH-II
            // =================================================================

            // =================================================================
            // 3. ESCALA LIKERT_4 (4 PONTOS)
            // =================================================================
            [
                'scale_code' => 'LIKERT_4',
                'score_value' => 1,
                'option_text' => 'Discordo Totalmente',
            ],
            [
                'scale_code' => 'LIKERT_4',
                'score_value' => 2,
                'option_text' => 'Discordo Parcialmente',
            ],
            [
                'scale_code' => 'LIKERT_4',
                'score_value' => 3,
                'option_text' => 'Concordo Parcialmente',
            ],
            [
                'scale_code' => 'LIKERT_4',
                'score_value' => 4,
                'option_text' => 'Concordo Totalmente',
            ],

            // =================================================================
            // 4. ESCALA LIKERT_5 (5 PONTOS)
            // =================================================================
            [
                'scale_code' => 'LIKERT_5',
                'score_value' => 0,
                'option_text' => 'Nunca',
            ],
            [
                'scale_code' => 'LIKERT_5',
                'score_value' => 1,
                'option_text' => 'Raramente',
            ],
            [
                'scale_code' => 'LIKERT_5',
                'score_value' => 2,
                'option_text' => 'Às Vezes',
            ],
            [
                'scale_code' => 'LIKERT_5',
                'score_value' => 3,
                'option_text' => 'Frequentemente',
            ],
            [
                'scale_code' => 'LIKERT_5',
                'score_value' => 4,
                'option_text' => 'Sempre',
            ],
            
            // =================================================================
            // 5. ESCALA YES_NO (2 PONTOS)
            // =================================================================
            [
                'scale_code' => 'YES_NO',
                'score_value' => 0,
                'option_text' => 'Não',
            ],
            [
                'scale_code' => 'YES_NO',
                'score_value' => 1,
                'option_text' => 'Sim',
            ],
        ];
    }
}