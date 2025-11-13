<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    /**
     * As colunas que podem ser preenchidas via atribuição em massa.
     */
    protected $fillable = [
        'questionnaire_id',
        'question_identifier',
        'display_order',
        'question_text',
        'response_type',
        'options_json', // JSON
        'dimensions_json', // JSON
    ];

    /**
     * As colunas que devem ser convertidas automaticamente (Casting).
     * Isso garante que os campos JSON sejam manipulados como arrays PHP.
     */
    protected $casts = [
        'options_json' => 'array',
        'dimensions_json' => 'array',
    ];

    // --- RELACIONAMENTOS ---

    /**
     * Uma pergunta pertence a um Questionário.
     * Define o relacionamento BelongsTo com o Model Questionnaire.
     */
    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }
}