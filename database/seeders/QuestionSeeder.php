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
        // OPÇÕES DE RESPOSTAS
        // -------------------------------------------------------------

        $opcoes_respostas = [
            // {PALO} - 4 pontos (0-3)
            'PALO' => [
                ["text" => "0 - Absolutamente não.", "score" => 0],
                ["text" => "1 - Levemente (não me incomodou muito).", "score" => 1],
                ["text" => "2 - Moderadamente (foi muito desagradável).", "score" => 2],
                ["text" => "3 - Gravemente (dificilmente pude suportar).", "score" => 3],
            ],

            // Beck Anxiety Inventory (BAI) - 4 pontos (0-3)
            'BAI' => [
                ["text" => "0 - Absolutamente não.", "score" => 0],
                ["text" => "1 - Levemente (não me incomodou muito).", "score" => 1],
                ["text" => "2 - Moderadamente (foi muito desagradável).", "score" => 2],
                ["text" => "3 - Gravemente (dificilmente pude suportar).", "score" => 3],
            ],

            // Inventário de Fatores de Personalidade (IFP) - Binário (0-1)
            'IFP' => [
                ["text" => "Não me identifico/Falso", "score" => 0],
                ["text" => "Me identifico/Verdadeiro", "score" => 1],
            ],

            // NEO-PI-R - LIKERT de 5 pontos (1-5)
            'NEO-PI-R' => [
                ['text' => 'Discordo Totalmente', 'score' => 1],
                ['text' => 'Discordo Parcialmente', 'score' => 2],
                ['text' => 'Neutro', 'score' => 3],
                ['text' => 'Concordo Parcialmente', 'score' => 4],
                ['text' => 'Concordo Totalmente', 'score' => 5],
            ],

            // RSES - LIKERT de 4 pontos (1-4)
            'RSES' => [
                ['text' => 'Discordo Fortemente', 'score' => 1],
                ['text' => 'Discordo', 'score' => 2],
                ['text' => 'Concordo', 'score' => 3],
                ['text' => 'Concordo Fortemente', 'score' => 4],
            ],

            // PCL-5 - LIKERT de 5 pontos de Frequência (0-4)
            'PCL-5' => [
                ['text' => 'Não me incomodou', 'score' => 0],
                ['text' => 'Um pouco', 'score' => 1],
                ['text' => 'Moderadamente', 'score' => 2],
                ['text' => 'Bastante', 'score' => 3],
                ['text' => 'Extremamente', 'score' => 4],
            ],

            // BDI-II (Exemplo de item único, 4 pontos 0-3)
            'BDI-II' => [
                ["text" => "Não me sinto triste.", "score" => 0],
                ["text" => "Sinto-me triste parte do tempo.", "score" => 1],
                ["text" => "Sinto-me triste o tempo todo.", "score" => 2],
                ["text" => "Sinto-me tão triste ou infeliz que não consigo suportar.", "score" => 3],
            ],

            // BDI-II (Exemplo de item único, 4 pontos 0-3)
            'BDI-II' => [
                ["text" => "Não me sinto triste.", "score" => 0],
                ["text" => "Sinto-me triste parte do tempo.", "score" => 1],
                ["text" => "Sinto-me triste o tempo todo.", "score" => 2],
                ["text" => "Sinto-me tão triste ou infeliz que não consigo suportar.", "score" => 3],
            ],
        ];


        // -------------------------------------------------------------
        // TIPOS DE RESPOSTAS
        // -------------------------------------------------------------

        $tipos_respostas = [
            'IFP' => 'BINARY_AGREEMENT', 
            'BDI-II' => 'CATEGORICAL_CHOICE', 
            'NEO-PI-R' => 'LIKERT_5_POINT', 
            'PALO' => 'LIKERT_4_POINT', 
            'RSES' => 'LIKERT_4_POINT',
            'PCL-5' => 'PCL_5_FREQUENCIA',
            'WAIS-IV' => 'MULTIPLE_CHOICE_SINGLE',
        ];

        // -------------------------------------------------------------
        // DADOS FINAIS
        // -------------------------------------------------------------

        $questions = [];
        $todos_questionarios = [];
        $tmp_questions = [];

        // =========================================================
        //  BDI-II (Código: BDI-II)
        // =========================================================
        $tmp_questions = [
            ['id' => '1', 'text' => 'Tristeza:', 'dimension' => ['SOMATICO-AFETIVO']],
            ['id' => '2', 'text' => 'Pessimismo:', 'dimension' => ['COGNITIVO']],
            ['id' => '3', 'text' => 'Satisfação (Pergunta de pontuação inversa):', 'dimension' => ['COGNITIVO']],
            ['id' => '4', 'text' => 'Tristeza:', 'dimension' => ['SOMATICO-AFETIVO']],
        ];
        $todos_questionarios['BDI-II'] = $tmp_questions;

        // =========================================================
        //  PALO (Código: PALO)
        // =========================================================
        $tmp_questions = [
            ['id' => '1', 'text' => 'Dormência ou formigamento', 'dimension' => ['SOMATICO']],
            ['id' => '2', 'text' => 'Sensação de calor', 'dimension' => ['SOMATICO', 'AFETIVO']], 
        ];
        $todos_questionarios['PALO'] = $tmp_questions;

        
        // =============================================================
        // NOVO: 100 QUESTÕES DO IFP (Código: IFP)
        // =============================================================
        // As dimensões (Necessidades) foram inferidas com base no conteúdo da questão e na estrutura do IFP.
        $tmp_questions = [
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
        $todos_questionarios['IFP'] = $tmp_questions;


        // =============================================================
        // QUESTÕES NEO-PI-R (Código: NEO-PI-R)
        // =============================================================

        $tmp_questions = [
            ['id' => 'N01', 'text' => 'Fico facilmente nervoso e chateado(a) quando as coisas não saem como planejado.', 'dimension' => ['N_ANSIEDADE']],
            ['id' => 'E01', 'text' => 'Eu gosto de ser o centro das atenções em reuniões sociais.', 'dimension' => ['E_CALOR']],
            ['id' => 'N02', 'text' => 'Eu me preocupo muito com o que os outros pensam de mim.', 'dimension' => ['N_VERGONHA']],
        ];
        $todos_questionarios['NEO-PI-R'] = $tmp_questions;

        // =============================================================
        //  QUESTÕES RSES (Código: RSES)
        // =============================================================

        $tmp_questions = [
            ['id' => 'RS01', 'text' => 'Sinto que sou uma pessoa de valor, pelo menos num plano igual ao dos outros.', 'dimension' => ['AE']],
            ['id' => 'RS02', 'text' => 'Sinto que não tenho muitas razões para me orgulhar.', 'dimension' => ['AE']],
            ['id' => 'RS03', 'text' => 'Eu me sinto inútil às vezes.', 'dimension' => ['AE']],
        ];
         $todos_questionarios['RSES'] = $tmp_questions;




        // =============================================================
        //  QUESTÕES PCL-5 (Código: PCL-5)
        // =============================================================

        $tmp_questions = [
            ['id' => 'PCL01', 'text' => 'Problemas para lembrar de partes importantes do evento estressor?', 'dimension' => ['B_REEXPERIENCIA']],
            ['id' => 'PCL02', 'text' => 'Sentimentos de culpa ou ser culpado(a) por causa do evento estressor?', 'dimension' => ['C_EVITAMENTO']],
            ['id' => 'PCL03', 'text' => 'Ter sonhos ruins sobre o evento estressor?', 'dimension' => ['B_REEXPERIENCIA']],
        ];
        $todos_questionarios['PCL-5'] = $tmp_questions;



        // =============================================================
        // QUESTÕES WAIS-IV (Código: WAIS-IV)
        // =============================================================

        // As opções são específicas por questão
        $tmp_questions = [
            [
                'id' => 'W01',
                'text' => 'Qual é o nome do pintor famoso pela obra "A Noite Estrelada"?',
                'dimension' => ['VC_CONHECIMENTO'],
                'options_json' => [
                    ['score' => 0, 'text' => 'Claude Monet'],
                    ['score' => 1, 'text' => 'Vincent van Gogh'],
                    ['score' => 0, 'text' => 'Pablo Picasso'],
                    ['score' => 0, 'text' => 'Salvador Dalí'],
                ],
            ],
            [
                'id' => 'W02',
                'text' => 'Qual dos seguintes é um metal alcalino-terroso?',
                'dimension' => ['VC_CONHECIMENTO'],
                'options_json' => [
                    ['score' => 0, 'text' => 'Sódio (Na)'],
                    ['score' => 1, 'text' => 'Magnésio (Mg)'],
                    ['score' => 0, 'text' => 'Ouro (Au)'],
                    ['score' => 0, 'text' => 'Cloro (Cl)'],
                ],
            ],
        ];
        $todos_questionarios['WAIS-IV'] = $tmp_questions;


        // =============================================================
        //  Loop para popular o array questions
        // =============================================================

        foreach ($todos_questionarios as $questionnaire_code => $question_list) {

            // $options
            $options = [];


            // Itera sobre as perguntas do questionário atual
            foreach ($question_list as $q) {

                If ($questionnaire_code == 'WAIS-IV' ){
                    if ( $q['id'] == 'W01'){
                        $options = [    
                            ['score' => 0, 'text' => 'Claude Monet'],
                            ['score' => 1, 'text' => 'Vincent van Gogh'],
                            ['score' => 0, 'text' => 'Pablo Picasso'],
                            ['score' => 0, 'text' => 'Salvador Dalí'],
                        ];
                    } elseif ($q['id'] == 'W02'){
                        $options = [
                            ['score' => 0, 'text' => 'Sódio (Na)'],
                            ['score' => 1, 'text' => 'Magnésio (Mg)'],
                            ['score' => 0, 'text' => 'Ouro (Au)'],
                            ['score' => 0, 'text' => 'Cloro (Cl)'],
                        ];
                    } }
                 else {
                        $options = $opcoes_respostas[$questionnaire_code];
                }
                $questions[] = [
                'questionnaire_code' => $questionnaire_code, 
                'question_identifier' => $q['id'],
                'question_text' => $q['text'],
                'response_type' => $tipos_respostas[$questionnaire_code],
                'dimensions_json' => $q['dimension'],
                'options_json' => $options,
            ];
            }
        }
        return $questions;
    }
}   