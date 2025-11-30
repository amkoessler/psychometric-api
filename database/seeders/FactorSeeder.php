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
     * Retorna o array de dados estáticos para os Fatores (36 itens).
     */
    private function getStaticFactorData(): array
    {
        return [
            // =============================================================
            // FATORES ORIGINAIS (12)
            // =============================================================
            
            // --- Fatores Originais (4) ---
            // [1/36] RE - Regulação Emocional
            [
                'code' => 'RE',
                'name' => 'Regulação Emocional',
                'description' => 'Mede a capacidade do indivíduo de monitorar, avaliar e modificar a intensidade e duração de suas experiências e expressões emocionais.',
                'is_active' => true,
                'dimension_codes' => ['EST', 'ANX', 'DEP'], 
            ],
            // [2/36] HI - Hiperatividade / Impulsividade (TDAH)
            [
                'code' => 'HI',
                'name' => 'Hiperatividade / Impulsividade (TDAH)',
                'description' => 'Comportamentos de externalização relacionados à incapacidade de controlar movimentos e reações, associado ao TDAH.',
                'is_active' => true,
                'dimension_codes' => ['CEXT'], 
            ],
            // [3/36] CA - Comportamento Adaptativo
            [
                'code' => 'CA',
                'name' => 'Comportamento Adaptativo',
                'description' => 'Avaliação dos traços de personalidade que promovem organização, disciplina, sociabilidade e habilidades interpessoais.',
                'is_active' => true,
                'dimension_codes' => ['CSC', 'EXT'], 
            ],
            // [4/36] A - Atenção
            [
                'code' => 'A',
                'name' => 'Atenção (Concentrada, Dividida, Alternada)',
                'description' => 'Fator que abrange as diferentes facetas da função atencional: concentrada, dividida e alternada.',
                'is_active' => true,
                'dimension_codes' => ['AC', 'AD', 'AA'], 
            ],

            // --- Fatores Cognitivos/Neuropsicológicos (4) ---
            // [5/36] PENS - Flexibilidade de Pensamento
            [
                'code' => 'PENS',
                'name' => 'Flexibilidade de Pensamento e Execução',
                'description' => 'Habilidade para alternar entre diferentes conceitos ou conjuntos de regras e mudar estratégias rapidamente, essencial para a adaptabilidade e funções executivas.',
                'is_active' => true,
                'dimension_codes' => ['FE', 'AA'], // Funções Executivas, Atenção Alternada
            ],
            // [6/36] MEMR - Aprendizagem e Memória
            [
                'code' => 'MEMR',
                'name' => 'Aprendizagem e Memória',
                'description' => 'Fator que mede a eficácia na aquisição, codificação e evocação de novas informações ao longo do tempo (curto e longo prazo).',
                'is_active' => true,
                'dimension_codes' => ['MLP', 'MCP'], // Memória de Longo e Curto Prazo
            ],
            // [7/36] RACV - Raciocínio Verbal Complexo
            [
                'code' => 'RACV',
                'name' => 'Raciocínio Verbal Complexo',
                'description' => 'Avaliação avançada da capacidade de compreender, inferir e manipular conceitos expressos verbalmente, indicando inteligência cristalizada e aptidão verbal.',
                'is_active' => true,
                'dimension_codes' => ['RV', 'FG'], // Raciocínio Verbal, Fator G
            ],
            // [8/36] RAOB - Raciocínio Abstrato e Lógico
            [
                'code' => 'RAOB',
                'name' => 'Raciocínio Abstrato e Lógico',
                'description' => 'Fator central da inteligência fluida, medindo a capacidade de resolver novos problemas, identificar padrões e utilizar o raciocínio dedutivo e indutivo.',
                'is_active' => true,
                'dimension_codes' => ['RL', 'RA'], // Raciocínio Lógico, Raciocínio Abstrato
            ],
            
            // --- Fatores de Personalidade/Clínicos/Vocacionais (4) ---
            // [9/36] AVEC - Abertura e Valores
            [
                'code' => 'AVEC',
                'name' => 'Abertura à Experiência e Valores',
                'description' => 'Mede a curiosidade intelectual, imaginação, apreciação estética e o grau de liberalismo ou conservadorismo do indivíduo (Big Five - Abertura).',
                'is_active' => true,
                'dimension_codes' => ['OPN'], // Assumindo OPN como Abertura
            ],
            // [10/36] AMAB - Amabilidade e Empatia
            [
                'code' => 'AMAB',
                'name' => 'Amabilidade e Empatia',
                'description' => 'Mede a orientação interpessoal, incluindo altruísmo, confiança, modéstia e preocupação com o bem-estar alheio (Big Five - Amabilidade).',
                'is_active' => true,
                'dimension_codes' => ['AE', 'AGR'], // Autoestima, Amabilidade
            ],
            // [11/36] INTV - Interesses Vocacionais
            [
                'code' => 'INTV',
                'name' => 'Interesses Vocacionais e Profissionais',
                'description' => 'Avaliação do perfil de interesses do indivíduo que orienta escolhas de carreira e ambientes de trabalho mais compatíveis.',
                'is_active' => true,
                'dimension_codes' => ['REA', 'INV', 'SOC'], // Realista, Investigativo, Social (RIASEC)
            ],
            // [12/36] SINT - Sintomas Clínicos Gerais
            [
                'code' => 'SINT',
                'name' => 'Sintomas Clínicos Gerais (Afeto Negativo)',
                'description' => 'Fator amplo que agrupa indicadores de sofrimento psicológico (distress), como sentimentos de ansiedade, depressão e somatização.',
                'is_active' => true,
                'dimension_codes' => ['DEP', 'ANX', 'EST'], // Depressão, Ansiedade, Estresse
            ],

            // =============================================================
            // NOVOS FATORES (24 Novos)
            // =============================================================

            // --- NOVOS: BDI-II (1) ---
            // [13/36] CAFET - Cognitivo/Afetivo (BDI-II)
            [
                'code' => 'CAFET',
                'name' => 'Cognitivo/Afetivo',
                'description' => 'Agrupamento de sintomas depressivos que refletem a visão negativa de si e do mundo (cognitivo) e a experiência de humor triste/perda de prazer (afetivo).',
                'is_active' => true,
                'dimension_codes' => ['DEP', 'EST'],
            ],

            // --- NOVOS: NEO-PI-R (2) ---
            // [14/36] NEUR - Neuroticismo (Big Five)
            [
                'code' => 'NEUR',
                'name' => 'Neuroticismo',
                'description' => 'Tendência a experimentar estados emocionais desagradáveis, como raiva, ansiedade, depressão e vulnerabilidade psicológica.',
                'is_active' => true,
                'dimension_codes' => ['ANX', 'DEP', 'EST'],
            ],
            // [15/36] EXTV - Extroversão (Big Five)
            [
                'code' => 'EXTV',
                'name' => 'Extroversão',
                'description' => 'Qualidade e intensidade da interação interpessoal, nível de atividade, necessidade de estimulação e capacidade de alegria.',
                'is_active' => true,
                'dimension_codes' => ['EXT', 'SOC'],
            ],

            // --- NOVOS: RSES (1) ---
            // [16/36] AETM - Autoestima
            [
                'code' => 'AETM',
                'name' => 'Fator Único (Autoestima)',
                'description' => 'Avaliação e sentimento de valor pessoal; uma atitude global de aceitação e aprovação de si mesmo. (Baseado na Escala de Rosenberg).',
                'is_active' => true,
                'dimension_codes' => ['AE'],
            ],

            // --- NOVOS: DFH-IV (2) ---
            // [17/36] DHFM - Fator 1: Figura Masculina (Desenho da Figura Humana)
            [
                'code' => 'DHFM',
                'name' => 'Fator 1: Figura Masculina (DFH)',
                'description' => 'Medida do nível de desenvolvimento cognitivo refletido nos detalhes e proporções do desenho da figura humana masculina.',
                'is_active' => true,
                'dimension_codes' => ['FG'], // Ligado ao Fator G / Cognitivo
            ],
            // [18/36] DHFF - Fator 2: Figura Feminina (Desenho da Figura Humana)
            [
                'code' => 'DHFF',
                'name' => 'Fator 2: Figura Feminina (DFH)',
                'description' => 'Medida do nível de desenvolvimento cognitivo refletido nos detalhes e proporções do desenho da figura humana feminina.',
                'is_active' => true,
                'dimension_codes' => ['FG'], // Ligado ao Fator G / Cognitivo
            ],

            // --- NOVOS: PCL-5 (4) ---
            // [19/36] INTRU - Sintomas de Intrusão (Cluster B)
            [
                'code' => 'INTRU',
                'name' => 'Sintomas de Intrusão',
                'description' => 'Sintomas de reexperiência traumática, como recordações angustiantes recorrentes, sonhos e reações dissociativas (flashbacks).',
                'is_active' => true,
                'dimension_codes' => ['EST', 'ANX'],
            ],
            // [20/36] EVIT - Evitação (Cluster C)
            [
                'code' => 'EVIT',
                'name' => 'Evitação',
                'description' => 'Esforços persistentes para evitar memórias, pensamentos, sentimentos ou lembretes externos relacionados ao trauma.',
                'is_active' => true,
                'dimension_codes' => ['ANX'],
            ],
            // [21/36] CHNEG - Cognições e Humor Negativo (Cluster D)
            [
                'code' => 'CHNEG',
                'name' => 'Cognições e Humor Negativo',
                'description' => 'Alterações negativas persistentes nas cognições e no humor, como crenças distorcidas sobre si/mundo e humor persistentemente negativo.',
                'is_active' => true,
                'dimension_codes' => ['DEP', 'EST'],
            ],
            // [22/36] HIPA - Hiperexcitação/Arousal (Cluster E)
            [
                'code' => 'HIPA',
                'name' => 'Hiperexcitação/Arousal',
                'description' => 'Alterações acentuadas na reatividade e excitação, incluindo irritabilidade, hipervigilância, problemas de concentração e sono.',
                'is_active' => true,
                'dimension_codes' => ['CEXT', 'EST'],
            ],

            // --- NOVOS: IFP-II (14 Necessidades) ---
            // [23/36] AUTI - Autonomia/Independência
            [
                'code' => 'AUTI',
                'name' => 'Autonomia/Independência',
                'description' => 'Necessidade de agir de forma independente, fazer as próprias escolhas e evitar coerção, valorizando a liberdade pessoal.',
                'is_active' => true,
                'dimension_codes' => ['OPN', 'EXT'],
            ],
            // [24/36] REALZ - Realização (Abertura/Desempenho)
            [
                'code' => 'REALZ',
                'name' => 'Realização (Abertura/Desempenho)',
                'description' => 'Necessidade de superar obstáculos, atingir padrões elevados, ser bem-sucedido e competitivo em tarefas difíceis.',
                'is_active' => true,
                'dimension_codes' => ['NREAL', 'CSC'],
            ],
            // [25/36] AGRS - Agressão
            [
                'code' => 'AGRS',
                'name' => 'Agressão',
                'description' => 'Necessidade de atacar, culpar, criticar ou se vingar de outros; tendência a expressar hostilidade e desafiar a autoridade.',
                'is_active' => true,
                'dimension_codes' => ['CEXT'],
            ],
            // [26/36] SUBM - Submissão/Obediência
            [
                'code' => 'SUBM',
                'name' => 'Submissão/Obediência',
                'description' => 'Tendência a cooperar, buscar orientação, seguir regras e aceitar a liderança de pessoas respeitadas.',
                'is_active' => true,
                'dimension_codes' => ['AGR', 'CSC'],
            ],
            // [27/36] PERS - Persistência/Perseverança
            [
                'code' => 'PERS',
                'name' => 'Persistência/Perseverança',
                'description' => 'Tendência a trabalhar com afinco, concluir tarefas iniciadas e manter-se focado em objetivos difíceis, demonstrando tenacidade.',
                'is_active' => true,
                'dimension_codes' => ['CSC'],
            ],
            // [28/36] AFIL - Afiliação/Amizade
            [
                'code' => 'AFIL',
                'name' => 'Afiliação/Amizade',
                'description' => 'Necessidade de formar laços de amizade, ser leal e buscar intimidade social.',
                'is_active' => true,
                'dimension_codes' => ['NAFIL', 'EXT'],
            ],
            // [29/36] ORGZ - Ordem/Organização
            [
                'code' => 'ORGZ',
                'name' => 'Ordem/Organização',
                'description' => 'Necessidade de planejar, ser arrumado, metódico e manter as coisas limpas e organizadas.',
                'is_active' => true,
                'dimension_codes' => ['CSC'],
            ],
            // [30/36] EXPO - Exposição
            [
                'code' => 'EXPO',
                'name' => 'Exposição',
                'description' => 'Necessidade de ser notado, chamar a atenção, ser o centro das atenções, contar histórias e falar sobre seus sucessos.',
                'is_active' => true,
                'dimension_codes' => ['EXT'],
            ],
            // [31/36] ASST - Assistência/Apoio
            [
                'code' => 'ASST',
                'name' => 'Assistência/Apoio (Receptivo)',
                'description' => 'Necessidade de receber ajuda, simpatia, proteção e conforto de amigos ou figuras de autoridade em momentos de dificuldade.',
                'is_active' => true,
                'dimension_codes' => ['AGR'],
            ],
            // [32/36] INOV - Inovação/Mudança
            [
                'code' => 'INOV',
                'name' => 'Inovação/Mudança',
                'description' => 'Necessidade de buscar novidades, variar a rotina, viajar e experimentar coisas novas e diferentes.',
                'is_active' => true,
                'dimension_codes' => ['OPN'],
            ],
            // [33/36] DOMP - Dominância/Poder
            [
                'code' => 'DOMP',
                'name' => 'Dominância/Poder',
                'description' => 'Necessidade de controlar, influenciar, liderar e ser considerado uma autoridade; de dirigir os atos dos outros.',
                'is_active' => true,
                'dimension_codes' => ['EXT'],
            ],
            // [34/36] COMP - Compreensão/Conhecimento
            [
                'code' => 'COMP',
                'name' => 'Compreensão/Conhecimento',
                'description' => 'Necessidade de buscar conhecimento, analisar sentimentos/intenções (próprias e alheias) e entender fenômenos complexos.',
                'is_active' => true,
                'dimension_codes' => ['FG', 'RV'],
            ],
            // [35/36] CUID - Cuidado/Prestatividade
            [
                'code' => 'CUID',
                'name' => 'Cuidado/Prestatividade (Doação)',
                'description' => 'Necessidade de ajudar, proteger, ser gentil, prestar favores e demonstrar afeto e lealdade aos amigos e pessoas necessitadas.',
                'is_active' => true,
                'dimension_codes' => ['AGR'],
            ],
            // [36/36] RACS - Reação/Autoconservação
            [
                'code' => 'RACS',
                'name' => 'Reação/Autoconservação',
                'description' => 'Fator que envolve a tendência a reagir a críticas e a necessidade de evitar o perigo ou de buscar segurança.',
                'is_active' => true,
                'dimension_codes' => ['EST', 'AGR'],
            ],
        ];
    }
}