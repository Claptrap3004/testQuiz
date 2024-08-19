<?php

namespace quiz;

interface CanSelectQuestions
{
    public function select(int $numberOfQuestions,array $categoryIds, int $startIndex) : array;

}