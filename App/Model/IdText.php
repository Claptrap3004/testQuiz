<?php
// instantiation of this class works for answers and categories, most other classes can inherit this as base.
// kindOfText defines to which type in the DB the class refers. The Enum deals as kind of controller for choosing the
// correct implementation of CanHandleDB Interface
namespace quiz;
use JsonSerializable;
use quiz\CanConnectDB;

class IdText implements JsonSerializable
{
    protected int $id;
    protected string $text;
    protected KindOf $kindOf;

    /**
     * @param int $id
     * @param string $text
     * @param KindOf $kindOf
     */
    public function __construct(int $id, string $text, KindOf $kindOf)
    {
        $this->id = $id;
        $this->text = $text;
        $this->kindOf = $kindOf;

    }


    public function getId(): int
    {
        return $this->id;
    }


    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
        $this->update();
    }

    public function getKindOf(): KindOf
    {
        return $this->kindOf;
    }

    public function equals(IdText $idText): bool
    {
        return ($this->id === $idText->id & $this->text === $idText->text);
    }
    protected function update(): void
    {
        $handler = $this->kindOf->getDBHandler();
        $handler->update(['id' => $this->id, 'text' => $this->text]);
    }

    public function jsonSerialize(): mixed
    {
        return ['id' => $this->id, 'text' => $this->text];
    }
}