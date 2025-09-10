<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use App\Http\Resources\V1\EvidenciaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProcesoResource extends JsonResource
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
            'falla' => $this->falla,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_cierre' => $this->fecha_cierre,
            'evidencias' => EvidenciaResource::collection($this->whenLoaded('evidencias')),
        ];
    }
}
