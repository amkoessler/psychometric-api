<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'answer_id', 
        'question_identifier',
        'question_text',
        'display_order', 
    ];

    
    // --- RELACIONAMENTOS N:M ---

    /**
     * Relação N:M: Questão <-> Factor (Cálculo)
     */
    public function factors(): BelongsToMany
    {
        return $this->belongsToMany(Factor::class, 'factor_question')
                    ->withPivot(['weight', 'is_reverse_scored']); 
    }
    
    /**
     * Relação N:M: Questão <-> Questionário (Conteúdo)
     * A Tabela Pivô 'questionnaire_question' define a ordem específica no Teste.
     */
    public function questionnaires(): BelongsToMany
    {
        return $this->belongsToMany(Questionnaire::class, 'questionnaire_question')
                    ->withPivot('display_order'); // <--- Ordem específica do Questionário
    }
    

    // --- RELACIONAMENTO 1:N ---
    
    /**
     * Relação 1:N: Questão -> Answer (Opções de Resposta)
     */
    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }
}