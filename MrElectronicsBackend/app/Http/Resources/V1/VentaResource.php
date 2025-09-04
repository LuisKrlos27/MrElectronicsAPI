<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VentaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cliente' => [
                'id' => $this->cliente->id ?? null,
                'nombre' => $this->cliente->nombre ?? null,
                'documento' => $this->cliente->documento ?? null,
                'telefono' => $this->cliente->telefono ?? null,
                'direccion' => $this->cliente->direccion ?? null,
            ],
            'fecha_venta' => $this->fecha_venta,
            'total' => $this->total,
            'pago' => $this->pago,
            'cambio' => $this->cambio,
            // Productos de la venta con sus detalles
            'productos' => $this->productos->map(function ($producto) {
                return [
                    'id' => $producto->id,
                    'tipo' => $producto->tipo->nombre ?? null,
                    'marca' => $producto->marca->nombre ?? null,
                    'modelo' => $producto->modelo->nombre ?? null,
                    'pulgada' => $producto->pulgada->medida ?? null,
                    'cantidad' => $producto->pivot->cantidad ?? null,
                    'precio_unitario' => $producto->pivot->precio_unitario ?? null,
                    'subtotal' => $producto->pivot->subtotal ?? null,
                ];
            }),
        ];
    }
}
