<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AssessmentArea; 
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
                                ->orderBy('code')
                                ->get();

        // 2. Retorna a coleção diretamente
        // Usamos JsonResource para garantir que o formato JSON seja padrão e limpo.
        return JsonResource::collection($areas);
    }
}