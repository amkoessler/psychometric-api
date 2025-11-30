<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Questionnaire;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QuestionController extends Controller
{

    /**
     * Retorna uma questão específica pelo seu ID.
     * Rota: GET /api/questions/{id}
     */
    public function show(int $id, Request $request): JsonResponse
{
        $relations = array_filter(explode(',', $request->query('include', '')));

        // 1. Busca a Questão pelo ID
        $query = Question::where('id', $id);

        if (!empty($relations)) {
            $query->with($relations);
        }

        // 2. Executa a busca (USANDO first() EM VEZ DE firstOrFail())
        $question = $query->first(); 

        // 3. Validação Explícita: Se a questão NÃO for encontrada
        if (!$question) {
            // Retorna um erro 404 com uma mensagem JSON clara
            return response()->json([
                'error' => true,
                'message' => "Questão com ID '{$id}' não encontrada.",
                'details' => 'Verifique se o ID está correto ou se a questão existe no banco de dados.'
            ], 404); // <-- O status 404 é crucial
        }
        
        // 4. Se a questão for encontrada, retorna formatada
        return QuestionResource::make($question)->response();
    }

    /**
     * Retorna todas as questões de um questionário específico, usando o código.
     * Rota: GET /api/questions/code/{code}
     */
    public function getQuestionsByQuestionnaireCode(string $code, Request $request): JsonResponse
    {
        // 1. Garante que o código de busca está em MAIÚSCULAS para consistência
        $code = strtoupper($code);

        // 2. Processa o parâmetro 'include' do Request.
        // O cliente pode passar ?include=options, que será transformado em ['options']
        $relations = array_filter(explode(',', $request->query('include', '')));

        // 3. Busca o Questionário pelo 'code' e falha se não for encontrado (404)
        $questionnaire = Questionnaire::where('code', $code)
            ->firstOrFail();

        // 4. Inicia a query builder para as questões (a partir do relacionamento)
        $query = $questionnaire->questions();

        // 5. Aplica o carregamento dinâmico (Eager Loading) APENAS se houver relacionamentos solicitados.
        // Isso é crucial: se o cliente pedir ?include=options, o relacionamento 'options' 
        // será carregado nas questões.
        if (!empty($relations)){
            $query->with($relations);
        }

        // 6. Executa a busca das questões, ordenadas para a exibição
        $questions = $query->orderBy('display_order','asc')->get();

        // 7. Retorna a coleção de questões formatada pelo Resource
        // O QuestionResource deve usar whenLoaded('options') para incluí-las.
        return QuestionResource::collection($questions)->response();
    }
}