<?php

namespace quiz;

enum UseCase:string
{
    case LOGIN_REGISTER = 'login/login';
    case WELCOME = 'welcome';
    case SELECT_QUESTIONS = 'quiz/select';
    case ANSWER_QUESTION = 'quiz/answerQuestion';
    case EDIT_QUESTION = 'edit/editQuestion';
    case SELECT_EDIT_QUESTION = 'edit/selectQuestionToEdit';
    case IMPORT = 'importExport/import';
    case EXPORT = 'importExport/export';
    case FINALIZE_QUIZ = 'quiz/finalStats';
    case CHECK_BEFORE_FINALIZE ='quiz/beforeFinal';
    case UNEXPECTED_ERROR ='error';

    public function getView():string
    {
        return $this->value;
    }

    public function getName():string
    {
        return $this->name;
    }

    public function getController():Controller
    {
        return match ($this->getName()) {
            'LOGIN_REGISTER' => new UserController(),
            'EDIT_QUESTION','IMPORT','EXPORT','SELECT_EDIT_QUESTION' => new EditController(),
            'ERROR' => new PageNotFoundController(),
            default => new QuizQuestionController()
        };
    }
}
