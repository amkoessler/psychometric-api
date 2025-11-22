<?php

namespace App\Http\Controllers;

use App\Models\ResponseOption;
use App\Http\Resources\ResponseOptionResource;
use App\Http\Requests\StoreResponseOptionRequest;
use App\Http\Requests\UpdateResponseOptionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\QueryException;

class ResponseOptionController extends Controller
{
    /**
     * Lista todos os códigos de escala disponíveis. Opcionalmente, inclui
     * os detalhes das opções de resposta se ?include=options for fornecido.
     * Rota: GET /api/response-options[?include=options]
     */
    public function index(Request $request)
    {
        // 1. Verifica se o parâmetro 'include=options' foi solicitado.
        $includeOptions = $request->query('include') === 'options';

        // 2. Query Base: Busca códigos de escala distintos e suas contagens.
        $scales = ResponseOption::select('scale_code')
                                ->selectRaw('COUNT(id) as options_count')
                                ->groupBy('scale_code')
                                ->orderBy('scale_code', 'asc')
                                ->get();

        if ($includeOptions) {
            // Se solicitado, busca TODOS os detalhes das opções e agrupa.
            $allOptions = ResponseOption::orderBy('score_value')
                                        ->get()
                                        ->groupBy('scale_code');
            
            // 3. CORREÇÃO DO ERRO: Mapeia sobre a coleção de escalas para ANEXAR
            // a lista de opções formatada, evitando o conflito do Resource.
            $scales = $scales->map(function ($scale) use ($allOptions) {
                $scaleCode = $scale->scale_code;
                
                // Converte o objeto Eloquent/DB em array
                $data = $scale->toArray();
                
                if (isset($allOptions[$scaleCode])) {
                    // Aplica o Resource Collection para formatar a lista de opções anexada
                    $data['options'] = ResponseOptionResource::collection($allOptions[$scaleCode]);
                } else {
                    $data['options'] = []; 
                }
                
                // Retorna o array com a chave 'options' incluída.
                return $data;
            });
        }

        // 4. Retorna o resultado final.
        return JsonResource::make(['data' => $scales]);
    }

    /**
     * Busca um conjunto específico de opções de resposta baseado no scale_code.
     * Rota: GET /api/response-options/{scaleCode}
     */
    public function showByCode(string $scaleCode)
    {
        $scaleCode = strtoupper($scaleCode);

        $options = ResponseOption::where('scale_code', $scaleCode)
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
        // A função update() é mais rápida para este tipo de alteração.
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