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
        // Variável para a data formatada, garantindo que seja uma string
        $birthDateString = $this->birth_date ? $this->birth_date->format('Y-m-d') : null;

        return [
            // CAMPOS EXISTENTES
            'patient_code' => $this->patient_code, 
            'full_name' => $this->full_name,
            
            // Calculado a idade (Ainda requer Carbon)
            'age' => $this->birth_date ? $this->birth_date->age : null,
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
            'has_addiction' => (bool) $this->has_addiction,
            'addiction_details' => $this->addiction_details,

            'socioeconomic_level' => $this->socioeconomic_level,
            'education_level' => $this->education_level,

            'referral_reason' => $this->referral_reason,
            'referred_by' => $this->referred_by,

            // TIMESTAMPS
            'registered_at' => $this->created_at->format('Y-m-d H:i:s'), 
        ];
    }
}