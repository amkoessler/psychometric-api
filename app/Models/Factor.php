<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Factor extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    /**
     * Relação N:M: Fator <-> Dimensão
     * Um Fator contribui para múltiplas Dimensões (Classificação Conceitual).
     */
    public function dimensions(): BelongsToMany
    {
        // Usa a tabela pivô 'dimension_factor'
        return $this->belongsToMany(Dimension::class, 'dimension_factor');
    }

    /**
     * Relação N:M: Fator <-> Questionário
     * Um Fator pode ser usado em múltiplos Testes (Filtro e Regras de Uso no Teste).
     */
    public function questionnaires(): BelongsToMany
    {
        // Usa a tabela pivô 'questionnaire_factor'
        return $this->belongsToMany(Questionnaire::class, 'questionnaire_factor');
    }

    /**
     * Relação N:M: Fator <-> Questão (CRUCIAL PARA O CÁLCULO)
     * Um Fator agrupa muitas Questões, e uma Questão pode contribuir para múltiplos Fatores.
     * A Tabela Pivô 'factor_question' guardará as regras de pontuação (peso, reversão) por item/fator.
     */
    public function questions(): BelongsToMany
    {
        // Mudança para BelongsToMany para permitir que uma Questão pertença a múltiplos Fatores.
        return $this->belongsToMany(Question::class, 'factor_question')
                    ->withPivot(['weight', 'is_reverse_scored']); // Campos sugeridos para a Pivô
    }
}