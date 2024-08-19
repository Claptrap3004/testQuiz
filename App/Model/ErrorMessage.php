<?php

namespace quiz;

enum ErrorMessage:string
{
    case USER_DOES_NOT_EXIST = 'User does not exist';
    case USER_CREATE_FAILS = 'cannot create user';
    case EMAILS_NOT_MATCH = 'email and validate email do not match';
    case EMAIL_INCORRECT = 'email is not a valid email';
    case PASSWORDS_NOT_MATCH = 'password and validate password do not match';
    case PASSWORD_INVALID = 'Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.';
    case CREDENTIALS_INVALID = 'email or password wrong';
    case SOMETHING_WENT_WRONG = 'something went wrong';


    public function getErrorElement(string $input = ''):LoginRegisterErrorELement
    {
        return new LoginRegisterErrorELement($input,$this->value);

    }

}
