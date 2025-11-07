<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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