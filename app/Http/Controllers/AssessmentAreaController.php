<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AssessmentArea;
use App\Http\Resources\AssessmentAreaResource; 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource; // Para retornar uma coleção simples

class AssessmentAreaController extends Controller
{
    /**
     * Retorna uma lista de todas as Áreas de Avaliação ativas.
     */
    public function index(): JsonResource
    {
        // 1. Busca todas as áreas ativas, ordenadas por código (ou nome)
        $areas = AssessmentArea::where('is_active', true)
                                ->with('dimensions')
                                ->orderBy('code')
                                ->get();

        // 2. Retorna a coleção diretamente
        // Usamos JsonResource para garantir que o formato JSON seja padrão e limpo.
        return AssessmentAreaResource::collection($areas);
    }

    /**
     * Sincroniza o conjunto de Dimensões (IDs) para uma Área de Avaliação específica.
     * @param Request $request
     * @param int $id ID da AssessmentArea
     */
    public function syncDimensions(Request $request, int $id): AssessmentAreaResource
    {
        // 1. Validação: Garante que dimension_ids seja um array de IDs existentes
        $request->validate([
            'dimension_ids' => 'required|array',
            'dimension_ids.*' => 'exists:dimensions,id', 
        ]);

        // 2. Busca a Área
        $area = AssessmentArea::findOrFail($id);

        // 3. Sincronização: Usa o método sync() para atualizar a tabela pivô
        // O sync() remove as ligações que não estão na lista e adiciona as novas.
        $area->dimensions()->sync($request->input('dimension_ids'));

        // 4. Retorna a Área atualizada com as novas dimensões
        $area->load('dimensions');
        
        // Usamos make() pois estamos retornando apenas um item (a área atualizada)
        return AssessmentAreaResource::make($area);
    }
}