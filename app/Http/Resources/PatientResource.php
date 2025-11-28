<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // Se o cast 'date' estiver no Model, $this->birth_date já é um objeto Carbon ou null.
        // Formatamos explicitamente para 'Y-m-d' (ISO 8601 Date).
        $birthDateString = $this->birth_date?->format('Y-m-d');

        return [
            // DADOS PRIMÁRIOS E CHAVE
            'id' => $this->id, // Adicionado a chave primária
            'patient_code' => $this->patient_code, 
            'full_name' => $this->full_name,
            
            // DADOS DE DATA/IDADE
            // Calculado a idade (Requer objeto Carbon)
            'age' => $this->birth_date?->age, 
            // Retornando a data de nascimento como STRING formatada
            'birth_date' => $birthDateString, 
            
            // NOVOS CAMPOS ADICIONADOS À TABELA PATIENTS
            'gender' => $this->gender,
            'cpf' => $this->cpf,
            'marital_status' => $this->marital_status,
            
            'nationality' => $this->nationality,
            'birth_city' => $this->birth_city,
            'profession' => $this->profession,
            'current_occupation' => $this->current_occupation,

            'birth_order' => $this->birth_order,
            'family_members' => $this->family_members,
            // Não é necessário o (bool) se o cast 'boolean' estiver no Model
            'has_addiction' => $this->has_addiction, 
            'addiction_details' => $this->addiction_details,

            'socioeconomic_level' => $this->socioeconomic_level,
            'education_level' => $this->education_level,

            'referral_reason' => $this->referral_reason,
            'referred_by' => $this->referred_by,

            // RELACIONAMENTOS (Opcional, mas útil para resources)
            // 'sessions' => QuestionnaireSessionResource::collection($this->whenLoaded('sessions')),

            // TIMESTAMPS - Usando ISO 8601 para APIs REST (Padrão internacional)
            'registered_at' => $this->created_at?->toISOString(), 
            // Opcional, incluir o updated_at também
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}