<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 

class ResponseOption extends Model
{
    use HasFactory;

    /**
     * Define as colunas que podem ser preenchidas através da atribuição em massa (Mass Assignment).
     * Esta é a correção para o erro MassAssignmentException.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'scale_id', 
        'option_text', 
        'score_value',
    ];

    /**
     * Relação N:1: Uma Opção de Resposta pertence a uma Escala.
     */
    public function scale(): BelongsTo
    {
        return $this->belongsTo(Scale::class, 'scale_id');
    }
}