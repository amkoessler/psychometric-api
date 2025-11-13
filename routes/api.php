<?php

use App\Http\Controllers\Api\QuestionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController; 
use App\Http\Controllers\QuestionnaireController;

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
Route::apiResource('questionnaires', QuestionnaireController::class); // <-- ADICIONEIESTA LINHA
// Rota para buscar um questionário pelo seu código
// Ex: GET /api/questionnaires/code/BDI-II
Route::get('questionnaires/code/{code}', [QuestionnaireController::class, 'showByCode']);
// Rota para buscar todas as questões de um questionário (usando o código)
Route::get('questions/code/{code}', [QuestionController::class, 'getQuestionsByQuestionnaireCode']);