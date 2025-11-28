<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientResponse extends Model
{
    use HasFactory;
    
    protected $table = 'patient_responses';

    // A chave primária é composta, então definimos o uso de 'id' como false
    public $incrementing = false; 

    protected $primaryKey = ['questionnaire_session_id', 'question_id'];

    // Colunas que são chaves estrangeiras.
    protected $fillable = [
        'questionnaire_session_id',
        'question_id',
        'response_option_id',
    ];

    /**
     * Relação N:1: Uma resposta pertence a UMA Sessão de Questionário.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireSession::class);
    }

    /**
     * Relação N:1: Uma resposta pertence a UMA Questão.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Relação N:1: Uma resposta aponta para UMA Opção de Resposta.
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(ResponseOption::class, 'response_option_id');
    }
}