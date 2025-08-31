<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Tipo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Pulgada;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\V1\ProductoResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;







class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productos = Producto::with(['tipo', 'marca', 'modelo', 'pulgada'])->get();
        return ProductoResource::collection($productos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación básica
        $request->validate([
            'tipo' => 'required|string',
            'marca' => 'required|string',
            'modelo' => 'required|string',
            'pulgada' => 'required|string',
            'numero_pieza' => 'nullable|string',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric',
            'cantidad' => 'required|integer',
            //Opcionalmente, puedes permitir IDs si vienen desde frontend
            'tipo_id' => 'nullable|integer|exists:tipos,id',
            'marca_id' => 'nullable|integer|exists:marcas,id',
            'modelo_id' => 'nullable|integer|exists:modelos,id',
            'pulgada_id' => 'nullable|integer|exists:pulgadas,id',
        ]);

        // Tipo
        if ($request->filled('tipo_id') && $request->tipo_id !== 'nuevo') {
            $tipo = Tipo::find($request->tipo_id);
        } else {
            $tipo = Tipo::firstOrCreate(['nombre' => $request->tipo]);
        }
        // Marca
        if ($request->filled('marca_id') && $request->marca_id !== 'nueva') {
            $marca = Marca::find($request->marca_id);
        } else {
            $marca = Marca::firstOrCreate(['nombre' => $request->marca]);
        }
        // Modelo
        if ($request->filled('modelo_id') && $request->modelo_id !== 'nuevo') {
            $modelo = Modelo::find($request->modelo_id);
        } else {
            $modelo = Modelo::firstOrCreate([ 'nombre' => $request->modelo, 'marca_id' => $marca->id ]);
        }
        // Pulgada
        if ($request->filled('pulgada_id') && $request->pulgada_id !== 'nuevo') {
            $pulgada = Pulgada::find($request->pulgada_id);
        } else {
            $pulgada = Pulgada::firstOrCreate(['medida' => $request->pulgada]);
        }
        // Crear producto
        $producto = Producto::create([
            'tipo_id' => $tipo->id,
            'marca_id' => $marca->id,
            'modelo_id' => $modelo->id,
            'pulgada_id' => $pulgada->id,
            'numero_pieza' => $request->numero_pieza,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'cantidad' => $request->cantidad
        ]);

        return response()->json([
            'message' => 'Producto registrado correctamente',
            'data' => $producto
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $producto = Producto::with(['tipo', 'marca', 'modelo', 'pulgada'])->find($id);

        if (!$producto) {
            return response()->json([
                'message' => 'Producto no encontrado',
            ], Response::HTTP_NOT_FOUND);
        }

        return new ProductoResource($producto);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'message' => 'Producto no encontrado',
            ], Response::HTTP_NOT_FOUND);
        }

        // Validamos solo los campos básicos
        $validated = $request->validate([
            'tipo' => 'required|string',
            'marca' => 'required|string',
            'modelo' => 'required|string',
            'pulgada' => 'required|string',
            'numero_pieza' => 'nullable|string',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric',
            'cantidad' => 'required|integer',
            //Opcionalmente, puedes permitir IDs si vienen desde frontend
            'tipo_id' => 'nullable|integer|exists:tipos,id',
            'marca_id' => 'nullable|integer|exists:marcas,id',
            'modelo_id' => 'nullable|integer|exists:modelos,id',
            'pulgada_id' => 'nullable|integer|exists:pulgadas,id',
        ]);

            // Tipo
            if ($request->filled('tipo_id') && $request->tipo_id !== 'nuevo') {
                $tipo = Tipo::find($request->tipo_id); } else { $tipo = Tipo::firstOrCreate(['nombre' => $request->tipo]);
            }
            // Marca
            if ($request->filled('marca_id') && $request->marca_id !== 'nueva') {
                $marca = Marca::find($request->marca_id);
            } else {
                $marca = Marca::firstOrCreate(['nombre' => $request->marca]);
            }
            // Modelo
            if ($request->filled('modelo_id') && $request->modelo_id !== 'nuevo') {
                $modelo = Modelo::find($request->modelo_id);
            } else {
                $modelo = Modelo::firstOrCreate([ 'nombre' => $request->modelo, 'marca_id' => $marca->id ]);
            }
            // Pulgada
            if ($request->filled('pulgada_id') && $request->pulgada_id !== 'nuevo') {
                $pulgada = Pulgada::find($request->pulgada_id);
            } else {
                $pulgada = Pulgada::firstOrCreate(['medida' => $request->pulgada]);
            }
        // Actualizamos producto con los IDs correctos
        $producto->update([
            'tipo_id' => $tipo->id,
            'marca_id' => $marca->id,
            'modelo_id' => $modelo->id,
            'pulgada_id' => $pulgada->id,
            'numero_pieza' => $validated['numero_pieza'],
            'descripcion' => $validated['descripcion'],
            'precio' => $validated['precio'],
            'cantidad' => $validated['cantidad'],
        ]);

        return response()->json([
                    'message' => 'Producto actualiazado correctamente',
                    'data' => $producto
                ], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        $producto->delete();

        return response()->json([
            'message' => 'Producto eliminado correctamente',
        ], Response::HTTP_ACCEPTED);
    }
}
