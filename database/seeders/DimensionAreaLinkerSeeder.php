<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Dimension;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DimensionAreaLinkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Iniciando o Seeder de Ligações Dimensão-Área (Area -> Dimensões).');

        // 1. Mapear Dimensões para acesso rápido (Cache)
        // Usa o método pluck('id', 'code') para criar um array associativo de [code => id]
        $dimensions = Dimension::all()->pluck('id', 'code');
        
        // 2. Definir as Associações de Área (Estrutura: Área => [Dimensões])
        $areaToDimensionMap = $this->getAreaToDimensionMap();

        foreach ($areaToDimensionMap as $areaCode => $dimensionCodes) {
            
            $this->command->line("--------------------------------------------------");
            $this->command->info("Processando Área: {$areaCode}");
            
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
                        $this->command->warn("Dimensão '{$code}' (para Área {$areaCode}) NÃO foi encontrada no banco.");
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
            
            $this->command->info("SUCESSO: Área '{$area->name}' ({$areaCode}) sincronizada com " . count($dimensionIdsToAttach) . " Dimensão(ões).");
        }
        
        $this->command->info('--------------------------------------------------');
        $this->command->info('Dimension-Area Linker Seeder concluído.');
    }

    /**
     * Retorna o mapa de ligações (Muitos-para-Muitos) na estrutura Área => Dimensões.
     */
    private function getAreaToDimensionMap(): array
    {
        return [
            // [Área] => [Dimensões que pertencem a esta Área]
            
            // COG (Função Cognitiva)
            'COG' => ['FG', 'RL', 'RA', 'AC', 'AD', 'AA', 'VP'], 

            // PER (Personalidade)
            'PER' => ['AE', 'EXT', 'CSC', 'ANX', 'N.AFL', 'N.REA'],

            // PRO (Projetivo)
            'PRO' => [], 

            // NEU (Neuropsicológico)
            'NEU' => ['AC', 'AD', 'AA', 'VP', 'FE', 'MCP', 'MLP', 'COM-EXT'],

            // APT (Aptidão)
            'APT' => ['RL', 'RV', 'RN', 'RM'],

            // INT (Interesses)
            'INT' => ['REA', 'INV', 'SOC'],

            // EMO (Regulação Emocional / Clínico)
            'EMO' => ['AE', 'ANX', 'DEP', 'EST', 'COM-EXT'],

            // SOC (Habilidades Sociais e Comportamento)
            'SOC' => ['COM-EXT'],
        ];
    }
}