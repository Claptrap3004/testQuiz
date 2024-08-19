<?php

namespace quiz;
use JsonSerializable;

class QuizQuestion extends Question implements JsonSerializable
{
    private Stats $stats;
    private array $givenAnswers;
    private bool $answeredCorrect = false;

    /**
     * @param int $id
     * @param string $text
     * @param string $explanation
     * @param IdText $category
     * @param IdText[] $rightAnswers
     * @param IdText[] $wrongAnswers
     * @param Stats $stats
     */
    public function __construct(int $id, string $text,string $explanation,IdText $category, array $rightAnswers, array $wrongAnswers,Stats $stats)
    {
        parent::__construct($id,$text,$explanation,$category,$rightAnswers,$wrongAnswers);
        $this->stats = $stats;
        $this->givenAnswers = [];
    }

    /**
     * checks given answers against right answers, only if both (sorted) arrays match the result is true, if only some
     * answers are correct the result will be false
     * @return bool
     */
    public function validate(): bool
    {
        usort($this->givenAnswers, fn($a, $b) => strcmp($a->getId(), $b->getId()));
        usort($this->rightAnswers, fn($a, $b) => strcmp($a->getId(), $b->getId()));
        $this->stats->incrementTimesAsked();
        if ($this->givenAnswers == $this->rightAnswers){
            $this->stats->incrementTimesRight();
            $this->answeredCorrect = true;
            return true;
        }
        return false;
    }

    public function getStats(): Stats
    {
        return $this->stats;
    }

    public function getGivenAnswers(): array
    {
        return $this->givenAnswers;
    }

    public function addGivenAnswer(IdText $answer): void
    {
        if (!$this->existsInGivenAnswers($answer))
            $this->givenAnswers[] = $answer;
    }

    public function removeGivenAnswer(IdText $answer): void
    {
        if ($this->givenAnswers == []) return;
        foreach ($this->givenAnswers as $index => $givenAnswer){
            if ($givenAnswer->equals($answer)) {
                unset($this->givenAnswers[$index]);
                $this->givenAnswers = array_values($this->givenAnswers);
                break;
            }
        }
    }

    public function existsInGivenAnswers(IdText $answer): bool
    {
        foreach ($this->givenAnswers as $givenAnswer)
            if ($givenAnswer->equals($answer)) return true;
        return false;
    }

    public function setGivenAnswers(array $givenAnswers): void
    {
        $this->givenAnswers = $givenAnswers;
    }

    public function writeResultDB(): void
    {
        $answerIds = [];
        foreach ($this->givenAnswers as $answer) $answerIds[]=$answer->getId();
        KindOf::QUIZCONTENT->getDBHandler()->update(['question_id' => $this->id, 'answers' => $answerIds]);

    }

    public function isAnsweredCorrect(): bool
    {
        return $this->answeredCorrect;
    }
    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'category' => $this->category,
            'explanation' => $this->explanation,
            'answers' =>$this->getAnswers(),
            'rightAnswers' => $this->rightAnswers,
            'wrongAnswers' => $this->wrongAnswers,
            'stats' => $this->stats,
            'givenAnswers' => $this->givenAnswers,
            'answeredCorrect' => $this->answeredCorrect
        ];
    }


}