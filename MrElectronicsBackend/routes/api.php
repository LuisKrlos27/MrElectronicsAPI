<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TipoController;
use App\Http\Controllers\Api\V1\MarcaController;
use App\Http\Controllers\Api\V1\VentaController;
use App\Http\Controllers\Api\V1\ModeloController;
use App\Http\Controllers\Api\V1\ClienteController;
use App\Http\Controllers\Api\V1\PulgadaController;
use App\Http\Controllers\Api\V1\ProductoController;


// Route::get('/', function () {
//     return view('Landing.Langing');
// });

Route::apiResource('V1/clientes',ClienteController::class );
Route::apiResource('V1/productos',ProductoController::class );
Route::apiResource('V1/tipos', TipoController::class);
Route::apiResource('V1/marcas', MarcaController::class);
Route::apiResource('V1/modelos', ModeloController::class);
Route::apiResource('V1/pulgadas', PulgadaController::class);
Route::apiResource('V1/Ventas', VentaController::class);


