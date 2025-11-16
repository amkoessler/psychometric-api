<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Dimension; // Importe o Model correto
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DimensionController extends Controller
{
    /**
     * Retorna uma lista de todas as Dimensões ativas.
     */
    public function index(): JsonResource
    {
        // Busca todas as dimensões ativas, ordenadas por código
        $dimensions = Dimension::where('is_active', true)
                                ->orderBy('code')
                                ->get();

        // Retorna a coleção
        return JsonResource::collection($dimensions);
    }
}