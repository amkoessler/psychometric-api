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
     * Uma Dimensão pertence a muitas Áreas de Avaliação (Muitos-para-Muitos).
     */
    public function assessmentAreas(): BelongsToMany
    {
        // Usa a tabela pivô 'assessment_area_dimension'
        return $this->belongsToMany(AssessmentArea::class, 'assessment_area_dimension');
    }
}