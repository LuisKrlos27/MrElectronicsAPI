<?php

namespace App\Http\Controllers;

use App\Models\Evidencia;
use App\Models\Proceso;
use Illuminate\Http\Request;

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

        if($request->hasFile('imagenes')) {
            foreach($request->file('imagenes') as $index => $imagen) {
                $path = $imagen->store('evidencias', 'public');

                Evidencia::create([
                    'proceso_id' => $proceso_id,
                    'imagen' => $path,
                    'comentario' => $request->comentarios[$index] ?? null,
                ]);
            }
        }

        return back()->with('success', 'Evidencias registradas correctamente.');
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
