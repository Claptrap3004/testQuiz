<?php

namespace quiz;

use JsonSerializable;

class LoginRegisterErrorELement implements JsonSerializable
{
    public string $input;
    public string $errorMessage;

    /**
     * @param string $input
     * @param string $errorMessage
     */
    public function __construct(string $input, string $errorMessage = '')
    {
        $this->input = $input;
        $this->errorMessage = $errorMessage;
    }

    public function jsonSerialize(): array
    {
        return [
            'input' => $this->input,
            'errorMessage' => $this->errorMessage
        ];
    }
}