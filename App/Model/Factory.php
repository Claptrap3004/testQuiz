<?php
// responsible for creating objects of classes IdText, QuizQuestion, EditQuestion, Stats and so on
// DBHandler is provided through KindOf enum
namespace quiz;
use Exception;

class Factory
{
    private static ?Factory $factory = null;

    private function __construct()
    {

    }

    public static function getFactory(): Factory
    {
        if (self::$factory === null) self::$factory = new Factory();
        return self::$factory;
    }

    public function createIdTextObject(string $text, KindOf $kindOf): ?IdText
    {
        $id = $kindOf->getDBHandler()->create(['text' => $text]);
        return $id > 0 ? new IdText($id,
                                    $text,
                                    $kindOf)
                        : null;
    }
    public function findIdTextObjectById(int $id, KindOf $kindOf): ?IdText
    {
        $infos = $kindOf->getDBHandler()->findById($id);
        return $id > 0 ? new IdText($id,
                                    $infos['text'],
                                    $kindOf
                                    )
                        : null;
    }

    public function findAllIdTextObject(KindOf $kindOf): array
    {
        $answers = [];
        $answerInfos = $kindOf->getDBHandler()->findAll();
        foreach ($answerInfos as $answerInfo)
            $answers[] = new IdText($answerInfo['id'],
                                    $answerInfo['text'],
                                    $kindOf);
        return $answers;
    }

    /**
     * @throws Exception
     */
    private function prepareQuestionData(int $questionId):?array
    {
        $questionAttributes = KindOf::QUESTION->getDBHandler()->findById($questionId);
        if ($questionAttributes === []) throw new Exception('Question does not exist');

        $category = $this->findIdTextObjectById($questionAttributes['category_id'],
            KindOf::CATEGORY);

        $relations = KindOf::RELATION->getDBHandler()->findById($questionId);
        $rightAnswers = [];
        $wrongAnswers = [];
        foreach ($relations as $relation){
            $answer = $this->findIdTextObjectById($relation['answer_id'],
                KindOf::ANSWER);
            if ($relation['is_right']) $rightAnswers[] = $answer;
            else $wrongAnswers[] = $answer;
        }
        return [
            'text' => $questionAttributes['text'],
            'explanation' => $questionAttributes['explanation'],
            'category' => $category,
            'rightAnswers' => $rightAnswers,
            'wrongAnswers' => $wrongAnswers
        ];
    }

    public function createEmptyEditQuestion():EditQuestion
    {
        return new EditQuestion(0,'','',new IdText(0,'',KindOf::CATEGORY),[],[]);
    }
    /**
     * @throws Exception
     */
    public function createEditQuestionById(int $id):EditQuestion
    {
        $data = $this->prepareQuestionData($id);
        return new EditQuestion(
            $id,
            $data['text'],
            $data['explanation'],
            $data['category'],
            $data['rightAnswers'],
            $data['wrongAnswers']
        );
    }

    /**
     * @throws Exception
     */
    public function createQuizQuestionById(int $id): ?QuizQuestion
    {
        $data = $this->prepareQuestionData($id);
        $stats = $this->createStatsByQuestionId($id);
        return new QuizQuestion(
            $id,
            $data['text'],
            $data['explanation'],
            $data['category'],
            $data['rightAnswers'],
            $data['wrongAnswers'],
            $stats
        );
    }

    public function createStatsByQuestionId(int $questionId): ?Stats
    {
        $statsAttributes = KindOf::STATS->getDBHandler()->findById($questionId);
        return $statsAttributes ? new Stats($statsAttributes['id'],
                        $statsAttributes['user_id'],
                        $statsAttributes['question_id'],
                        $statsAttributes['times_asked'],
                        $statsAttributes['times_right']) :
            null;
    }


    public function createUser(int $id):?User
    {
        $userData = KindOf::USER->getDBHandler()->findById($id);
        return $userData ? new User($userData['id'], $userData['username'], $userData['email'], $userData['password']) : null;
    }

    public function createQuestionsForEditView(): ?array
    {
        $questionIds = KindOf::QUESTION->getDBHandler()->findAll(['userIds' => [$_SESSION['UserId']]]);
        $questions = [];
        foreach ($questionIds as $questionId) {
            $id = (int)$questionId['id'];
            $text = $questionId['text'];
            $category = $this->findIdTextObjectById((int)$questionId['category_id'], KindOf::CATEGORY);
            $questions[] = new QuestionView($id, $category->getText(), $text);

        }
        return $questions;
    }


}