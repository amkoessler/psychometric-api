<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Dimension; // Seu Model de Dimensão
use Throwable; // Importar a classe Throwable

class DimensionSeeder extends Seeder
{
    /**
     * Run the database seeds. (Método principal no topo)
     */
    public function run(): void
    {
        // NOVO: Inicializa contadores
        $createdCount = 0;
        $updatedCount = 0;
        $errorCount = 0;

        // Obtém a lista completa e atualizada de dimensões
        $dimensions = $this->getStaticDimensionData();
        $totalCount = count($dimensions);
        $count = 0;

        // Feedback de Início
        $this->command->info('✨ Iniciando o Seeder de Dimensões (DimensionSeeder). Total de ' . $totalCount . ' registros.');
        $this->command->newLine();

        // Loop principal
        foreach ($dimensions as $data) {
            
            $dimensionCode = $data['code'];
            $dimensionName = $data['name'];

            try {
                // Tenta encontrar a Dimensão pelo código ou cria/atualiza
                $dimension = Dimension::updateOrCreate(
                    ['code' => $dimensionCode], // Condição de busca (chave única)
                    $data                     // Dados para criar ou ATUALIZAR
                );

                $count++;

                // Verifica se foi criado ou atualizado
                if ($dimension->wasRecentlyCreated) {
                    $this->command->info("[{$count}/{$totalCount}] ✅ CRIADA: Dimensão #{$dimensionCode} - {$dimensionName}");
                    $createdCount++;
                } else {
                    $this->command->comment("[{$count}/{$totalCount}] 🔄 ATUALIZADA: Dimensão #{$dimensionCode} - {$dimensionName}");
                    $updatedCount++;
                }

            } catch (Throwable $e) {
                // Loga qualquer erro durante a operação
                $this->command->error("❌ ERRO ao processar Dimensão #{$dimensionCode} ({$dimensionName}). Detalhe: " . $e->getMessage());
                $errorCount++;
            }
        }
        
        $this->command->newLine();
        $this->command->line("--------------------------------------------------");
        
        // Sumário Final
        $this->command->info('📊 Sumário da Execução:');
        
        if ($createdCount > 0) {
            $this->command->line("  - Novas Dimensões Criadas: **{$createdCount}**");
        }
        if ($updatedCount > 0) {
            $this->command->line("  - Dimensões Existentes Atualizadas: **{$updatedCount}**");
        }
        if ($errorCount > 0) {
            $this->command->warn("  - Dimensões com Erro: **{$errorCount}**");
        }
        
        $this->command->info('DimensionSeeder concluído.');
    }

    /**
     * Retorna o array de dados estáticos para as Dimensões (27 itens).
     */
    private function getStaticDimensionData(): array
    {
        return [
            // --- COGNITIVO / NEUROPSICOLÓGICO (10) ---
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

            // --- PERSONALIDADE / EMOCIONAL / CLÍNICO (8 + 2 Novos = 10) ---
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
                'code' => 'OPN', // NOVO: Abertura
                'name' => 'Abertura à Experiência',
                'description' => 'Apreciação pela arte, emoção, aventura, ideias não convencionais e curiosidade intelectual. Um dos fatores do Big Five.',
                'is_active' => true,
            ],
            [
                'code' => 'AGR', // NOVO: Amabilidade
                'name' => 'Amabilidade (Agreeableness)',
                'description' => 'Tendência a ser compassivo e cooperativo em vez de suspeito e antagônico em relação aos outros. Um dos fatores do Big Five.',
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
                'code' => 'NAFIL', 
                'name' => 'Necessidade de Afiliação',
                'description' => 'Desejo de estabelecer e manter relações sociais harmoniosas, ser aceito e fazer parte de um grupo.',
                'is_active' => true,
            ],
            [
                'code' => 'NREAL', 
                'name' => 'Necessidade de Realização',
                'description' => 'Desejo de superação, busca por excelência, competência e sucesso em tarefas difíceis.',
                'is_active' => true,
            ],

            // --- APTIDÃO / INTERESSES / CLÍNICO (7) ---
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
            [
                'code' => 'CEXT', 
                'name' => 'Comportamento de Externalização (TDAH)',
                'description' => 'Mede a frequência e intensidade dos comportamentos hiperativos, impulsivos e problemas de conduta que definem o quadro clínico do Transtorno do Déficit de Atenção e Hiperatividade.',
                'is_active' => true, 
            ],
            // Modelo de Seeder para a Dimensão ETDAH-PAIS

            [
                'code' => 'ETDAH-PAIS', 
                'name' => 'Avaliação Comportamental TDAH (Versão Pais)',
                'description' => '
                    <h4 style="color: #6d28d9; border-bottom: 2px solid #ddd6fe; padding-bottom: 5px; margin-top: 0;">
                        📝 Instrumento de Avaliação
                    </h4>
                    <p>
                        O <strong>ETDAH-PAIS</strong> é um instrumento fundamental para avaliar o Transtorno do Déficit de Atenção e Hiperatividade (TDAH) através da perspectiva e experiência dos pais.
                    </p>
                    <p style="margin-top: 10px;">
                        Sua pontuação se baseia em quatro fatores críticos para o diagnóstico:
                    </p>
                    <ul style="padding-left: 20px;">
                        <li><strong style="color: #059669;">Regulação Emocional</strong></li>
                        <li><strong style="color: #2563eb;">Hiperatividade / Impulsividade</strong></li>
                        <li><strong style="color: #f59e0b;">Comportamento Adaptativo</strong></li>
                        <li><strong style="color: #ef4444;">Atenção</strong></li>
                    </ul>
                    <p style="margin-top: 10px;">
                        É uma ferramenta essencial para capturar a intensidade e a frequência dos sintomas no ambiente familiar.
                    </p>
                ',
                'is_active' => true, 
            ],
        ];
    }
}