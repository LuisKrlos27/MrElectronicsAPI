<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ClienteController extends Controller
{
    /**
     * Listar clientes (GET /V1/clientes).
     */
    public function index()
    {
        $url = env('URL_SERVER_API') . '/clientes';

        $response = Http::get($url);

        $clientes = $response->json()['data'] ?? [];

        return view('Clientes.ClientesIndex', compact('clientes'));
    }

    /**
     * Formulario para crear un cliente.
     */
    public function create()
    {
        return view('Clientes.ClientesForm');
    }

    /**
     * Guardar un cliente nuevo en el backend (POST /V1/clientes).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'documento' => 'required|numeric',
            'telefono' => 'required|numeric',
            'direccion' => 'required|string'
        ]);

        $url = env('URL_SERVER_API') . '/clientes';

        Http::post($url, $validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente registrado correctamente');
    }

    /**
     * Editar un cliente (GET /V1/clientes/{id}).
     */
    public function edit($id)
    {
        $url = env('URL_SERVER_API') . "/clientes/{$id}";

        $response = Http::get($url);

        $cliente = $response->json()['data'] ?? null;

        return view('Clientes.ClientesEdit', compact('cliente'));
    }

    /**
     * Actualizar un cliente (PUT /V1/clientes/{id}).
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'documento' => 'required|numeric',
            'telefono' => 'required|numeric',
            'direccion' => 'required|string'
        ]);

        $url = env('URL_SERVER_API') . "/clientes/{$id}";

        Http::put($url, $validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente');
    }

    /**
     * Eliminar un cliente (DELETE /V1/clientes/{id}).
     */
    public function destroy($id)
    {
        $url = env('URL_SERVER_API') . "/clientes/{$id}";

        Http::delete($url);

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente');
    }
}
