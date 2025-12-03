<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\QuestionnaireResource;
use App\Models\Questionnaire;
use Illuminate\Support\Facades\Log ;
// use Mews\Purifier\Facades\Purifier; // Importação necessária (será descomentada depois)


// ====================================================================
        // 🚨 LEMBRETE DE SEGURANÇA CRÍTICO: SANITIZAÇÃO DE HTML (WYSIWYG)
        //
        // O campo 'description' está sendo renderizado no frontend (React)
        // via dangerouslySetInnerHTML. Isso torna a aplicação VULNERÁVEL a
        // ataques XSS (Cross-Site Scripting) se o conteúdo vier de um editor
        // WYSIWYG e não for limpo (sanitizado).
        //
        // A SOLUÇÃO CORRETA é instalar o 'HTML Purifier' e usá-lo AQUI.
        //
        // Exemplo (após instalar e importar o Purifier):
        // $cleanDescription = Purifier::clean($request->input('description'));
        // ====================================================================

class QuestionnaireController extends Controller
{

    /**
     * Retorna uma lista de todos os questionários, com carregamento dinâmico de relacionamentos.
     */
    public function index(Request $request) // <--- Recebe o Request
    {
        // 1. Processa o parâmetro 'include'.
        // explode(',', ...) transforma a string (ex: "questions,assessmentAreas") em um array.
        // array_filter(...) remove qualquer string vazia que possa surgir.
        $relations = array_filter(explode(',', $request->query('include', '')));
        
        // 2. Inicia a query builder
        $query = Questionnaire::query();
        
        // 3. Aplica o carregamento dinâmico APENAS se houver relacionamentos solicitados.
        if (!empty($relations)) {
            $query->with($relations);
        }

        // 4. Busca todos os questionários com os relacionamentos carregados (se houver)
        $questionnaires = $query->get();

        // 5. Retorna a coleção formatada pelo Resource
        // O Resource usará o whenLoaded() e só incluirá os dados se eles tiverem sido carregados acima.
        return QuestionnaireResource::collection($questionnaires);
    }


    //  Busca um questionário específico com base no seu acrônimo/código.
    public function showByCode(string $code, Request $request)
    {
        // // 1. Garante que o código de busca está em MAIÚSCULAS
        // $code = strtoupper($code);

        // 2. Processa o parâmetro 'include' para carregamento dinâmico.
        $relations = array_filter(explode(',', $request->query('include', '')));

        Log::info('O Codigo pesquisado é '. $code);
        // 3. Inicia e constrói a query:
        $query = Questionnaire::where('code', $code); 
        
        // 4. Aplica o carregamento dinâmico (Eager Loading).
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        // 5. EXECUTA a busca e pega APENAS o primeiro modelo encontrado.
        // O método ->first() retorna o modelo ou null.
        $questionnaire = $query->first(); 

        // 6. Verifica se o questionário foi encontrado.
        if (!$questionnaire) { // <-- Verifica se o resultado é null
            return response()->json([
                'message' => 'Questionário não encontrado.',
            ], 404);
        }

        // 7. Retorna o questionário formatado pelo Resource.
        // Agora o $questionnaire é um único objeto Model, conforme esperado.
        return new QuestionnaireResource($questionnaire);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
