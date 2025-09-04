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

    public function factura(Venta $venta)
    {
        $venta->load(['cliente', 'detalles.producto']);
        $pdf = Pdf::loadView('Ventas.VentasShow', compact('venta'));
        return $pdf->download('FACVENT-' . str_pad($venta->id, 5, '0', STR_PAD_LEFT) . '.pdf');
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
    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'detalles.producto']); // Carga cliente y productos
        return view('Ventas.VentasShow', compact('venta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {

        $url = env('URL_SERVER_API');

        //Obtener el producto
        $response = Http::get("{$url}/ventas/{$id}");

        if ($response->failed()) {
            return redirect()->route('ventas.index')
                ->with('error', 'No se pudo obtener la información de la venta');
        }
        // Extraer los datos correctamente
        $data = $response->json();
        $venta = $data['data'] ?? $data ?? null;

        //obtener lista de clientes y productos
        $clienteResponse = Http::get("{$url}/clientes");
        $productoResponse = Http::get("{$url}/productos");

        $cliente = $clienteResponse->json()['data'] ?? [];
        $producto = $productoResponse->json()['data'] ?? [];

        return view('Ventas.VentasEdit', compact('venta', 'cliente', 'producto'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venta $venta)
    {
        // 1. Validar datos básicos
        $request->validate([
            'cliente_id' => 'required',
            'fecha_venta' => 'required|date',
            'pago' => 'required|numeric|min:0',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|numeric|min:1',
        ]);

        // 2. Crear nuevo cliente si se seleccionó "nuevo"
        if ($request->cliente_id === 'nuevo') {
            $request->validate([
                'nuevo_cliente_nombre' => 'required|string|max:255',
                'nuevo_cliente_documento' => 'required|string|max:50',
                'nuevo_cliente_telefono' => 'nullable|string|max:50',
                'nuevo_cliente_direccion' => 'nullable|string|max:255',
            ]);

            $cliente = Cliente::create([
                'nombre' => $request->nuevo_cliente_nombre,
                'documento' => $request->nuevo_cliente_documento,
                'telefono' => $request->nuevo_cliente_telefono,
                'direccion' => $request->nuevo_cliente_direccion,
            ]);
            $cliente_id = $cliente->id;
        } else {
            $cliente_id = $request->cliente_id;
        }

        // 3. Revertir stock de los productos anteriores
        foreach ($venta->detalles as $detalle) {
            $producto = Producto::findOrFail($detalle->producto_id);
            $producto->cantidad += $detalle->cantidad; // devolver stock
            $producto->save();
        }

        // 4. Eliminar detalles antiguos
        $venta->detalles()->delete();

        // 5. Calcular nuevo total y cambio
        $total = 0;
        foreach ($request->productos as $item) {
            $producto = Producto::findOrFail($item['producto_id']);
            $subtotal = $producto->precio * $item['cantidad'];
            $total += $subtotal;
        }

        $pago = $request->pago;
        $cambio = max($pago - $total, 0);

        // 6. Actualizar venta
        $venta->update([
            'cliente_id' => $cliente_id,
            'fecha_venta' => $request->fecha_venta,
            'total' => $total,
            'pago' => $pago,
            'cambio' => $cambio,
        ]);

        // 7. Crear los nuevos detalles y actualizar stock
        foreach ($request->productos as $item) {
            $producto = Producto::findOrFail($item['producto_id']);
            $subtotal = $producto->precio * $item['cantidad'];

            // Crear detalle
            $venta->detalles()->create([
                'producto_id' => $producto->id,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $producto->precio,
                'subtotal' => $subtotal,
            ]);

            // Actualizar stock
            $producto->cantidad -= $item['cantidad'];
            $producto->save();
        }

        return redirect()->route('ventas.index')->with('success', 'Venta actualizada correctamente');
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
