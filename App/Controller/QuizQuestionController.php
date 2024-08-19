<?php

namespace quiz;

use Exception;

class QuizQuestionController extends Controller
{

    /**
     * checks whether there is still an unfinished quiz. In case unfinished quiz exists user is directed to actual
     * question of quiz, else user is directed to welcome screen
     * @param array $data
     * @return void
     */
    public function index(array $data = []): void
    {
        if (isset($_SESSION['final'])) {
            KindOf::QUIZCONTENT->getDBHandler()->createTables();
            unset($_SESSION['final']);
        }
        $handler = KindOf::QUIZCONTENT->getDBHandler();
        $questions = $handler->findAll();
        if (count($questions) === 0) {
            $this->welcome();
        } else $this->answer();
    }

    /**
     * shows welcome screen after login or back to welcome screen request
     * @return void
     */
    public function welcome(): void
    {
        $user = $this->factory->createUser($_SESSION['UserId']);
        $stats = new UserStats($user);
        $this->view(UseCase::WELCOME->getView(), ['user' => $user, 'stats' => $stats]);
    }

    /**
     * expects key 'question_ids' which holds int[] storing selected question_ids that will be asked in the quiz.
     * populates quiz_content table of actual user and directs to answer options of the first question
     * @param array $data
     * @return void
     */
    private function fillTables(array $data = []): void
    {
        $handler = KindOf::QUIZCONTENT->getDBHandler();
        $handler->create(['question_ids' => $data]);


    }

    /**
     * directs to category selection page, after categories and number of questions are selected QuestionSelector object
     * is created and random selection of question ids is sent to according method to create the content
     * @return void
     */
    public function select(): void
    {
            $questionsByCategories = KindOf::QUESTION->getDBHandler()->findAll(['question_by_category' => null]);
            $jsData = json_encode($questionsByCategories);
            $this->view('quiz/selectQuestions', ['categories' => $questionsByCategories, 'jsData' => $jsData]);
    }

    public function makeSelection(): void
    {

        $categories = $_REQUEST['categories'] ?? [];
        $numberOfQuestions = (int)$_REQUEST['range'] ?? 0;
        $preferUnperfect = (isset($_REQUEST['prefered']));
        $altMethod = (isset($_REQUEST['chooseAlt']));
        $start = (int)($_REQUEST['startQuestion'] ?? 0);
        $end = (int)($_REQUEST['endQuestion'] ?? 0);
        if ($altMethod && $start > 0 && $end > 0) {
            $selector = new RangeQuestionSelector();
            $numberOfQuestions = $end - $start;
            $questions = $selector->select($numberOfQuestions, $categories, $start);
        } else {
            $selector = new QuestionSelector($preferUnperfect);
            $questions = $selector->select($numberOfQuestions, $categories);
        }
        $json = json_encode($questions);
        file_put_contents('test.log',$json,FILE_APPEND);
        $this->fillTables($questions);
    }


