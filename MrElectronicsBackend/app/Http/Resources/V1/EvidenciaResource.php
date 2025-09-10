<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EvidenciaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
            'id' => $this->id,
            'proceso_id' => $this->proceso_id,
            'imagen' => $this->imagen ? asset('storage/' . $this->imagen) : null,
            'comentario' => $this->comentario,
        ];
    }
}
