<?php

namespace Database\Seeders;

use App\Models\Questionnaire;
use Illuminate\Database\Seeder;

class QuestionnaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // O método run() chama a função privada no final da classe.
        $questionnaires = $this->getStaticQuestionnaireData();
        $totalCount = count($questionnaires);
        $count = 0;

        echo "Iniciando o seeding de {$totalCount} Questionários...\n";
        
        foreach ($questionnaires as $data) {
            
            // Tenta criar ou atualizar (updateOrCreate) o registro
            $questionnaire = Questionnaire::updateOrCreate( 
                ['code' => $data['code']], // Condição de busca (chave única)
                $data  
            );
            
            $count++;
            
            // Verifica se o modelo foi recém-criado ou atualizado
            $action = $questionnaire->wasRecentlyCreated ? 'CRIADO' : 'ATUALIZADO';
            
            // Mensagem de log no terminal
            echo "  [{$count}/{$totalCount}] {$action}: {$data['code']} - {$data['title']}\n";

        }
        
        echo "Seeding de Questionários concluído com sucesso.\n";
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
            // // [7/10] Escala Wechsler de Inteligência para Adultos
            // [
            //     'code' => 'WAIS-IV',
            //     'title' => 'Escala Wechsler de Inteligência para Adultos (WAIS-IV)',
            //     'description' => 'Avaliação do funcionamento cognitivo e da inteligência (QI) em adultos, abrangendo 4 índices: Compreensão Verbal, Raciocínio Perceptual, Memória Operacional e Velocidade de Processamento.',
            //     'edition' => '4ª Edição',
            //     'is_active' => true,
            // ],
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
            // [12/10] Teste de Apercepção Temática
            [
                'code' => 'DFH-IV',
                'title' => 'Desenho da Figura Humana (versão IV)',
                'description' => 'DFH-IV. Teste gráfico de inteligência não verbal para crianças de 5 a 12 anos. Utiliza o desenho de duas figuras humanas como método objetivo para avaliar o nível de desenvolvimento cognitivo e maturidade intelectual.',
                'edition' => 'Versão Original',
                'is_active' => true, // 
            ],            
            // [11/10] EXCEL - Questionário IFP
            [
                'code' => 'IFP-II',
                'title' => 'Inventário Fatorial de Personalidade (IFP-ii)',
                'description' => 'Mede 15 necessidades psicológicas e 3 escalas de validade.',
                'edition' => '2020',
                'is_active' => true, 
            ],
            // [13/XX] Teste G-36 (Raciocínio)
    [
        'code' => 'G-36',
        'title' => 'Teste Não Verbal de Inteligência G-36',
        'description' => 'Avalia o Fator g da inteligência (raciocínio não verbal e abstrato) em adolescentes e adultos, composto por 36 itens de completamento de série.',
        'edition' => 'Revisada',
        'is_active' => true,
    ],
    // [14/XX] Matrizes Progressivas de Raven
    [
        'code' => 'RAVEN',
        'title' => 'Matrizes Progressivas de Raven (Escala Geral)',
        'description' => 'Mede o componente edutivo do Fator g (capacidade de dar sentido a complexidade, raciocínio lógico e abstrato) em adolescentes e adultos.',
        'edition' => 'Revisada',
        'is_active' => true,
    ],
    // [15/XX] Inventário de Interesses de Holland (RIASEC)
    [
        'code' => 'HOLLAND',
        'title' => 'Inventário de Interesses Profissionais (Modelo RIASEC)',
        'description' => 'Mede as preferências e os interesses vocacionais e profissionais baseados nos seis tipos de personalidade (Realista, Investigativo, Artístico, Social, Empreendedor e Convencional).',
        'edition' => 'Revisada',
        'is_active' => true,
    ],
    // [16/XX] Inventário de Ansiedade de Beck
    [
        'code' => 'BAI',
        'title' => 'Inventário de Ansiedade de Beck (BAI)',
        'description' => 'Instrumento de auto-resposta para avaliar a intensidade dos sintomas de ansiedade, focando em sintomas somáticos, cognitivos e afetivos.',
        'edition' => 'Revisada',
        'is_active' => true,
    ],
    // [17/XX] Teste de Atenção Concentrada (AC)
    [
        'code' => 'AC',
        'title' => 'Teste de Atenção Concentrada',
        'description' => 'Mede a capacidade de selecionar um estímulo relevante em meio a distratores e manter o foco na tarefa por um período de tempo (frequentemente usado em contextos de seleção).',
        'edition' => 'Versão Genérica',
        'is_active' => true,
    ],
    // [18/XX] Escala de Transtorno do Déficit de Atenção e Hiperatividade (ETDAH-II)
    [
        'code' => 'ETDAH-II *',
        'title' => 'Escala de Transtorno do Déficit de Atenção e Hiperatividade (versão II)',
        'description' => 'Instrumento psicométrico para avaliação dos sintomas de TDAH em crianças e adolescentes. Avalia as dimensões de Desatenção, Hiperatividade/Impulsividade, Aspectos Emocionais e Autorregulação.',
        'edition' => 'Versão Atual', // Ajustar conforme o manual que você possui.
        'is_active' => true, 
    ],
    [
        'code' => 'ETDAH-Pais',
        'title' => 'Escala de Avaliação de Comportamentos Infantojuvenis no TDAH em Ambiente Familiar',
        'description' => '<section>
    <h2>📝 Sobre o ETDAH-Pais</h2>
    
    <p>O <strong>ETDAH-Pais</strong> (Escala de Avaliação de Comportamentos Infantojuvenis no TDAH em Ambiente Familiar - Versão para Pais) é um instrumento psicométrico focado na percepção dos responsáveis.</p>

    <h3>🎯 Objetivo Principal</h3>
    <p>Coletar e organizar a percepção dos pais sobre a frequência de diversos comportamentos que podem estar envolvidos no quadro de <strong>Transtorno do Déficit de Atenção e Hiperatividade (TDAH)</strong>.</p>

    <hr>

    <h3>🔑 Características Chave</h3>
    <ul>
        <li><strong>Público-alvo:</strong> Pais de crianças e jovens, geralmente na faixa etária de <strong>2 a 17 anos</strong>.</li>
        <li><strong>Aplicação:</strong> É uma escala de classificação preenchida pelos pais, indicando a frequência com que o comportamento ocorreu (geralmente usando uma escala Likert, de 1 a 6).</li>
        <li><strong>Contexto de Avaliação:</strong> Permite uma avaliação compreensiva e ampla do comportamento da criança ou adolescente, focando no <strong>ambiente familiar</strong>.</li>
    </ul>

    <hr>

    <h3>🧠 Domínios Avaliados</h3>
    <p>O teste avalia diversos domínios do comportamento, que vão além dos critérios básicos de atenção e hiperatividade/impulsividade do DSM-5, oferecendo uma visão mais completa:</p>
    
    <ol>
        <li><strong>Regulação Emocional:</strong> A capacidade de regular o comportamento e as emoções (como explosões de raiva, irritabilidade).</li>
        <li><strong>Atenção:</strong> Dificuldade em manter o foco, distração, dificuldade em observar detalhes.</li>
        <li><strong>Hiperatividade e Impulsividade:</strong> Inquietude, agitação, agir sem pensar.</li>
        <li><strong>Comportamento Social e Opositor:</strong> Dificuldade em manter amizades, implicar com os outros, rebeldia e agressividade.</li>
    </ol>
</section>',
        'edition' => 'Versão Pais',
        'is_active' => true, 
    ],
    
];
    }
}