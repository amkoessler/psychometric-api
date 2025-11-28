<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionnaireSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            
            'patient_id' => $this->patient_id,
            'questionnaire_id' => $this->questionnaire_id,
            
            // Campos Transacionais
            'status' => $this->status,
            'total_score' => $this->total_score,
            
            // Datas no padrão ISO 8601 (Ideal para APIs)
            'started_at' => $this->started_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(), 
            
            // Relacionamentos: Usando 'whenLoaded' para evitar N+1
            'patient' => PatientResource::make($this->whenLoaded('patient')),
            'questionnaire' => QuestionnaireResource::make($this->whenLoaded('questionnaire')),
            'responses' => PatientResponseResource::collection($this->whenLoaded('responses')),
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}