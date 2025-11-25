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
        // Variável para rastrear o progresso do log
        $logSummary = []; 
        $currentQuestionnaireCode = null;
        $displayOrder = 1;

        $allQuestions = $this->getStaticQuestionData();

        // 1. Itera sobre todas as perguntas
        foreach ($allQuestions as $data) {
            
            // --- Lógica de Logging (Início) ---
            if ($data['questionnaire_code'] !== $currentQuestionnaireCode) {
                // Novo questionário encontrado, resetar displayOrder para cada questionário
                $displayOrder = 1; 
                $currentQuestionnaireCode = $data['questionnaire_code'];
                
                // Inicializa o contador para o novo questionário
                $logSummary[$currentQuestionnaireCode] = 0; 

                echo "\n➡️ Processando Questionário: **{$currentQuestionnaireCode}**\n";
            }
            // --- Lógica de Logging (Fim) ---

            // 2. Busca ou Cacheia o ID do Questionário
            $questionnaireId = $this->getQuestionnaireIdByCode($data['questionnaire_code']);
            
            if (!$questionnaireId) {
                // Se o Questionário pai não existir, pule esta pergunta (melhoria de segurança)
                echo "    ⚠️ ATENÇÃO: Questionário '{$data['questionnaire_code']}' não encontrado. Pulo.\n";
                continue;
            }

            // 3. Prepara os dados para o Question Model
            $questionData = [
                'questionnaire_id' => $questionnaireId,
                'question_identifier' => $data['question_identifier'],
                // display_order é sequencial dentro do questionário
                'display_order' => $displayOrder++,
                'question_text' => $data['question_text'],
                'response_type' => $data['response_type'],
            ];

            // 4. Usa updateOrCreate para garantir a unicidade e atualização
            Question::updateOrCreate(
                [
                    'questionnaire_id' => $questionData['questionnaire_id'],
                    'question_identifier' => $questionData['question_identifier'],
                ],
                $questionData
            );

            // 5. Incrementa o contador do log
            $logSummary[$currentQuestionnaireCode]++;
        }
        
        // --- 6. Log final (Resumo) ---
        echo "\n\n✅ **Resumo de Questões Inseridas/Atualizadas**:\n";
        echo "----------------------------------------------------\n";
        foreach ($logSummary as $code => $count) {
            echo "   - [{$code}]: {$count} questões processadas.\n";
        }
        echo "----------------------------------------------------\n";
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
            'IFP-II' => [
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
            
             // DFH-IV (Presente(marcar) 1, Ausente (se não checar, a pontuação já é 0,)
            'DFH-IV' => [
                ["text" => "Presente", "score" => 1],
            ],

        ];


        // -------------------------------------------------------------
        // TIPOS DE RESPOSTAS
        // -------------------------------------------------------------

        $tipos_respostas = [
            'IFP-II' => 'BINARY_AGREEMENT', 
            'BDI-II' => 'CATEGORICAL_CHOICE', 
            'NEO-PI-R' => 'LIKERT_5_POINT', 
            'PALO' => 'LIKERT_4_POINT', 
            'RSES' => 'LIKERT_4_POINT',
            'PCL-5' => 'PCL_5_FREQUENCIA',
            'DFH-IV' => 'YES_NO',
            'ETDAH-II' => 'LIKERT_5_POINT', 
        ];

        // -------------------------------------------------------------
        // DADOS FINAIS
        // -------------------------------------------------------------

       $questions = [];
    $todos_questionarios = [];
    $tmp_questions = [];

    // =========================================================
    //  BDI-II (Código: BDI-II) - Identificadores Alterados para Letras (A, B, C...)
    // =========================================================
    $id_questionnaire = "BDI-II";
    $scaleCode = $tipos_respostas[$id_questionnaire];
    $tmp_questions = [
        // Usando identificadores A, B, C, D... para o BDI-II
        ['question_identifier' => 'A', 'text' => 'Tristeza:', 'scale_code' => $scaleCode,],
        ['question_identifier' => 'B', 'text' => 'Pessimismo:', 'scale_code' => $scaleCode,],
        ['question_identifier' => 'C', 'text' => 'Satisfação (Pergunta de pontuação inversa):', 'scale_code' => $scaleCode,],
        ['question_identifier' => 'D', 'text' => 'Tristeza:', 'scale_code' => $scaleCode,],
        // ... (Itens restantes do BDI-II, se houver) ...
    ];
    $todos_questionarios[$id_questionnaire] = $tmp_questions;

    // =========================================================
    //  PALO (Código: PALO) - Identificadores Alterados para Letras (A, B...)
    // =========================================================
    $id_questionnaire = 'PALO';
    $scaleCode = $tipos_respostas[$id_questionnaire];
    $tmp_questions = [
        // Usando identificadores A, B, C, D... para o PALO
        ['question_identifier' => 'A', 'text' => 'Dormência ou formigamento', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'B', 'text' => 'Sensação de calor', 'scale_code' => $scaleCode,], 
        // ... (Itens restantes do PALO, se houver) ...
    ];
    $todos_questionarios[$id_questionnaire] = $tmp_questions;

    // =============================================================
    // NOVO: 100 QUESTÕES DO IFP-II (Código: IFP-II) - MANTÉM Numeração (01, 02...)
    // =============================================================
    $id_questionnaire = 'IFP-II';
    $scaleCode = $tipos_respostas[$id_questionnaire];
    $tmp_questions = [
        // Mantido '01', '02', etc. e renomeado a chave 'id'
        ['question_identifier' => '01', 'text' => 'Gosto de fazer coisas que outras pessoas consideram fora do comum', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '02', 'text' => 'Gostaria de realizar um grande feito ou grande obra na minha vida', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '03', 'text' => 'Gosto de experimentar novidades e mudanças em meu dia-a-dia', 'scale_code' => $scaleCode,], 
        // ... (Itens 04 a 100 do IFP-II) ...
        ['question_identifier' => '04', 'text' => 'Não gosto de situações em que se exige que eu me comporte de determinada maneira', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '05', 'text' => 'Gosto de dizer o que eu penso a respeito das coisas', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '06', 'text' => 'Gosto de saber o que grandes personalidades disseram sobre os problemas pelos quais eu me interesso', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '07', 'text' => 'Gosto de ser capaz de fazer as coisas melhor do que as outras pessoas', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '08', 'text' => 'Gosto de concluir qualquer trabalho ou tarefa que tenha começado', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '09', 'text' => 'Gosto de ajudar meus amigos quando eles estão com problemas', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '10', 'text' => 'Não costumo abandonar um quebra-cabeça ou problema antes que consiga resolvê-lo', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '11', 'text' => 'Gosto de dizer aos outros como fazer seus trabalhos', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '12', 'text' => 'Gostaria de ser considerado(a) uma autoridade em algum trabalho, profissão ou campo de especialização', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '13', 'text' => 'Gosto de experimentar e provar coisas novas', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '14', 'text' => 'Quando tenho alguma tarefa para fazer, gosto de começar logo e permanecer trabalhando até completá-la', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '15', 'text' => 'Aceito com prazer a liderança das pessoas que admiro', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '16', 'text' => 'Gosto de trabalhar horas a fio sem ser interrompido(a)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '17', 'text' => 'Gosto que meus amigos me dêem muita atenção quando estou sofrendo ou doente', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '18', 'text' => 'Costumo analisar minhas intenções e sentimentos', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '19', 'text' => 'Gosto de fazer com carinho pequenas favores a meus amigos', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '20', 'text' => 'Gosto de ficar acordado(a) até tarde para terminar um trabalho', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '21', 'text' => 'Gosto de andar pelo país e viver em lugares diferentes', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '22', 'text' => 'Gosto de analisar os sentimentos e intenções dos outros', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '23', 'text' => 'Gosto de fazer gozação com pessoas que fazem coisas que eu considero estúpidas', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '24', 'text' => 'Tenho vontade de me vingar quando alguém me insulta', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '25', 'text' => 'Gosto de pensar sobre o caráter dos meus amigos e tentar descobrir o que os faz serem como são', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '26', 'text' => 'Sou leal aos meus amigos', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '27', 'text' => 'Gosto de levar um trabalho ou tarefa até o fim antes de começar outro', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '28', 'text' => 'Gosto de dizer aos meus superiores que eles fizeram um bom trabalho, quando acredito nisso', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '29', 'text' => 'Gosto que meus amigos sejam solidários comigo e me animem quando estou deprimido(a)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '30', 'text' => 'Antes de começar um trabalho, gosto de organizá-lo e planejá-lo', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '31', 'text' => 'Gosto que meus amigos demostrem muito afeto por mim', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '32', 'text' => 'Gosto de realizar tarefas que, na opinião dos outros, exigem habilidade e esforço', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '33', 'text' => 'Gosto de ser bem-sucedido nas coisas que faço', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '34', 'text' => 'Gosto de fazer amizades', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '35', 'text' => 'Gosto de ser considerado(a) um(a) líder pelos outros', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '36', 'text' => 'Gosto de realizar com afinco (sem descanso) qualquer trabalho que faço', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '37', 'text' => 'Gosto de participar de grupos cujos membros se tratem com afeto e amizade', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '38', 'text' => 'Sinto-me satisfeito(a) quando realizo bem um trabalho difícil', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '39', 'text' => 'Tenho vontade de mandar os outros calarem a boca quando discordo deles', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '40', 'text' => 'Gosto de fazer coisas do meu jeito sem me importar com o que os outros possam pensar', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '41', 'text' => 'Gosto de viajar e conhecer o país', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '42', 'text' => 'Gosto de me fixar em um trabalho ou problema mesmo quando a solução pareça extremamente difícil', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '43', 'text' => 'Gosto de conhecer novas pessoas', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '44', 'text' => 'Gosto de dividir coisas com os outros', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '45', 'text' => 'Sinto-me satisfeito(a) quando consigo convencer e influenciar os outros', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '46', 'text' => 'Gosto de demonstrar muita afeição por meus amigos', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '47', 'text' => 'Gosto de prestar favores aos outros', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '48', 'text' => 'Gosto de seguir instruções e fazer o que é esperado de mim', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '49', 'text' => 'Gosto de elogiar alguém que admiro', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '50', 'text' => 'Quando planejo alguma coisa, procuro sugestões de pessoas que respeito', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '51', 'text' => 'Gosto de manter minhas coisas limpas e ordenadas em minha escrivaninha ou em meu local de trabalho', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '52', 'text' => 'Gosto de manter fortes laços de amizade', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '53', 'text' => 'Gosto que meus amigos me ajudem quando estou com problema', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '54', 'text' => 'Gosto que meus amigos mostrem boa vontade em me prestar pequenos favores', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '55', 'text' => 'Gosto de manter minhas cartas, contas e outros papéis bem arrumados e arquivados de acordo com algum sistema', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '56', 'text' => 'Gosto que meus amigos sejam solidários e compreensivos quando tenho problemas', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '57', 'text' => 'Prefiro fazer coisas com meus amigos a fazer sozinho', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '58', 'text' => 'Gosto de tratar outras pessoas com bondade e compaixão', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '59', 'text' => 'Gosto de comer em restaurantes novos e exóticos(diferentes)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '60', 'text' => 'Procuro entender como meus amigos se sentem a respeito de problemas que eles enfrentam', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '61', 'text' => 'Gosto de ser o centro das atenções em um grupo', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '62', 'text' => 'Gosto de ser um dos líderes nas organizações e grupos aos quais pertenço', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '63', 'text' => 'Gosto de ser independente dos outros para decidir o que quero fazer', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '64', 'text' => 'Gosto de me manter em contato com meus amigos', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '65', 'text' => 'Quando participo de uma comissão (reunião), gosto de ser indicado ou eleito presidente', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '66', 'text' => 'Gosto de fazer tantos amigos quanto possível', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '67', 'text' => 'Gosto de observar como uma outra pessoa se sente numa determinada situação', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '68', 'text' => 'Quando estou em um grupo, aceito com prazer a liderança de outra pessoa para decidir o que o grupo fará', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '69', 'text' => 'Não gosto de me sentir pressionado(a) por responsabilidades e deveres', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '70', 'text' => 'Às vezes, fico tão irritado(a) que sinto vontade de jogar e quebrar coisas', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '71', 'text' => 'Gosto de fazer perguntas que ninguém será capaz de responder', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '72', 'text' => 'Às vezes, gosto de fazer coisas simplesmente para ver o efeito que terão sobre os outros', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '73', 'text' => 'Sou solidário com meus amigos quando machucados ou doentes', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '74', 'text' => 'Não tenho medo de criticar pessoas que ocupam posições de autoridade', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '75', 'text' => 'Gosto de fiscalizar e dirigir os atos dos outros sempre que posso', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '76', 'text' => 'Culpo os outros quando as coisas dão errado comigo', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '77', 'text' => 'Gosto de ajudar pessoas que têm menos sorte do que eu', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '78', 'text' => 'Gosto de planejar e organizar, em todos os detalhes, qualquer trabalho que eu faço', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '79', 'text' => 'Gosto de fazer coisas novas e diferentes', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '80', 'text' => 'Gostaria de realizar com sucesso alguma coisa de grande importância', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '81', 'text' => 'Quando estou com um grupo de pessoas, gosto de decidir sobre o que vamos fazer', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '82', 'text' => 'Interesso-me em conhecer a vida de grandes personalidades', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '83', 'text' => 'Procuro me adaptar ao modo de ser das pessoas que admiro', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '84', 'text' => 'Gosto de resolver quebra-cabeças e problemas com os quais pessoas têm dificuldades', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '85', 'text' => 'Gosto de falar sobre os meus sucessos', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '86', 'text' => 'Gosto de dar o melhor de mim em tudo que faço', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '87', 'text' => 'Gosto de estudar e analisar o comportamento dos outros', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '88', 'text' => 'Gosto de contar aos outros aventuras e coisas estranhas que acontecem comigo', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '89', 'text' => 'Perdôo as pessoas que às vezes possam me magoar', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '90', 'text' => 'Gosto de prever (entender) como meus amigos irão agir em diferentes situações', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '91', 'text' => 'Gosto de me sentir livre para fazer o que quero', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '92', 'text' => 'Gosto de me sentir livre para ir e vir quando quiser', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '93', 'text' => 'Gosto de usar palavras cujo significado as outras pessoas desconhecem', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '94', 'text' => 'Gosto de planejar antes de iniciar algo difícil', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '95', 'text' => 'Qualquer trabalho escrito que faço, gosto que seja preciso, limpo e bem-organizado', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '96', 'text' => 'Gosto que as pessoas notem e comentem a minha aparência quando estou em público', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '97', 'text' => 'Gosto que meus amigos me tratem com delicadeza', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '98', 'text' => 'Gosto de ser generoso(a) com os outros', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '99', 'text' => 'Gosto de contar estórias e piadas engraçadas em festas', 'scale_code' => $scaleCode,], 
        ['question_identifier' => '100', 'text' => 'Gosto de dizer coisas que os outros consideram engraçadas e inteligentes', 'scale_code' => $scaleCode,], 
    ];
    $todos_questionarios['IFP-II'] = $tmp_questions;

    // =======================================================================
    // DFH-IV (Código: DFH-IV) - MANTÉM Identificadores Alfanuméricos
    // =======================================================================
    $id_questionnaire = 'DFH-IV';
    $scaleCode = $tipos_respostas[$id_questionnaire];
    $tmp_questions = [
        // FIGURA MASCULINA (FM) - MANTÉM DFH-FM-xxx
        ['question_identifier' => 'DFH-FM-101', 'text' => 'Cabeça presente (e fechada)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FM-102', 'text' => 'Pescoço presente (ligando cabeça e tronco)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FM-103', 'text' => 'Tronco presente (diferente da cabeça)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FM-104', 'text' => 'Tronco desenhado em duas dimensões (2D)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FM-105', 'text' => 'Proporção da cabeça para o tronco é razoável', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FM-106', 'text' => 'Braços presentes e unidos ao tronco', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FM-107', 'text' => 'Braços articulados (ombros, cotovelos)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FM-108', 'text' => 'Pernas presentes e unidas ao tronco', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FM-109', 'text' => 'Pés ou calçados presentes e diferenciados', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FM-110', 'text' => 'Cinco dedos (ou indicação clara de dedos)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FM-111', 'text' => 'Olhos presentes e com pupila/íris', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FM-112', 'text' => 'Nariz presente (2D ou com narinas)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FM-113', 'text' => 'Boca presente (com lábios ou contorno)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FM-114', 'text' => 'Orelhas presentes', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FM-115', 'text' => 'Cabelo presente (não rascunho ou rabisco)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FM-116', 'text' => 'Vestuário (pelo menos duas peças)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FM-117', 'text' => 'Detalhes de vestuário específicos (ex: gola, cinto)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FM-118', 'text' => 'Transparência ausente (corpo não visível através da roupa)', 'scale_code' => $scaleCode,], 
        
        // FIGURA FEMININA (FF) - MANTÉM DFH-FF-xxx
        ['question_identifier' => 'DFH-FF-201', 'text' => 'Cabeça presente (e fechada)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FF-202', 'text' => 'Pescoço presente (ligando cabeça e tronco)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FF-203', 'text' => 'Tronco presente (diferente da cabeça)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FF-204', 'text' => 'Tronco desenhado em duas dimensões (2D)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FF-205', 'text' => 'Proporção da cabeça para o tronco é razoável', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FF-206', 'text' => 'Braços presentes e unidos ao tronco', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FF-207', 'text' => 'Braços articulados (ombros, cotovelos)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FF-208', 'text' => 'Pernas presentes e unidas ao tronco', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FF-209', 'text' => 'Pés ou calçados presentes e diferenciados', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FF-210', 'text' => 'Cinco dedos visíveis na mão', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FF-211', 'text' => 'Olhos presentes e com pupila/íris', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FF-212', 'text' => 'Nariz presente (2D ou com narinas)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FF-213', 'text' => 'Boca presente (com lábios ou contorno)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FF-214', 'text' => 'Orelhas presentes', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FF-215', 'text' => 'Cabelo presente (com detalhes femininos, ex: longo, penteado)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FF-216', 'text' => 'Vestuário feminino (ex: vestido, saia, blusa)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FF-217', 'text' => 'Detalhes de vestuário ou adereços (ex: brincos, colar, laços)', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FF-218', 'text' => 'Proporção geral correta e simetria', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'DFH-FF-219', 'text' => 'Ausência de monitorização (tentativa de apagar e corrigir)', 'scale_code' => $scaleCode,], 
    ];
    $todos_questionarios['DFH-IV'] = $tmp_questions;

    // =============================================================
    // QUESTÕES NEO-PI-R (Código: NEO-PI-R) - MANTÉM Identificadores Alfanuméricos (Nxx, Exx...)
    // =============================================================
    $id_questionnaire = 'NEO-PI-R';
    $scaleCode = $tipos_respostas[$id_questionnaire];
    $tmp_questions = [
        ['question_identifier' => 'N01', 'text' => 'Fico facilmente nervoso e chateado(a) quando as coisas não saem como planejado.', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'E01', 'text' => 'Eu gosto de ser o centro das atenções em reuniões sociais.', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'N02', 'text' => 'Eu me preocupo muito com o que os outros pensam de mim.', 'scale_code' => $scaleCode,], 
    ];
    $todos_questionarios[$id_questionnaire] = $tmp_questions;

    // =============================================================
    //  QUESTÕES RSES (Código: RSES) - MANTÉM Identificadores Alfanuméricos (RSxx)
    // =============================================================
    $id_questionnaire = 'RSES';
    $scaleCode = $tipos_respostas[$id_questionnaire];
    $tmp_questions = [
        ['question_identifier' => 'RS01', 'text' => 'Sinto que sou uma pessoa de valor, pelo menos num plano igual ao dos outros.', 'scale_code' => $scaleCode,],
        ['question_identifier' => 'RS02', 'text' => 'Sinto que não tenho muitas razões para me orgulhar.', 'scale_code' => $scaleCode,],
        ['question_identifier' => 'RS03', 'text' => 'Eu me sinto inútil às vezes.', 'scale_code' => $scaleCode,],
    ];
    $todos_questionarios['RSES'] = $tmp_questions;

    // =============================================================
    //  QUESTÕES PCL-5 (Código: PCL-5) - MANTÉM Identificadores Alfanuméricos (PCLxx)
    // =============================================================
    $id_questionnaire = 'PCL-5';
    $scaleCode = $tipos_respostas[$id_questionnaire];
    $tmp_questions = [
        ['question_identifier' => 'PCL01', 'text' => 'Problemas para lembrar de partes importantes do evento estressor?', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'PCL02', 'text' => 'Sentimentos de culpa ou ser culpado(a) por causa do evento estressor?', 'scale_code' => $scaleCode,], 
        ['question_identifier' => 'PCL03', 'text' => 'Ter sonhos ruins sobre o evento estressor?', 'scale_code' => $scaleCode,], 
    ];
    $todos_questionarios['PCL-5'] = $tmp_questions;
    
    // =========================================================
    //  ETDAH-II (Código: ETDAH-II) - MANTÉM Numeração (01, 02...)
    // =========================================================
    $id_questionnaire = 'ETDAH-II';
    $scaleCode = $tipos_respostas[$id_questionnaire];
    $tmp_questions = [
        // Fator 1: IMPULSIVIDADE / DESCONTROLE
        ['question_identifier' => '01', 'text' => 'Fiz asneiras, mas não consegui mantê-las?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '02', 'text' => 'Impaciente com tudo?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '03', 'text' => 'Tenho reações emocionais (explosões de raiva)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '04', 'text' => 'Sou agitado (muita inércia)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '05', 'text' => 'Muda facilmente de humor?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '06', 'text' => 'Explodo com facilidade (é do tipo "pavlo curto")?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '07', 'text' => 'Dá a impressão de estar sempre insatisfeito (nada o agrada)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '08', 'text' => 'É o tipo "dá o ponto final"?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '09', 'text' => 'É agressivo?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '10', 'text' => 'Sente-se infeliz?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '11', 'text' => 'Fica birra quando quer algo?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '12', 'text' => 'Mostra-se teimoso e rígido?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '13', 'text' => 'Impõe a sua vontade?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '14', 'text' => 'As atividades e reuniões são desagradáveis?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '15', 'text' => 'Todos têm que fazer o que ele quer?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '16', 'text' => 'A hora de acordar e das refeições é desagradável?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '17', 'text' => 'Exige mais tempo e atenção dos pais do que as outras filhos?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '18', 'text' => 'Tem dificuldade para se adaptar às mudanças?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '19', 'text' => 'É sensível?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '20', 'text' => 'Impede a convivência social?', 'scale_code' => $scaleCode,],
        
        // Fator 2: HIPERATIVIDADE / IMPULSIVIDADE (HI)
        ['question_identifier' => '21', 'text' => 'É extremamente ativo (parece estar ligado com um motor ou a todo vapor)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '22', 'text' => 'É inquieto, agitado?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '23', 'text' => 'Mexe-se e contorce-se durante as refeições e sem realizar as tarefas de casa?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '24', 'text' => 'Tem sempre muita pressa?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '25', 'text' => 'Age sem pensar (é impulsivo)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '26', 'text' => 'É muito destemido (não considera os perigos da situação)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '27', 'text' => 'Intromete-se em assuntos que não lhe dizem respeito?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '28', 'text' => 'Responde antes de ouvir a pergunta inteira?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '29', 'text' => 'É imprudente?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '30', 'text' => 'Cita os outros com seus patrimônios?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '31', 'text' => 'Tende a brincar contra as regras e normas do jogos?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '32', 'text' => 'É persistente e insiste diante de uma ideia?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '33', 'text' => 'Faz os deveres escolares rápido demais?', 'scale_code' => $scaleCode,],

        // Fator 3: COMPORTAMENTO ADAPTATIVO (CA) - (Continuação da numeração)
        ['question_identifier' => '34', 'text' => 'Faz asseio sozinho, não necessita de ajuda?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '35', 'text' => 'Parece ser uma criança tranquila e sossegada?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '36', 'text' => 'É tolerante, quando precisa?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '37', 'text' => 'Respeita normas e regras?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '38', 'text' => 'É obediente?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '39', 'text' => 'Obedece aos pais e as normas da casa?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '40', 'text' => 'Sabe aguardar sua vez (é paciente)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '41', 'text' => 'Faz suas tarefas e almoça com bastante tranquilidade?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '42', 'text' => 'Faz coisas com muito cuidado, prevendo todos os riscos de suas ações?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '43', 'text' => 'Seu comportamento é adequado socialmente?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '44', 'text' => 'Não se cansa?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '45', 'text' => 'Atinge e permite que o ambiente (familiar seja tranquilo e harmonioso)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '46', 'text' => 'Consegue expressar claramente os seus pensamentos?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '47', 'text' => 'Tem atenção quando conversa com alguém?', 'scale_code' => $scaleCode,],

        // Fator 4: DESATENÇÃO (DA)
        ['question_identifier' => '48', 'text' => 'É negligente para realizar as suas tarefas de casa?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '49', 'text' => 'É distraído com quase tudo?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '50', 'text' => 'Evita atividades que exigem esforço mental constante (deveres escolares, jogos)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '51', 'text' => 'Esquece rápido o que acabou de ser dito?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '52', 'text' => 'Não usa o que aprendeu em casa, mesmo se obtiveram ajuda até o final (é do tipo fogo de palha)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '53', 'text' => 'Tem dificuldade para realizar as coisas importantes (fácil, por exemplo)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '54', 'text' => 'Não termina o que começa?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '55', 'text' => 'Parece sonhar acordado ( "está no mundo da lua")?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '56', 'text' => 'Dificilmente consegue se organizar em atividades do seu prazer?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '57', 'text' => 'Dá a impressão de que não ouve (se esquece o que ouve)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '58', 'text' => 'Muitas dificuldades de observação?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '59', 'text' => 'Ocurrem discusuões entre os pais e a criança, em função de falta de responsabilidade e de organização?', 'scale_code' => $scaleCode,],
    ];
    $todos_questionarios['ETDAH-II'] = $tmp_questions;


        // =========================================================
    //  ETDAH-II (Código: ETDAH-II) - Identificadores Simples, REPETIDOS por Fator
    // =========================================================
    $id_questionnaire = 'ETDAH-II';
    $scaleCode = $tipos_respostas[$id_questionnaire]; // LIKERT_5_POINT

    $tmp_questions = [
        // Fator 1: IMPULSIVIDADE / DESCONTROLE (Itens 1 a 20)
        ['question_identifier' => '01', 'text' => 'Fiz asneiras, mas não consegui mantê-las?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '02', 'text' => 'Impaciente com tudo?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '03', 'text' => 'Tenho reações emocionais (explosões de raiva)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '04', 'text' => 'Sou agitado (muita inércia)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '05', 'text' => 'Muda facilmente de humor?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '06', 'text' => 'Explodo com facilidade (é do tipo "pavlo curto")?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '07', 'text' => 'Dá a impressão de estar sempre insatisfeito (nada o agrada)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '08', 'text' => 'É o tipo "dá o ponto final"?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '09', 'text' => 'É agressivo?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '10', 'text' => 'Sente-se infeliz?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '11', 'text' => 'Fica birra quando quer algo?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '12', 'text' => 'Mostra-se teimoso e rígido?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '13', 'text' => 'Impõe a sua vontade?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '14', 'text' => 'As atividades e reuniões são desagradáveis?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '15', 'text' => 'Todos têm que fazer o que ele quer?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '16', 'text' => 'A hora de acordar e das refeições é desagradável?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '17', 'text' => 'Exige mais tempo e atenção dos pais do que as outras filhos?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '18', 'text' => 'Tem dificuldade para se adaptar às mudanças?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '19', 'text' => 'É sensível?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '20', 'text' => 'Impede a convivência social?', 'scale_code' => $scaleCode,],
        
        // Fator 2: HIPERATIVIDADE / IMPULSIVIDADE (HI) (Itens 1 a 13)
        ['question_identifier' => '01', 'text' => 'É extremamente ativo (parece estar ligado com um motor ou a todo vapor)?', 'scale_code' => $scaleCode,], // REPETIDO: '01'
        ['question_identifier' => '02', 'text' => 'É inquieto, agitado?', 'scale_code' => $scaleCode,], // REPETIDO: '02'
        ['question_identifier' => '03', 'text' => 'Mexe-se e contorce-se durante as refeições e sem realizar as tarefas de casa?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '04', 'text' => 'Tem sempre muita pressa?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '05', 'text' => 'Age sem pensar (é impulsivo)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '06', 'text' => 'É muito destemido (não considera os perigos da situação)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '07', 'text' => 'Intromete-se em assuntos que não lhe dizem respeito?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '08', 'text' => 'Responde antes de ouvir a pergunta inteira?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '09', 'text' => 'É imprudente?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '10', 'text' => 'Cita os outros com seus patrimônios?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '11', 'text' => 'Tende a brincar contra as regras e normas do jogos?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '12', 'text' => 'É persistente e insiste diante de uma ideia?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '13', 'text' => 'Faz os deveres escolares rápido demais?', 'scale_code' => $scaleCode,],

        // Fator 3: COMPORTAMENTO ADAPTATIVO (CA) (Itens 1 a 12)
        ['question_identifier' => '01', 'text' => 'Faz asseio sozinho, não necessita de ajuda?', 'scale_code' => $scaleCode,], // REPETIDO: '01'
        ['question_identifier' => '02', 'text' => 'Parece ser uma criança tranquila e sossegada?', 'scale_code' => $scaleCode,], // REPETIDO: '02'
        ['question_identifier' => '03', 'text' => 'É tolerante, quando precisa?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '04', 'text' => 'Respeita normas e regras?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '05', 'text' => 'É obediente?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '06', 'text' => 'Obedece aos pais e as normas da casa?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '07', 'text' => 'Sabe aguardar sua vez (é paciente)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '08', 'text' => 'Faz suas tarefas e almoça com bastante tranquilidade?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '09', 'text' => 'Faz coisas com muito cuidado, prevendo todos os riscos de suas ações?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '10', 'text' => 'Seu comportamento é adequado socialmente?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '11', 'text' => 'Não se cansa?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '12', 'text' => 'Atinge e permite que o ambiente (familiar seja tranquilo e harmonioso)?', 'scale_code' => $scaleCode,],

        // Fator 4: DESATENÇÃO (DA) (Itens 1 a 12)
        ['question_identifier' => '01', 'text' => 'É negligente para realizar as suas tarefas de casa?', 'scale_code' => $scaleCode,], // REPETIDO: '01'
        ['question_identifier' => '02', 'text' => 'É distraído com quase tudo?', 'scale_code' => $scaleCode,], // REPETIDO: '02'
        ['question_identifier' => '03', 'text' => 'Evita atividades que exigem esforço mental constante (deveres escolares, jogos)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '04', 'text' => 'Esquece rápido o que acabou de ser dito?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '05', 'text' => 'Não usa o que aprendeu em casa, mesmo se obtiveram ajuda até o final (é do tipo fogo de palha)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '06', 'text' => 'Tem dificuldade para realizar as coisas importantes (fácil, por exemplo)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '07', 'text' => 'Não termina o que começa?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '08', 'text' => 'Parece sonhar acordado ( "está no mundo da lua")?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '09', 'text' => 'Dificilmente consegue se organizar em atividades do seu prazer?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '10', 'text' => 'Dá a impressão de que não ouve (se esquece o que ouve)?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '11', 'text' => 'Muitas dificuldades de observação?', 'scale_code' => $scaleCode,],
        ['question_identifier' => '12', 'text' => 'Ocurrem discusuões entre os pais e a criança, em função de falta de responsabilidade e de organização?', 'scale_code' => $scaleCode,],
    ];

    $todos_questionarios[$id_questionnaire] = $tmp_questions;


    // =============================================================
    //  Loop final para gerar o array $questions
    // =============================================================

    foreach ($todos_questionarios as $questionnaire_code => $question_list) {
        $options = []; // Manter esta variável, mesmo que não seja usada aqui
        foreach ($question_list as $q) {
            // OBSERVAÇÃO: Seu array original não define $opcoes_respostas, então o usei diretamente.
            // Se você precisar do 'response_type', ele está em $tipos_respostas
            $questions[] = [
                'questionnaire_code' => $questionnaire_code, 
                'question_identifier' => $q['question_identifier'], 
                'question_text' => $q['text'],
                'response_type' => $tipos_respostas[$questionnaire_code], 
                'scale_code' => $q['scale_code'],
            ];
        }
    }
    return $questions;
}
}