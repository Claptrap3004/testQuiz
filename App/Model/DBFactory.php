<?php

namespace quiz;

use Exception;

class DBFactory
{
    private static ?DBFactory $factory = null;

    private function __construct()
    {

    }
    public static function getFactory(): DBFactory
    {
        if (self::$factory === null) self::$factory = new DBFactory();
        return self::$factory;
    }

    /**
     * @throws Exception
     */
    public function createQuizQuestionByCSVImport(string $questionText, string $categoryText,string $explanation, array $answers): int
    {
        if ($this->getIdByText($questionText, KindOf::QUESTION->getDBHandler()) !== 0) throw new Exception('Frage existert bereits');

        $categoryId = $this->createIdText($categoryText, KindOf::CATEGORY);
        $questionId = $this->createQuestion($categoryId, $questionText, $explanation);

        foreach ($answers as $key => $answer) {
            $answerId = $this->createIdText($key, KindOf::ANSWER);
            $this->createRelation($questionId, $answerId, $answer);
        }

        return $questionId;
    }

    public function createNewQuizQuestion(string $questionText, int $categoryId):int
    {
        return $this->createQuestion($categoryId,$questionText,'');

    }


    public function createRelation(int $questionId, int $answerId, bool $isRight): void
    {
        KindOf::RELATION->getDBHandler()->create([
            'question_id' => $questionId,
            'answer_id' => $answerId,
            'is_right' => $isRight ? 1 : 0
        ]);
    }

    private function createIdText(string $text, KindOf $kindOf): int
    {
        $id = $this->getIdByText($text, $kindOf->getDBHandler());
        return $id > 0 ? $id : $kindOf->getDBHandler()->create(['text' => $text]);
    }


    private function createQuestion(int $categoryId,string $text,string $explanation): int
    {
        return KindOf::QUESTION->getDBHandler()->create([
            'category_id' => $categoryId,
            'user_id' => $_SESSION['UserId'],
            'text' => $text,
            'explanation' => $explanation
        ]);
    }

    private function getIdByText(string $searchText, CanHandleDB $handler): int
    {
        $id = 0;
        $items = $handler->findAll();
        foreach ($items as $item) {
            if (($item['text']) === trim($searchText)) $id = $item['id'];
            if ($id !== 0) break;
        }
        return $id;
    }

    /**
     * @throws Exception
     */

    public function createUser(string $username, string $email, string $password): int
    {
        $userData = KindOf::USER->getDBHandler()->findAll();
        foreach ($userData as $user) {
            if ($user['email'] === trim($email)) throw new Exception('User exists for this email already');
        }
        $pwHash = password_hash($password,PASSWORD_BCRYPT);
        return KindOf::USER->getDBHandler()->create([
            'username' => $username,
            'email' => $email,
            'password' => $pwHash
        ]);
    }


    public function createCategory(string $text): int
    {
        return $this->createIdText($text, KindOf::CATEGORY);
    }

    public function createAnswer(string $text): int
    {
        return $this->createIdText($text, KindOf::ANSWER);
    }

}