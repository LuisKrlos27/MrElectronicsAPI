<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ModeloResource;

class ModeloController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $modelo = Modelo::get();

        return ModeloResource::collection($modelo);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $modelo = new Modelo();

        $modelo->nombre = $request->input('nombre');

        $modelo->save();

        return response()->json([
            'message' => 'Modelo registrado correctamente',
            'data' => $modelo
        ], Response::HTTP_ACCEPTED);

    }

    /**
     * Display the specified resource.
     */
    public function show(Modelo $modelo)
    {
        return new ModeloResource($modelo);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
