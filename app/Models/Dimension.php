<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Dimension extends Model
{
    use HasFactory;

    // O nome da tabela é 'dimensions'
    
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];
    
    /**
     * Relação N:M: Dimensão <-> Área
     * Uma Dimensão pertence a muitas Áreas (Classificação Conceitual).
     */
    public function areas(): BelongsToMany
    {
        // Usa o nome limpo da tabela pivô (area_dimension)
        return $this->belongsToMany(Area::class, 'area_dimension');
    }

    /**
     * Relação N:M Faltante: Dimensão <-> Fator
     * Uma Dimensão agrupa muitos Fatores, e um Fator contribui para muitas Dimensões.
     * Esta tabela pivô ('dimension_factor') será crucial para as regras de agregação.
     */
    public function factors(): BelongsToMany
    {
        // Usa a tabela pivô 'dimension_factor'
        return $this->belongsToMany(Factor::class, 'dimension_factor');
    }
}