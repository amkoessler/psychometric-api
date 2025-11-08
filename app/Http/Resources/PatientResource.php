<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Mapeia os campos da tabela
            'patient_code' => $this->patient_id, // Usamos o código de 6 dígitos
            'full_name' => $this->full_name,
            'birth_date' => $this->birth_date,
            // Adicionando o campo calculado: IDADE
            'age' => $this->birth_date ? $this->birth_date->age : null,
            'registered_at' => $this->created_at->format('Y-m-d H:i:s'), // Formata o timestamp
        ];
    }
}
