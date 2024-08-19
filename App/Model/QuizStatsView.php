<?php

namespace quiz;

use JsonSerializable;

class QuizStatsView implements JsonSerializable
{
    public array $questions = [];
    public int $questionsAsked = 0;
    public int $answeredCorrect = 0;
    public float $rate = 0;


    public function __construct()
    {
        $this->getQuestionsFromDB();
    }


    /**
     * loads questions from actual users quiz_content table in db, as well as the given answers stores in
     * track_quiz_content table in db
     * @return void
     */
    private function getQuestionsFromDB(): void
    {
        $dbHandler = KindOf::QUIZCONTENT->getDBHandler();
        $factory = Factory::getFactory();
        $answeredQuestionsData = $dbHandler->findAll();
        $this->questionsAsked = count($answeredQuestionsData);
        foreach ($answeredQuestionsData as $questionData) {
            try {
                $question = $factory->createQuizQuestionById($questionData['question_id']);
                $answersData = $dbHandler->findById($questionData['question_id']);
                $answers = [];
                foreach ($answersData as $answer) $answers[] = $factory->findIdTextObjectById($answer['answer_id'], KindOf::ANSWER);
                $question->setGivenAnswers($answers);
                $this->questions[] = $question;
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * calls validation method inside QuizQuestion and maps each question id to bool value in validatedQuestions array
     * as well as to increment counter for tracking the number of correctly answered questions
     * @return void
     */
    public function validate(): void
    {
        foreach ($this->questions as $question) {
            if ($question->validate()) {
                $this->answeredCorrect++;
            }
            $this->setRate();
        }
    }

    private function setRate():void
    {
        $percentage = $this->questionsAsked != 0 ? $this->answeredCorrect * 100 / $this->questionsAsked : 0;
        $this->rate = round($percentage, 2);
    }


    public function jsonSerialize(): mixed
    {
        return [
            'questions' => $this->questions,
            'questionsAsked' => $this->questionsAsked,
            'answeredCorrect' => $this->answeredCorrect,
            'rate' => $this->rate
        ];
    }
}
