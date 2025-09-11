<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Cliente;
use App\Models\Proceso;
use App\Models\Pulgada;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
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
        $url = env('URL_SERVER_API');

        //consultamos cada endpoint
        $clientesResponse = Http::get("{$url}/clientes");
        $marcasResponse = Http::get("{$url}/marcas");
        $modelosResponse = Http::get("{$url}/modelos");
        $pulgadasResponse = Http::get("{$url}/pulgadas");

        //extraemos la data (Laravel API Resources devuelve ["data" => [...]])
        $clientes = $clientesResponse->json()['data'] ?? [];
        $marcas = $marcasResponse->json()['data'] ?? [];
        $modelos = $modelosResponse->json()['data'] ?? [];
        $pulgadas = $pulgadasResponse->json()['data'] ?? [];

        //pasamos todo a la vista de creación
        return view('Procesos.ProcesosForm', compact('clientes', 'marcas', 'modelos', 'pulgadas'));

        }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $url = env('URL_SERVER_API') . '/procesos';

        //determinar que enviar para cada campo
        $data = [
            'falla'=> $request->falla,
            'descripcion'=> $request->descripcion,
            'estado'=> $request->estado,

        ];

        //logica para marca
        if ($request->marca_id === 'nueva' && $request->filled('nueva_marca')) {
            $data['marca'] = $request->nueva_marca; //enviar nombre de la nueva marca
        } else {
            $data['marca_id'] = (int)$request->marca_id; //enviar ID numerico
        }

        //logica para modelo
        if ($request->modelo_id === 'nuevo' && $request->filled('nuevo_modelo')) {
            $data['modelo'] = $request->nuevo_modelo; //enviar nombre del nuevo modelo
        } else {
            $data['modelo_id'] = (int)$request->modelo_id; //enviar ID numerico
        }

        //logica para pulgada
        if ($request->pulgada_id === 'nuevo' && $request->filled('nueva_pulgada')) {
            $data['pulgada'] = $request->nueva_pulgada; //enviar medida de la nueva pulgada
        } else {
            $data['pulgada_id'] = (int)$request->pulgada_id; //enviar ID numerico
        }

        //logica para cliente
        if ($request->cliente_id === 'nuevo' && $request->filled('nuevo_cliente_nombre')) {
            $data['cliente'] = $request->nuevo_cliente_nombre;
            $data['documento'] = $request->nuevo_cliente_documento;
            $data['telefono'] = $request->nuevo_cliente_telefono;
            $data['direccion'] = $request->nuevo_cliente_direccion;
        } else {
            $data['cliente_id'] = (int)$request->cliente_id; //enviar nombre del nuevo cliente
        }
        //DEBUG: ver que vamos a enviar
        //Log::debug('📤 DATOS ENVIADOS A API:', $data);

        $response = Http::post($url, $data);

        if ($response->successful()) {
            return redirect()->route('procesos.index')->with('success', $response->json()['message']);
        }

        //mostrar error especifico de la API
        $errorMessage = $response->json()['message'] ?? 'No se pudo guardar el proceso';
        return back()->withErrors(['error' => $errorMessage])->withInput();
    }



    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $url = env('URL_SERVER_API') . "/procesos/{$id}";

        try {
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();
                $proceso = $data['data'] ?? $data;

                // Verificar que el proceso tiene datos
                if (empty($proceso)) {
                    return redirect()->route('procesos.index')
                        ->with('error', 'Proceso no encontrado');
                }

                // Convertir estructura para la vista
                if (isset($proceso['evidencias'])) {
                    $proceso['evidencias'] = array_map(function ($evidencia) {
                        return [
                            'id' => $evidencia['id'],
                            'imagen' => $evidencia['imagen'],
                            'comentario' => $evidencia['comentario'],
                        ];
                    }, $proceso['evidencias']);
                }
                return view('Procesos.EvidenciasShow', compact('proceso'));
            }

            return redirect()->route('procesos.index')
                ->with('error', 'No se pudo obtener el proceso');

        } catch (\Exception $e) {
            return redirect()->route('procesos.index')
                ->with('error', 'Error de conexión: ' . $e->getMessage());
        }
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
    public function factura(int $id)
    {
        $url = env('URL_SERVER_API') . "/procesos/{$id}/factura";

        try {
            // 1. Hacer petición GET a la API del backend
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json()['data'];

                // 2. Decodificar el PDF base64 que devuelve la API
                $pdfContent = base64_decode($data['pdf_base64']);
                $filename = $data['filename'];

                // 3. Descargar el PDF
                return response()->make($pdfContent, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                ]);
            }

            // 4. Si la API falla, mostrar error
            $errorMessage = $response->json()['message'] ?? 'No se pudo generar la factura';
            return back()->with('error', $errorMessage);

        } catch (\Exception $e) {
            // si hay error de conexión
            return back()->with('error', 'Error al conectar con el servidor: ' . $e->getMessage());
        }

    }

}
