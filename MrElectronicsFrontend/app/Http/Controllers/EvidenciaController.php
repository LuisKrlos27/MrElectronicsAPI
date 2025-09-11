<?php

namespace App\Http\Controllers;

use App\Models\Proceso;
use App\Models\Evidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EvidenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $proceso_id )
    {
        $request->validate([
            'imagenes.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'comentarios.*' => 'nullable|string',
        ]);

        $url = env('URL_SERVER_API') . "/procesos/{$proceso_id}/evidencias";

        $imagenes = $request->file('imagenes');
        $comentarios = $request->input('comentarios', []);

        $http = Http::accept('application/json')->asMultipart();

        // adjuntar imagenes
        foreach ($imagenes as $index => $imagen) {
            $http = $http->attach(
                "imagenes[$index]",
                fopen($imagen->getPathname(), 'r'),
                $imagen->getClientOriginalName()
            );
        }

        // Adjuntar comentario si existe, sino no enviar
        if (!empty($comentarios[$index])) {
            $http = $http->attach(
                "comentarios[$index]",
                $comentarios[$index]
            );
        }


        // Enviar la petición POST
        $response = $http->post($url);

        if ($response->successful()) {
            return back()->with('success', $response->json()['message'] ?? 'Evidencias registradas correctamente.');
        }

        $errorMessage = $response->json()['message'] ?? 'No se pudo registrar la evidencia';
        return back()->withErrors(['error' => $errorMessage])->withInput();
    }
    /**
     * Display the specified resource.
     */
    public function show(Evidencia $evidencia)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Evidencia $evidencia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Evidencia $evidencia)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Evidencia $evidencia)
    {
        $evidencia->delete();

        return redirect()->back()->with('success','Evidencia eliminada correctamente.');
    }
}
