<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\VentaResource;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ventas = Venta::with(['cliente', 'detalles.producto'])->get();
        return VentaResource::collection($ventas);
    }

/**
 * Generar factura PDF de una venta
 */
    public function factura(string $id)
    {
        try {
        // Cargar la venta con todas las relaciones necesarias
        $venta = Venta::with([
            'cliente',
            'detalles.producto.tipo',
            'detalles.producto.marca',
            'detalles.producto.modelo',
            'detalles.producto.pulgada'
        ])->find($id);

        if (!$venta) {
            return response()->json([
                'message' => 'Venta no encontrada',
            ], 404);
        }

        // Convertir a array con la estructura que espera la vista
        $ventaArray = [
            'id' => $venta->id,
            'fecha_venta' => $venta->fecha_venta,
            'total' => $venta->total,
            'pago' => $venta->pago,
            'cambio' => $venta->cambio,
            'cliente' => [
                'nombre' => $venta->cliente->nombre,
                'documento' => $venta->cliente->documento,
                'telefono' => $venta->cliente->telefono,
                'direccion' => $venta->cliente->direccion,
            ],
            'productos' => $venta->detalles->map(function ($detalle) {
                return [
                    'tipo' => $detalle->producto->tipo->nombre ?? 'N/A',
                    'marca' => $detalle->producto->marca->nombre ?? 'N/A',
                    'modelo' => $detalle->producto->modelo->nombre ?? 'N/A',
                    'pulgada' => $detalle->producto->pulgada->medida ?? null,
                    'cantidad' => $detalle->cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                    'subtotal' => $detalle->subtotal
                ];
            })->toArray()
        ];

        // Generar el PDF
        $pdf = Pdf::loadView('PdfVentas.factura', ['venta' => $ventaArray]);

        // Devolver la respuesta en formato JSON con el PDF en base64
        return response()->json([
            'message' => 'Factura generada correctamente',
            'data' => [
                'pdf_base64' => base64_encode($pdf->output()),
                'filename' => 'FACVENT-' . str_pad($venta->id, 5, '0', STR_PAD_LEFT) . '.pdf'
            ]
        ]);

        } catch (\Exception $e) {
            Log::error('Error generando factura: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error al generar la factura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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

        // 3. Calcular total y cambio
        $total = 0;
        foreach ($request->productos as $item) {
            $producto = Producto::findOrFail($item['producto_id']);

            // 🚨 Validar stock antes de descontar
            if ($producto->cantidad < $item['cantidad']) {
                return response()->json([
                    'message' => "Stock insuficiente para el producto {$producto->nombre}",
                ], 422);
            }

            $subtotal = $producto->precio * $item['cantidad'];
            $total += $subtotal;
        }

        $pago = $request->pago;
        $cambio = max($pago - $total, 0);

        // 4. Crear la venta
        $venta = Venta::create([
            'cliente_id' => $cliente_id,
            'fecha_venta' => $request->fecha_venta,
            'total' => $total,
            'pago' => $pago,
            'cambio' => $cambio,
        ]);

        // 5. Crear los detalles de venta y actualizar stock
        foreach ($request->productos as $item) {
            $producto = Producto::findOrFail($item['producto_id']);
            $subtotal = $producto->precio * $item['cantidad'];

            $venta->detalles()->create([
                'producto_id' => $producto->id,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $producto->precio,
                'subtotal' => $subtotal,
            ]);

            $producto->cantidad -= $item['cantidad'];
            $producto->save();
        }

        // 6. Respuesta en JSON estilo ProductoController
        return response()->json([
            'message' => 'Venta registrada correctamente',
            'data' => new VentaResource(
                $venta->load(['cliente', 'detalles.producto'])
            )
        ],Response::HTTP_ACCEPTED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $venta = Venta::with(['cliente', 'detalles.producto'])->find($id);

        if (!$venta) {
            return response()->json([
                'message' => 'Venta no encontrada',
            ], 404);
        }

        return new VentaResource($venta);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $venta = Venta::with('detalles')->find($id);

        if (!$venta) {
            return response()->json([
                'message' => 'Venta no encontrada',
            ], Response::HTTP_NOT_FOUND);
        }

        // 1) Validación básica (igual que en store)
        $request->validate([
            'cliente_id' => 'required',
            'fecha_venta' => 'required|date',
            'pago' => 'required|numeric|min:0',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|numeric|min:1',
        ]);

        // 2) Crear cliente si viene "nuevo"
        if ($request->cliente_id === 'nuevo') {
            $request->validate([
                'nuevo_cliente_nombre' => 'required|string|max:255',
                'nuevo_cliente_documento' => 'required|string|max:50',
                'nuevo_cliente_telefono' => 'nullable|string|max:50',
                'nuevo_cliente_direccion' => 'nullable|string|max:255',
            ]);

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

        // 3) Transacción para mantener consistencia
        try {
            DB::beginTransaction();

            // 3.1) Revertir stock de detalles anteriores
            foreach ($venta->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                if ($producto) {
                    $producto->cantidad += $detalle->cantidad;
                    $producto->save();
                }
            }

            // 3.2) Eliminar detalles antiguos
            $venta->detalles()->delete();

            // 3.3) Calcular nuevo total y validar stock actual (tras revertir)
            $total = 0;

            // Usamos lockForUpdate para evitar carreras de concurrencia en stock
            foreach ($request->productos as $item) {
                $producto = Producto::lockForUpdate()->findOrFail($item['producto_id']);

                if ($producto->cantidad < $item['cantidad']) {
                    throw ValidationException::withMessages([
                        'productos' => [
                            "Stock insuficiente para el producto {$producto->id} (disponible: {$producto->cantidad}, solicitado: {$item['cantidad']})"
                        ],
                    ]);
                }

                $subtotal = $producto->precio * $item['cantidad'];
                $total += $subtotal;
            }

            $pago   = $request->pago;
            $cambio = max($pago - $total, 0);

            // 3.4) Actualizar cabecera de venta
            $venta->update([
                'cliente_id'  => $cliente_id,
                'fecha_venta' => $request->fecha_venta,
                'total'       => $total,
                'pago'        => $pago,
                'cambio'      => $cambio,
            ]);

            // 3.5) Crear nuevos detalles y descontar stock
            foreach ($request->productos as $item) {
                $producto = Producto::lockForUpdate()->findOrFail($item['producto_id']);
                $subtotal = $producto->precio * $item['cantidad'];

                $venta->detalles()->create([
                    'producto_id'     => $producto->id,
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'subtotal'        => $subtotal,
                ]);

                $producto->cantidad -= $item['cantidad'];
                $producto->save();
            }

            DB::commit();

            // Recargar relaciones para la respuesta
            $venta->load([
                'cliente',
                'productos.tipo',
                'productos.marca',
                'productos.modelo',
                'productos.pulgada',
            ]);

            return response()->json([
                'message' => 'Venta actualizada correctamente',
                'data'    => new VentaResource($venta),
            ], Response::HTTP_ACCEPTED);

        } catch (ValidationException $e) {
            DB::rollBack();
            // 422 con detalle del error de stock u otro de validación
            return response()->json([
                'message' => 'Error de validación',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Ocurrió un error al actualizar la venta',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venta $venta)
    {
        $venta->delete();

        return response()->json([
            'message' => 'Venta eliminada correctamente',
        ], Response::HTTP_ACCEPTED);
    }
}
