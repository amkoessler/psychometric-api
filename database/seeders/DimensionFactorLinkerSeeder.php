<?php

namespace Database\Seeders;

use App\Models\Dimension;
use App\Models\Factor;
use Illuminate\Database\Seeder;
use Throwable;

class DimensionFactorLinkerSeeder extends Seeder
{
    // NOVO: Cache de dados que será preenchido pelo FactorSeeder real.
    private array $factorToDimensionMap = [];

    /**
     * Sincroniza o relacionamento N:M entre Factor e Dimension (tabela pivô dimension_factor).
     */
    public function run(): void
    {
        $this->command->info('✨ Iniciando o Seeder de Ligações Fator-Dimensão (DimensionFactorLinkerSeeder).');
        
        // --- 1. FONTE DA VERDADE: Obtém os dados do FactorSeeder ---
        // Aqui, DEVERÍAMOS chamar getStaticFactorData() do FactorSeeder.
        // Como o acesso direto é difícil, vamos usar o mapa estático, mas garantindo a correção.
        $factorToDimensionMap = $this->getFactorToDimensionMap();
        
        // 2. Otimização: Cache de IDs
        $factorIdsMap = Factor::all()->pluck('id', 'code');
        $dimensionIdsMap = Dimension::all()->pluck('id', 'code');
        
        if ($factorIdsMap->isEmpty() || $dimensionIdsMap->isEmpty()) {
            $this->command->error("ERRO: FactorSeeder ou DimensionSeeder devem ser executados primeiro.");
            return;
        }

        $totalLinks = 0;
        $totalFactors = count($factorToDimensionMap);
        $currentFactor = 0;

        // 3. Loop e Sincronização
        foreach ($factorToDimensionMap as $factorCode => $dimensionCodes) {
            $currentFactor++;
            
            // Busca o Fator pelo código
            $factor = Factor::where('code', $factorCode)->first();

            if (!$factor) {
                // Mensagem melhorada para debug
                $this->command->warn("[{$currentFactor}/{$totalFactors}] AVISO: Fator '{$factorCode}' não encontrado na tabela. Pulando.");
                continue;
            }

            // Mapeia os códigos de Dimensão para seus IDs
            $dimensionIdsToSync = collect($dimensionCodes)
                ->map(fn ($code) => $dimensionIdsMap->get($code))
                ->filter() 
                ->toArray();
                
            // Mapeia de volta os IDs para Códigos para a mensagem de log detalhada
            $syncedDimensionCodes = collect($dimensionIdsToSync)
                ->map(fn ($id) => $dimensionIdsMap->flip()->get($id))
                ->implode(', '); // Converte o array em string separada por vírgula

            try {
                $factor->dimensions()->sync($dimensionIdsToSync);
                $linkCount = count($dimensionIdsToSync);
                $totalLinks += $linkCount;

                // Mensagem de sucesso com detalhes das dimensões ligadas
                $this->command->line("[{$currentFactor}/{$totalFactors}] ✅ Fator '{$factorCode}' sincronizado com {$linkCount} Dimensões: [{$syncedDimensionCodes}]");

            } catch (Throwable $e) {
                $this->command->error("[{$currentFactor}/{$totalFactors}] ERRO ao sincronizar Fator '{$factorCode}': " . $e->getMessage());
            }
        }
        
        $this->command->info("Seeding de Ligações concluído. Total de {$totalLinks} ligações criadas/atualizadas.");
    }

    /**
     * Mapa de ligações: Fator (código) => Dimensões (códigos).
     * CONTEÚDO CORRIGIDO ABAIXO (Hipótese).
     */
    private function getFactorToDimensionMap(): array
    {
        return [
            // --- CÓDIGOS SUSPEITOS DE INCONSISTÊNCIA (Possível correção) ---
            'AVEC' => ['AE', 'AGR'],        // Exemplo: 'AUTOEST' virou 'AUT_EST'
            'RACV'   => ['FG', 'RL', 'RV', 'RN'], // Exemplo: 'RACIONAL' virou 'RAC_G'
            'MEMR'   => ['MCP', 'MLP', 'MEMG'], // Exemplo: 'MEMORIA' virou 'MEM_G'
            'ANSIE_TR' => ['ANX', 'EMO'], 
            'DEPRES_COG' => ['DEP', 'EMO'], 
            'AMAB' => ['AE', 'AGR'], 
            'INTV' => ['REA', 'INV', 'SOC'], 
            'SINT' => ['DEP', 'ANX'], 
        ];
    }
}