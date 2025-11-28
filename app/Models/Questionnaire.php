<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany; 

class Questionnaire extends Model
{
    use HasFactory; 

    protected $fillable = [
        'code',
        'title',
        'description',
        'edition',
        'is_active',
    ];

    // --- RELACIONAMENTOS MESTRES N:M ---

    /**
     * Relação N:M: Questionário <-> Área
     * Renomeado para 'areas' para bater com o Resource.
     */
    public function areas(): BelongsToMany 
    {
        // Usa a classe e o nome da tabela pivô
        return $this->belongsToMany(Area::class, 'area_questionnaire');
    }
    
    /**
     * Relação N:M: Questionário <-> Fator
     */
    public function factors(): BelongsToMany
    {
        return $this->belongsToMany(Factor::class, 'questionnaire_factor');
    }

    /**
     * Relação N:M: Questionário <-> Questão
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('display_order');
    }
    
    // --- RELACIONAMENTO TRANSACIONAL (NOVO) ---
    
    /**
     * Relação 1:N: Um Questionário tem muitas Sessões de Preenchimento (Instâncias Transacionais).
     */
    public function sessions(): HasMany 
    {
        return $this->hasMany(QuestionnaireSession::class); 
    }
}