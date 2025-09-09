<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Proceso;
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $procesos = Proceso::with(['cliente', 'marca', 'modelo', 'pulgada'])->find($id);
        
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
