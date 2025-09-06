<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $url = env('URL_SERVER_API') . '/ventas';

        $response = Http::get($url);

        $venta = $response->json()['data'] ?? [];

        return view('Ventas.VentasIndex', compact('venta'));
    }

    public function factura(int $id)
    {
        $url = env('URL_SERVER_API') . "/ventas/{$id}/factura";

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
            // 5. Si hay error de conexión
            return back()->with('error', 'Error al conectar con el servidor: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $url = env('URL_SERVER_API');

        // Obtener datos auxiliares (productos, clientes, etc.)
        try {
            // Si tu API devuelve datos auxiliares (productos, clientes, etc.)
            $productoResponse = Http::get("{$url}/productos");
            $clienteResponse = Http::get("{$url}/clientes");

            $producto = $productoResponse->json()['data'] ?? [];
            $cliente = $clienteResponse->json()['data'] ?? [];

            return view('Ventas.VentasForm', compact('producto', 'cliente'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $url = env('URL_SERVER_API');

        try {
            // 1. Si el cliente es "nuevo", lo creamos primero en la API de clientes
            if ($request->cliente_id === 'nuevo') {
                $clienteResponse = Http::post("{$url}/clientes", [
                    'nombre'    => $request->nuevo_cliente_nombre,
                    'documento' => $request->nuevo_cliente_documento,
                    'telefono'  => $request->nuevo_cliente_telefono,
                    'direccion' => $request->nuevo_cliente_direccion,
                ]);

                if (!$clienteResponse->successful()) {
                    return back()->withErrors(['error' => 'No se pudo registrar el nuevo cliente.']);
                }

                // Guardamos el id del cliente creado
                $cliente_id = $clienteResponse->json()['data']['id'];
            } else {
                $cliente_id = $request->cliente_id;
            }

            // 2. Crear la venta con el cliente correcto
            $ventaResponse = Http::post("{$url}/ventas", [
                'cliente_id' => $cliente_id,
                'fecha_venta' => $request->fecha_venta,
                'pago'       => $request->pago,
                'productos'  => $request->productos, // viene como array [producto_id, cantidad]
            ]);

            if ($ventaResponse->successful()) {
                return redirect()->route('ventas.index')
                    ->with('success', 'Venta registrada correctamente');
            }

            return back()->withErrors(['error' => 'No se pudo registrar la venta.']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al conectar con el servidor: ' . $e->getMessage()]);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $url = env('URL_SERVER_API') . "/ventas/{$id}";

        try {
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();
                $venta = $data['data'] ?? $data;

                // Verificar que la venta tiene datos
                if (empty($venta)) {
                    return redirect()->route('ventas.index')
                        ->with('error', 'Venta no encontrada');
                }

                // Convertir estructura para la vista
                if (isset($venta['productos'])) {
                    $venta['detalles'] = array_map(function ($producto) {
                        return [
                            'producto_id' => $producto['id'],
                            'cantidad' => $producto['cantidad'],
                            'precio_unitario' => $producto['precio_unitario'],
                            'subtotal' => $producto['subtotal'],
                            'producto' => $producto
                        ];
                    }, $venta['productos']);
                }

                return view('Ventas.VentasShow', compact('venta'));
            }

            return redirect()->route('ventas.index')
                ->with('error', 'No se pudo obtener la venta');

        } catch (\Exception $e) {
            return redirect()->route('ventas.index')
                ->with('error', 'Error de conexión: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {

        $url = env('URL_SERVER_API');

        // Obtener la venta
        $response = Http::get("{$url}/ventas/{$id}");

        if ($response->failed()) {
            return redirect()->route('ventas.index')
                ->with('error', 'No se pudo obtener la información de la venta');
        }

        // Extraer los datos correctamente
        $data = $response->json();
        $venta = $data['data'] ?? $data;

        // Convertir la estructura de productos a detalles para la vista
        if (isset($venta['productos'])) {
            $venta['detalles'] = array_map(function ($producto) {
                return [
                    'producto_id' => $producto['id'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio_unitario'],
                    'subtotal' => $producto['subtotal'],
                    'producto' => $producto // Mantener toda la info del producto
                ];
            }, $venta['productos']);
        }

        // Obtener lista de clientes y productos
        $clienteResponse = Http::get("{$url}/clientes");
        $productoResponse = Http::get("{$url}/productos");

        $cliente = $clienteResponse->json()['data'] ?? [];
        $producto = $productoResponse->json()['data'] ?? [];

        return view('Ventas.VentasEdit', compact('venta', 'cliente', 'producto'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $url = env('URL_SERVER_API') . "/ventas/{$id}";

        try {
            // 1. Si el cliente es "nuevo", crearlo primero
            if ($request->cliente_id === 'nuevo') {
                $clienteResponse = Http::post(env('URL_SERVER_API') . '/clientes', [
                    'nombre'    => $request->nuevo_cliente_nombre,
                    'documento' => $request->nuevo_cliente_documento,
                    'telefono'  => $request->nuevo_cliente_telefono,
                    'direccion' => $request->nuevo_cliente_direccion,
                ]);

                if (!$clienteResponse->successful()) {
                    return back()->withErrors(['error' => 'No se pudo registrar el nuevo cliente.']);
                }

                $cliente_id = $clienteResponse->json()['data']['id'];
            } else {
                $cliente_id = $request->cliente_id;
            }

            // 2. Preparar datos para la venta
            $ventaData = [
                'cliente_id' => $cliente_id,
                'fecha_venta' => $request->fecha_venta,
                'pago'       => $request->pago,
                'productos'  => $request->productos,
            ];

            // 3. Si hay cliente nuevo, agregar los datos adicionales
            if ($request->cliente_id === 'nuevo') {
                $ventaData['nuevo_cliente_nombre'] = $request->nuevo_cliente_nombre;
                $ventaData['nuevo_cliente_documento'] = $request->nuevo_cliente_documento;
                $ventaData['nuevo_cliente_telefono'] = $request->nuevo_cliente_telefono;
                $ventaData['nuevo_cliente_direccion'] = $request->nuevo_cliente_direccion;
            }

            // 4. Hacer la petición PUT a la API
            $response = Http::put($url, $ventaData);

            if ($response->successful()) {
                return redirect()->route('ventas.index')
                    ->with('success', 'Venta actualizada correctamente');
            }

            // Manejar errores de la API
            $errorData = $response->json();
            return back()->withErrors(['error' => $errorData['message'] ?? 'Error al actualizar la venta']);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al conectar con el servidor: ' . $e->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venta $venta)
    {
        $venta->delete();

        return redirect()->route('ventas.index')->with('success', 'Venta eliminada correctamente');

    }
}
