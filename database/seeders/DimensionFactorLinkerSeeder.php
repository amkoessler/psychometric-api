<?php

namespace Database\Seeders;

use App\Models\Dimension;
use App\Models\Factor;
use Illuminate\Database\Seeder;
use Throwable;

class DimensionFactorLinkerSeeder extends Seeder
{
    /**
     * Sincroniza o relacionamento N:M entre Factor e Dimension (tabela pivô dimension_factor).
     */
    public function run(): void
    {
        // NOVO: Inicializa contadores
        $totalLinks = 0;
        $processedFactors = 0;
        $successFactors = 0;
        $errorFactors = 0;

        // --- 1. FONTE DA VERDADE: Obtém o mapa de ligações ---
        $factorToDimensionMap = $this->getFactorToDimensionMap();
        $totalFactors = count($factorToDimensionMap);

        // Feedback de Início
        $this->command->info('✨ Iniciando o Seeder de Ligações Fator-Dimensão (DimensionFactorLinkerSeeder). Total de Fatores a processar: ' . $totalFactors);
        $this->command->newLine();
        
        // 2. Otimização: Cache de IDs
        $factorIdsMap = Factor::all()->pluck('id', 'code');
        $dimensionIdsMap = Dimension::all()->pluck('id', 'code');
        
        if ($factorIdsMap->isEmpty() || $dimensionIdsMap->isEmpty()) {
            $this->command->error("ERRO: FactorSeeder ou DimensionSeeder devem ser executados primeiro. Abortando.");
            return;
        }

        // 3. Loop principal
        foreach ($factorToDimensionMap as $factorCode => $dimensionCodes) {
            $processedFactors++;
            
            // Tenta encontrar o Fator
            $factor = Factor::where('code', $factorCode)->first();

            if (!$factor) {
                $this->command->error("[{$processedFactors}/{$totalFactors}] ERRO: Fator '{$factorCode}' não encontrado no banco de dados.");
                $errorFactors++;
                continue;
            }

            // Mapeia os códigos das dimensões para seus IDs. Filtra códigos inexistentes.
            $dimensionIdsToSync = [];
            $syncedDimensionCodes = [];

            foreach ($dimensionCodes as $dimCode) {
                if (isset($dimensionIdsMap[$dimCode])) {
                    $dimensionIdsToSync[] = $dimensionIdsMap[$dimCode];
                    $syncedDimensionCodes[] = $dimCode;
                }
            }

            try {
                // Sincroniza o relacionamento (cria/atualiza/deleta ligações)
                $factor->dimensions()->sync($dimensionIdsToSync);
                
                $linkCount = count($dimensionIdsToSync);
                $totalLinks += $linkCount;
                $successFactors++;

                // Mensagem de sucesso com detalhes das dimensões ligadas
                $syncedDimensionList = implode(', ', $syncedDimensionCodes);
                $this->command->info("[{$processedFactors}/{$totalFactors}] ✅ SUCESSO: Fator '{$factorCode}' sincronizado com {$linkCount} Dimensões: [{$syncedDimensionList}]");

            } catch (Throwable $e) {
                $this->command->error("[{$processedFactors}/{$totalFactors}] ❌ ERRO FATAL ao sincronizar Fator '{$factorCode}': " . $e->getMessage());
                $errorFactors++;
            }
        }
        
        // --- Feedback Final ---
        $this->command->newLine();
        if ($errorFactors === 0) {
            $this->command->info("🎉 Seeding de Ligações concluído com sucesso! Total de Fatores processados: {$processedFactors}.");
        } else {
            $this->command->warn("⚠️ Seeding de Ligações concluído com {$errorFactors} erro(s). Total de Fatores processados: {$processedFactors}.");
        }
        $this->command->info("Total de {$totalLinks} ligações na tabela pivô 'dimension_factor' criadas/atualizadas.");
    }

    /**
     * Mapa de ligações: Fator (código) => Dimensões (códigos).
     * CONTEÚDO CORRIGIDO ABAIXO (DEVE SER PREENCHIDO COM SEUS DADOS).
     */
    private function getFactorToDimensionMap(): array
    {
        // TODO: PREENCHER COM OS DADOS CORRETOS DO SEU PROJETO
        return [
            // Exemplo da nossa nova estrutura:
            'AVEC' => ['AE', 'AGR'],   
            'AFIL' => ['EXT', 'NAFIL'],        
            'AGRS' => [ 'CEXT'],        
            'AMAB' => ['AE', 'AGR'], 
            'MEMR'   => ['MCP', 'MLP'], 
            'ASST' => ['AGR'], 
            'A' => ['ETDAH-PAIS', 'AC','AD','AA'],
            'AUTI' => ['EXT', 'OPN'], 
            'CHNEG' => ['DEP', 'EST'], 
            'CAFET' => ['DEP', 'EST'], 
            'CA' => ['ETDAH-PAIS', 'EXT','CSC'],
            'COMP' => ['FG', 'RV'], 
            'CUID' => ['AGR'], 
            'DOMP' => ['EXT'], 
            'EVIT' => ['ANX'], 
            'EXPO' => ['EXT'], 
            'EXTV' => ['EXT','SOC'], 
            'DHFM' => ['FG'], 
            'DHFF' => ['FG'], 
            'AETM' => ['AE'], 
            'PENS' => ['FE','AA'], 
            'HI' => ['ETDAH-PAIS', 'CEXT'],
            'HIPA' => ['EST', 'CEXT'],
            'INOV' => ['OPN'],
            'INTV' => ['REA', 'INV', 'SOC'], 
            'NEUR' => ['ANX', 'DEP', 'EST'], 
            'ORGZ' => ['CSC'], 
            'PERS' => ['CSC'], 
            'RAOB' => ['RL','RA'], 
            'RACV'   => ['FG', 'RL', 'RV', 'RN'], 
            'RACS' => ['AGR', 'EST'], 
            'REALZ' => ['CSC', 'NREAL'], 
            'RE' => ['ETDAH-PAIS', 'ANX','DEP','EST'],
            'SINT' => ['DEP', 'ANX'], 
            'INTRU' => ['ANX', 'EST'], 
            'SUBM' => ['CSC', 'AGR'], 
        ];
    }
}

