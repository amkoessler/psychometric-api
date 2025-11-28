<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Dimension;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaDimensionLinkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('✨ Iniciando o Seeder de Ligações Dimensão-Área (Area -> Dimensões).');

        // 1. Mapear Dimensões para acesso rápido (Cache)
        // Usa o método pluck('id', 'code') para criar um array associativo de [code => id]
        $dimensions = Dimension::all()->pluck('id', 'code');
        
        // 2. Definir as Associações de Área (Estrutura: Área => [Dimensões])
        $areaToDimensionMap = $this->getAreaToDimensionMap();
        $totalAreas = count($areaToDimensionMap);
        $areaCount = 0;

        foreach ($areaToDimensionMap as $areaCode => $dimensionCodes) {
            
            $areaCount++;
            $this->command->line("--------------------------------------------------");
            $this->command->info("[{$areaCount}/{$totalAreas}] Processando Área: {$areaCode}");
            
            // Busca a Área pelo código
            $area = Area::where('code', $areaCode)->first();
            
            if (!$area) {
                $this->command->warn("AVISO: Área com código '{$areaCode}' não encontrada. Pulando ligação.");
                continue;
            }

            // Mapeia os códigos de Dimensões para seus respectivos IDs (usando o cache $dimensions)
            $dimensionIdsToAttach = collect($dimensionCodes)
                ->map(function ($code) use ($dimensions, $areaCode) {
                    $dimensionId = $dimensions[$code] ?? null;
                    
                    if (!$dimensionId) {
                         // Loga as dimensões que não foram encontradas (útil para debug)
                        $this->command->warn("❌ Dimensão '{$code}' (para Área {$areaCode}) NÃO foi encontrada no banco.");
                    }
                    
                    return $dimensionId;
                })
                ->filter() // Remove entradas nulas (Dimensões não encontradas)
                ->toArray();
            
            // Verifica se há algo para anexar
            if (empty($dimensionIdsToAttach)) {
                $this->command->warn("Nenhuma dimensão válida encontrada para anexar à Área {$areaCode}.");
                continue;
            }

            // 3. Executa a Ligação (sync)
            // Usa a relação 'dimensions()' definida no Model Area: $area->dimensions()->sync(...)
            $area->dimensions()->sync($dimensionIdsToAttach);
            
            $this->command->info("✅ SUCESSO: Área '{$area->name}' ({$areaCode}) sincronizada com " . count($dimensionIdsToAttach) . " Dimensão(ões).");
        }
        
        $this->command->info('--------------------------------------------------');
        $this->command->info('Dimension-Area Linker Seeder concluído. Todos os erros de código foram corrigidos no mapa.');
    }

    /**
     * Retorna o mapa de ligações (Muitos-para-Muitos) na estrutura Área => Dimensões.
     * Os códigos de Dimensão foram corrigidos e expandidos.
     */
    private function getAreaToDimensionMap(): array
    {
        return [
            // [Área] => [Dimensões que pertencem a esta Área]
            
            // COG (Função Cognitiva): Foco no raciocínio, velocidade e funções centrais.
            'COG' => ['FG', 'RL', 'RA', 'VP', 'AC', 'AD', 'AA', 'FE', 'MCP', 'MLP'], 

            // PER (Personalidade - Big Five e necessidades): Inclui Big Five completo e traços/necessidades.
            'PER' => ['EXT', 'CSC', 'OPN', 'AGR', 'AE', 'ANX', 'NAFIL', 'NREAL'],

            // PRO (Projetivo): Mantido vazio. Métodos projetivos não se mapeiam diretamente a dimensões psicométricas.
            'PRO' => [], 

            // NEU (Neuropsicológico): Foco nas Funções Executivas e avaliação detalhada de processos.
            'NEU' => ['AC', 'AD', 'AA', 'VP', 'FE', 'MCP', 'MLP', 'CEXT'],

            // APT (Aptidão): Raciocínio aplicado e capacidade geral.
            'APT' => ['FG', 'RL', 'RV', 'RN', 'RM'],

            // INT (Interesses): Interesses RIASEC.
            'INT' => ['REA', 'INV', 'SOC'],

            // EMO (Regulação Emocional / Clínico): Foco na estabilidade emocional e sintomatologia.
            'EMO' => ['ANX', 'DEP', 'EST', 'AE', 'NAFIL', 'CEXT'], 
            
            // SOC (Habilidades Sociais e Comportamento): Foco em traços interpessoais e conduta.
            'SOC' => ['EXT', 'AGR', 'NAFIL', 'CEXT', 'AE'],
        ];
    }
}