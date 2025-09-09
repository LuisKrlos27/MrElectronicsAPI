<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Cliente;
use App\Models\Proceso;
use App\Models\Pulgada;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class ProcesoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $url = env('URL_SERVER_API') . '/procesos';

        $response = Http::get($url);

        $procesos = $response->json()['data'] ?? [];

        return view('Procesos.ProcesosIndex', compact('procesos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $procesos = Proceso::with(['cliente', 'marca', 'modelo', 'pulgada'])->get();
        $clientes = Cliente::all();
        $marcas = Marca::all();
        $modelos = Modelo::all();
        $pulgadas = Pulgada::all();

        return view('Procesos.ProcesosForm', compact('clientes','procesos','marcas','modelos','pulgadas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required',
            'marca_id'   => 'required',
            'modelo_id'  => 'required',
            'pulgada_id' => 'required',
            'falla'      => 'required|string|max:255',
            'estado'     => 'required|in:0,1',
        ]);

        // ==============================
        // 1. Cliente
        // ==============================
        if ($request->cliente_id === "nuevo") {
            $cliente = Cliente::create([
                'nombre'     => $request->nuevo_cliente_nombre,
                'documento'  => $request->nuevo_cliente_documento,
                'telefono'   => $request->nuevo_cliente_telefono,
                'direccion'  => $request->nuevo_cliente_direccion,
            ]);
            $cliente_id = $cliente->id;
        } else {
            $cliente_id = $request->cliente_id;
        }

        // ==============================
        // 2. Marca
        // ==============================
        if ($request->marca_id === "nueva") {
            $marca = Marca::create([
                'nombre' => $request->nueva_marca,
            ]);
            $marca_id = $marca->id;
        } else {
            $marca_id = $request->marca_id;
        }

        // ==============================
        // 3. Modelo
        // ==============================
        if ($request->modelo_id === "nuevo") {
            $modelo = Modelo::create([
                'nombre'   => $request->nuevo_modelo,
                'marca_id' => $marca_id, // relacionar modelo con su marca
            ]);
            $modelo_id = $modelo->id;
        } else {
            $modelo_id = $request->modelo_id;
        }

        // ==============================
        // 4. Pulgada
        // ==============================
        if ($request->pulgada_id === 'nuevo') {
            $pulgada = Pulgada::create([
                'medida' => $request->nueva_pulgada,
            ]);
            $pulgada_id = $pulgada->id;
        } else {
            $pulgada_id = $request->pulgada_id;
        }

        // ==============================
        // 5. Guardar el Proceso
        // ==============================
        Proceso::create([
            'cliente_id'  => $cliente_id,
            'marca_id'    => $marca_id,
            'modelo_id'   => $modelo_id,
            'pulgada_id'  => $pulgada_id,
            'falla'       => $request->falla,
            'descripcion' => $request->descripcion,
            'estado'      => $request->estado,
        ]);

        return redirect()->route('procesos.index')->with('success', 'Proceso registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Proceso $proceso)
    {
        // Cargamos el cliente, marca, modelo y evidencias
        $proceso->load(['cliente', 'marca', 'modelo', 'evidencias']);

        return view('Procesos.EvidenciasShow', compact('proceso'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proceso $proceso)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proceso $proceso)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proceso $proceso)
    {
        $proceso->delete();

        return redirect()->route('procesos.index')->with('success', 'Proceso eliminado correctamente.');
    }

    /**
     * Display an invoice for the specified resource.
     */
    public function factura(Proceso $proceso)
    {
        // The 'with' is important to load relationships
        $proceso->load(['cliente', 'marca', 'modelo', 'pulgada']);

        return view('Procesos.Factura', compact('proceso'));
    }

    public function imprimirFactura(Proceso $proceso)
    {
        $proceso->load(['cliente', 'marca','modelo','pulgada']);
        $pdf = Pdf::loadView('procesos.Factura', compact('proceso'));
        return $pdf->download('FACPRO-' . str_pad($proceso->id, 5, '0', STR_PAD_LEFT) . '.pdf');
    }
}
