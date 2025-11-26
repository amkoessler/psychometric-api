<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    /**
     * Relação N:M: Questionário <-> Área
     * Um Questionário possui muitas Áreas de Avaliação (Classificação Primária).
     */
    public function areas(): BelongsToMany 
    {
        // Usa a classe e o nome da tabela pivô corrigidos
        return $this->belongsToMany(Area::class, 'area_questionnaire');
    }
    
    /**
     * Relação N:M: Questionário <-> Fator (FALTANTE)
     * Um Questionário é composto por muitos Fatores (Estrutura do Teste).
     * A pivô 'questionnaire_factor' guarda o peso do Fator no Teste.
     */
    public function factors(): BelongsToMany
    {
        return $this->belongsToMany(Factor::class, 'questionnaire_factor');
    }


    /**
     * Relação N:M: Questionário <-> Questão (Reutilização)
     * Um Questionário tem muitas Questões, e uma Questão pode estar em múltiplos Questionários.
     * Mudar de HasMany para BelongsToMany para permitir reutilização de itens.
     */
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'questionnaire_question');
    }
}