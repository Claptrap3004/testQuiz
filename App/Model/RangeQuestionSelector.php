<?php

namespace quiz;

class RangeQuestionSelector implements CanSelectQuestions
{

    public function select(int $numberOfQuestions, array $categoryIds, int $startIndex): array
    {
        $questions = [];
        if (count($categoryIds) !== 1) return [];
        $questionPool = KindOf::QUESTION->getDBHandler()->findAll(Filters::CATEGORY->createArray($categoryIds));
        $end = $startIndex + $numberOfQuestions;
        if ($end > (count($questionPool)-1)) $end = count($questionPool)-1;
        for ($i = $startIndex; $i <= $end; $i++) {
            $questions[] = (int)$questionPool[$i]['id'];
        }
        return $questions;
    }
}