<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TipoController;
use App\Http\Controllers\Api\V1\MarcaController;
use App\Http\Controllers\Api\V1\VentaController;
use App\Http\Controllers\Api\V1\ModeloController;
use App\Http\Controllers\Api\V1\ClienteController;
use App\Http\Controllers\Api\V1\ProcesoController;
use App\Http\Controllers\Api\V1\PulgadaController;
use App\Http\Controllers\Api\V1\ProductoController;
use App\Http\Controllers\Api\V1\EvidenciaController;


// Route::get('/', function () {
//     return view('Landing.Langing');
// });
//ruta para clientes
Route::apiResource('V1/clientes',ClienteController::class );

//ruta para productos
Route::apiResource('V1/productos',ProductoController::class );
Route::apiResource('V1/tipos', TipoController::class);
Route::apiResource('V1/marcas', MarcaController::class);
Route::apiResource('V1/modelos', ModeloController::class);
Route::apiResource('V1/pulgadas', PulgadaController::class);

//ruta para ventas
Route::apiResource('V1/ventas', VentaController::class);
Route::get('V1/ventas/{venta}/factura', [VentaController::class, 'factura']);

//ruta para procesos
Route::apiResource('V1/procesos', ProcesoController::class);

// Rutas para evidencias (anidadas bajo procesos)
Route::prefix('V1/procesos/{proceso_id}')->group(function () {
    Route::get('evidencias', [EvidenciaController::class, 'index']);
    Route::post('evidencias', [EvidenciaController::class, 'store']);
    Route::get('evidencias/{evidencia_id}', [EvidenciaController::class, 'show']);
    Route::put('evidencias/{evidencia_id}', [EvidenciaController::class, 'update']);
    Route::delete('evidencias/{evidencia_id}', [EvidenciaController::class, 'destroy']);
});
Route::get('V1/procesos/{proceso}/factura', [ProcesoController::class, 'factura']);

