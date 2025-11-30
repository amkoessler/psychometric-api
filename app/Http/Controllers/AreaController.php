<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Http\Resources\AreaResource; 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource; // Para retornar uma coleção simples

class AreaController extends Controller
{
    /**
     * Retorna uma lista de todas as Áreas de Avaliação ativas.
     */
    public function index(Request $request): JsonResource //  Recebe o Request "include"
    {
        // 1. Inicia a query com os filtros padrão
        $query = Area::where('is_active', true)
                                ->orderBy('code');
        
        // 2. Verifica se o parâmetro ?include=dimensions foi passado na URL
        if ($request->query('include') === 'dimensions') {
            // Se sim, carrega as dimensões (Eager Loading)
            $query->with('dimensions');
        }

        // 3. Executa a query
        $areas = $query->get();

        // 4. O Resource (AreaResource) usará whenLoaded() e incluirá as dimensões
        // somente se elas foram carregadas acima.
        return AreaResource::collection($areas);
    }

    /**
     * Sincroniza o conjunto de Dimensões (IDs) para uma Área de Avaliação específica.
     * @param Request $request
     * @param int $id ID da Area
     */
    public function syncDimensions(Request $request, int $id): AreaResource
    {
        // 1. Validação: Garante que dimension_ids seja um array de IDs existentes
        $request->validate([
            'dimension_ids' => 'required|array',
            'dimension_ids.*' => 'exists:dimensions,id', 
        ]);

        // 2. Busca a Área
        $area = Area::findOrFail($id);

        // 3. Sincronização: Usa o método sync() para atualizar a tabela pivô
        // O sync() remove as ligações que não estão na lista e adiciona as novas.
        $area->dimensions()->sync($request->input('dimension_ids'));

        // 4. Retorna a Área atualizada com as novas dimensões
        $area->load('dimensions');
        
        // Usamos make() pois estamos retornando apenas um item (a área atualizada)
        return AreaResource::make($area);
    }
}