<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResponseOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Assume autorização por enquanto
    }

    public function rules(): array
    {
        return [
            'scale_code' => ['required', 'string', 'max:50'],
            'option_text' => ['required', 'string', 'max:255'],
            // score_value deve ser um número inteiro, obrigatório
            'score_value' => ['required', 'integer'], 
            // Crucial: A combinação de scale_code e score_value deve ser única na tabela
            Rule::unique('response_options')->where(fn ($query) => $query->where('scale_code', $this->scale_code)),
        ];
    }
}