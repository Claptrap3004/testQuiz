<?php

namespace quiz;

use JsonSerializable;

class ContentInfos implements JsonSerializable
{
    public int $totalQuestions = 0;
    public int $actual = 0;

    public function __construct()
    {
        $this->collectInfos();
    }

    private function collectInfos(): void
    {
        $data = KindOf::QUIZCONTENT->getDBHandler()->findAll();
        $this->totalQuestions = count($data);
        foreach ($data as $item) {
            if ($item['is_actual']) $this->actual = $item['id'];
        }

    }

    public function jsonSerialize(): array
    {
        return [
            'totalQuestions' => $this->totalQuestions,
            'actual' => $this->actual
        ];
    }
}
