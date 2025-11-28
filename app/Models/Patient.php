<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory; 

    // Opcional, mas recomendado: Defender contra atribuição em massa
    protected $fillable = [
        // CAMPOS ORIGINAIS
        'patient_code',
        'full_name',
        'birth_date',
        // NOVOS CAMPOS ADICIONADOS - OBRIGATÓRIOS PARA ATRIBUIÇÃO EM MASSA
        'gender',
        'cpf',
        'marital_status',
        'nationality',
        'birth_city',
        'profession',
        'current_occupation',
        'birth_order',
        'family_members',
        'has_addiction', // Sim, precisa estar aqui
        'addiction_details',
        'socioeconomic_level',
        'education_level',
        'referral_reason',
        'referred_by',
    ];
    
    // Adicionar os Casts para tratar a data e booleano como objetos corretos
    protected $casts = [
        'birth_date' => 'date',
        'has_addiction' => 'boolean', // Adicionado para garantir o tipo
        // Opcional, para campos unsignedSmallInteger
        'birth_order' => 'integer',
        'family_members' => 'integer',
    ];

    /**
     * Relação 1:N: Um Paciente tem muitas Sessões de Questionário.
     * Esta é a ponta 'Muitos' do relacionamento N:M com Questionnaires.
     */
    public function sessions(): HasMany
    {
        // Assumindo que o nome da tabela intermediária será QuestionnaireSession
        return $this->hasMany(QuestionnaireSession::class); 
    }
}