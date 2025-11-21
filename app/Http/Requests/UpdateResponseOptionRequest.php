<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateResponseOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        // Obtém o ID da rota para a regra de unicidade (necessário para ignorar o registro atual)
        $id = $this->route('id');

        return [
            'scale_code' => ['sometimes', 'string', 'max:50'],
            'option_text' => ['sometimes', 'string', 'max:255'],
            'score_value' => ['sometimes', 'integer'],
            
            // Regra de Unicidade: Garante que a combinação scale_code/score_value
            // não cause conflito com OUTROS registros (mas ignora o registro atual).
            Rule::unique('response_options')
                ->ignore($id)
                ->where(fn ($query) => $query->where('scale_code', $this->scale_code)),
        ];
    }
}