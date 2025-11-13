<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Questionnaire;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QuestionController extends Controller
{
    /**
     * Retorna todas as questões de um questionário específico, usando o código.
     * Rota: GET /api/questions/code/{code}
     */
    public function getQuestionsByQuestionnaireCode(string $code): JsonResponse
    {
        // 1. Busca o Questionário pelo 'code' e falha se não for encontrado (404)
        $questionnaire = Questionnaire::where('code', $code)
            ->firstOrFail();

        // 2. Carrega as questões relacionadas, ordenadas para a exibição (display_order)
        $questions = $questionnaire->questions()
            ->orderBy('display_order', 'asc')
            ->get();

        // 3. Retorna a coleção de questões formatada pelo Resource
        return QuestionResource::collection($questions)->response();
    }
}