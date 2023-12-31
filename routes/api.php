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
    Route::apiResource('cliente', 'App\Http\Controllers\ClienteController');
    Route::get('registrosclientes', 'App\Http\Controllers\ClienteController@getClientes');
    Route::apiResource('carro', 'App\Http\Controllers\CarroController');
    Route::get('registroscarros', 'App\Http\Controllers\CarroController@getCarros');
    Route::apiResource('locacao', 'App\Http\Controllers\LocacaoController');
    Route::apiResource('marca', 'App\Http\Controllers\MarcaController');
    Route::get('registrosmarca', 'App\Http\Controllers\MarcaController@getMarcas');
    Route::apiResource('modelo', 'App\Http\Controllers\ModeloController');
    Route::get('registrosmodelos', 'App\Http\Controllers\ModeloController@getModelos');
    Route::post('me', 'App\Http\Controllers\AuthController@me');
    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
});


Route::post('login', 'App\Http\Controllers\AuthController@login');
Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');

