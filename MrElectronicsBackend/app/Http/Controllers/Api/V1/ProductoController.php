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
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\V1\ProductoResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;







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
        //Log::debug("📦 Datos recibidos en backend:", $request->all());

        // VALIDACIÓN CORREGIDA - Los campos correctos
        $request->validate([
            'numero_pieza' => 'nullable|string',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric',
            'cantidad' => 'required|integer',
            // Campos que realmente envía el frontend
            'tipo_id' => 'nullable|integer',
            'marca_id' => 'nullable|integer',
            'modelo_id' => 'nullable|integer',
            'pulgada_id' => 'nullable|integer',
            // Campos para nuevos (opcionales)
            'tipo' => 'nullable|string',
            'marca' => 'nullable|string',
            'modelo' => 'nullable|string',
            'pulgada' => 'nullable|string',
        ]);

        // LÓGICA CORREGIDA PARA TIPO
        if ($request->filled('tipo_id') && is_numeric($request->tipo_id)) {
            // Usar tipo existente por ID
            $tipo = Tipo::find($request->tipo_id);
            if (!$tipo) {
                return response()->json(['message' => 'Tipo no encontrado'], 404);
            }
        } elseif ($request->filled('tipo')) {
            // Crear nuevo tipo por nombre
            $tipo = Tipo::firstOrCreate(['nombre' => $request->tipo]);
        } else {
            return response()->json(['message' => 'Debe proporcionar un tipo'], 422);
        }

        // LÓGICA CORREGIDA PARA MARCA
        if ($request->filled('marca_id') && is_numeric($request->marca_id)) {
            $marca = Marca::find($request->marca_id);
            if (!$marca) {
                return response()->json(['message' => 'Marca no encontrada'], 404);
            }
        } elseif ($request->filled('marca')) {
            $marca = Marca::firstOrCreate(['nombre' => $request->marca]);
        } else {
            return response()->json(['message' => 'Debe proporcionar una marca'], 422);
        }

        // LÓGICA CORREGIDA PARA MODELO
        if ($request->filled('modelo_id') && is_numeric($request->modelo_id)) {
            $modelo = Modelo::find($request->modelo_id);
            if (!$modelo) {
                return response()->json(['message' => 'Modelo no encontrado'], 404);
            }
        } elseif ($request->filled('modelo')) {
            $modelo = Modelo::firstOrCreate([
                'nombre' => $request->modelo,
                'marca_id' => $marca->id
            ]);
        } else {
            return response()->json(['message' => 'Debe proporcionar un modelo'], 422);
        }

        // LÓGICA CORREGIDA PARA PULGADA
        if ($request->filled('pulgada_id') && is_numeric($request->pulgada_id)) {
            $pulgada = Pulgada::find($request->pulgada_id);
            if (!$pulgada) {
                return response()->json(['message' => 'Pulgada no encontrada'], 404);
            }
        } elseif ($request->filled('pulgada')) {
            $pulgada = Pulgada::firstOrCreate(['medida' => $request->pulgada]);
        } else {
            return response()->json(['message' => 'Debe proporcionar una pulgada'], 422);
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

        // Cargar relaciones para la respuesta
        $producto->load(['tipo', 'marca', 'modelo', 'pulgada']);

        return response()->json([
            'message' => 'Producto registrado correctamente',
            'data' => new ProductoResource($producto)
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
