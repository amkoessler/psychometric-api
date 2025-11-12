<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\QuestionnaireResource;
use App\Models\Questionnaire;

class QuestionnaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Busca todos os questionários
        $questionnaires = Questionnaire::all();

        // 2. Retorna a coleção formatada pelo Resource
        return QuestionnaireResource::collection($questionnaires);
    }


    // NOVO MÉTODO: Busca um questionário específico com base no seu acrônimo/código.
    public function showByCode(string $code)
    {
        // 1. Garante que o código de busca está em MAIÚSCULAS (consistência com os Seeders)
        $code = strtoupper($code);

        // 2. Busca o questionário pela coluna 'code'.
        $questionnaire = Questionnaire::where('code', $code)->first(); // Usa a coluna 'code'

        // 3. Verifica se o questionário foi encontrado.
        if (!$questionnaire) {
            return response()->json([
                'message' => 'Questionário não encontrado.',
            ], 404);
        }

        // 4. Retorna o questionário formatado pelo Resource.
        // O Resource garante que a saída JSON estará sempre no formato esperado.
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
