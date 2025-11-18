<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Questionnaire extends Model
{
    use HasFactory; 

    // Opcional: Proteger contra atribuição em massa
    protected $fillable = [
        'code',
        'title',
        'description',
        'edition',
        'is_active',
    ];

    /**
     * Um questionário tem muitas perguntas.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Um questionário possui muitas Áreas de Avaliação (M:N).
     * @return BelongsToMany
     */
    public function assessmentAreas(): BelongsToMany // <-- NOVIDADE
    {
        // ATENÇÃO: É necessário criar a tabela pivô (ex: 'assessment_area_questionnaire')
        return $this->belongsToMany(AssessmentArea::class, 'assessment_area_questionnaire');
    }
}