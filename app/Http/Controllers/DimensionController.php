<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Dimension; // Importe o Model correto
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\DimensionResource;

class DimensionController extends Controller
{
/**
     * Retorna uma lista de todas as Dimensões, com opção de incluir inativas para gerenciamento.
     */
    public function index(Request $request): JsonResource // <--- Recebe o Request
    {
        // 1. Inicia a query builder
        $query = Dimension::orderBy('code');
        
        // 2. Verifica se o parâmetro 'all' foi passado (ex: ?all=true)
        $includeAll = filter_var($request->query('all'), FILTER_VALIDATE_BOOLEAN);

        // 3. Aplica o filtro SOMENTE se o modo "all" não foi ativado.
        if (!$includeAll) {
            $query->where('is_active', true); 
        }
        
        // 4. Busca as dimensões (ativas, ou todas se ?all=true)
        $dimensions = $query->orderBy('code','asc')->get();

        // 5. Retorna a coleção
        return DimensionResource::collection($dimensions); 
    }
}