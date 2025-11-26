<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Answer extends Model
{
    use HasFactory;

    // Tabela será 'answers'
    
    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    /**
     * Um Conjunto de Respostas pode ser usado por muitas Questões (1:N).
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
