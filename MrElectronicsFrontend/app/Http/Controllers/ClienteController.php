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
        // Realizar la solicitud HTTP GET a la API
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
        $url = env('URL_SERVER_API') . '/clientes';
        $response = Http::get($url);
        $clientes = $response->json()['data'] ?? [];

        return view('Clientes.ClientesForm',['clientes' => $clientes]);
    }

    /**
     * Guardar un cliente nuevo en el backend (POST /V1/clientes).
     */
    public function store(Request $request)
    {
        $url = env('URL_SERVER_API') . '/clientes';

        $request->validate([
            'nombre' => 'required|string',
            'documento' => 'required|numeric',
            'telefono'=>'required|numeric',
            'direccion' => 'required|string'
        ]);

        $response = Http::post($url,[
            'nombre' => $request->nombre,
            'documento' => $request->documento,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion
        ]);

        if($response->successful()){
            return redirect()->route('clientes.index')->with('success', 'Cliente registrado correctamente');
        }else{
            return redirect()->route('clientes.create')->withErrors('message', 'Error al registrar el cliente');
        }


    }

    /**
     * Editar un cliente (GET /V1/clientes/{id}).
     */
    public function edit(int $id)
    {
        $url = env('URL_SERVER_API') . "/clientes/{$id}";

        $response = Http::get($url);

        if ($response->failed()) {
            return redirect()->route('clientes.index')->with('error', 'No se pudo obtener la información del cliente');
        }

        $data = $response->json();

        // Verifica si existe la clave 'data' y si no, usa $data directamente
        $cliente = $data['data'] ?? $data ?? null;

        if (!$cliente) {
            return redirect()->route('clientes.index')->with('error', 'Cliente no encontrado');
        }

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
