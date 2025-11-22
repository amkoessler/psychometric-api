<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\ResponseOption;

class UpdateResponseOptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {

        
        // 1. Obtém o ID da rota (response-options/{id}).
        $id = $this->route('id');
        
        // 2. Busca o registro existente usando o ID da rota.
        $existingOption = ResponseOption::find($id);

        if (!$existingOption) {
            // Se a opção não existir, retornamos regras vazias. 
            // O Controller se encarregará de retornar 404.
            return []; 
        }
        
        // 3. Determina o scale_code que será usado para a checagem de unicidade.
        $scaleCodeToUse = $this->input('scale_code', $existingOption->scale_code);

        return [
            'scale_code' => ['sometimes', 'string', 'max:50'],
            'option_text' => ['sometimes', 'string', 'max:255'],
            
            'score_value' => [
                'sometimes', 
                'integer',
                // AGORA FUNCIONA: Implementação final da regra Rule::unique
                Rule::unique('response_options')
                    // 🚨 ESSENCIAL: Ignora o próprio ID da opção que estamos atualizando.
                    ->ignore($id)
                    // Garante que a unicidade seja checada apenas dentro da mesma escala.
                    ->where(fn ($query) => $query->where('scale_code', $scaleCodeToUse)),
            ],
        ];
    }
}