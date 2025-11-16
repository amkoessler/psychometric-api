<?php

namespace Database\Seeders;

use App\Models\AssessmentArea;
use Illuminate\Database\Seeder;


class AssessmentAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // O método run() chama a função privada no final da classe para obter os dados.
        $areas = $this->getStaticAreaData();

        foreach ($areas as $data) {
            // Assumindo que você usa um modelo chamado AssessmentArea ou similar
            AssessmentArea::updateOrCreate( 
                ['code' => $data['code']], // Condição de busca (chave única)
                $data 
            );
        }
    }

    //---------------------------------------------------------
    // DECLARAÇÃO DOS DADOS ESTÁTICOS NO FINAL DA CLASSE
    //---------------------------------------------------------
    
    /**
     * Retorna um array de dados estáticos com as Grandes Áreas de Avaliação.
     */
    private function getStaticAreaData(): array
    {
        return [
            // [1/7] COG - Cognitivo
            [
                'code' => 'COG',
                'name' => 'Função Cognitiva',
                'description' => 'Avalia processos de pensamento, memória, atenção, raciocínio lógico e funções executivas.',
                'is_active' => true,
            ],
            // [2/7] PER - Personalidade
            [
                'code' => 'PER',
                'name' => 'Traços de Personalidade (Big Five)',
                'description' => 'Estrutura fundamental que abrange os fatores de Neuroticismo, Extroversão, Abertura, Amabilidade e Conscienciosidade.',
                'is_active' => true,
            ],
            // [3/7] PRO - Projetivo
            [
                'code' => 'PRO',
                'name' => 'Projetivo',
                'description' => 'Avaliação de aspectos emocionais, inconscientes e dinâmicos da personalidade através de estímulos ambíguos ou desenhos.',
                'is_active' => true,
            ],
            // [4/7] NEU - Neuropsicológico
            [
                'code' => 'NEU',
                'name' => 'Neuropsicológico',
                'description' => 'Avaliação das Funções Executivas e das relações entre o funcionamento cerebral e o comportamento (memória, atenção, linguagem, etc.).',
                'is_active' => true,
            ],
            // [5/7] APT - Aptidão
            [
                'code' => 'APT',
                'name' => 'Aptidão',
                'description' => 'Avaliação do potencial ou da proficiência do indivíduo em uma habilidade específica (ex: mecânica, numérica, espacial, fluência verbal).',
                'is_active' => true,
            ],
            // [6/7] INT - Interesses
            [
                'code' => 'INT',
                'name' => 'Interesses',
                'description' => 'Avaliação das preferências e motivações do indivíduo por diferentes tipos de atividades, fundamental para orientação vocacional e profissional.',
                'is_active' => true,
            ],
            // [7/7] EMO - Emocional / Clínico
            [
                'code' => 'EMO',
                'name' => 'Regulação Emocional',
                'description' => 'Mede a estabilidade emocional, capacidade de lidar com estresse, ansiedade e sintomas de humor (depressão).',
                'is_active' => true,
            ],
            // [8/7] Área Social e Comportamental
            [
                'code' => 'SOC',
                'name' => 'Habilidades Sociais e Comportamento',
                'description' => 'Foca em traços de extroversão, habilidades interpessoais, comunicação e padrões comportamentais adaptativos.',
                'is_active' => true,
            ],
        ];
    }
}