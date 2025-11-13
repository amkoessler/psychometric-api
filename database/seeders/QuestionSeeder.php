<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Questionnaire;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Cache para IDs de Questionários para evitar consultas repetidas.
     */
    private array $questionnaireIds = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garante que o ID do Questionário seja buscado e cacheado
        $allQuestions = $this->getStaticQuestionData();
        $displayOrder = 1;

        foreach ($allQuestions as $data) {
            // 1. Busca ou Cacheia o ID do Questionário
            $questionnaireId = $this->getQuestionnaireIdByCode($data['questionnaire_code']);
            
            if (!$questionnaireId) {
                // Se o Questionário pai não existir, pule esta pergunta (melhoria de segurança)
                echo "ATENÇÃO: Questionário '{$data['questionnaire_code']}' não encontrado. Execute o QuestionnaireSeeder primeiro.\n";
                continue;
            }

            // 2. Prepara os dados para o Question Model
            $questionData = [
                'questionnaire_id' => $questionnaireId,
                'question_identifier' => $data['question_identifier'],
                // display_order é sequencial (para drag&drop inicial)
                'display_order' => $displayOrder++,
                'question_text' => $data['question_text'],
                'response_type' => $data['response_type'],
                // Garantir que os dados JSON sejam arrays PHP
                'options_json' => $data['options_json'], 
                'dimensions_json' => $data['dimensions_json'], 
            ];

            // 3. Usa updateOrCreate para garantir a unicidade e atualização
            Question::updateOrCreate(
                [
                    'questionnaire_id' => $questionData['questionnaire_id'],
                    'question_identifier' => $questionData['question_identifier'],
                ],
                $questionData
            );
        }
    }

    /**
     * Retorna o ID do questionário baseado no seu código (com caching).
     */
    private function getQuestionnaireIdByCode(string $code): ?int
    {
        if (isset($this->questionnaireIds[$code])) {
            return $this->questionnaireIds[$code];
        }

        $questionnaire = Questionnaire::where('code', $code)->first();
        
        if ($questionnaire) {
            $this->questionnaireIds[$code] = $questionnaire->id;
            return $questionnaire->id;
        }

        return null;
    }

    /**
     * Retorna o array de dados estáticos das perguntas (BDI, BAI e IFP).
     */
    private function getStaticQuestionData(): array
    {
        // -------------------------------------------------------------
        // OPÇÕES PADRÃO
        // -------------------------------------------------------------
        
        // Exemplo: Likert de 4 pontos para BDI/BAI
        $options_bai = [
            ["text" => "0 - Absolutamente não.", "score" => 0],
            ["text" => "1 - Levemente (não me incomodou muito).", "score" => 1],
            ["text" => "2 - Moderadamente (foi muito desagradável).", "score" => 2],
            ["text" => "3 - Gravemente (dificilmente pude suportar).", "score" => 3],
        ];

        // Exemplo: Binário para IFP
        $options_ifp = [
            ["text" => "Não me identifico/Falso", "score" => 0],
            ["text" => "Me identifico/Verdadeiro", "score" => 1],
        ];
        
        // -------------------------------------------------------------
        // DADOS FINAIS
        // -------------------------------------------------------------

        $questions = [];

        // Adiciona as perguntas BDI-II e BAI (mantendo os 5 exemplos anteriores)
        $questions = array_merge($questions, [
            // =========================================================
            // EXEMPLOS DO QUESTIONÁRIO BDI-II (Código: BDI-II)
            // ... (3 perguntas)
            // =========================================================
            [
                'questionnaire_code' => 'BDI-II', 
                'question_identifier' => '1',
                'question_text' => 'Tristeza:',
                'response_type' => 'LIKERT_BDI',
                'dimensions_json' => ['SOMATICO-AFETIVO'],
                'options_json' => [
                    ["text" => "Não me sinto triste.", "score" => 0],
                    ["text" => "Sinto-me triste parte do tempo.", "score" => 1],
                    ["text" => "Sinto-me triste o tempo todo.", "score" => 2],
                    ["text" => "Sinto-me tão triste ou infeliz que não consigo suportar.", "score" => 3],
                ],
            ],
            [
                'questionnaire_code' => 'BDI-II', 
                'question_identifier' => '2',
                'question_text' => 'Pessimismo:',
                'response_type' => 'LIKERT_BDI',
                'dimensions_json' => ['COGNITIVO'],
                'options_json' => [
                    ["text" => "Não estou desencorajado(a) quanto ao meu futuro.", "score" => 0],
                    ["text" => "Sinto-me mais desencorajado(a) que no passado.", "score" => 1],
                    ["text" => "Sinto que não vou me recuperar.", "score" => 2],
                    ["text" => "Sinto que meu futuro não tem esperança.", "score" => 3],
                ],
            ],
            [
                'questionnaire_code' => 'BDI-II', 
                'question_identifier' => '3',
                'question_text' => 'Satisfação (Pergunta de pontuação inversa):',
                'response_type' => 'LIKERT_BDI',
                'dimensions_json' => ['COGNITIVO'],
                'options_json' => [
                    ["text" => "Sinto-me totalmente satisfeito(a).", "score" => 3], // Pontuação invertida
                    ["text" => "Sinto-me ligeiramente satisfeito(a).", "score" => 2], 
                    ["text" => "Estou insatisfeito(a).", "score" => 1],
                    ["text" => "Estou completamente insatisfeito(a).", "score" => 0],
                ],
            ],

            // =========================================================
            // EXEMPLOS DO QUESTIONÁRIO BAI (Código: BAI)
            // ... (2 perguntas)
            // =========================================================
            [
                'questionnaire_code' => 'PALO',
                'question_identifier' => '1',
                'question_text' => 'Dormência ou formigamento',
                'response_type' => 'LIKERT_BAI',
                'dimensions_json' => ['SOMATICO'],
                'options_json' => $options_bai,
            ],
            [
                'questionnaire_code' => 'PALO',
                'question_identifier' => '2',
                'question_text' => 'Sensação de calor',
                'response_type' => 'LIKERT_BAI',
                'dimensions_json' => ['SOMATICO', 'AFETIVO'], 
                'options_json' => $options_bai,
            ],
        ]);
        
        // =============================================================
        // NOVO: 100 QUESTÕES DO IFP (Código: IFP)
        // =============================================================
        // As dimensões (Necessidades) foram inferidas com base no conteúdo da questão e na estrutura do IFP.
        $ifp_questions = [
            ['id' => '01', 'text' => 'Gosto de fazer coisas que outras pessoas consideram fora do comum', 'dimension' => ['MU']],
            ['id' => '02', 'text' => 'Gostaria de realizar um grande feito ou grande obra na minha vida', 'dimension' => ['RE']],
            ['id' => '03', 'text' => 'Gosto de experimentar novidades e mudanças em meu dia-a-dia', 'dimension' => ['MU']],
            ['id' => '04', 'text' => 'Não gosto de situações em que se exige que eu me comporte de determinada maneira', 'dimension' => ['AU']],
            ['id' => '05', 'text' => 'Gosto de dizer o que eu penso a respeito das coisas', 'dimension' => ['OP']],
            ['id' => '06', 'text' => 'Gosto de saber o que grandes personalidades disseram sobre os problemas pelos quais eu me interesso', 'dimension' => ['NF']],
            ['id' => '07', 'text' => 'Gosto de ser capaz de fazer as coisas melhor do que as outras pessoas', 'dimension' => ['RE']],
            ['id' => '08', 'text' => 'Gosto de concluir qualquer trabalho ou tarefa que tenha começado', 'dimension' => ['PE']],
            ['id' => '09', 'text' => 'Gosto de ajudar meus amigos quando eles estão com problemas', 'dimension' => ['AF']],
            ['id' => '10', 'text' => 'Não costumo abandonar um quebra-cabeça ou problema antes que consiga resolvê-lo', 'dimension' => ['PE']],
            ['id' => '11', 'text' => 'Gosto de dizer aos outros como fazer seus trabalhos', 'dimension' => ['DO']],
            ['id' => '12', 'text' => 'Gostaria de ser considerado(a) uma autoridade em algum trabalho, profissão ou campo de especialização', 'dimension' => ['DO']],
            ['id' => '13', 'text' => 'Gosto de experimentar e provar coisas novas', 'dimension' => ['MU']],
            ['id' => '14', 'text' => 'Quando tenho alguma tarefa para fazer, gosto de começar logo e permanecer trabalhando até completá-la', 'dimension' => ['PE']],
            ['id' => '15', 'text' => 'Aceito com prazer a liderança das pessoas que admiro', 'dimension' => ['DE']],
            ['id' => '16', 'text' => 'Gosto de trabalhar horas a fio sem ser interrompido(a)', 'dimension' => ['PE']],
            ['id' => '17', 'text' => 'Gosto que meus amigos me dêem muita atenção quando estou sofrendo ou doente', 'dimension' => ['SU']],
            ['id' => '18', 'text' => 'Costumo analisar minhas intenções e sentimentos', 'dimension' => ['IN']],
            ['id' => '19', 'text' => 'Gosto de fazer com carinho pequenas favores a meus amigos', 'dimension' => ['AF']],
            ['id' => '20', 'text' => 'Gosto de ficar acordado(a) até tarde para terminar um trabalho', 'dimension' => ['PE']],
            ['id' => '21', 'text' => 'Gosto de andar pelo país e viver em lugares diferentes', 'dimension' => ['MU']],
            ['id' => '22', 'text' => 'Gosto de analisar os sentimentos e intenções dos outros', 'dimension' => ['IN']],
            ['id' => '23', 'text' => 'Gosto de fazer gozação com pessoas que fazem coisas que eu considero estúpidas', 'dimension' => ['AG']],
            ['id' => '24', 'text' => 'Tenho vontade de me vingar quando alguém me insulta', 'dimension' => ['AG']],
            ['id' => '25', 'text' => 'Gosto de pensar sobre o caráter dos meus amigos e tentar descobrir o que os faz serem como são', 'dimension' => ['IN']],
            ['id' => '26', 'text' => 'Sou leal aos meus amigos', 'dimension' => ['AF']],
            ['id' => '27', 'text' => 'Gosto de levar um trabalho ou tarefa até o fim antes de começar outro', 'dimension' => ['PE']],
            ['id' => '28', 'text' => 'Gosto de dizer aos meus superiores que eles fizeram um bom trabalho, quando acredito nisso', 'dimension' => ['DE']],
            ['id' => '29', 'text' => 'Gosto que meus amigos sejam solidários comigo e me animem quando estou deprimido(a)', 'dimension' => ['SU']],
            ['id' => '30', 'text' => 'Antes de começar um trabalho, gosto de organizá-lo e planejá-lo', 'dimension' => ['OR']],
            ['id' => '31', 'text' => 'Gosto que meus amigos demostrem muito afeto por mim', 'dimension' => ['SU']],
            ['id' => '32', 'text' => 'Gosto de realizar tarefas que, na opinião dos outros, exigem habilidade e esforço', 'dimension' => ['RE']],
            ['id' => '33', 'text' => 'Gosto de ser bem-sucedido nas coisas que faço', 'dimension' => ['RE']],
            ['id' => '34', 'text' => 'Gosto de fazer amizades', 'dimension' => ['AF']],
            ['id' => '35', 'text' => 'Gosto de ser considerado(a) um(a) líder pelos outros', 'dimension' => ['DO']],
            ['id' => '36', 'text' => 'Gosto de realizar com afinco (sem descanso) qualquer trabalho que faço', 'dimension' => ['PE']],
            ['id' => '37', 'text' => 'Gosto de participar de grupos cujos membros se tratem com afeto e amizade', 'dimension' => ['AF']],
            ['id' => '38', 'text' => 'Sinto-me satisfeito(a) quando realizo bem um trabalho difícil', 'dimension' => ['RE']],
            ['id' => '39', 'text' => 'Tenho vontade de mandar os outros calarem a boca quando discordo deles', 'dimension' => ['AG']],
            ['id' => '40', 'text' => 'Gosto de fazer coisas do meu jeito sem me importar com o que os outros possam pensar', 'dimension' => ['AU']],
            ['id' => '41', 'text' => 'Gosto de viajar e conhecer o país', 'dimension' => ['MU']],
            ['id' => '42', 'text' => 'Gosto de me fixar em um trabalho ou problema mesmo quando a solução pareça extremamente difícil', 'dimension' => ['PE']],
            ['id' => '43', 'text' => 'Gosto de conhecer novas pessoas', 'dimension' => ['AF']],
            ['id' => '44', 'text' => 'Gosto de dividir coisas com os outros', 'dimension' => ['AS']],
            ['id' => '45', 'text' => 'Sinto-me satisfeito(a) quando consigo convencer e influenciar os outros', 'dimension' => ['DO']],
            ['id' => '46', 'text' => 'Gosto de demonstrar muita afeição por meus amigos', 'dimension' => ['AF']],
            ['id' => '47', 'text' => 'Gosto de prestar favores aos outros', 'dimension' => ['AS']],
            ['id' => '48', 'text' => 'Gosto de seguir instruções e fazer o que é esperado de mim', 'dimension' => ['DE']],
            ['id' => '49', 'text' => 'Gosto de elogiar alguém que admiro', 'dimension' => ['DE']],
            ['id' => '50', 'text' => 'Quando planejo alguma coisa, procuro sugestões de pessoas que respeito', 'dimension' => ['DE']],
            ['id' => '51', 'text' => 'Gosto de manter minhas coisas limpas e ordenadas em minha escrivaninha ou em meu local de trabalho', 'dimension' => ['OR']],
            ['id' => '52', 'text' => 'Gosto de manter fortes laços de amizade', 'dimension' => ['AF']],
            ['id' => '53', 'text' => 'Gosto que meus amigos me ajudem quando estou com problema', 'dimension' => ['SU']],
            ['id' => '54', 'text' => 'Gosto que meus amigos mostrem boa vontade em me prestar pequenos favores', 'dimension' => ['SU']],
            ['id' => '55', 'text' => 'Gosto de manter minhas cartas, contas e outros papéis bem arrumados e arquivados de acordo com algum sistema', 'dimension' => ['OR']],
            ['id' => '56', 'text' => 'Gosto que meus amigos sejam solidários e compreensivos quando tenho problemas', 'dimension' => ['SU']],
            ['id' => '57', 'text' => 'Prefiro fazer coisas com meus amigos a fazer sozinho', 'dimension' => ['AF']],
            ['id' => '58', 'text' => 'Gosto de tratar outras pessoas com bondade e compaixão', 'dimension' => ['AS']],
            ['id' => '59', 'text' => 'Gosto de comer em restaurantes novos e exóticos(diferentes)', 'dimension' => ['MU']],
            ['id' => '60', 'text' => 'Procuro entender como meus amigos se sentem a respeito de problemas que eles enfrentam', 'dimension' => ['IN']],
            ['id' => '61', 'text' => 'Gosto de ser o centro das atenções em um grupo', 'dimension' => ['EX']],
            ['id' => '62', 'text' => 'Gosto de ser um dos líderes nas organizações e grupos aos quais pertenço', 'dimension' => ['DO']],
            ['id' => '63', 'text' => 'Gosto de ser independente dos outros para decidir o que quero fazer', 'dimension' => ['AU']],
            ['id' => '64', 'text' => 'Gosto de me manter em contato com meus amigos', 'dimension' => ['AF']],
            ['id' => '65', 'text' => 'Quando participo de uma comissão (reunião), gosto de ser indicado ou eleito presidente', 'dimension' => ['DO']],
            ['id' => '66', 'text' => 'Gosto de fazer tantos amigos quanto possível', 'dimension' => ['AF']],
            ['id' => '67', 'text' => 'Gosto de observar como uma outra pessoa se sente numa determinada situação', 'dimension' => ['IN']],
            ['id' => '68', 'text' => 'Quando estou em um grupo, aceito com prazer a liderança de outra pessoa para decidir o que o grupo fará', 'dimension' => ['DE']],
            ['id' => '69', 'text' => 'Não gosto de me sentir pressionado(a) por responsabilidades e deveres', 'dimension' => ['AU']],
            ['id' => '70', 'text' => 'Às vezes, fico tão irritado(a) que sinto vontade de jogar e quebrar coisas', 'dimension' => ['AG']],
            ['id' => '71', 'text' => 'Gosto de fazer perguntas que ninguém será capaz de responder', 'dimension' => ['OP']],
            ['id' => '72', 'text' => 'Às vezes, gosto de fazer coisas simplesmente para ver o efeito que terão sobre os outros', 'dimension' => ['EX']],
            ['id' => '73', 'text' => 'Sou solidário com meus amigos quando machucados ou doentes', 'dimension' => ['AS']],
            ['id' => '74', 'text' => 'Não tenho medo de criticar pessoas que ocupam posições de autoridade', 'dimension' => ['AG', 'OP']], // 2 dimensões
            ['id' => '75', 'text' => 'Gosto de fiscalizar e dirigir os atos dos outros sempre que posso', 'dimension' => ['DO']],
            ['id' => '76', 'text' => 'Culpo os outros quando as coisas dão errado comigo', 'dimension' => ['AG']],
            ['id' => '77', 'text' => 'Gosto de ajudar pessoas que têm menos sorte do que eu', 'dimension' => ['AS']],
            ['id' => '78', 'text' => 'Gosto de planejar e organizar, em todos os detalhes, qualquer trabalho que eu faço', 'dimension' => ['OR']],
            ['id' => '79', 'text' => 'Gosto de fazer coisas novas e diferentes', 'dimension' => ['MU', 'AU']], // 2 dimensões
            ['id' => '80', 'text' => 'Gostaria de realizar com sucesso alguma coisa de grande importância', 'dimension' => ['RE']],
            ['id' => '81', 'text' => 'Quando estou com um grupo de pessoas, gosto de decidir sobre o que vamos fazer', 'dimension' => ['DO']],
            ['id' => '82', 'text' => 'Interesso-me em conhecer a vida de grandes personalidades', 'dimension' => ['NF']],
            ['id' => '83', 'text' => 'Procuro me adaptar ao modo de ser das pessoas que admiro', 'dimension' => ['DE']],
            ['id' => '84', 'text' => 'Gosto de resolver quebra-cabeças e problemas com os quais pessoas têm dificuldades', 'dimension' => ['RE']],
            ['id' => '85', 'text' => 'Gosto de falar sobre os meus sucessos', 'dimension' => ['EX']],
            ['id' => '86', 'text' => 'Gosto de dar o melhor de mim em tudo que faço', 'dimension' => ['RE']],
            ['id' => '87', 'text' => 'Gosto de estudar e analisar o comportamento dos outros', 'dimension' => ['IN']],
            ['id' => '88', 'text' => 'Gosto de contar aos outros aventuras e coisas estranhas que acontecem comigo', 'dimension' => ['EX']],
            ['id' => '89', 'text' => 'Perdôo as pessoas que às vezes possam me magoar', 'dimension' => ['AS']],
            ['id' => '90', 'text' => 'Gosto de prever (entender) como meus amigos irão agir em diferentes situações', 'dimension' => ['IN']],
            ['id' => '91', 'text' => 'Gosto de me sentir livre para fazer o que quero', 'dimension' => ['AU']],
            ['id' => '92', 'text' => 'Gosto de me sentir livre para ir e vir quando quiser', 'dimension' => ['AU']],
            ['id' => '93', 'text' => 'Gosto de usar palavras cujo significado as outras pessoas desconhecem', 'dimension' => ['OP']],
            ['id' => '94', 'text' => 'Gosto de planejar antes de iniciar algo difícil', 'dimension' => ['OR']],
            ['id' => '95', 'text' => 'Qualquer trabalho escrito que faço, gosto que seja preciso, limpo e bem-organizado', 'dimension' => ['OR']],
            ['id' => '96', 'text' => 'Gosto que as pessoas notem e comentem a minha aparência quando estou em público', 'dimension' => ['EX']],
            ['id' => '97', 'text' => 'Gosto que meus amigos me tratem com delicadeza', 'dimension' => ['SU']],
            ['id' => '98', 'text' => 'Gosto de ser generoso(a) com os outros', 'dimension' => ['AS']],
            ['id' => '99', 'text' => 'Gosto de contar estórias e piadas engraçadas em festas', 'dimension' => ['EX']],
            ['id' => '100', 'text' => 'Gosto de dizer coisas que os outros consideram engraçadas e inteligentes', 'dimension' => ['EX']],
        ];

        // Mapeia os dados do IFP para o formato final do Seeder
        foreach ($ifp_questions as $q) {
            $questions[] = [
                'questionnaire_code' => 'IFP',
                'question_identifier' => $q['id'],
                'question_text' => $q['text'],
                'response_type' => 'BINARY_AGREEMENT',
                'dimensions_json' => $q['dimension'],
                'options_json' => $options_ifp,
            ];
        }

        return $questions;
    }
}