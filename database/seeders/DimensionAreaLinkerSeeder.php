<?php

namespace Database\Seeders;

use App\Models\AssessmentArea;
use App\Models\Dimension;
use Illuminate\Database\Seeder;

class DimensionAreaLinkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Mapear Áreas para acesso rápido
        // Usa o método pluck('id', 'code') para criar um array associativo de [code => id]
        $areas = AssessmentArea::all()->pluck('id', 'code');
        
        // 2. Definir as Associações de Dimensão
        // Mapeamos o código da Dimensão para uma lista de códigos de Áreas
        $dimensionToAreaMap = $this->getDimensionToAreaMap();

        foreach ($dimensionToAreaMap as $dimensionCode => $areaCodes) {
            
            // Busca a Dimensão pelo código
            $dimension = Dimension::where('code', $dimensionCode)->first();
            
            if (!$dimension) {
                echo "AVISO: Dimensão com código '{$dimensionCode}' não encontrada. Pulando ligação.\n";
                continue;
            }

            // Mapeia os códigos de Áreas para seus respectivos IDs (usando o cache $areas)
            $areaIdsToAttach = collect($areaCodes)
                ->map(fn ($code) => $areas[$code] ?? null) // Mapeia código para ID
                ->filter() // Remove entradas nulas caso o código da área não exista
                ->toArray();
            
            // 3. Executa a Ligação (attach)
            // O sync garante que apenas as associações definidas aqui permaneçam.
            $dimension->assessmentAreas()->sync($areaIdsToAttach);
        }
    }


    // ...
// Conteúdo anterior do DimensionAreaLinkerSeeder.php
// ...

    /**
     * Retorna o mapa de ligações (Muitos-para-Muitos).
     * Dimensões que pertencem a múltiplas áreas devem ter vários códigos de área.
     */
    private function getDimensionToAreaMap(): array
    {
        return [
            // [Dimensão] => [Áreas às quais pertence]
            // COG
            'FG' => ['COG'],
            'RL' => ['COG', 'APT'],
            'RA' => ['COG'],

            // COG, NEU
            'AC' => ['COG', 'NEU'],
            'AD' => ['COG', 'NEU'],
            'AA' => ['COG', 'NEU'],
            'VP' => ['COG', 'NEU'],

            // NEU
            'FE' => ['NEU'],
            'MCP' => ['NEU'],
            'MLP' => ['NEU'],
            'COM-EXT' => ['NEU'],

            // PER, EMO
            'AE' => ['PER', 'EMO'],
            'EXT' => ['PER'],
            'CSC' => ['PER'],
            'ANX' => ['PER', 'EMO'],
            'N.AFL' => ['PER'],
            'N.REA' => ['PER'],

            // EMO
            'DEP' => ['EMO'],
            'EST' => ['EMO'],
            'COM-EXT' => ['EMO'],

            // APT
            'RV' => ['APT'],
            'RN' => ['APT'],
            'RM' => ['APT'],

            // INT
            'REA' => ['INT'],
            'INV' => ['INT'],
            'SOC' => ['INT'],

            //SOC
            'COM-EXT' => ['SOC'],
        ];
    }
}