<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Tipo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TipoResource;

class TipoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipo = Tipo::get();
        return TipoResource::collection($tipo);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tipo = new Tipo();

        $tipo->nombre = $request->input('nombre');

        $tipo->save();

        return response()->json([
            'message' => 'Tipo registrado correctamente',
            'data' => $tipo
        ], Response::HTTP_ACCEPTED);

    }

    /**
     * Display the specified resource.
     */
    public function show(Tipo $tipo)
    {
        return new TipoResource($tipo);
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