    /**
     * loads actual question with possible answers and displays it. On post request of page it sets actual attribute
     * in quiz_content table after storing given answer(s) in track_quiz_content table. If post request is set next question
     * as actual while actual question was last one is_actual is set to false for all questions to trigger validation of
     * quiz.
     * @return void
     */
    public function answer(): void
    {
        $id = KindOf::QUIZCONTENT->getDBHandler()->getActualQuestionId();
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $answers = $_POST['answers'] ?? [];
            if ($id !== null && $this->isActionSet()) {
                $this->evaluateUserInput($id, $answers);
            }
            $id = KindOf::QUIZCONTENT->getDBHandler()->getActualQuestionId();
        }
        $this->clearActionRequests();
        if (!$id) $this->final();
        else {
            $this->showNextQuestion($id);
        }
    }


    /**
     *
     * @return void
     */
    private function clearActionRequests(): void
    {
        unset($_POST['finish']);
        unset($_POST['setPrev']);
        unset($_POST['setNext']);
        $_SERVER['REQUEST_METHOD'] = null;
    }

    /**
     * creates actual QuizQuestion, fills its givenAnswers array if answers stored in track_quiz_content table and
     * prepares necessary JSON strings for view. Then calls the view.
     * @param int $id
     * @return void
     */
    private function showNextQuestion(int $id): void
    {
        try {
            $question = $this->factory->createQuizQuestionById($id);
            $trackContent = KindOf::QUIZCONTENT->getDBHandler()->findById($id);
            $answers = [];
            foreach ($trackContent as $item) $answers[] = $item['answer_id'];
            $question->setGivenAnswers($answers);
            $jsData = json_encode($question);
            $content = new ContentInfos();
            $jsContent = json_encode($content);
            $this->view(UseCase::ANSWER_QUESTION->getView(), ['contentInfo' => $jsContent, 'jsData' => $jsData]);
        } catch (Exception $e) {
            $this->reactOnQuestionCreateError($id);
        }
    }

    /**
     * updates track_quiz_content table accordingly to users answers and sets pointer actual to requested position.
     * @param int $id
     * @param array $answers
     * @return void
     */
    private function evaluateUserInput(int $id, array $answers): void
    {
        try {
            $question = $this->factory->createQuizQuestionById($id);
            foreach ($answers as $answer) {
                if ((int)$answer > 0)
                    $question->addGivenAnswer($this->factory->findIdTextObjectById((int)$answer, KindOf::ANSWER));
            }

            $question->writeResultDB();

            if (isset($_POST['finish'])) $whichActual = SetActual::NONE;
            else $whichActual = isset($_POST['setNext']) ? SetActual::NEXT : SetActual::PREVIOUS;

            KindOf::QUIZCONTENT->getDBHandler()->setActual($whichActual);
        } catch (Exception $e) {
            $this->reactOnQuestionCreateError($id);
        }
    }


    /**
     * On error (unable to create question) the question is deleted from quiz_content and quiz goes on with next question
     * @param int $id
     * @return void
     */
    private function reactOnQuestionCreateError(int $id): void
    {
        if (KindOf::QUIZCONTENT->getDBHandler()->getActualQuestionId() === $id)
            KindOf::QUIZCONTENT->getDBHandler()->deleteAtId($id);
        $this->answer();
    }

    /**
     * necessary to avoid skip to next question on users page refresh od answerQuestion.html.twig
     * @return bool
     */
    public function isActionSet(): bool
    {
        return isset($_POST['finish']) || isset($_POST['setPrev']) || isset($_POST['setNext']);
    }

    /**
     * after user clicked finish quiz the user gets an overview of all questions with info if user chose any answer(s).
     * user can either decide to go through the questions again or to get result of the quiz
     * @return void
     */
    public function final(): void
    {
        $quizStats = new QuizStatsView();
        $quizStatsView = json_encode($quizStats);

        if (isset($_REQUEST['reset'])) {
            KindOf::QUIZCONTENT->getDBHandler()->setActual(SetActual::FIRST);
            $this->answer();
        } elseif (isset($_REQUEST['confirm'])) {
            $_SESSION['final'] = true;
            $quizStats->validate();
            $quizStatsView = json_encode($quizStats);
            $this->view(UseCase::FINALIZE_QUIZ->getView(), ['questionsJS' => $quizStatsView]);
        } else {
            $this->view(UseCase::CHECK_BEFORE_FINALIZE->getView(), ['questionsJS' => $quizStatsView]);
        }
        $_SERVER['REQUEST_METHOD'] = null;
    }

    /**
     * quick randomly generated quiz from all available question with standard selection method
     * @return void
     */
    public function quickStart(): void
    {
        $numberOfQuestions = $_REQUEST['numberOfQuestions'] ?? 20;
        $selector = new QuestionSelector();
        $questions = $selector->select((int)$numberOfQuestions);
        $this->fillTables($questions);
        $this->answer();
    }

    /**
     * method to be called via ajax from answerQuestion.html.twig
     * @return void
     */
    public function deleteStatsQuestion(): void
    {
        $id = $_REQUEST['id'] ?? 0;
        if ($id > 0) $this->factory->createStatsByQuestionId((int) $id)->reset();
    }

    /**
     * method to be called via ajax from welcome.html.twig
     * @return void
     */
    public function deleteStatsAll(): void
    {
        KindOf::STATS->getDBHandler()->deleteAll();
    }

    public function test(): void
    {

    }


}