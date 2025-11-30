<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * Relação 1:N: Fator -> Questão
     * Um Fator possui muitas Questões, e CADA Questão pertence SOMENTE a este Fator.
     */
    public function questions(): HasMany
    {
        // O Laravel assume que a coluna 'factor_id' está na tabela 'questions'
        return $this->hasMany(Question::class);
    }
}