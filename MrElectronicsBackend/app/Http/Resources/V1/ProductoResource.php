<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
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
            'tipo' => [
                'id' => $this->tipo->id ?? null,
                'nombre' => $this->tipo->nombre ?? null,
            ],
            'marca' => [
                'id' => $this->marca->id ?? null,
                'nombre' => $this->marca->nombre ?? null,
            ],
            'modelo' => [
                'id' => $this->modelo->id ?? null,
                'nombre' => $this->modelo->nombre ?? null,
            ],
            'pulgada' => [
                'id' => $this->pulgada->id ?? null,
                'medida' => $this->pulgada->medida ?? null,
            ],
            'numero_pieza' => $this->numero_pieza,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'cantidad' => $this->cantidad,

        ];
    }
}
