<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ClienteResource;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Cliente::all();
        return ClienteResource::collection($clientes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $clientes = new Cliente();

        $clientes->nombre = $request->input('nombre');
        $clientes->documento = $request->input('documento');
        $clientes->telefono = $request->input('telefono');
        $clientes->direccion = $request->input('direccion');

        $clientes->save();

        return response()->json(
            [
                'message' => 'Cliente registrado correctamente',
                'data' => $clientes
            ], Response::HTTP_ACCEPTED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        return new ClienteResource($cliente);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $cliente->update($request->all());

        return response()->json([
            'message' => 'Cliente actualizado correctamente',
            'data' => $cliente
        ], Response::HTTP_ACCEPTED);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return response()->json([
            'message' => 'Cliente eliminado correctamente.'
        ], Response::HTTP_ACCEPTED);
    }
}
