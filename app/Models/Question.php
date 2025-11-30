<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'questionnaire_id', // para o relacionamento 1:N
        "scale_code", 
        'question_identifier', //
        'question_text', //
        'display_order', 
        'factor_id',
    ];

    
    // --- RELACIONAMENTO N:M (MANTIDO ONDE FAZ SENTIDO) ---

    /**
    * Relação N:1: Cada Questão pertence a UM Fator.
         */
    public function factor(): BelongsTo
    {
        // Retorna o fator ao qual esta questão pertence.
        return $this->belongsTo(Factor::class); 
    }
    
    
    // --- RELACIONAMENTOS 1:N ---

    /**
     *  Relação 1:N: Questão -> Questionário (Pertence a UM)
     */
    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }
    
    /**
     * Relação 1:N: Questão -> Opcoes de Resposta (Opções de Resposta)
     */
    public function scale(): BelongsTo
    {
        return $this->belongsTo(Scale::class, 'scale_id');
    }

/**
     * Relação 1:N: Questão -> Opcoes de Resposta
     *
     * Este é o relacionamento que a API espera ao usar ?include=options.
     * Ele carrega as ResponseOptions que pertencem à Scale associada a esta Questão.
     */
    public function options(): HasMany
    {
        return $this->hasMany(ResponseOption::class, 'scale_id', 'scale_id');
    }

}