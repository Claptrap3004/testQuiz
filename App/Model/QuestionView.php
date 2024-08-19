<?php

namespace quiz;

use JsonSerializable;

class QuestionView implements JsonSerializable
{
    public int $id;
    public string $category;
    public string $text;

    /**
     * @param int $id
     * @param string $category
     * @param string $text
     */
    public function __construct(int $id, string $category, string $text)
    {
        $this->id = $id;
        $this->category = $category;
        $this->text = $text;
    }


    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id   ,
            'text' => $this->text,
            'category' => $this->category
        ];
    }
}