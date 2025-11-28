<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
// Importa o QuestionResource para formatar as perguntas aninhadas
use App\Http\Resources\QuestionResource; 

class QuestionnaireResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            // Campos de Identificação
            'id' => $this->id,
            'code' => $this->code,
            
            // Campos de Conteúdo
            'title' => $this->title,
            'description' => $this->description,
            'edition' => $this->edition,
            'is_active' => $this->is_active,

            // NOVO: Inclui as Áreas de Avaliação (com as dimensões aninhadas)
            'assessment_areas' => AreaResource::collection($this->whenLoaded('areas')),

            // NOVO: Inclui as questões aninhadas.
            // whenLoaded('questions') garante que este array só apareça se o ->with('questions') foi chamado.
            'questions' => QuestionResource::collection($this->whenLoaded('questions')),

            // Timestamps
            'created_at' => $this->created_at,
        ];
    }
}