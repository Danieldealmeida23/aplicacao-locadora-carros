<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('v1')->middleware('jwt.auth')->group(function (){
    Route::apiResource('cliente', App\Http\Controllers\ClienteController::Class);
    Route::apiResource('carro', App\Http\Controllers\CarroController::Class);
    Route::apiResource('locacao', App\Http\Controllers\LocacaoController::Class);
    Route::apiResource('marca', App\Http\Controllers\MarcaController::Class);
    Route::apiResource('modelo', App\Http\Controllers\ModeloController::Class);
    Route::post('me', 'App\Http\Controllers\AuthController@me');
    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
    Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');
});


Route::post('login', 'App\Http\Controllers\AuthController@login');

