<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Pulgada;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PulgadaResource;

class PulgadaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pulgada = Pulgada::get();

        return PulgadaResource::collection($pulgada);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $pulgada = new Pulgada();
        $pulgada->medida = $request->input('medida');

        $pulgada->save();

        return response()->json([
            'message' => 'Pulgada registrada correctamente',
            'data' => $pulgada
        ], Response::HTTP_ACCEPTED);

    }

    /**
     * Display the specified resource.
     */
    public function show(Pulgada $pulgada)
    {
        return new PulgadaResource($pulgada);
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
