<?php
namespace quiz;
include_once 'Question.php';
use Exception;


class EditQuestion extends Question
{
    private CanHandleDB $relator;

    /**
     * @param int $id
     * @param string $text
     * @param string $explanation
     * @param IdText $category
     * @param IdText[] $rightAnswers
     * @param IdText[] $wrongAnswers
     */
    public function __construct(int $id, string $text,string $explanation, IdText $category, array $rightAnswers, array $wrongAnswers)
    {
        parent::__construct($id, $text,$explanation, $category, $rightAnswers, $wrongAnswers);
        $this->relator = KindOf::RELATION->getDBHandler();
    }


    public function setCategory(IdText $category): void
    {
        $this->category = $category;
    }
    public function setExplanation(string $explanation): void
    {
        $this->explanation = $explanation;
    }

    public function resetAnswers():void
    {
        $this->relator->deleteAtId($this->id);
        $this->wrongAnswers = [];
        $this->rightAnswers = [];
    }

    public function setAnswer(IdText $answer, bool $isRight): void
    {
        if ($isRight) $this->rightAnswers[] = $answer;
        else $this->wrongAnswers[] = $answer;
    }

    public function delete(EditQuestion $question):void
    {

    }

    /**
     * checks if minimum of 4 choices / answers to questions is fulfilled, if not exception is thrown
     * @throws Exception
     */
    public function saveQuestion():void
    {
        if (count($this->getAnswers()) >= 4) $this->update();
        else throw new Exception("Speichern der Frage nicht möglich, da weniger als 4 Antwortmöglichkeiten hinterlegt wurden");
    }

    // after check by saveQuestion the question text and category id are being updated in db, relations are either being
    // uodated or created
    protected function update(): void
    {
        $handler = $this->kindOf->getDBHandler();
        $handler->update(['id' => $this->id,
            'text' => $this->text,
            'explanation' => $this->explanation,
            'category_id' => $this->category->getId(),
            'user_id' => $_SESSION['UserId']
            ]);
        foreach ($this->rightAnswers as $answer) DBFactory::getFactory()->createRelation($this->id, $answer->id,true);
        foreach ($this->wrongAnswers as $answer) DBFactory::getFactory()->createRelation($this->id, $answer->id,false);
    }
    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'category' => $this->category,
            'explanation' => $this->explanation,
            'rightAnswers' => $this->rightAnswers,
            'wrongAnswers' => $this->wrongAnswers,
        ];
    }


}