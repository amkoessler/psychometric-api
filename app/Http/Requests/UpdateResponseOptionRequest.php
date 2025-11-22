<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\ResponseOption;

class UpdateResponseOptionRequest extends FormRequest
{
   public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        $id = $this->route('id');
        
        // 1. Busca o registro existente usando o ID da rota
        $existingOption = ResponseOption::find($id);

        if (!$existingOption) {
            // Se a opção não existir, o Controller retornará 404. A validação pode parar aqui.
            return []; 
        }
        
        // 2. Determina o scale_code que será usado para a checagem de unicidade.
        // Se o usuário enviou um novo 'scale_code', usamos ele.
        // Caso contrário (PATCH sem scale_code), usamos o código atual do registro.
        $scaleCodeToUse = $this->input('scale_code', $existingOption->scale_code);

        return [
            'scale_code' => ['sometimes', 'string', 'max:50'],
            'option_text' => ['sometimes', 'string', 'max:255'],
            
            'score_value' => [
                'sometimes', 
                'integer',
                // AGORA FUNCIONA: Verifica a unicidade usando o scaleCodeToUse,
                // que garante que o scale_code atual seja incluído na checagem.
                Rule::unique('response_options')
                    ->ignore($id)
                    ->where(fn ($query) => $query->where('scale_code', $scaleCodeToUse)),
            ],
        ];
    }
}