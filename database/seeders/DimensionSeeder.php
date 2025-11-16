<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Dimension; // Seu Model de Dimensão

class DimensionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtém a lista completa e atualizada de dimensões
        $dimensions = $this->getStaticDimensionData();

        foreach ($dimensions as $data) {
            // Usa updateOrCreate para garantir que novos registros sejam criados
            // e existentes sejam atualizados com a descrição e nome mais recentes.
            Dimension::updateOrCreate(
                ['code' => $data['code']], // Chave de busca: code
                $data                      // Dados para criação/atualização
            );
        }
    }

    /**
     * Retorna o array de dados estáticos para as Dimensões (23 itens).
     */
    private function getStaticDimensionData(): array
    {
        return [
            // --- COGNITIVO / NEUROPSICOLÓGICO ---
            [
                'code' => 'FG',
                'name' => 'Fator G (Inteligência Geral)',
                'description' => 'Capacidade de raciocínio, resolução de problemas e adaptabilidade intelectual. Representa a inteligência fluida e cristalizada.',
                'is_active' => true,
            ],
            [
                'code' => 'RL',
                'name' => 'Raciocínio Lógico',
                'description' => 'Habilidade de pensar de forma coerente e dedutiva, identificando padrões, relações e sequências em estímulos não verbais.',
                'is_active' => true,
            ],
            [
                'code' => 'RA',
                'name' => 'Raciocínio Abstrato',
                'description' => 'Capacidade de trabalhar com conceitos e símbolos não concretos e formar princípios gerais a partir de exemplos específicos.',
                'is_active' => true,
            ],
            [
                'code' => 'VP',
                'name' => 'Velocidade de Processamento',
                'description' => 'Rapidez para processar informações e executar tarefas cognitivas, especialmente aquelas que exigem atenção e coordenação motora fina.',
                'is_active' => true,
            ],
            [
                'code' => 'FE',
                'name' => 'Funções Executivas',
                'description' => 'Conjunto de habilidades cognitivas de alto nível, incluindo planejamento, organização, inibição de respostas e flexibilidade cognitiva.',
                'is_active' => true,
            ],
            [
                'code' => 'MCP',
                'name' => 'Memória de Curto Prazo',
                'description' => 'Retenção e manipulação imediata de informações relevantes por um breve período (também chamada de Memória Operacional).',
                'is_active' => true,
            ],
            [
                'code' => 'MLP',
                'name' => 'Memória de Longo Prazo',
                'description' => 'Aprendizagem, codificação e evocação de informações após um período de tempo (memória episódica e semântica).',
                'is_active' => true,
            ],
            [
                'code' => 'AC',
                'name' => 'Atenção Concentrada',
                'description' => 'Habilidade de focar e manter o foco em um único estímulo, resistindo a distratores internos e externos.',
                'is_active' => true,
            ],
            [
                'code' => 'AD',
                'name' => 'Atenção Dividida',
                'description' => 'Habilidade de focar simultaneamente em múltiplas tarefas ou estímulos, distribuindo recursos cognitivos.',
                'is_active' => true,
            ],
            [
                'code' => 'AA',
                'name' => 'Atenção Alternada',
                'description' => 'Habilidade de mudar o foco atencional de forma flexível entre diferentes tarefas ou conjuntos de regras.',
                'is_active' => true,
            ],

            // --- PERSONALIDADE / EMOCIONAL / CLÍNICO ---
            [
                'code' => 'EXT',
                'name' => 'Extroversão',
                'description' => 'Orientação para o mundo externo, sociabilidade, assertividade, nível de energia e busca por excitação.',
                'is_active' => true,
            ],
            [
                'code' => 'CSC',
                'name' => 'Conscienciosidade',
                'description' => 'Organização, responsabilidade, disciplina, auto-eficácia, e cumprimento de metas e regras.',
                'is_active' => true,
            ],
            [
                'code' => 'AE',
                'name' => 'Autoestima',
                'description' => 'Avaliação e sentimento de valor pessoal; atitude global de aceitação e aprovação de si mesmo.',
                'is_active' => true,
            ],
            [
                'code' => 'ANX',
                'name' => 'Ansiedade',
                'description' => 'Nível de tensão, preocupação excessiva, apreensão e tendência a experimentar medo e distúrbios somáticos relacionados.',
                'is_active' => true,
            ],
            [
                'code' => 'DEP',
                'name' => 'Depressão',
                'description' => 'Intensidade dos sintomas relacionados ao humor deprimido, perda de interesse (anedonia), e sentimentos de desesperança.',
                'is_active' => true,
            ],
            [
                'code' => 'EST',
                'name' => 'Estresse',
                'description' => 'Reações psicofisiológicas e comportamentais a demandas externas percebidas como excessivas ou ameaçadoras.',
                'is_active' => true,
            ],
            [
                'code' => 'N.AFL',
                'name' => 'Necessidade de Afiliação',
                'description' => 'Desejo de estabelecer e manter relações sociais harmoniosas, ser aceito e fazer parte de um grupo.',
                'is_active' => true,
            ],
            [
                'code' => 'N.REA',
                'name' => 'Necessidade de Realização',
                'description' => 'Desejo de superação, busca por excelência, competência e sucesso em tarefas difíceis.',
                'is_active' => true,
            ],

            // --- APTIDÃO / INTERESSES ---
            [
                'code' => 'RV',
                'name' => 'Raciocínio Verbal',
                'description' => 'Habilidade de compreender, analisar e raciocinar com conceitos expressos em palavras.',
                'is_active' => true,
            ],
            [
                'code' => 'RN',
                'name' => 'Raciocínio Numérico',
                'description' => 'Habilidade para lidar com números, cálculos, interpretação de dados e conceitos matemáticos.',
                'is_active' => true,
            ],
            [
                'code' => 'RM',
                'name' => 'Raciocínio Mecânico',
                'description' => 'Habilidade de compreender princípios de física, máquinas e relações espaciais de objetos.',
                'is_active' => true,
            ],
            [
                'code' => 'REA',
                'name' => 'Interesse Realista (RIASEC)',
                'description' => 'Preferência por atividades práticas, manuais, técnicas, trabalho com máquinas, ferramentas ou na natureza.',
                'is_active' => true,
            ],
            [
                'code' => 'INV',
                'name' => 'Interesse Investigativo (RIASEC)',
                'description' => 'Preferência por atividades de pesquisa, análise, solução de problemas científicos e teóricos.',
                'is_active' => true,
            ],
            [
                'code' => 'SOC',
                'name' => 'Interesse Social (RIASEC)',
                'description' => 'Preferência por atividades de ajuda, ensino, serviço, aconselhamento e trabalho em equipe.',
                'is_active' => true,
            ],
        ];
    }
}