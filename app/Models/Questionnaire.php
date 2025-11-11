<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

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
}