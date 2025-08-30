<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ClienteController;


// Route::get('/', function () {
//     return view('Landing.Langing');
// });

Route::apiResource('V1/clientes',ClienteController::class );
