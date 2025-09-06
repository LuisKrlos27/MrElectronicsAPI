<?php

namespace App\Http\Controllers;


use App\Models\Tipo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Pulgada;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        // Determinar qué enviar para cada campo CORREGIDO
        $data = [
            'numero_pieza' => $request->numero_pieza,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'cantidad' => $request->cantidad,
        ];

        // Lógica para tipo - CORREGIDO
        if ($request->tipo_id === 'nuevo' && $request->filled('nuevo_tipo')) {
            $data['tipo'] = $request->nuevo_tipo; // Enviar nombre del nuevo tipo
        } else {
            $data['tipo_id'] = (int)$request->tipo_id; // Enviar ID numérico
        }

        // Lógica para marca - CORREGIDO
        if ($request->marca_id === 'nueva' && $request->filled('nueva_marca')) {
            $data['marca'] = $request->nueva_marca; // Enviar nombre de la nueva marca
        } else {
            $data['marca_id'] = (int)$request->marca_id; // Enviar ID numérico
        }

        // Lógica para modelo - CORREGIDO
        if ($request->modelo_id === 'nuevo' && $request->filled('nuevo_modelo')) {
            $data['modelo'] = $request->nuevo_modelo; // Enviar nombre del nuevo modelo
        } else {
            $data['modelo_id'] = (int)$request->modelo_id; // Enviar ID numérico
        }

        // Lógica para pulgada - CORREGIDO
        if ($request->pulgada_id === 'nuevo' && $request->filled('nueva_pulgada')) {
            $data['pulgada'] = $request->nueva_pulgada; // Enviar medida de la nueva pulgada
        } else {
            $data['pulgada_id'] = (int)$request->pulgada_id; // Enviar ID numérico
        }

        // DEBUG: Ver qué vamos a enviar
        //Log::debug('📤 DATOS ENVIADOS A API:', $data);

        $response = Http::post($url, $data);

        if ($response->successful()) {
            return redirect()
                ->route('productos.index')
                ->with('success', $response->json()['message']);
        }

        // Mostrar error específico de la API
        $errorMessage = $response->json()['message'] ?? 'No se pudo guardar el producto';
        return back()->withErrors(['error' => $errorMessage])->withInput();
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
    public function update(Request $request, int $id)
    {
        // Validar los campos básicos que siempre se envían
        $validated = $request->validate([
            'tipo_id'      => 'nullable|integer|exists:tipos,id',
            'marca_id'     => 'nullable|integer|exists:marcas,id',
            'modelo_id'    => 'nullable|integer|exists:modelos,id',
            'pulgada_id'   => 'nullable|integer|exists:pulgadas,id',
            'numero_pieza' => 'nullable|string',
            'descripcion'  => 'nullable|string',
            'precio'       => 'required|numeric',
            'cantidad'     => 'required|integer|min:0',
            'tipo'         => 'required|string',
            'marca'        => 'required|string',
            'modelo'       => 'required|string',
            'pulgada'      => 'required|string',
        ]);

        $url = env('URL_SERVER_API') . "/productos/{$id}";

        try {
            // Enviar la solicitud PUT a la API
            $response = Http::acceptJson()->put($url, [
                'tipo_id'      => $validated['tipo_id'] ?? null,
                'marca_id'     => $validated['marca_id'] ?? null,
                'modelo_id'    => $validated['modelo_id'] ?? null,
                'pulgada_id'   => $validated['pulgada_id'] ?? null,
                'tipo'         => $validated['tipo'],
                'marca'        => $validated['marca'],
                'modelo'       => $validated['modelo'],
                'pulgada'      => $validated['pulgada'],
                'numero_pieza' => $validated['numero_pieza'] ?? null,
                'descripcion'  => $validated['descripcion'] ?? null,
                'precio'       => $validated['precio'],
                'cantidad'     => $validated['cantidad'],
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'No se pudo conectar con la API: ' . $e->getMessage()])
                        ->withInput();
        }

        // Si la actualización fue exitosa
        if ($response->successful()) {
            $msg = $response->json()['message'] ?? 'Producto actualizado correctamente';
            return redirect()->route('productos.index')->with('success', $msg);
        }

        // Manejo de errores de validación desde la API
        if ($response->status() === 422) {
            $errors = $response->json()['errors'] ?? $response->json();
            return back()->withErrors($errors)->withInput();
        }

        // Producto no encontrado
        if ($response->status() === 404) {
            $msg = $response->json()['message'] ?? 'Producto no encontrado';
            return redirect()->route('productos.index')->withErrors(['error' => $msg]);
        }

        // Otro error genérico
        $msg = $response->json()['message'] ?? 'Error al actualizar el producto';
        return back()->withErrors(['error' => $msg])->withInput();
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
