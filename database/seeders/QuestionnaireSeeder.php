<?php

namespace Database\Seeders;

use App\Models\Questionnaire;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class QuestionnaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // O método run() está limpo. Ele chama a função privada no final da classe.
        $questionnaires = $this->getStaticQuestionnaireData();

        foreach ($questionnaires as $data) {
            Questionnaire::create($data);
        }
    }

    //---------------------------------------------------------
    // DECLARAÇÃO DOS DADOS ESTÁTICOS NO FINAL DA CLASSE
    //---------------------------------------------------------
    
    /**
     * Retorna um array de dados estáticos com testes psicológicos.
     */
    private function getStaticQuestionnaireData(): array
    {
        return [
            // [1/10] Inventário de Personalidade NEO-PI-R
            [
                'code' => 'NEO-PI-R',
                'title' => 'Inventário de Personalidade Revisado (NEO-PI-R)',
                'description' => 'Avalia os cinco grandes fatores de personalidade (Big Five): Neuroticismo, Extroversão, Abertura à Experiência, Amabilidade e Conscienciosidade.',
                'edition' => '2002 (Versão Brasileira)',
                'is_active' => true,
            ],
            // [2/10] Inventário de Beck para Depressão
            [
                'code' => 'BDI-II',
                'title' => 'Inventário de Depressão de Beck (BDI-II)',
                'description' => 'Instrumento de auto-resposta amplamente utilizado para medir a intensidade, gravidade e profundidade da depressão em adolescentes e adultos.',
                'edition' => 'Revisada (BDI-II)',
                'is_active' => true,
            ],
            // [3/10] Escala de Ansiedade de Hamilton
            [
                'code' => 'HAM-A',
                'title' => 'Escala de Ansiedade de Hamilton (HAM-A)',
                'description' => 'Escala de avaliação clínica que quantifica a gravidade da ansiedade, incluindo sintomas psíquicos e somáticos.',
                'edition' => '1959',
                'is_active' => true,
            ],
            // [4/10] Escala de Autoestima de Rosenberg
            [
                'code' => 'RSES',
                'title' => 'Escala de Autoestima de Rosenberg (RSES)',
                'description' => 'Mede a avaliação global do indivíduo sobre seu valor próprio (autoestima global), composta por 10 itens.',
                'edition' => '1965',
                'is_active' => true,
            ],
            // [5/10] Teste Palográfico
            [
                'code' => 'PALO',
                'title' => 'Teste Palográfico',
                'description' => 'Teste expressivo de personalidade que avalia ritmo, qualidade e produtividade motora, frequentemente usado para motoristas e seleção de pessoal.',
                'edition' => '2016',
                'is_active' => true,
            ],
            // [6/10] Rorschach
            [
                'code' => 'RORSCHACH',
                'title' => 'Teste de Rorschach (Manchas de Tinta)',
                'description' => 'Método projetivo de avaliação da personalidade e funcionamento emocional, utilizando interpretação de 10 pranchas com manchas simétricas de tinta.',
                'edition' => 'Sistema Exner',
                'is_active' => false, // Exemplo de teste não ativo
            ],
            // [7/10] Escala Wechsler de Inteligência para Adultos
            [
                'code' => 'WAIS-IV',
                'title' => 'Escala Wechsler de Inteligência para Adultos (WAIS-IV)',
                'description' => 'Avaliação do funcionamento cognitivo e da inteligência (QI) em adultos, abrangendo 4 índices: Compreensão Verbal, Raciocínio Perceptual, Memória Operacional e Velocidade de Processamento.',
                'edition' => '4ª Edição',
                'is_active' => true,
            ],
            // [8/10] Inventário de Estilos Parentais
            [
                'code' => 'IEP',
                'title' => 'Inventário de Estilos Parentais (IEP)',
                'description' => 'Mede a percepção que o filho adolescente tem do comportamento dos pais, focando em atitudes e práticas parentais.',
                'edition' => '2005',
                'is_active' => true,
            ],
            // [9/10] Escala de Transtorno de Estresse Pós-Traumático
            [
                'code' => 'PCL-5',
                'title' => 'Lista de Verificação do TEPT (PCL-5)',
                'description' => 'Mede os 20 sintomas do Transtorno de Estresse Pós-Traumático (TEPT) de acordo com o DSM-5.',
                'edition' => 'DSM-5',
                'is_active' => true,
            ],
            // [10/10] Teste de Apercepção Temática
            [
                'code' => 'TAT',
                'title' => 'Teste de Apercepção Temática (TAT)',
                'description' => 'Método projetivo que explora o conteúdo dinâmico da personalidade, como motivos, emoções e conflitos, através de narrativas sobre imagens.',
                'edition' => 'Versão Original',
                'is_active' => false, // Exemplo de teste não ativo
            ],
        ];
    }
}