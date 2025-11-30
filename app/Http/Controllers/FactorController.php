<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Factor;
use App\Http\Resources\FactorResource; 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FactorController extends Controller
{
    /**
     * Retorna uma lista de todos os Fatores ativos, com opção de incluir Dimensões.
     */
    public function index(Request $request): JsonResource 
    {
        // 1. Inicia a query com os filtros padrão: buscar apenas ativos e ordenar por código.
        $query = Factor::where('is_active', true)
                        ->orderBy('code');
        
        // 2. Verifica o parâmetro ?include=dimensions
        if ($request->query('include') === 'dimensions') {
            // Se sim, carrega as dimensões (Eager Loading)
            $query->with('dimensions');
        }

        // 3. Executa a query
        $factors = $query->get();

        // 4. Retorna a coleção
        return FactorResource::collection($factors);
    }

    /**
     * Sincroniza o conjunto de Dimensões (IDs) para um Fator específico.
     * Rota: PUT/PATCH /api/factors/{id}/dimensions
     * @param Request $request
     * @param int $id ID do Fator
     */
    public function syncDimensions(Request $request, int $id): FactorResource
    {
        // 1. Validação: Garante que dimension_ids seja um array de IDs existentes
        $request->validate([
            'dimension_ids' => 'required|array',
            'dimension_ids.*' => 'exists:dimensions,id', 
        ]);

        // 2. Busca o Fator
        $factor = Factor::findOrFail($id);

        // 3. Sincronização: Usa o método sync() para atualizar a tabela pivô 'dimension_factor'
        $factor->dimensions()->sync($request->input('dimension_ids'));

        // 4. Recarrega o fator com as dimensões para o retorno
        $factor->load('dimensions');
        
        // 5. Retorna o Fator atualizado (com as dimensões carregadas)
        return FactorResource::make($factor);
    }
}