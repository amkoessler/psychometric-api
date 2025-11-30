<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Importe o HasMany

class Scale extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'code', 
        'name', 
        'description'
    ];

    /**
     * Relação 1:N: Uma Escala possui muitas Opções de Resposta.
     */
    public function responseOptions(): HasMany
    {
        return $this->hasMany(ResponseOption::class, 'scale_id');
    }
    
    /**
     * Relação 1:N: Uma Escala pode ser usada por muitas Questões.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'scale_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ResponseOption::class);
    }
}