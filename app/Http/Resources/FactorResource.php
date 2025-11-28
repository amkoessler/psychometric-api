<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FactorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
            
            // Inclui o relacionamento com Dimensões (se for carregado com ->with('dimensions'))
            // É útil para mostrar a qual Dimensão o Fator pertence.
            'dimensions' => DimensionResource::collection($this->whenLoaded('dimensions')),
            
            'created_at' => $this->created_at,
        ];
    }
}