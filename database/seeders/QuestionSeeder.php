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
            'DFH-IV' => 'SCORE_CHECKLIST',
            'WAIS-IV' => 'MULTIPLE_CHOICE_SINGLE',
        ];

        // -------------------------------------------------------------
        // DADOS FINAIS
        // -------------------------------------------------------------

        $questions = [];
        $todos_questionarios = [];
        $tmp_questions = [];

        // =========================================================
        //  BDI-II (Código: BDI-II) - DIMENSÕES AGORA SÃO 'DEP'
        // =========================================================
        $tmp_questions = [
            ['id' => '1', 'text' => 'Tristeza:', 'dimension' => ['DEP']], // Antes: 'SOMATICO-AFETIVO'
            ['id' => '2', 'text' => 'Pessimismo:', 'dimension' => ['DEP']], // Antes: 'COGNITIVO'
            ['id' => '3', 'text' => 'Satisfação (Pergunta de pontuação inversa):', 'dimension' => ['DEP']], // Antes: 'COGNITIVO'
            ['id' => '4', 'text' => 'Tristeza:', 'dimension' => ['DEP']], // Antes: 'SOMATICO-AFETIVO'
        ];
        $todos_questionarios['BDI-II'] = $tmp_questions;

        // =========================================================
        //  PALO (Código: PALO) - DIMENSÕES AGORA SÃO 'ANX'
        // =========================================================
        $tmp_questions = [
            ['id' => '1', 'text' => 'Dormência ou formigamento', 'dimension' => ['ANX']], // Antes: 'SOMATICO'
            ['id' => '2', 'text' => 'Sensação de calor', 'dimension' => ['ANX']], // Antes: 'SOMATICO', 'AFETIVO'
        ];
        $todos_questionarios['PALO'] = $tmp_questions;

        
        // =============================================================
        // NOVO: 100 QUESTÕES DO IFP-II (Código: IFP-II) - DIMENSÕES MAPEADAS
        // =============================================================
        $tmp_questions = [
            ['id' => '01', 'text' => 'Gosto de fazer coisas que outras pessoas consideram fora do comum', 'dimension' => ['EXT']], // Antes: MU -> EXT
            ['id' => '02', 'text' => 'Gostaria de realizar um grande feito ou grande obra na minha vida', 'dimension' => ['N.REA']], // Antes: RE -> N.REA
            ['id' => '03', 'text' => 'Gosto de experimentar novidades e mudanças em meu dia-a-dia', 'dimension' => ['EXT']], // Antes: MU -> EXT
            ['id' => '04', 'text' => 'Não gosto de situações em que se exige que eu me comporte de determinada maneira', 'dimension' => ['EXT']], // Antes: AU -> EXT
            ['id' => '05', 'text' => 'Gosto de dizer o que eu penso a respeito das coisas', 'dimension' => ['EXT']], // Antes: OP -> EXT
            ['id' => '06', 'text' => 'Gosto de saber o que grandes personalidades disseram sobre os problemas pelos quais eu me interesso', 'dimension' => ['INV']], // Antes: NF -> INV
            ['id' => '07', 'text' => 'Gosto de ser capaz de fazer as coisas melhor do que as outras pessoas', 'dimension' => ['N.REA']], // Antes: RE -> N.REA
            ['id' => '08', 'text' => 'Gosto de concluir qualquer trabalho ou tarefa que tenha começado', 'dimension' => ['CSC']], // Antes: PE -> CSC
            ['id' => '09', 'text' => 'Gosto de ajudar meus amigos quando eles estão com problemas', 'dimension' => ['N.AFL']], // Antes: AF -> N.AFL
            ['id' => '10', 'text' => 'Não costumo abandonar um quebra-cabeça ou problema antes que consiga resolvê-lo', 'dimension' => ['CSC']], // Antes: PE -> CSC
            ['id' => '11', 'text' => 'Gosto de dizer aos outros como fazer seus trabalhos', 'dimension' => ['EXT']], // Antes: DO -> EXT
            ['id' => '12', 'text' => 'Gostaria de ser considerado(a) uma autoridade em algum trabalho, profissão ou campo de especialização', 'dimension' => ['EXT']], // Antes: DO -> EXT
            ['id' => '13', 'text' => 'Gosto de experimentar e provar coisas novas', 'dimension' => ['EXT']], // Antes: MU -> EXT
            ['id' => '14', 'text' => 'Quando tenho alguma tarefa para fazer, gosto de começar logo e permanecer trabalhando até completá-la', 'dimension' => ['CSC']], // Antes: PE -> CSC
            ['id' => '15', 'text' => 'Aceito com prazer a liderança das pessoas que admiro', 'dimension' => ['CSC']], // Antes: DE -> CSC
            ['id' => '16', 'text' => 'Gosto de trabalhar horas a fio sem ser interrompido(a)', 'dimension' => ['CSC']], // Antes: PE -> CSC
            ['id' => '17', 'text' => 'Gosto que meus amigos me dêem muita atenção quando estou sofrendo ou doente', 'dimension' => ['N.AFL']], // Antes: SU -> N.AFL
            ['id' => '18', 'text' => 'Costumo analisar minhas intenções e sentimentos', 'dimension' => ['INV']], // Antes: IN -> INV
            ['id' => '19', 'text' => 'Gosto de fazer com carinho pequenas favores a meus amigos', 'dimension' => ['N.AFL']], // Antes: AF -> N.AFL
            ['id' => '20', 'text' => 'Gosto de ficar acordado(a) até tarde para terminar um trabalho', 'dimension' => ['CSC']], // Antes: PE -> CSC
            ['id' => '21', 'text' => 'Gosto de andar pelo país e viver em lugares diferentes', 'dimension' => ['EXT']], // Antes: MU -> EXT
            ['id' => '22', 'text' => 'Gosto de analisar os sentimentos e intenções dos outros', 'dimension' => ['INV']], // Antes: IN -> INV
            ['id' => '23', 'text' => 'Gosto de fazer gozação com pessoas que fazem coisas que eu considero estúpidas', 'dimension' => ['EXT']], // Antes: AG -> EXT
            ['id' => '24', 'text' => 'Tenho vontade de me vingar quando alguém me insulta', 'dimension' => ['EXT']], // Antes: AG -> EXT
            ['id' => '25', 'text' => 'Gosto de pensar sobre o caráter dos meus amigos e tentar descobrir o que os faz serem como são', 'dimension' => ['INV']], // Antes: IN -> INV
            ['id' => '26', 'text' => 'Sou leal aos meus amigos', 'dimension' => ['N.AFL']], // Antes: AF -> N.AFL
            ['id' => '27', 'text' => 'Gosto de levar um trabalho ou tarefa até o fim antes de começar outro', 'dimension' => ['CSC']], // Antes: PE -> CSC
            ['id' => '28', 'text' => 'Gosto de dizer aos meus superiores que eles fizeram um bom trabalho, quando acredito nisso', 'dimension' => ['CSC']], // Antes: DE -> CSC
            ['id' => '29', 'text' => 'Gosto que meus amigos sejam solidários comigo e me animem quando estou deprimido(a)', 'dimension' => ['N.AFL']], // Antes: SU -> N.AFL
            ['id' => '30', 'text' => 'Antes de começar um trabalho, gosto de organizá-lo e planejá-lo', 'dimension' => ['CSC']], // Antes: OR -> CSC
            ['id' => '31', 'text' => 'Gosto que meus amigos demostrem muito afeto por mim', 'dimension' => ['N.AFL']], // Antes: SU -> N.AFL
            ['id' => '32', 'text' => 'Gosto de realizar tarefas que, na opinião dos outros, exigem habilidade e esforço', 'dimension' => ['N.REA']], // Antes: RE -> N.REA
            ['id' => '33', 'text' => 'Gosto de ser bem-sucedido nas coisas que faço', 'dimension' => ['N.REA']], // Antes: RE -> N.REA
            ['id' => '34', 'text' => 'Gosto de fazer amizades', 'dimension' => ['N.AFL']], // Antes: AF -> N.AFL
            ['id' => '35', 'text' => 'Gosto de ser considerado(a) um(a) líder pelos outros', 'dimension' => ['EXT']], // Antes: DO -> EXT
            ['id' => '36', 'text' => 'Gosto de realizar com afinco (sem descanso) qualquer trabalho que faço', 'dimension' => ['CSC']], // Antes: PE -> CSC
            ['id' => '37', 'text' => 'Gosto de participar de grupos cujos membros se tratem com afeto e amizade', 'dimension' => ['N.AFL']], // Antes: AF -> N.AFL
            ['id' => '38', 'text' => 'Sinto-me satisfeito(a) quando realizo bem um trabalho difícil', 'dimension' => ['N.REA']], // Antes: RE -> N.REA
            ['id' => '39', 'text' => 'Tenho vontade de mandar os outros calarem a boca quando discordo deles', 'dimension' => ['EXT']], // Antes: AG -> EXT
            ['id' => '40', 'text' => 'Gosto de fazer coisas do meu jeito sem me importar com o que os outros possam pensar', 'dimension' => ['EXT']], // Antes: AU -> EXT
            ['id' => '41', 'text' => 'Gosto de viajar e conhecer o país', 'dimension' => ['EXT']], // Antes: MU -> EXT
            ['id' => '42', 'text' => 'Gosto de me fixar em um trabalho ou problema mesmo quando a solução pareça extremamente difícil', 'dimension' => ['CSC']], // Antes: PE -> CSC
            ['id' => '43', 'text' => 'Gosto de conhecer novas pessoas', 'dimension' => ['N.AFL']], // Antes: AF -> N.AFL
            ['id' => '44', 'text' => 'Gosto de dividir coisas com os outros', 'dimension' => ['SOC']], // Antes: AS -> SOC
            ['id' => '45', 'text' => 'Sinto-me satisfeito(a) quando consigo convencer e influenciar os outros', 'dimension' => ['EXT']], // Antes: DO -> EXT
            ['id' => '46', 'text' => 'Gosto de demonstrar muita afeição por meus amigos', 'dimension' => ['N.AFL']], // Antes: AF -> N.AFL
            ['id' => '47', 'text' => 'Gosto de prestar favores aos outros', 'dimension' => ['SOC']], // Antes: AS -> SOC
            ['id' => '48', 'text' => 'Gosto de seguir instruções e fazer o que é esperado de mim', 'dimension' => ['CSC']], // Antes: DE -> CSC
            ['id' => '49', 'text' => 'Gosto de elogiar alguém que admiro', 'dimension' => ['CSC']], // Antes: DE -> CSC
            ['id' => '50', 'text' => 'Quando planejo alguma coisa, procuro sugestões de pessoas que respeito', 'dimension' => ['CSC']], // Antes: DE -> CSC
            ['id' => '51', 'text' => 'Gosto de manter minhas coisas limpas e ordenadas em minha escrivaninha ou em meu local de trabalho', 'dimension' => ['CSC']], // Antes: OR -> CSC
            ['id' => '52', 'text' => 'Gosto de manter fortes laços de amizade', 'dimension' => ['N.AFL']], // Antes: AF -> N.AFL
            ['id' => '53', 'text' => 'Gosto que meus amigos me ajudem quando estou com problema', 'dimension' => ['N.AFL']], // Antes: SU -> N.AFL
            ['id' => '54', 'text' => 'Gosto que meus amigos mostrem boa vontade em me prestar pequenos favores', 'dimension' => ['N.AFL']], // Antes: SU -> N.AFL
            ['id' => '55', 'text' => 'Gosto de manter minhas cartas, contas e outros papéis bem arrumados e arquivados de acordo com algum sistema', 'dimension' => ['CSC']], // Antes: OR -> CSC
            ['id' => '56', 'text' => 'Gosto que meus amigos sejam solidários e compreensivos quando tenho problemas', 'dimension' => ['N.AFL']], // Antes: SU -> N.AFL
            ['id' => '57', 'text' => 'Prefiro fazer coisas com meus amigos a fazer sozinho', 'dimension' => ['N.AFL']], // Antes: AF -> N.AFL
            ['id' => '58', 'text' => 'Gosto de tratar outras pessoas com bondade e compaixão', 'dimension' => ['SOC']], // Antes: AS -> SOC
            ['id' => '59', 'text' => 'Gosto de comer em restaurantes novos e exóticos(diferentes)', 'dimension' => ['EXT']], // Antes: MU -> EXT
            ['id' => '60', 'text' => 'Procuro entender como meus amigos se sentem a respeito de problemas que eles enfrentam', 'dimension' => ['INV']], // Antes: IN -> INV
            ['id' => '61', 'text' => 'Gosto de ser o centro das atenções em um grupo', 'dimension' => ['EXT']], // Antes: EX -> EXT
            ['id' => '62', 'text' => 'Gosto de ser um dos líderes nas organizações e grupos aos quais pertenço', 'dimension' => ['EXT']], // Antes: DO -> EXT
            ['id' => '63', 'text' => 'Gosto de ser independente dos outros para decidir o que quero fazer', 'dimension' => ['EXT']], // Antes: AU -> EXT
            ['id' => '64', 'text' => 'Gosto de me manter em contato com meus amigos', 'dimension' => ['N.AFL']], // Antes: AF -> N.AFL
            ['id' => '65', 'text' => 'Quando participo de uma comissão (reunião), gosto de ser indicado ou eleito presidente', 'dimension' => ['EXT']], // Antes: DO -> EXT
            ['id' => '66', 'text' => 'Gosto de fazer tantos amigos quanto possível', 'dimension' => ['N.AFL']], // Antes: AF -> N.AFL
            ['id' => '67', 'text' => 'Gosto de observar como uma outra pessoa se sente numa determinada situação', 'dimension' => ['INV']], // Antes: IN -> INV
            ['id' => '68', 'text' => 'Quando estou em um grupo, aceito com prazer a liderança de outra pessoa para decidir o que o grupo fará', 'dimension' => ['CSC']], // Antes: DE -> CSC
            ['id' => '69', 'text' => 'Não gosto de me sentir pressionado(a) por responsabilidades e deveres', 'dimension' => ['EXT']], // Antes: AU -> EXT
            ['id' => '70', 'text' => 'Às vezes, fico tão irritado(a) que sinto vontade de jogar e quebrar coisas', 'dimension' => ['EXT']], // Antes: AG -> EXT
            ['id' => '71', 'text' => 'Gosto de fazer perguntas que ninguém será capaz de responder', 'dimension' => ['EXT']], // Antes: OP -> EXT
            ['id' => '72', 'text' => 'Às vezes, gosto de fazer coisas simplesmente para ver o efeito que terão sobre os outros', 'dimension' => ['EXT']], // Antes: EX -> EXT
            ['id' => '73', 'text' => 'Sou solidário com meus amigos quando machucados ou doentes', 'dimension' => ['SOC']], // Antes: AS -> SOC
            ['id' => '74', 'text' => 'Não tenho medo de criticar pessoas que ocupam posições de autoridade', 'dimension' => ['EXT']], // Antes: AG, OP -> EXT
            ['id' => '75', 'text' => 'Gosto de fiscalizar e dirigir os atos dos outros sempre que posso', 'dimension' => ['EXT']], // Antes: DO -> EXT
            ['id' => '76', 'text' => 'Culpo os outros quando as coisas dão errado comigo', 'dimension' => ['EXT']], // Antes: AG -> EXT
            ['id' => '77', 'text' => 'Gosto de ajudar pessoas que têm menos sorte do que eu', 'dimension' => ['SOC']], // Antes: AS -> SOC
            ['id' => '78', 'text' => 'Gosto de planejar e organizar, em todos os detalhes, qualquer trabalho que eu faço', 'dimension' => ['CSC']], // Antes: OR -> CSC
            ['id' => '79', 'text' => 'Gosto de fazer coisas novas e diferentes', 'dimension' => ['EXT']], // Antes: MU, AU -> EXT
            ['id' => '80', 'text' => 'Gostaria de realizar com sucesso alguma coisa de grande importância', 'dimension' => ['N.REA']], // Antes: RE -> N.REA
            ['id' => '81', 'text' => 'Quando estou com um grupo de pessoas, gosto de decidir sobre o que vamos fazer', 'dimension' => ['EXT']], // Antes: DO -> EXT
            ['id' => '82', 'text' => 'Interesso-me em conhecer a vida de grandes personalidades', 'dimension' => ['INV']], // Antes: NF -> INV
            ['id' => '83', 'text' => 'Procuro me adaptar ao modo de ser das pessoas que admiro', 'dimension' => ['CSC']], // Antes: DE -> CSC
            ['id' => '84', 'text' => 'Gosto de resolver quebra-cabeças e problemas com os quais pessoas têm dificuldades', 'dimension' => ['N.REA']], // Antes: RE -> N.REA
            ['id' => '85', 'text' => 'Gosto de falar sobre os meus sucessos', 'dimension' => ['EXT']], // Antes: EX -> EXT
            ['id' => '86', 'text' => 'Gosto de dar o melhor de mim em tudo que faço', 'dimension' => ['N.REA']], // Antes: RE -> N.REA
            ['id' => '87', 'text' => 'Gosto de estudar e analisar o comportamento dos outros', 'dimension' => ['INV']], // Antes: IN -> INV
            ['id' => '88', 'text' => 'Gosto de contar aos outros aventuras e coisas estranhas que acontecem comigo', 'dimension' => ['EXT']], // Antes: EX -> EXT
            ['id' => '89', 'text' => 'Perdôo as pessoas que às vezes possam me magoar', 'dimension' => ['SOC']], // Antes: AS -> SOC
            ['id' => '90', 'text' => 'Gosto de prever (entender) como meus amigos irão agir em diferentes situações', 'dimension' => ['INV']], // Antes: IN -> INV
            ['id' => '91', 'text' => 'Gosto de me sentir livre para fazer o que quero', 'dimension' => ['EXT']], // Antes: AU -> EXT
            ['id' => '92', 'text' => 'Gosto de me sentir livre para ir e vir quando quiser', 'dimension' => ['EXT']], // Antes: AU -> EXT
            ['id' => '93', 'text' => 'Gosto de usar palavras cujo significado as outras pessoas desconhecem', 'dimension' => ['EXT']], // Antes: OP -> EXT
            ['id' => '94', 'text' => 'Gosto de planejar antes de iniciar algo difícil', 'dimension' => ['CSC']], // Antes: OR -> CSC
            ['id' => '95', 'text' => 'Qualquer trabalho escrito que faço, gosto que seja preciso, limpo e bem-organizado', 'dimension' => ['CSC']], // Antes: OR -> CSC
            ['id' => '96', 'text' => 'Gosto que as pessoas notem e comentem a minha aparência quando estou em público', 'dimension' => ['EXT']], // Antes: EX -> EXT
            ['id' => '97', 'text' => 'Gosto que meus amigos me tratem com delicadeza', 'dimension' => ['N.AFL']], // Antes: SU -> N.AFL
            ['id' => '98', 'text' => 'Gosto de ser generoso(a) com os outros', 'dimension' => ['SOC']], // Antes: AS -> SOC
            ['id' => '99', 'text' => 'Gosto de contar estórias e piadas engraçadas em festas', 'dimension' => ['EXT']], // Antes: EX -> EXT
            ['id' => '100', 'text' => 'Gosto de dizer coisas que os outros consideram engraçadas e inteligentes', 'dimension' => ['EXT']], // Antes: EX -> EXT
        ];
        $todos_questionarios['IFP-II'] = $tmp_questions;


        // =======================================================================
        // DFH-IV - DIMENSÕES AGORA SÃO 'FG' (Fator G)
        // =======================================================================
        $tmp_questions = [
            // FIGURA MASCULINA (FM)
            ['id' => 'DFH-FM-101', 'text' => 'Cabeça presente (e fechada)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FM-102', 'text' => 'Pescoço presente (ligando cabeça e tronco)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FM-103', 'text' => 'Tronco presente (diferente da cabeça)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FM-104', 'text' => 'Tronco desenhado em duas dimensões (2D)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FM-105', 'text' => 'Proporção da cabeça para o tronco é razoável', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FM-106', 'text' => 'Braços presentes e unidos ao tronco', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FM-107', 'text' => 'Braços articulados (ombros, cotovelos)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FM-108', 'text' => 'Pernas presentes e unidas ao tronco', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FM-109', 'text' => 'Pés ou calçados presentes e diferenciados', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FM-110', 'text' => 'Cinco dedos (ou indicação clara de dedos)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FM-111', 'text' => 'Olhos presentes e com pupila/íris', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FM-112', 'text' => 'Nariz presente (2D ou com narinas)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FM-113', 'text' => 'Boca presente (com lábios ou contorno)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FM-114', 'text' => 'Orelhas presentes', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FM-115', 'text' => 'Cabelo presente (não rascunho ou rabisco)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FM-116', 'text' => 'Vestuário (pelo menos duas peças)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FM-117', 'text' => 'Detalhes de vestuário específicos (ex: gola, cinto)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FM-118', 'text' => 'Transparência ausente (corpo não visível através da roupa)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            
            // FIGURA FEMININA (FF)
            ['id' => 'DFH-FF-201', 'text' => 'Cabeça presente (e fechada)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FF-202', 'text' => 'Pescoço presente (ligando cabeça e tronco)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FF-203', 'text' => 'Tronco presente (diferente da cabeça)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FF-204', 'text' => 'Tronco desenhado em duas dimensões (2D)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FF-205', 'text' => 'Proporção da cabeça para o tronco é razoável', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FF-206', 'text' => 'Braços presentes e unidos ao tronco', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FF-207', 'text' => 'Braços articulados (ombros, cotovelos)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FF-208', 'text' => 'Pernas presentes e unidas ao tronco', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FF-209', 'text' => 'Pés ou calçados presentes e diferenciados', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FF-210', 'text' => 'Cinco dedos visíveis na mão', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FF-211', 'text' => 'Olhos presentes e com pupila/íris', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FF-212', 'text' => 'Nariz presente (2D ou com narinas)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FF-213', 'text' => 'Boca presente (com lábios ou contorno)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FF-214', 'text' => 'Orelhas presentes', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FF-215', 'text' => 'Cabelo presente (com detalhes femininos, ex: longo, penteado)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FF-216', 'text' => 'Vestuário feminino (ex: vestido, saia, blusa)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FF-217', 'text' => 'Detalhes de vestuário ou adereços (ex: brincos, colar, laços)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FF-218', 'text' => 'Proporção geral correta e simetria', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
            ['id' => 'DFH-FF-219', 'text' => 'Ausência de monitorização (tentativa de apagar e corrigir)', 'dimension' => ['FG']], // Antes: 'COGNITIVO' -> FG
        ];
        $todos_questionarios['DFH-IV'] = $tmp_questions;

        // =============================================================
        // QUESTÕES NEO-PI-R (Código: NEO-PI-R) - DIMENSÕES MAPEADAS
        // =============================================================

        $tmp_questions = [
            ['id' => 'N01', 'text' => 'Fico facilmente nervoso e chateado(a) quando as coisas não saem como planejado.', 'dimension' => ['ANX']], // Antes: 'N_ANSIEDADE' -> ANX
            ['id' => 'E01', 'text' => 'Eu gosto de ser o centro das atenções em reuniões sociais.', 'dimension' => ['EXT']], // Antes: 'E_CALOR' -> EXT
            ['id' => 'N02', 'text' => 'Eu me preocupo muito com o que os outros pensam de mim.', 'dimension' => ['ANX']], // Antes: 'N_VERGONHA' -> ANX
        ];
        $todos_questionarios['NEO-PI-R'] = $tmp_questions;

        // =============================================================
        //  QUESTÕES RSES (Código: RSES) - DIMENSÃO 'AE' MANTIDA
        // =============================================================

        $tmp_questions = [
            ['id' => 'RS01', 'text' => 'Sinto que sou uma pessoa de valor, pelo menos num plano igual ao dos outros.', 'dimension' => ['AE']],
            ['id' => 'RS02', 'text' => 'Sinto que não tenho muitas razões para me orgulhar.', 'dimension' => ['AE']],
            ['id' => 'RS03', 'text' => 'Eu me sinto inútil às vezes.', 'dimension' => ['AE']],
        ];
         $todos_questionarios['RSES'] = $tmp_questions;


        // =============================================================
        //  QUESTÕES PCL-5 (Código: PCL-5) - DIMENSÕES AGORA SÃO 'EST'
        // =============================================================

        $tmp_questions = [
            ['id' => 'PCL01', 'text' => 'Problemas para lembrar de partes importantes do evento estressor?', 'dimension' => ['EST']], // Antes: 'B_REEXPERIENCIA' -> EST
            ['id' => 'PCL02', 'text' => 'Sentimentos de culpa ou ser culpado(a) por causa do evento estressor?', 'dimension' => ['EST']], // Antes: 'C_EVITAMENTO' -> EST
            ['id' => 'PCL03', 'text' => 'Ter sonhos ruins sobre o evento estressor?', 'dimension' => ['EST']], // Antes: 'B_REEXPERIENCIA' -> EST
        ];
        $todos_questionarios['PCL-5'] = $tmp_questions;


        // =============================================================
        // QUESTÕES WAIS-IV (Código: WAIS-IV) - DIMENSÃO AGORA É 'RV'
        // =============================================================

        // As opções são específicas por questão
        $tmp_questions = [
            [
                'id' => 'W01',
                'text' => 'Qual é o nome do pintor famoso pela obra "A Noite Estrelada"?',
                'dimension' => ['RV'], // Antes: 'VC_CONHECIMENTO' -> RV
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
                'dimension' => ['RV'], // Antes: 'VC_CONHECIMENTO' -> RV
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

                // Lógica que aplica as opções globais
                If ($questionnaire_code == 'WAIS-IV' ){
                    // Para o WAIS-IV, as opções são específicas e já estão no array $q.
                    // A lógica abaixo é redundante mas mantida para garantir a consistência
                    // com a estrutura anterior que você tinha, garantindo que $options seja populada.
                    if ( isset($q['options_json']) ){
                        $options = $q['options_json'];
                    } else {
                        // Caso $q['options_json'] não esteja definido, usa a lógica de id.
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
                        }
                    }
                }
                 else {
                        // Para todos os outros questionários, usa as opções pré-definidas.
                        $options = $opcoes_respostas[$questionnaire_code];
                }
                
                // Adiciona a pergunta ao array final
                $questions[] = [
                'questionnaire_code' => $questionnaire_code, 
                'question_identifier' => $q['id'],
                'question_text' => $q['text'],
                'response_type' => $tipos_respostas[$questionnaire_code],
                'dimensions_json' => $q['dimension'], // Agora contêm códigos canônicos
                'options_json' => $options,
            ];
            }
        }
        return $questions;
    }
}