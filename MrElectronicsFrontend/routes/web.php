<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EvidenciaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\ProcesoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Models\DetalleVenta;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Landing.Langing');
});


Route::resource('marcas', MarcaController::class);
Route::resource('modelos', ModeloController::class);
Route::resource('productos', ProductoController::class);
Route::resource('procesos', ProcesoController::class);
Route::resource('evidencias', EvidenciaController::class);
Route::resource('clientes', ClienteController::class);
Route::resource('ventas', VentaController::class);
Route::resource('detalleVentas', DetalleVenta::class);

Route::post('procesos/{proceso}/evidencias', [EvidenciaController::class, 'store'])->name('evidencias.store');

Route::get('procesos/{proceso}/factura', [ProcesoController::class, 'factura'])->name('procesos.factura');
Route::get('ventas/{venta}/factura', [VentaController::class, 'factura'])->name('ventas.factura');
Route::get('procesos/{proceso}/imprimirFactura', [ProcesoController::class, 'imprimirFactura'])->name('procesos.imprimirFactura');

