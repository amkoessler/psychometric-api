<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionnaireSession extends Model
{
    use HasFactory;
    
    protected $table = 'questionnaire_sessions';

    protected $fillable = [
        'patient_id',
        'questionnaire_id',
        'status',
        'started_at',
        'completed_at',
        'total_score',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_score' => 'float', 
    ];

    /**
     * Relação N:1: Uma Sessão pertence a UM Questionário Mestre.
     */
    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }

    /**
     * Relação N:1: Uma Sessão pertence a UM Paciente.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relação 1:N: Uma Sessão tem MUITAS Respostas Transacionais (PatientResponse).
     */
    public function responses(): HasMany
    {
        return $this->hasMany(PatientResponse::class);
    }
}