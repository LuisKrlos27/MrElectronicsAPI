<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\MarcaResource;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $marca = Marca::get();
        return MarcaResource::collection($marca);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $marca = new Marca();

        $marca->nombre = $request->input('nombre');

        $marca->save();

        return response()->json(
            [
                'message' => 'Marca registrada correctamente',
                'data' => $marca
            ], Response::HTTP_ACCEPTED);

    }

    /**
     * Display the specified resource.
     */
    public function show(Marca $marca)
    {
        return new MarcaResource($marca);
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
