<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds. (Método principal no topo)
     */
    public function run(): void
    {
        // O método run() está limpo. Ele chama a função privada no final da classe.
        $patients = $this->getStaticPatientData();

        foreach ($patients as $data) {
            // Atualiza o registro com TODOS os campos se o 'patient_code' existir,
            // ou cria um novo registro caso contrário.
            Patient::updateOrCreate(
                ['patient_code' => $data['patient_code']], // Condição de busca (chave única)
                $data                                     // Dados para criar ou ATUALIZAR TODOS OS CAMPOS
            );
        }
    }

    //---------------------------------------------------------
    // DECLARAÇÃO DOS DADOS ESTÁTICOS NO FINAL DA CLASSE
    //---------------------------------------------------------
    
    /**
     * Retorna o array de 20 dados estáticos dos pacientes, com faixa etária 0-80 anos.
     */
    private function getStaticPatientData(): array
    {
        return [
           
            // [1/20] Ana Clara Ribeiro (30 anos - Adulto)
            [
                'patient_code' => 'A8F3Z5', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Ana Clara Ribeiro',
                'birth_date' => '1995-03-15',
                'gender' => 'Feminino',
                'cpf' => '11122233344',
                'marital_status' => 'Solteiro',
                'nationality' => 'Brasileira',
                'birth_city' => 'São Paulo',
                'profession' => 'Psicóloga',
                'current_occupation' => 'Autônoma',
                'birth_order' => 2,
                'family_members' => 4,
                'has_addiction' => false,
                'addiction_details' => null,
                'socioeconomic_level' => 'B',
                'education_level' => 'Superior Completo',
                'referral_reason' => 'Ansiedade e estresse',
                'referred_by' => 'Dr. Carlos Silva'
            ],
            // [2/20] Bruno Alves Ferreira (45 anos - Adulto)
            [
                'patient_code' => 'C2D7R1', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Bruno Alves Ferreira',
                'birth_date' => '1980-11-20',
                'gender' => 'Masculino',
                'cpf' => '55566677788',
                'marital_status' => 'Casado',
                'nationality' => 'Brasileira',
                'birth_city' => 'Rio de Janeiro',
                'profession' => 'Engenheiro',
                'current_occupation' => 'Engenheiro',
                'birth_order' => 1,
                'family_members' => 3,
                'has_addiction' => true,
                'addiction_details' => 'Tabagismo',
                'socioeconomic_level' => 'A',
                'education_level' => 'Pós-graduado',
                'referral_reason' => 'Terapia de casal',
                'referred_by' => 'Esposa'
            ],
            // [3/20] João Pedro Lima (50 anos - Adulto)
            [
                'patient_code' => 'J9M4W6', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'João Pedro Lima',
                'birth_date' => '1975-06-10',
                'gender' => 'Masculino',
                'cpf' => '22233344455',
                'marital_status' => 'Casado',
                'nationality' => 'Brasileira',
                'birth_city' => 'Curitiba',
                'profession' => 'Professor Universitário',
                'current_occupation' => 'Professor Universitário',
                'birth_order' => 2,
                'family_members' => 4,
                'has_addiction' => false,
                'addiction_details' => null,
                'socioeconomic_level' => 'A',
                'education_level' => 'Pós-graduado',
                'referral_reason' => 'Dificuldade de concentração',
                'referred_by' => 'Auto-referência'
            ],
            // [4/20] Carla Dias Souza (25 anos - Jovem Adulto)
            [
                'patient_code' => 'L1T0P9', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Carla Dias Souza',
                'birth_date' => '2000-01-25',
                'gender' => 'Feminino',
                'cpf' => '33344455566',
                'marital_status' => 'Solteiro',
                'nationality' => 'Brasileira',
                'birth_city' => 'Porto Alegre',
                'profession' => 'Designer Gráfico',
                'current_occupation' => 'Designer Gráfico',
                'birth_order' => 1,
                'family_members' => 2,
                'has_addiction' => false,
                'addiction_details' => null,
                'socioeconomic_level' => 'B',
                'education_level' => 'Superior Completo',
                'referral_reason' => 'Transtorno de ansiedade social',
                'referred_by' => 'Mãe'
            ],
            // [5/20] Ricardo Neves (75 anos - Idoso)
            [
                'patient_code' => 'V5H2K4', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Ricardo Neves',
                'birth_date' => '1950-12-01',
                'gender' => 'Masculino',
                'cpf' => '44455566677',
                'marital_status' => 'Divorciado',
                'nationality' => 'Brasileira',
                'birth_city' => 'Fortaleza',
                'profession' => 'Aposentado', 
                'current_occupation' => 'Aposentado', 
                'birth_order' => 3,
                'family_members' => 1,
                'has_addiction' => true,
                'addiction_details' => 'Álcool',
                'socioeconomic_level' => 'C',
                'education_level' => 'Ensino Médio Completo',
                'referral_reason' => 'Recaída em alcoolismo',
                'referred_by' => 'Clínica de reabilitação'
            ],
            // [6/20] Fernanda Costa (1 ano - Bebê) - ALTERADO
            [
                'patient_code' => 'X0E6G3', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Fernanda Costa',
                'birth_date' => '2024-05-20',
                'gender' => 'Feminino',
                'cpf' => '55566677788',
                'marital_status' => 'Solteiro',
                'nationality' => 'Brasileira',
                'birth_city' => 'Salvador',
                'profession' => 'Bebê', // Ajustado
                'current_occupation' => 'Bebê', // Ajustado
                'birth_order' => 1,
                'family_members' => 5,
                'has_addiction' => false,
                'addiction_details' => null,
                'socioeconomic_level' => 'C',
                'education_level' => 'Nenhuma', // Ajustado
                'referral_reason' => 'Triagem de desenvolvimento', // Ajustado
                'referred_by' => 'Pediatra' // Ajustado
            ],
            // [7/20] Miguel Oliveira (70 anos - Idoso)
            [
                'patient_code' => 'R7S9B8', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Miguel Oliveira',
                'birth_date' => '1955-04-03',
                'gender' => 'Masculino',
                'cpf' => '66677788899',
                'marital_status' => 'Viúvo',
                'nationality' => 'Brasileira',
                'birth_city' => 'Belo Horizonte',
                'profession' => 'Aposentado',
                'current_occupation' => 'Aposentado',
                'birth_order' => 4,
                'family_members' => 3,
                'has_addiction' => false,
                'addiction_details' => null,
                'socioeconomic_level' => 'B',
                'education_level' => 'Fundamental Incompleto',
                'referral_reason' => 'Luto e depressão senil',
                'referred_by' => 'Filha'
            ],
            // [8/20] Helena Ramos (65 anos - Idoso)
            [
                'patient_code' => 'P3Y1Q0', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Helena Ramos',
                'birth_date' => '1960-07-29',
                'gender' => 'Feminino',
                'cpf' => '77788899900',
                'marital_status' => 'Casado',
                'nationality' => 'Brasileira',
                'birth_city' => 'Goiânia',
                'profession' => 'Aposentada/Consultora', 
                'current_occupation' => 'Consultora Autônoma', 
                'birth_order' => 2,
                'family_members' => 4,
                'has_addiction' => false,
                'addiction_details' => null,
                'socioeconomic_level' => 'A',
                'education_level' => 'Superior Completo',
                'referral_reason' => 'Burnout e estresse no trabalho',
                'referred_by' => 'RH da empresa'
            ],
            // [9/20] Lucas Pereira (34 anos - Adulto)
            [
                'patient_code' => 'N4V5A7', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Lucas Pereira',
                'birth_date' => '1991-10-14',
                'gender' => 'Masculino',
                'cpf' => '88899900011',
                'marital_status' => 'Solteiro',
                'nationality' => 'Brasileira',
                'birth_city' => 'Recife',
                'profession' => 'Eletricista',
                'current_occupation' => 'Eletricista autônomo',
                'birth_order' => 1,
                'family_members' => 1,
                'has_addiction' => true,
                'addiction_details' => 'Nicotina',
                'socioeconomic_level' => 'C',
                'education_level' => 'Ensino Médio Completo',
                'referral_reason' => 'Apoio para parar de fumar',
                'referred_by' => 'Auto-referência'
            ],
            // [10/20] Beatriz Rocha (43 anos - Adulto)
            [
                'patient_code' => 'T6K8D2', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Beatriz Rocha',
                'birth_date' => '1982-02-19',
                'gender' => 'Feminino',
                'cpf' => '99900011122',
                'marital_status' => 'Divorciado',
                'nationality' => 'Brasileira',
                'birth_city' => 'Manaus',
                'profession' => 'Médica',
                'current_occupation' => 'Pediatra',
                'birth_order' => 3,
                'family_members' => 3,
                'has_addiction' => false,
                'addiction_details' => null,
                'socioeconomic_level' => 'A',
                'education_level' => 'Pós-graduado',
                'referral_reason' => 'Traumas da infância',
                'referred_by' => 'Colega de profissão'
            ],
            // [11/20] Gabriel Mendes (55 anos - Adulto)
            [
                'patient_code' => 'B0Z3F9', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Gabriel Mendes',
                'birth_date' => '1970-05-05',
                'gender' => 'Masculino',
                'cpf' => '00011122233',
                'marital_status' => 'Casado',
                'nationality' => 'Brasileira',
                'birth_city' => 'Campinas',
                'profession' => 'Empresário',
                'current_occupation' => 'Empresário',
                'birth_order' => 1,
                'family_members' => 5,
                'has_addiction' => false,
                'addiction_details' => null,
                'socioeconomic_level' => 'A',
                'education_level' => 'Superior Incompleto',
                'referral_reason' => 'Problemas conjugais',
                'referred_by' => 'Esposa'
            ],
            // [12/20] Juliana Siqueira (32 anos - Adulto)
            [
                'patient_code' => 'G4C1X7', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Juliana Siqueira',
                'birth_date' => '1993-08-30',
                'gender' => 'Feminino',
                'cpf' => '12345678901',
                'marital_status' => 'Solteiro',
                'nationality' => 'Brasileira',
                'birth_city' => 'Ribeirão Preto',
                'profession' => 'Recepcionista',
                'current_occupation' => 'Recepcionista',
                'birth_order' => 2,
                'family_members' => 3,
                'has_addiction' => false,
                'addiction_details' => null,
                'socioeconomic_level' => 'B',
                'education_level' => 'Ensino Médio Completo',
                'referral_reason' => 'Baixa autoestima',
                'referred_by' => 'Auto-referência'
            ],
            // [13/20] Rafael Toledo (60 anos - Idoso)
            [
                'patient_code' => 'M2W9R0', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Rafael Toledo',
                'birth_date' => '1965-03-22',
                'gender' => 'Masculino',
                'cpf' => '23456789012',
                'marital_status' => 'Casado',
                'nationality' => 'Brasileira',
                'birth_city' => 'São José dos Campos',
                'profession' => 'Executivo de Vendas',
                'current_occupation' => 'Executivo de Vendas',
                'birth_order' => 3,
                'family_members' => 4,
                'has_addiction' => false,
                'addiction_details' => null,
                'socioeconomic_level' => 'A',
                'education_level' => 'Pós-graduado',
                'referral_reason' => 'Gestão de raiva e impulsividade',
                'referred_by' => 'Esposa'
            ],
            // [14/20] Marina Queiroz (8 anos - Criança) - ALTERADO
            [
                'patient_code' => 'Q5L6H3', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Marina Queiroz',
                'birth_date' => '2017-08-10',
                'gender' => 'Feminino',
                'cpf' => '34567890123',
                'marital_status' => 'Solteiro',
                'nationality' => 'Brasileira',
                'birth_city' => 'Natal',
                'profession' => 'Estudante Fundamental', // Ajustado
                'current_occupation' => 'Estudante Fundamental', // Ajustado
                'birth_order' => 1,
                'family_members' => 3,
                'has_addiction' => false,
                'addiction_details' => null,
                'socioeconomic_level' => 'C',
                'education_level' => 'Ensino Fundamental Incompleto', // Ajustado
                'referral_reason' => 'Problemas de aprendizagem', // Ajustado
                'referred_by' => 'Escola' // Ajustado
            ],
            // [15/20] Eduardo Santos (47 anos - Adulto)
            [
                'patient_code' => 'S7J0E1', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Eduardo Santos',
                'birth_date' => '1978-01-08',
                'gender' => 'Masculino',
                'cpf' => '45678901234',
                'marital_status' => 'Divorciado',
                'nationality' => 'Brasileira',
                'birth_city' => 'Florianópolis',
                'profession' => 'Arquiteto',
                'current_occupation' => 'Arquiteto autônomo',
                'birth_order' => 2,
                'family_members' => 2,
                'has_addiction' => true,
                'addiction_details' => 'Jogos online',
                'socioeconomic_level' => 'B',
                'education_level' => 'Superior Completo',
                'referral_reason' => 'Vício em jogos e isolamento social',
                'referred_by' => 'Amigo'
            ],
            // [16/20] Patrícia Lima (40 anos - Adulto)
            [
                'patient_code' => 'H3T8I5', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Patrícia Lima',
                'birth_date' => '1985-05-17',
                'gender' => 'Feminino',
                'cpf' => '56789012345',
                'marital_status' => 'Casado',
                'nationality' => 'Brasileira',
                'birth_city' => 'Cuiabá',
                'profession' => 'Dona de Casa',
                'current_occupation' => 'Dona de Casa',
                'birth_order' => 1,
                'family_members' => 5,
                'has_addiction' => false,
                'addiction_details' => null,
                'socioeconomic_level' => 'B',
                'education_level' => 'Ensino Médio Completo',
                'referral_reason' => 'Estresse parental',
                'referred_by' => 'Auto-referência'
            ],
            // [17/20] Felipe Aguiar (28 anos - Jovem Adulto)
            [
                'patient_code' => 'I1A4B9', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Felipe Aguiar',
                'birth_date' => '1997-04-04',
                'gender' => 'Masculino',
                'cpf' => '67890123456',
                'marital_status' => 'Solteiro',
                'nationality' => 'Brasileira',
                'birth_city' => 'Belém',
                'profession' => 'Programador',
                'current_occupation' => 'Programador',
                'birth_order' => 3,
                'family_members' => 2,
                'has_addiction' => false,
                'addiction_details' => null,
                'socioeconomic_level' => 'A',
                'education_level' => 'Superior Completo',
                'referral_reason' => 'Dificuldade em manter relacionamentos',
                'referred_by' => 'Auto-referência'
            ],
            // [18/20] Lúcia Guimarães (80 anos - Idosa)
            [
                'patient_code' => 'E9U2P7', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Lúcia Guimarães',
                'birth_date' => '1945-09-09',
                'gender' => 'Feminino',
                'cpf' => '78901234567',
                'marital_status' => 'Viúvo',
                'nationality' => 'Brasileira',
                'birth_city' => 'Teresina',
                'profession' => 'Aposentada', 
                'current_occupation' => 'Aposentada', 
                'birth_order' => 1,
                'family_members' => 1,
                'has_addiction' => false,
                'addiction_details' => null,
                'socioeconomic_level' => 'C',
                'education_level' => 'Fundamental Completo',
                'referral_reason' => 'Sentimento de solidão',
                'referred_by' => 'Médico de família'
            ],
            // [19/20] Daniel Castro (42 anos - Adulto)
            [
                'patient_code' => 'K6D0M4', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Daniel Castro',
                'birth_date' => '1983-11-27',
                'gender' => 'Masculino',
                'cpf' => '89012345678',
                'marital_status' => 'Casado',
                'nationality' => 'Brasileira',
                'birth_city' => 'Maceió',
                'profession' => 'Advogado',
                'current_occupation' => 'Advogado',
                'birth_order' => 2,
                'family_members' => 4,
                'has_addiction' => false,
                'addiction_details' => null,
                'socioeconomic_level' => 'A',
                'education_level' => 'Pós-graduado',
                'referral_reason' => 'Estresse por pressão profissional',
                'referred_by' => 'Auto-referência'
            ],
            // [20/20] Pedro Souza (6 meses - Bebê) - ALTERADO
            [
                'patient_code' => 'Z8V7N3', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Pedro Souza',
                'birth_date' => '2025-05-20',
                'gender' => 'Masculino',
                'cpf' => '90123456789',
                'marital_status' => 'Solteiro',
                'nationality' => 'Brasileira',
                'birth_city' => 'João Pessoa',
                'profession' => 'Bebê', // Ajustado
                'current_occupation' => 'Bebê', // Ajustado
                'birth_order' => 1,
                'family_members' => 3,
                'has_addiction' => false,
                'addiction_details' => null,
                'socioeconomic_level' => 'B',
                'education_level' => 'Nenhuma', // Ajustado
                'referral_reason' => 'Triagem de desenvolvimento', // Ajustado
                'referred_by' => 'Pediatra' // Ajustado
            ],
            // [20/20] Pedro Souza (6 meses - Bebê) - ALTERADO
            [
                'patient_code' => 'El11AW', // CÓDIGO ALEATÓRIO MAIÚSCULO
                'full_name' => 'Isabelle Klarkson',
                'birth_date' => '2012-10-05',
                'gender' => 'Faminino',
                'cpf' => '02547236517',
                'marital_status' => 'Solteiro',
                'nationality' => 'Canadense',
                'birth_city' => 'Rome',
                'profession' => 'Diretora Cinematográfica', // Ajustado
                'current_occupation' => 'Diretora Cinematográfica', // Ajustado
                'birth_order' => 1,
                'family_members' => 3,
                'has_addiction' => false,
                'addiction_details' => null,
                'socioeconomic_level' => 'A+',
                'education_level' => 'Pós-Graduda', // Ajustado
                'referral_reason' => 'Dra. Fátima', // Ajustado
                'referred_by' => 'Philadelphie Hospital' // Ajustado
            ],
        ];
    }
}