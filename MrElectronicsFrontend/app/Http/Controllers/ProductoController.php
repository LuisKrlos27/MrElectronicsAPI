<?php

namespace App\Http\Controllers;

use App\Models\Tipo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Pulgada;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $url = env('URL_SERVER_API') . '/productos';

        $response = Http::get($url);

        $productos = $response->json()['data'] ?? [];

        return view('Productos.ProductosIndex', compact('productos'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $url = env('URL_SERVER_API') . "/productos/{$id}";
        $response = Http::delete($url);

        if ($response->successful()) {
            return redirect()->route('productos.index')
                ->with('success', 'Producto eliminado correctamente.');
        }

        return redirect()->route('productos.index')
            ->with('error', 'Error al eliminar el producto.');
    }
}
