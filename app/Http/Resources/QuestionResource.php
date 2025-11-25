<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            // Identificação e Ordenação
            'id' => $this->id,
            'question_identifier' => $this->question_identifier, // Ex: '01', 'A'
            'display_order' => $this->display_order, // Ordem de exibição (UI)

            // Conteúdo
            'text' => $this->question_text,
            'response_type' => $this->response_type,
            
            'scale_code' => $this->scale_code,

            
            // Timestamps (opcional)
            'created_at' => $this->created_at,
        ];
    }
}