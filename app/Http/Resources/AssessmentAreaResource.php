<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentAreaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
            
            // INCLUI AS DIMENSÕES: Carrega o relacionamento 'dimensions'
            'dimensions' => DimensionResource::collection($this->whenLoaded('dimensions')),
            
            'created_at' => $this->created_at,
        ];
    }
}