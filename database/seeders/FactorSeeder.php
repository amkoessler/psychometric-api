<?php

namespace Database\Seeders;

use App\Models\Dimension;
use App\Models\Factor;
use Illuminate\Database\Seeder;
use Throwable;

class FactorSeeder extends Seeder
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

        // 1. Obtém os dados estáticos dos Fatores
        $factorData = $this->getStaticFactorData();
        $totalCount = count($factorData);
        $count = 0;

        // Feedback de Início
        $this->command->info('✨ Iniciando o Seeder de Fatores (FactorSeeder). Total de ' . $totalCount . ' registros.');
        $this->command->newLine();

        // 2. Loop principal de Criação/Atualização de Fatores
        foreach ($factorData as $data) {
            
            $factorCode = $data['code'];
            $factorName = $data['name'];
            
            // Remove a chave de relacionamento antes de passar para updateOrCreate
            $dimensionCodes = $data['dimension_codes'];
            unset($data['dimension_codes']);

            try {
                // Tenta encontrar o Fator pelo código ou cria/atualiza
                $factor = Factor::updateOrCreate(
                    ['code' => $factorCode], // Condição de busca (chave única)
                    $data                     // Dados para criar ou ATUALIZAR
                );

                // 3. Conecta o Fator às Dimensões (Relacionamento N:M)
                $this->syncDimensions($factor, $dimensionCodes);
                
                $count++;

                // Verifica se foi criado ou atualizado
                if ($factor->wasRecentlyCreated) {
                    $this->command->info("[{$count}/{$totalCount}] ✅ CRIADO: Fator #{$factorCode} - {$factorName} (Dimensões Sincronizadas)");
                    $createdCount++;
                } else {
                    $this->command->comment("[{$count}/{$totalCount}] 🔄 ATUALIZADO: Fator #{$factorCode} - {$factorName} (Dimensões Sincronizadas)");
                    $updatedCount++;
                }

            } catch (Throwable $e) {
                // Loga qualquer erro durante a operação
                $this->command->error("❌ ERRO ao processar Fator #{$factorCode} ({$factorName}). Detalhe: " . $e->getMessage());
                $errorCount++;
            }
        }
        
        $this->command->newLine();
        $this->command->line("--------------------------------------------------");
        
        // Sumário Final
        $this->command->info('📊 Sumário da Execução:');
        
        if ($createdCount > 0) {
            $this->command->line("  - Novos Fatores Criados: **{$createdCount}**");
        }
        if ($updatedCount > 0) {
            $this->command->line("  - Fatores Existentes Atualizados: **{$updatedCount}**");
        }
        if ($errorCount > 0) {
            $this->command->warn("  - Fatores com Erro: **{$errorCount}**");
        }
        
        $this->command->info('FactorSeeder concluído.');
    }

    /**
     * Sincroniza o Fator com suas Dimensões associadas.
     * @param Factor $factor
     * @param array $dimensionCodes
     * @return void
     */
    private function syncDimensions(Factor $factor, array $dimensionCodes): void
    {
        // Encontra os IDs das Dimensões pelos códigos 
        $dimensionIds = Dimension::whereIn('code', $dimensionCodes)->pluck('id');
        
        // Anexa/sincroniza (Adiciona se não existir, remove o que não estiver na lista)
        $factor->dimensions()->sync($dimensionIds);
    }


    //---------------------------------------------------------
    // DECLARAÇÃO DOS DADOS ESTÁTICOS NO FINAL DA CLASSE
    //---------------------------------------------------------
    
    /**
     * Retorna o array de dados estáticos para os Fatores (12 itens).
     */
    private function getStaticFactorData(): array
    {
        return [
            // --- Fatores Originais (4) ---
            // [1/12] RE - Regulação Emocional
            [
                'code' => 'RE',
                'name' => 'Regulação Emocional',
                'description' => 'Mede a capacidade do indivíduo de monitorar, avaliar e modificar a intensidade e duração de suas experiências e expressões emocionais.',
                'is_active' => true,
                'dimension_codes' => ['EST', 'ANX', 'DEP'], 
            ],
            // [2/12] HI - Hiperatividade / Impulsividade
            [
                'code' => 'HI',
                'name' => 'Hiperatividade / Impulsividade',
                'description' => 'Comportamentos de externalização relacionados à incapacidade de controlar movimentos e reações, associado ao TDAH.',
                'is_active' => true,
                'dimension_codes' => ['CEXT'], 
            ],
            // [3/12] CA - Comportamento Adaptativo
            [
                'code' => 'CA',
                'name' => 'Comportamento Adaptativo',
                'description' => 'Avaliação dos traços de personalidade que promovem organização, disciplina, sociabilidade e habilidades interpessoais.',
                'is_active' => true,
                'dimension_codes' => ['CSC', 'EXT'], 
            ],
            // [4/12] A - Atenção
            [
                'code' => 'A',
                'name' => 'Atenção',
                'description' => 'Fator que abrange as diferentes facetas da função atencional: concentrada, dividida e alternada.',
                'is_active' => true,
                'dimension_codes' => ['AC', 'AD', 'AA'], 
            ],

            // --- Fatores Cognitivos/Neuropsicológicos (4 Novos) ---
            // [5/12] PENS - Flexibilidade de Pensamento
            [
                'code' => 'PENS',
                'name' => 'Flexibilidade de Pensamento e Execução',
                'description' => 'Habilidade para alternar entre diferentes conceitos ou conjuntos de regras e mudar estratégias rapidamente, essencial para a adaptabilidade e funções executivas.',
                'is_active' => true,
                'dimension_codes' => ['FE', 'AA'], // Funções Executivas, Atenção Alternada
            ],
            // [6/12] MEMR - Aprendizagem e Memória
            [
                'code' => 'MEMR',
                'name' => 'Aprendizagem e Memória',
                'description' => 'Fator que mede a eficácia na aquisição, codificação e evocação de novas informações ao longo do tempo (curto e longo prazo).',
                'is_active' => true,
                'dimension_codes' => ['MLP', 'MCP'], // Memória de Longo e Curto Prazo
            ],
            // [7/12] RACV - Raciocínio Verbal Complexo
            [
                'code' => 'RACV',
                'name' => 'Raciocínio Verbal Complexo',
                'description' => 'Avaliação avançada da capacidade de compreender, inferir e manipular conceitos expressos verbalmente, indicando inteligência cristalizada e aptidão verbal.',
                'is_active' => true,
                'dimension_codes' => ['RV', 'FG'], // Raciocínio Verbal, Fator G
            ],
            // [8/12] RAOB - Raciocínio Abstrato e Lógico
            [
                'code' => 'RAOB',
                'name' => 'Raciocínio Abstrato e Lógico',
                'description' => 'Fator central da inteligência fluida, medindo a capacidade de resolver novos problemas, identificar padrões e utilizar o raciocínio dedutivo e indutivo.',
                'is_active' => true,
                'dimension_codes' => ['RL', 'RA'], // Raciocínio Lógico, Raciocínio Abstrato
            ],
            
            // --- Fatores de Personalidade/Clínicos/Vocacionais (4 Novos) ---
            // [9/12] AVEC - Abertura e Valores
            [
                'code' => 'AVEC',
                'name' => 'Abertura à Experiência e Valores',
                'description' => 'Mede a curiosidade intelectual, imaginação, apreciação estética e o grau de liberalismo ou conservadorismo do indivíduo (Big Five - Abertura).',
                'is_active' => true,
                'dimension_codes' => ['OPN'], // Assumindo OPN como Abertura
            ],
            // [10/12] AMAB - Amabilidade e Empatia
            [
                'code' => 'AMAB',
                'name' => 'Amabilidade e Empatia',
                'description' => 'Mede a orientação interpessoal, incluindo altruísmo, confiança, modéstia e preocupação com o bem-estar alheio (Big Five - Amabilidade).',
                'is_active' => true,
                'dimension_codes' => ['AE', 'AGR'], // Autoestima, Assumindo AGR como Amabilidade
            ],
            // [11/12] INTV - Interesses Vocacionais
            [
                'code' => 'INTV',
                'name' => 'Interesses Vocacionais e Profissionais',
                'description' => 'Avaliação do perfil de interesses do indivíduo que orienta escolhas de carreira e ambientes de trabalho mais compatíveis.',
                'is_active' => true,
                'dimension_codes' => ['REA', 'INV', 'SOC'], // Realista, Investigativo, Social (RIASEC)
            ],
            // [12/12] SINT - Sintomas Clínicos Gerais
            [
                'code' => 'SINT',
                'name' => 'Sintomas Clínicos Gerais (Afeto Negativo)',
                'description' => 'Fator amplo que agrupa indicadores de sofrimento psicológico (distress), como sentimentos de ansiedade, depressão e somatização.',
                'is_active' => true,
                'dimension_codes' => ['DEP', 'ANX', 'EST'], // Depressão, Ansiedade, Estresse
            ],
        ];
    }
}