<?php

namespace quiz;

use JsonSerializable;

class LoginRegisterError implements JsonSerializable
{
    public ?LoginRegisterErrorELement $email = null;
    public ?LoginRegisterErrorELement $emailValidate = null;
    public ?LoginRegisterErrorELement $password = null;
    public ?LoginRegisterErrorELement $passwordValidate = null;
    public ?LoginRegisterErrorELement $username = null;
    public ?LoginRegisterErrorELement $fatal = null;

    public bool $isLoginError = true;

    public function isNoError():bool
    {
        return
            $this->email === null &&
            $this->emailValidate === null &&
            $this->password === null &&
            $this->passwordValidate === null &&
            $this->username === null &&
            $this->fatal === null;

    }

    public function jsonSerialize(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
            'username' => $this->username,
            'emailValidate' => $this->emailValidate,
            'passwordValidate' => $this->passwordValidate,
            'fatal' => $this->fatal,
            'isLoginError' => $this->isLoginError
        ];
    }
}