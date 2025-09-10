<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Proceso;
use App\Models\Evidencia;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\V1\EvidenciaResource;

class EvidenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($proceso_id)
    {
        $proceso = Proceso::find($proceso_id);

        if (!$proceso) {
            return response()->json(['message' => 'Proceso no encontrado'], 404);
        }

        $evidencias = Evidencia::where('proceso_id', $proceso->id)->get();

        return response()->json([
            'proceso_id' => $proceso->id,
            'evidencias' => EvidenciaResource::collection($evidencias)
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $proceso_id)
    {
        $proceso = Proceso::find($proceso_id);

        if(!$proceso){
            return response()->json(['message' => 'Proceso no encontrado'], 404);
        }

        $request->validate([
            'imagenes.*' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            'comentarios.*' => 'nullable|string|max:500'
        ]);

        $evidenciasCreadas = [];

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $index => $imagen) {
                $path = $imagen->store('evidencias','public');

                $evidencia = Evidencia::create([
                    'proceso_id' => $proceso_id,
                    'imagen' => $path,
                    'comentario' => $request->comentarios[$index] ?? null,
                ]);

                $evidencia->load('proceso');


                $evidenciasCreadas[] = new EvidenciaResource($evidencia);
            }
        }

        return response()->json([
            'message' => 'Evidencias registradas correctamente',
            'data' => $evidenciasCreadas
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Display the specified resource.
     */
    public function show($proceso_id, $evidencia_id)
    {
        $evidencia = Evidencia::where('proceso_id', $proceso_id)->where('id', $evidencia_id)->first();

        if (!$evidencia) {
            return response()->json(['message' => 'Evidencia no encontrada'], 404);
        }

        return new EvidenciaResource($evidencia);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $proceso_id, $evidencia_id)
    {
        $evidencia = Evidencia::where('proceso_id', $proceso_id)->where('id', $evidencia_id)->first();

        if(!$evidencia){
            return response()->json(['message' => 'Evidencia no encontrada'], 404);
        }

        $request->validate([
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'comentario' => 'nullable|string|max:500'
        ]);

        $evidencia->comentario = $request->input('comentario');


        if ($request->hasFile('imagen')) {
            //eliminar imagen anterior
            if($evidencia->imagen && Storage::disk('public')->exists($evidencia->imagen)){
                Storage::disk('public')->delete($evidencia->imagen);
            }

            //guardar nueva imagen
            $imagenPath = $request->file('imagen')->store('evidencias','public');

            $evidencia->imagen = $imagenPath;

        }

        $evidencia->save();

        return response()->json([
            'message' => 'Evidencia actualizada correctamente',
            'data' => new EvidenciaResource($evidencia)
        ], Response::HTTP_OK);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($proceso_id, $evidencia_id)
    {
        $evidencia = Evidencia::where('proceso_id', $proceso_id)
                            ->where('id', $evidencia_id)
                            ->first();

        if (!$evidencia) {
            return response()->json([
                'message' => 'Evidencia no encontrada',
            ], Response::HTTP_NOT_FOUND);
        }

        // Eliminar la imagen del storage
        if ($evidencia->imagen && Storage::disk('public')->exists($evidencia->imagen)) {
            Storage::disk('public')->delete($evidencia->imagen);
        }

        $evidencia->delete();

        return response()->json([
            'message' => 'Evidencia eliminada correctamente'
        ], Response::HTTP_OK);
    }
}
