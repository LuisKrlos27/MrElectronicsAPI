<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TipoController;
use App\Http\Controllers\Api\V1\MarcaController;
use App\Http\Controllers\Api\V1\ModeloController;
use App\Http\Controllers\Api\V1\ClienteController;
use App\Http\Controllers\Api\V1\PulgadaController;
use App\Http\Controllers\Api\V1\ProductoController;


// Route::get('/', function () {
//     return view('Landing.Langing');
// });

Route::apiResource('V1/clientes',ClienteController::class );
Route::apiResource('V1/productos',ProductoController::class );
