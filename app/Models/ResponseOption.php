<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'scale_code', 
        'option_text', 
        'score_value',
    ];

    // O Model ResponseOption não precisa de relacionamentos diretos por enquanto.
}