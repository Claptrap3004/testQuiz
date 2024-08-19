<?php

namespace quiz;

use Random\RandomException;

class QuestionSelector implements CanSelectQuestions
{
    private array $questionPool;
    private bool $preferUnperfect;

    public function __construct(bool $preferUnperfect = false)
    {
        $this->preferUnperfect = $preferUnperfect;
    }

    public function select(int $numberOfQuestions, array $categoryIds = [], int $startIndex = 0): array
    {
        $questions = [];
        $this->questionPool = KindOf::QUESTION->getDBHandler()->findAll(Filters::CATEGORY->createArray($categoryIds));
        if ($numberOfQuestions > count($this->questionPool)) return [];
        if ($this->preferUnperfect) $this->modifyPool($numberOfQuestions);
        for ($i = 0; $i < $numberOfQuestions; $i++) {
            $questions[] = $this->pickOne();
        }
        return $questions;
    }

    private function modifyPool(int $numberOfQuestions):void
    {
        $tempPool = [];
        $newPool = [];
        foreach ($this->questionPool as $data){
            $id = (int)($data['id']);
            $stats = Factory::getFactory()->createStatsByQuestionId($id);
            if ($stats === null || $this->statsUnperfect($stats)) $newPool[] = $data;
            else $tempPool[] = $data;
        }
        $availableQuestions = count($newPool);
        if ($availableQuestions < $numberOfQuestions){
            shuffle($tempPool);
            $difference = $numberOfQuestions - $availableQuestions;
            $add = array_slice($tempPool,0,$difference);
            $newPool = array_merge($newPool,$add);
        }
        $this->questionPool = $newPool;
    }

    private function statsUnperfect(Stats $stats):bool
    {
        return ($stats->getTimesAsked() !== $stats->getTimesRight());
    }

    private function pickOne() : int
    {
        try {
            $index = random_int(0, count($this->questionPool) - 1);
        } catch (RandomException $e) {
            $index = 0;
        }
        $id = $this->questionPool[$index]['id'];
        unset( $this->questionPool[$index]);
        $this->questionPool = array_values($this->questionPool);
        return $id;
    }

}