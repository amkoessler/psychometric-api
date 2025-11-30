<?php

namespace App\Http\Controllers;

use App\Models\Scale;
use App\Models\ResponseOption;
use App\Http\Resources\ResponseOptionResource;
use App\Http\Requests\StoreResponseOptionRequest;
use App\Http\Requests\UpdateResponseOptionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class ResponseOptionController extends Controller
{

/**
     * Retorna UMA única opção de resposta pelo ID da chave primária.
     * Comportamento de 'show', nomeado como 'index' a seu pedido (Método simples).
     * Rota: GET /api/response-options/{id}
     */
    public function index(string $id) // <-- Agora recebe o ID como string simples
    {
        // 1. Busca manual do registro
        $responseOption = ResponseOption::find($id);

        // 2. Verifica se encontrou o registro
        if (!$responseOption) {
            return response()->json([
                'message' => "Opção de resposta com ID '{$id}' não encontrada.",
            ], 404);
        }
        
        // 3. Retorna o item único
        return new ResponseOptionResource($responseOption);
    }



    /**
     * Lista todos os códigos de escala disponíveis. Opcionalmente, inclui
     * os detalhes das opções de resposta se ?include=options for fornecido.
     * Rota: GET /api/response-options[?include=options]
     */
    public function listScales(Request $request)
    {
        // 1. Prepara a query base no Model Scale.
        $query = Scale::query();
        
        // 2. Verifica se o parâmetro 'include=options' foi solicitado.
        $includeOptions = $request->query('include') === 'options';

        if ($includeOptions) {
            // 3. Se solicitado, usa Eager Loading para carregar as opções de resposta.
            //    Aplica um constraint para ordenar as opções pelo 'score_value'.
            $query->with(['responseOptions' => function ($q) {
                $q->orderBy('score_value', 'asc');
            }]);
        }

        // 4. Executa a query.
        //    Ordena as escalas por código, o que é mais útil.
        $scales = $query->orderBy('code', 'asc')->get();

        if ($scales->isEmpty()) {
            return response()->json([
                'message' => 'Nenhuma escala encontrada.',
            ], 404);
        }

        // 5. Mapeia o resultado final.
        //    Como não temos um ScaleResource, vamos criar a saída na mão para fins de demonstração
        //    e para manter a estrutura de saída similar à sua intenção.
        return $scales->map(function (Scale $scale) use ($includeOptions) {
            $data = [
                'id' => $scale->id,
                'code' => $scale->code,
                'name' => $scale->name,
                'description' => $scale->description,
                'options_count' => $scale->responseOptions->count(), // Conta baseada no Eager Load (se carregado)
            ];

            if ($includeOptions) {
                // Se as opções foram carregadas, anexa a coleção formatada pelo Resource.
                $data['options'] = ResponseOptionResource::collection($scale->responseOptions);
            }
            
            return $data;
        });
    }

    /**
     * Busca um conjunto específico de opções de resposta baseado no scale_code.
     * Rota: GET /api/response-options/{scaleCode}
     */
    public function showByCode(string $scaleCode)
    {
        $scaleCode = strtoupper($scaleCode);

        // 1. Busca  a Escala pelo código
        $scale = Scale::where('code', $scaleCode)->first();

        if (!$scale) {
            return response()->json([
                'message' => "Código de escala não encontrado: {$scaleCode}",
            ], 404);
        }

        // 2. Use o ID da Scale para buscar as ResponseOptions relacionadas
        //    A coluna de busca agora é 'scale_id'
        $options = ResponseOption::where('scale_id', $scale->id)
                                 ->orderBy('score_value', 'asc')
                                 ->get();

        if ($options->isEmpty()) {
            return response()->json([
                'message' => "Nenhuma opção encontrada para o código de escala: {$scaleCode}",
            ], 404);
        }

        // Retorna a coleção de opções formatada
        return ResponseOptionResource::collection($options);
    }
    
    
    // -------------------------------------------------------------------------
    // MÉTODOS CRUD (POST, PATCH, DELETE)
    // -------------------------------------------------------------------------

    /**
     * Cria uma nova opção de resposta.
     * Rota: POST /api/response-options
     */
    public function store(StoreResponseOptionRequest $request)
    {
        $option = ResponseOption::create($request->validated());
        
        // 201 Created
        return (new ResponseOptionResource($option))
                    ->response()
                    ->setStatusCode(201);
    }

    /**
     * Atualiza uma opção de resposta específica.
     * Rota: PATCH /api/response-options/{id}
     */
    public function update(UpdateResponseOptionRequest $request, string $id)
    {
        $option = ResponseOption::find($id);

        if (!$option) {
            return response()->json(['message' => 'Opção de resposta não encontrada.'], 404);
        }

        $option->update($request->validated());

        return new ResponseOptionResource($option);
    }

    /**
     * Remove uma opção de resposta específica.
     * Rota: DELETE /api/response-options/{id}
     */
    public function destroy(string $id)
    {
        $option = ResponseOption::find($id);

        if (!$option) {
            return response()->json(['message' => 'Opção de resposta não encontrada.'], 404);
        }

        try {
            $option->delete();
            
            // 204 No Content
            return response()->json(null, 204);

        } catch (QueryException $e) {
            // Trata a exceção de violação de chave estrangeira
            if ($e->getCode() == '23000' || $e->getCode() == '23503') {
                 return response()->json([
                    'message' => 'Não é possível excluir esta opção de resposta. Ela está em uso por uma ou mais perguntas.',
                ], 409); // 409 Conflict
            }
            throw $e;
        }
    }

    /**
     * Renomeia o scale_code de um conjunto inteiro de opções de resposta.
     * Rota: PATCH /api/response-options/rename
     */
    public function renameScale(Request $request)
    {
        // 1. Validação Manual: Garantir que os códigos estão presentes e são strings.
        try {
            $request->validate([
                'old_code' => ['required', 'string', 'max:50'],
                'new_code' => ['required', 'string', 'max:50', 'different:old_code'], // Novo código deve ser diferente
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $e->errors(),
            ], 422);
        }

        $oldCode = strtoupper($request->input('old_code'));
        $newCode = strtoupper($request->input('new_code'));

        // 2. Verifica se o código antigo existe antes de tentar a atualização.
        $count = ResponseOption::where('scale_code', $oldCode)->count();

        if ($count === 0) {
            return response()->json([
                'message' => "Nenhuma opção de resposta encontrada com o código '{$oldCode}'.",
            ], 404);
        }

        // 3. Executa a Atualização em Massa (Bulk Update).
        ResponseOption::where('scale_code', $oldCode)->update([
            'scale_code' => $newCode
        ]);

        // 4. Retorno de Sucesso.
        return response()->json([
            'message' => "Sucesso! O código de escala '{$oldCode}' foi renomeado para '{$newCode}'.",
            'updated_count' => $count,
            'new_code' => $newCode,
        ], 200);
    }
}