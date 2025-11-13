<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}