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

//rutas pra clientes
Route::resource('clientes', ClienteController::class);

//rutas para productos
Route::resource('productos', ProductoController::class);
Route::resource('marcas', MarcaController::class);
Route::resource('modelos', ModeloController::class);

//rutas para procesos
Route::resource('procesos', ProcesoController::class);
Route::resource('evidencias', EvidenciaController::class);
Route::post('procesos/{proceso}/evidencias', [EvidenciaController::class, 'store'])->name('evidencias.store');
Route::get('procesos/{proceso}/factura', [ProcesoController::class, 'factura'])->name('procesos.factura');
Route::get('procesos/{proceso}/VerFactura', [ProcesoController::class, 'VerFactura'])->name('procesos.VerFactura');

//rutas para ventas
Route::resource('ventas', VentaController::class);
Route::resource('detalleVentas', DetalleVenta::class);
Route::get('ventas/{venta}/factura', [VentaController::class, 'factura'])->name('ventas.factura');

