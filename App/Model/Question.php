<?php
// deals as parent class for EditQuestion and QuizQuestion class


namespace quiz;
use JsonSerializable;
abstract class Question extends IdText implements JsonSerializable
{
    protected string $explanation;
    protected IdText $category;
    protected array $rightAnswers;
    protected array $wrongAnswers;


    /**
     * @param int $id
     * @param string $text
     * @param string $explanation
     * @param IdText $category
     * @param IdText[] $rightAnswers
     * @param IdText[] $wrongAnswers
     */
    protected function __construct(int $id, string $text,  string $explanation,IdText $category,array $rightAnswers, array $wrongAnswers)
    {
        parent::__construct($id, $text, KindOf::QUESTION);
        $this->explanation = $explanation;
        $this->category = $category;
        $this->rightAnswers = $rightAnswers;
        $this->wrongAnswers = $wrongAnswers;
    }

    public function getCategory(): IdText
    {
        return $this->category;
    }

    public function getRightAnswers(): array
    {
        return $this->rightAnswers;
    }

    public function getWrongAnswers(): array
    {
        return $this->wrongAnswers;
    }

    /** returns shuffled merge of right and wrong answer arrays
     * @return array
     */
    public function getAnswers():array
    {
        $answers = array_merge($this->rightAnswers,$this->wrongAnswers);
        shuffle($answers);
        return $answers;
    }

    public function getExplanation(): string
    {
        return $this->explanation;
    }




    public function jsonSerialize(): mixed
    {
       return [
           'id' => $this->id,
           'text' => $this->text,
           'category' => $this->category,
           'rightAnswers' => $this->rightAnswers,
           'wrongAnswers' => $this->wrongAnswers
       ];
    }
}