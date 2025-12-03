<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;
use Throwable; // Importar a classe Throwable

class AreaSeeder extends Seeder
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

        $areas = $this->getStaticAreaData();
        $totalCount = count($areas);
        $count = 0;

        // Feedback de Início
        $this->command->info('✨ Iniciando o Seeder de Áreas (AreaSeeder). Total de ' . $totalCount . ' registros.');
        $this->command->newLine();

        // Loop principal
        foreach ($areas as $data) {
            
            $areaCode = $data['code'];
            $areaName = $data['name'];

            try {
                // Tenta encontrar a Área pelo código ou cria/atualiza
                $area = Area::updateOrCreate(
                    ['code' => $areaCode], // Condição de busca (chave única)
                    $data                     // Dados para criar ou ATUALIZAR
                );

                $count++;

                // Verifica se foi criado ou atualizado
                if ($area->wasRecentlyCreated) {
                    $this->command->info("[{$count}/{$totalCount}] ✅ CRIADA: Área #{$areaCode} - {$areaName}");
                    $createdCount++;
                } else {
                    $this->command->comment("[{$count}/{$totalCount}] 🔄 ATUALIZADA: Área #{$areaCode} - {$areaName}");
                    $updatedCount++;
                }

            } catch (Throwable $e) {
                // Loga qualquer erro durante a operação (NOVO)
                $this->command->error("❌ ERRO ao processar Área #{$areaCode} ({$areaName}). Detalhe: " . $e->getMessage());
                $errorCount++;
            }
        }
        
        $this->command->newLine();
        $this->command->line("--------------------------------------------------");
        
        // Sumário Final
        $this->command->info('📊 Sumário da Execução:');
        
        if ($createdCount > 0) {
            $this->command->line("  - Novas Áreas Criadas: **{$createdCount}**");
        }
        if ($updatedCount > 0) {
            $this->command->line("  - Áreas Existentes Atualizadas: **{$updatedCount}**");
        }
        if ($errorCount > 0) {
            $this->command->warn("  - Áreas com Erro: **{$errorCount}**");
        }
        
        $this->command->info('AreaSeeder concluído.');
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
            // [1/8] COG - Cognitivo
            [
                'code' => 'COG',
                'name' => 'Função Cognitiva',
                'description' => 'Avalia processos de pensamento, memória, atenção, raciocínio lógico e funções executivas.',
                'is_active' => true,
            ],
            // [2/8] PER - Personalidade
            [
                'code' => 'PER',
                'name' => 'Traços de Personalidade (Big Five)',
                'description' => 'Estrutura fundamental que abrange os fatores de Neuroticismo, Extroversão, Abertura, Amabilidade e Conscienciosidade.',
                'is_active' => true,
            ],
            // [3/8] PRO - Projetivo
            [
                'code' => 'PRO',
                'name' => 'Projetivo',
                'description' => 'Avaliação de aspectos emocionais, inconscientes e dinâmicos da personalidade através de estímulos ambíguos ou desenhos.',
                'is_active' => true,
            ],
            // [4/8] NEU - Neuropsicológico
            [
                'code' => 'NEU',
                'name' => 'Neuropsicológico',
                'description' => 'Avaliação das Funções Executivas e das relações entre o funcionamento cerebral e o comportamento (memória, atenção, linguagem, etc.).',
                'is_active' => true,
            ],
            // [5/8] APT - Aptidão
            [
                'code' => 'APT',
                'name' => 'Aptidão',
                'description' => 'Avaliação do potencial ou da proficiência do indivíduo em uma habilidade específica (ex: mecânica, numérica, espacial, fluência verbal).',
                'is_active' => true,
            ],
            // [6/8] INT - Interesses
            [
                'code' => 'INT',
                'name' => 'Interesses',
                'description' => 'Avaliação das preferências e motivações do indivíduo por diferentes tipos de atividades, fundamental para orientação vocacional e profissional.',
                'is_active' => true,
            ],
            // [7/8] EMO - Emocional / Clínico
            [
                'code' => 'EMO',
                'name' => 'Regulação Emocional',
                'description' => 'Mede a estabilidade emocional, capacidade de lidar com estresse, ansiedade e sintomas de humor (depressão).',
                'is_active' => true,
            ],
            // [8/8] Área Social e Comportamental
            [
                'code' => 'SOC',
                'name' => 'Habilidades Sociais e Comportamento',
                'description' => 'Foca em traços de extroversão, habilidades interpessoais, comunicação e padrões comportamentais adaptativos.',
                'is_active' => true,
            ],
            // [9/8]Transtorno de Déficit de Atenção/Hiperatividade
            [
                'code' => 'TDAH',
                'name' => 'Transtorno de Déficit de Atenção/Hiperatividade',
                'description' => '🧠 Descrição da Área: TDAH
O Transtorno de Déficit de Atenção/Hiperatividade (TDAH) é um transtorno do neurodesenvolvimento caracterizado por padrões persistentes de desatenção e/ou hiperatividade-impulsividade que têm impacto direto e negativo no funcionamento social, acadêmico ou profissional.

Em resumo:

Desatenção: Refere-se à dificuldade em manter o foco, seguir instruções detalhadas, organizar tarefas e evitar distrações.

Hiperatividade/Impulsividade: Envolve excesso de atividade motora (inquietação, agitação) e/ou dificuldade em controlar respostas imediatas (agir sem pensar, interromper os outros).

Esta área abrange instrumentos que avaliam a presença e a intensidade desses sintomas e o nível de prejuízo que causam em diversos contextos da vida do paciente.',
                'is_active' => true,
            ],
        ];
    }
}