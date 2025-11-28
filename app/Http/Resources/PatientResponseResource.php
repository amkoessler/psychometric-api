<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResponseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // Chaves Estrangeiras na Raiz (O que está na tabela patient_responses)
            'questionnaire_session_id' => $this->questionnaire_session_id,
            // ID da Questão na tabela PatientResponse
            'question_id' => $this->question_id,
            // ID da Opção ESCOLHIDA na tabela PatientResponse
            'response_option_id' => $this->response_option_id, 

            // 💡 Recursos Mestres Aninhados (O que está nas tabelas mestre)
            'question' => QuestionResource::make($this->whenLoaded('question')), 
            
            // 💡 Inclui a opção de resposta completa, que terá seu próprio ID
            'selected_option' => ResponseOptionResource::make($this->whenLoaded('option')),

            // Timestamps
            'responded_at' => $this->created_at?->toISOString(),
        ];
    }
}