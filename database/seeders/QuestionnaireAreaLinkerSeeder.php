<?php

namespace Database\Seeders;

use App\Models\Questionnaire;
use App\Models\AssessmentArea;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionnaireAreaLinkerSeeder extends Seeder
{
    /**
     * Executa a propagação (seeding) dos dados.
     */
    public function run(): void
    {
        // Limpa a tabela pivô antes de rodar, garantindo que não haja duplicatas
        // e que apenas as associações definidas neste seeder permaneçam.
        DB::table('assessment_area_questionnaire')->truncate();

        // 1. Mapear Áreas para acesso rápido (código => id)
        // Isso é uma otimização: evita múltiplas consultas dentro do loop.
        $areaIds = AssessmentArea::all()->pluck('id', 'code');
        
        // 2. Obter o mapa de associações.
        $questionnaireToAreaMap = $this->getQuestionnaireToAreaMap();

        foreach ($questionnaireToAreaMap as $questionnaireCode => $areaCodes) {
            
            // Busca o Questionário pelo código
            $questionnaire = Questionnaire::where('code', $questionnaireCode)->first();
            
            if (!$questionnaire) {
                echo "AVISO: Questionário com código '{$questionnaireCode}' não encontrado. Pulando ligação.\n";
                continue;
            }

            // Mapeia os códigos de Áreas para seus respectivos IDs (usando o cache $areaIds)
            $areaIdsToAttach = collect($areaCodes)
                ->map(fn ($code) => $areaIds[$code] ?? null) // Mapeia código para ID
                ->filter() // Remove entradas nulas (se a área não existir)
                ->toArray();
            
            // Executa a Ligação usando o relacionamento belongsToMany.
            // O método sync() cuida da inserção na tabela pivô.
            $questionnaire->assessmentAreas()->sync($areaIdsToAttach);

            echo "Ligadas " . count($areaIdsToAttach) . " Áreas de Avaliação ao Questionário: {$questionnaireCode}\n";
        }
    }


    /**
     * Define o mapa de ligações: Questionário (código) => Áreas (códigos).
     * Contém todos os dados fornecidos e agrupados.
     */
    private function getQuestionnaireToAreaMap(): array
    {
        return [
            // Total de 16 Questionários únicos
            'NEO-PI-R' => ['EMO', 'PER'],
            'BDI-II' => ['EMO', 'PER'],
            'WAIS-IV' => ['COG', 'NEU'],
            'TAT' => ['PER', 'PRO'],
            'G-36' => ['COG'],
            'RAVEN' => ['COG'],
            'HOLLAND' => ['INT'],
            'BAI' => ['EMO'],
            'AC' => ['APT', 'COG'],
            'HAM-A' => ['EMO', 'PER'],
            'RSES' => ['EMO', 'PER'],
            'PALO' => ['APT', 'PER'],
            'RORSCHACH' => ['PER', 'PRO'],
            'IEP' => ['PER'],
            'PCL-5' => ['EMO', 'NEU'],
            'DFH-IV' => ['COG', 'PRO'],
            'IFP-II' => ['INT', 'PER'],
        ];
    }
}