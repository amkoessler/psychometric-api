<?php

use App\Http\Controllers\QuestionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController; 
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\DimensionController;
use App\Http\Controllers\ResponseOptionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Adicionando a rota de Status
Route::get('/status', function () {
    return response()->json([
        'status' => 'ok',
        'environment' => config('app.env'),
    ]);
});

Route::apiResource('patients', PatientController::class);
// NOVO: Rota para o recurso Questionnaires
Route::apiResource('questionnaires', QuestionnaireController::class); 
// Rota para buscar um questionário pelo seu código
// Ex: GET /api/questionnaires/code/BDI-II
Route::get('questionnaires/code/{code}', [QuestionnaireController::class, 'showByCode']);
// Rota para buscar todas as questões de um questionário (usando o código)
Route::get('questions/code/{code}', [QuestionController::class, 'getQuestionsByQuestionnaireCode']);
// Rota para Grandes Áreas de Avaliação
Route::get('areas', [AreaController::class, 'index']);
// Rota para Dimensões
Route::get('dimensions', [DimensionController::class, 'index']);

  #######################
 ## Response Options ###
#######################
// Deve vir antes da rota GET /{scaleCode}
Route::patch('response-options/rename', [ResponseOptionController::class, 'renameScale']);
// Endpoint para buscar as opções de resposta por código da escala
// Exemplo de uso: GET /api/response-options/LIKERT_6_PONTOS_NORMAL
Route::get('response-options/code/{scaleCode}',[ResponseOptionController::class, 'showByCode']);
Route::get('response-options/scales', [ResponseOptionController::class, 'listScales']);
Route::get('response-options/{id}', [ResponseOptionController::class, 'index']);
// Rotas de ESCRITA (CRUD Faltante)
Route::post('response-options', [ResponseOptionController::class, 'store']);
Route::patch('response-options/{id}', [ResponseOptionController::class, 'update']); // Usamos ID da opção, não o scaleCode
Route::delete('response-options/{id}', [ResponseOptionController::class, 'destroy']);

  ##############
 ### P U T ####
##############
// Rota de Escrita (PUT): Sincroniza dimensões para uma área específica
Route::put('areas/{id}/dimensions', [AreaController::class, 'syncDimensions']);
