<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory; 

    // Opcional, mas recomendado: Defender contra atribuição em massa
    protected $fillable = [
        'patient_code   ',
        'full_name',
        'birth_date',
    ];
    //  Adicionar o Cast para tratar a data como objeto Carbon
    protected $casts = [
        'birth_date' => 'date',
    ];
}