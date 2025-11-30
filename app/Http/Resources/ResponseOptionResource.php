<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResponseOptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // O código da escala (Ex: LIKERT_6_PONTOS_NORMAL)
            'scale_code' => $this->scale->code, 
            // O texto que será exibido (Ex: 'Muito Frequentemente')
            'text' => $this->option_text,
            // O valor usado no cálculo do score (Ex: 6)
            'score' => $this->score_value,
        ];
    }
}   