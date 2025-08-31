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

        $producto = $response->json()['data'] ?? [];

        return view('Productos.ProductosIndex', compact('producto'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $url = env('URL_SERVER_API');

        // Consultamos cada endpoint
        $tiposResponse = Http::get("{$url}/tipos");
        $marcasResponse = Http::get("{$url}/marcas");
        $modelosResponse = Http::get("{$url}/modelos");
        $pulgadasResponse = Http::get("{$url}/pulgadas");

        // Extraemos la data (Laravel API Resources devuelve ["data" => [...]])
        $tipo = $tiposResponse->json()['data'] ?? [];
        $marca = $marcasResponse->json()['data'] ?? [];
        $modelo = $modelosResponse->json()['data'] ?? [];
        $pulgada = $pulgadasResponse->json()['data'] ?? [];

        // Pasamos todo a la vista de creación
        return view('Productos.ProductosForm', compact('tipo', 'marca', 'modelo', 'pulgada'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $url = env('URL_SERVER_API') . '/productos';

        // Enviar los datos al backend API
        $response = Http::post($url, [
            'tipo'        => $request->nuevo_tipo ?? $request->tipo_id,
            'marca'       => $request->nueva_marca ?? $request->marca_id,
            'modelo'      => $request->nuevo_modelo ?? $request->modelo_id,
            'pulgada'     => $request->nueva_pulgada ?? $request->pulgada_id,
            'numero_pieza'=> $request->numero_pieza,
            'descripcion' => $request->descripcion,
            'precio'      => $request->precio,
            'cantidad'    => $request->cantidad,
        ]);

        if ($response->successful()) {
            return redirect()
                ->route('productos.index')
                ->with('success', $response->json()['message']);
        }

        return back()->withErrors(['error' => 'No se pudo guardar el producto']);

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
        // URL base de la API
        $url = env('URL_SERVER_API');

        // 1. Obtener el producto
        $response = Http::get("{$url}/productos/{$id}");

        if ($response->failed()) {
            return redirect()->route('productos.index')
                ->with('error', 'No se pudo obtener la información del producto');
        }

        // Extraer los datos correctamente
        $data = $response->json();
        $producto = $data['data'] ?? $data ?? null;

        if (!$producto) {
            return redirect()->route('productos.index')
                ->with('error', 'Producto no encontrado');
        }

        // 2. Obtener listas auxiliares (tipos, marcas, modelos, pulgadas)
        $tipo    = Http::get("{$url}/tipos")->json()['data'] ?? [];
        $marca   = Http::get("{$url}/marcas")->json()['data'] ?? [];
        $modelo  = Http::get("{$url}/modelos")->json()['data'] ?? [];
        $pulgada = Http::get("{$url}/pulgadas")->json()['data'] ?? [];

        // 3. Enviar todo a la vista
        return view('Productos.ProductosEdit', compact(
            'producto', 'tipo', 'marca', 'modelo', 'pulgada'
        ));
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
