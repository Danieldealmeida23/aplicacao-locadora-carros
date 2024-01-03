<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::prefix('v1')->middleware('auth')->group(function (){
    Route::get('/carros', function(){
        return view('app.carros');
    })->name('carros');
    Route::get('/marcas', function(){
        return view('app.marcas');
    })->name('marcas');
    Route::get('/modelos', function(){
        return view('app.modelos');
    })->name('modelos');
    Route::get('/clientes', function(){
        return view('app.clientes');
    })->name('clientes');
    Route::get('/locacoes', function(){
        return view('app.locacoes');
    })->name('locacoes');
});

