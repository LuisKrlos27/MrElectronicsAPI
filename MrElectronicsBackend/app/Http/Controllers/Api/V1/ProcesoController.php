<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Cliente;
use App\Models\Proceso;
use App\Models\Pulgada;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProcesoResource;

class ProcesoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $procesos = Proceso::with(['cliente', 'marca', 'modelo', 'pulgada'])->get();
        return ProcesoResource::collection($procesos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validamos los datos
        $request->validate([
            'falla' => 'required|string',
            'descripcion' => 'required|string',
            'estado' => 'required|boolean',
            'fecha_inicio' => 'required|date',
            'fecha_cierre' => 'nullable|date',
            //campos que envia el frontend
            'cliente_id' => 'nullable|integer',
            'marca_id' => 'nullable|integer',
            'modelo_id' => 'nullable|integer',
            'pulgada_id' => 'nullable|integer',
            //campos para nuevos (opcionales)
            'cliente' => 'nullable|string',
            'documento' => 'nullable|string', 
            'telefono' => 'nullable|string',  
            'direccion' => 'nullable|string', 
            'marca' => 'nullable|string',
            'modelo' => 'nullable|string',
            'pulgada' => 'nullable|string'
        ]);

        //logica para cliente
        if ($request->filled('cliente_id') && is_numeric($request->cliente_id)) {
            // usar cliente existente por ID
            $cliente = Cliente::find($request->cliente_id);
            if (!$cliente) {
                return response()->json(['message' => 'Cliente no encontrado'], 404);
            }
        } elseif ($request->filled('cliente')) {
            // crear nuevo cliente por nombre
            $cliente = Cliente::firstOrCreate([ 
            'nombre' => $request->cliente,
            'documento' => $request->documento,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion]);

        } else {
            return response()->json(['message' => 'Debe proporcionar un cliente'], 422);
        }

        //logica para marca
        if ($request->filled('marca_id') && is_numeric($request->marca_id)) {
            $marca = Marca::find($request->marca_id);
            if (!$marca) {
                return response()->json(['message' => 'Marca no encontrada'], 404);
            }
        } elseif ($request->filled('marca')) {
            $marca = Marca::firstOrCreate(['nombre' => $request->marca]);
        } else{
            return response()->json(['message' => 'Debe proporcionar una marca'], 422);
        }

        //logica para modelo
        if ($request->filled('modelo_id') && is_numeric($request->modelo_id)) {
            $modelo = Modelo::find($request->modelo_id);
            if (!$modelo) {
                return response()->json(['message' => 'Modelo no encontrado'], 404);
            }
        } elseif ($request->filled('modelo')) {
            $modelo = Modelo::firstOrCreate([
                'nombre' => $request->modelo
            ]);
        } else{
            return response()->json(['message' => 'Debe proporcionar un modelo'], 422);
        }

        //logica para pulgada
        if ($request->filled('pulgada_id') && is_numeric($request->pulgada_id)) {
            $pulgada = Pulgada::find($request->pulgada_id);
            if (!$pulgada) {
                return response()->json(['message' => 'Pulgada no encontrada'], 404);
            }
        } elseif ($request->filled('pulgada')) {
            $pulgada = Pulgada::firstOrCreate(['medida' => $request->pulgada]);
        } else{
            return response()->json(['message' => 'Debe proporcionar una pulgada'], 422);
        }

        //crear proceso
        $proceso = Proceso::create([
            'cliente_id' => $cliente->id,
            'marca_id' => $marca->id,
            'modelo_id' => $modelo->id,
            'pulgada_id' => $pulgada->id,
            'falla' => $request->falla,
            'descripcion' => $request->descripcion,
            'estado' => $request->estado,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_cierre' => $request->fecha_cierre,
        ]);

        //cargar relaciones para la respuesta
        $proceso->load(['cliente', 'marca', 'modelo', 'pulgada']);

        return response()->json([
            'message' => 'Proceso registrado correctamente',
            'data' => new ProcesoResource($proceso)
        ], Response::HTTP_ACCEPTED);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $procesos = Proceso::with(['cliente', 'marca', 'modelo', 'pulgada','evidencias'])->find($id);
        
        if (!$procesos) {
            return response()->json([
                'message' => 'Proceso no encontrado',
            ], Response::HTTP_NOT_FOUND);
        }
        
        return new ProcesoResource($procesos);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $proceso = Proceso::find($id);

        if (!$proceso){
            return response()->json([
                'message' => 'Proceso no encontrado',
            ], Response::HTTP_NOT_FOUND);
        }

        // Validamos los datos (agregando campos para nuevos registros)
        $validated = $request->validate([
            'falla' => 'required|string',
            'descripcion' => 'required|string',
            'estado' => 'required|boolean',
            'fecha_inicio' => 'required|date',
            'fecha_cierre' => 'nullable|date',
            // Campos para IDs existentes
            'cliente_id' => 'nullable|integer',
            'marca_id' => 'nullable|integer',
            'modelo_id' => 'nullable|integer',
            'pulgada_id' => 'nullable|integer',
            // Campos para nuevos registros
            'cliente' => 'nullable|string',
            'documento' => 'nullable|string',
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string',
            'marca' => 'nullable|string',
            'modelo' => 'nullable|string',
            'pulgada' => 'nullable|string'
        ]);

        // Lógica para cliente
        if ($request->filled('cliente_id') && is_numeric($request->cliente_id)) {
            // usar cliente existente por ID
            $cliente = Cliente::find($request->cliente_id);
            if (!$cliente) {
                return response()->json(['message' => 'Cliente no encontrado'], 404);
            }
            $cliente_id = $cliente->id;
        } elseif ($request->filled('cliente')) {
            // crear o actualizar cliente
            $cliente = Cliente::updateOrCreate(
                ['documento' => $request->documento],
                [
                    'nombre' => $request->cliente,
                    'telefono' => $request->telefono,
                    'direccion' => $request->direccion
                ]
            );
            $cliente_id = $cliente->id;
        } else {
            // mantener el cliente actual
            $cliente_id = $proceso->cliente_id;
        }

        // Lógica para marca
        if ($request->filled('marca_id') && is_numeric($request->marca_id)) {
            $marca = Marca::find($request->marca_id);
            if (!$marca) {
                return response()->json(['message' => 'Marca no encontrada'], 404);
            }
            $marca_id = $marca->id;
        } elseif ($request->filled('marca')) {
            $marca = Marca::firstOrCreate(['nombre' => $request->marca]);
            $marca_id = $marca->id;
        } else {
            // mantener la marca actual
            $marca_id = $proceso->marca_id;
        }

        // Lógica para modelo
        if ($request->filled('modelo_id') && is_numeric($request->modelo_id)) {
            $modelo = Modelo::find($request->modelo_id);
            if (!$modelo) {
                return response()->json(['message' => 'Modelo no encontrado'], 404);
            }
            $modelo_id = $modelo->id;
        } elseif ($request->filled('modelo')) {
            $modelo = Modelo::firstOrCreate([
                'nombre' => $request->modelo,
                'marca_id' => $marca_id // Relacionar con la marca
            ]);
            $modelo_id = $modelo->id;
        } else {
            // mantener el modelo actual
            $modelo_id = $proceso->modelo_id;
        }

        // Lógica para pulgada
        if ($request->filled('pulgada_id') && is_numeric($request->pulgada_id)) {
            $pulgada = Pulgada::find($request->pulgada_id);
            if (!$pulgada) {
                return response()->json(['message' => 'Pulgada no encontrada'], 404);
            }
            $pulgada_id = $pulgada->id;
        } elseif ($request->filled('pulgada')) {
            $pulgada = Pulgada::firstOrCreate(['medida' => $request->pulgada]);
            $pulgada_id = $pulgada->id;
        } else {
            // mantener la pulgada actual
            $pulgada_id = $proceso->pulgada_id;
        }

        // Actualizamos los datos
        $proceso->update([
            'falla' => $validated['falla'],
            'descripcion' => $validated['descripcion'],
            'estado' => $validated['estado'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_cierre' => $validated['fecha_cierre'],
            'cliente_id' => $cliente_id,
            'marca_id' => $marca_id,
            'modelo_id' => $modelo_id,
            'pulgada_id' => $pulgada_id,
        ]);

        // Cargar relaciones para la respuesta
        $proceso->load(['cliente', 'marca', 'modelo', 'pulgada']);

        return response()->json([
            'message' => 'Proceso actualizado correctamente',   
            'data' => new ProcesoResource($proceso)
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proceso $proceso)
    {    
        $proceso->delete();

        return response()->json([
            'message' => 'Proceso eliminado correctamente',
        ], Response::HTTP_ACCEPTED);
    }
}
