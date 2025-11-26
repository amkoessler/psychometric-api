<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// Classe renomeada para 'Area'
class Area extends Model
{
    use HasFactory;
    
    // O nome da tabela é 'areas' (Laravel assume plural)
    
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    /**
     * Relação N:M: Área <-> Dimensão
     * Uma Área de Avaliação possui muitas Dimensões.
     */
    public function dimensions(): BelongsToMany
    {
        // Usando o nome limpo da tabela pivô (area_dimension)
        return $this->belongsToMany(Dimension::class, 'area_dimension');
    }

    /**
     * Relação N:M Faltante: Área <-> Questionário
     * Uma Área de Avaliação possui muitos Questionários (Filtro Primário).
     */
    public function questionnaires(): BelongsToMany
    {
        // Usando o nome limpo da tabela pivô (area_questionnaire)
        return $this->belongsToMany(Questionnaire::class, 'area_questionnaire');
    }
}