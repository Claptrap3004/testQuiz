<?php

namespace quiz;

use Exception;

class UserController extends Controller
{
    public function index(): void
    {
        $this->login();
    }

    public function logout(): void
    {
        unset($_SESSION['UserId']);
        $this->index();
    }

    /**
     * deals with both login and register, on correct login or new successful registration user is redirected to
     * welcome page, else encountered errors are reported on login page
     * @return void
     */
    public function login(): void
    {
        $pwLog = password_hash('quizAdmin', PASSWORD_BCRYPT);
        file_put_contents('pwHash.log',$pwLog);
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // user tries to log in
            if (isset($_POST['loginUser'])) {
                $error = $this->checkErrorsLogin($email, $password);
                if ($error->isNoError()) $this->successfulLogin($email);
                else $this->reportError($error);
            }
            // user tries to register
            elseif (isset($_POST['registerUser'])) {
                $emailValidate = $_POST['emailValidate'] ?? '';
                $passwordValidate = $_POST['passwordValidate'] ?? '';
                $userName = $_POST['userName'] ?? '';

                $error = $this->checkErrorsRegister($email, $emailValidate, $password, $passwordValidate, $userName);
                if ($error->isNoError()) $this->successfulRegister($userName,$email,$password);
                else $this->reportError($error);
            }
            // invalid post request
            else $this->view(UseCase::LOGIN_REGISTER->getView(), []);
        // no post request
        }
        else $this->view(UseCase::LOGIN_REGISTER->getView(), []);
    }

    private function successfulLogin(string $email):void{
        $userData = KindOf::USER->getDBHandler()->findAll(['userEmail' => $email]);
        $user = $this->factory->createUser($userData[0]['id']);
        $_SESSION['UserId'] = $user->getId();
        UseCase::WELCOME->getController()->index();
    }

    private function successfulRegister(string $userName, string $email, string $password):void
    {
        try {
            $id = $this->dbFactory->createUser($userName, $email, $password);
            $user = Factory::getFactory()->createUser($id);
            $_SESSION['UserId'] = $user->getId();
            KindOf::QUIZCONTENT->getDBHandler()->createTables();
            $stats = new UserStats($user);
            $this->view(UseCase::WELCOME->getView(), ['user' => $user, 'stats' => $stats]);
        } catch (Exception $e) {
            $error = new LoginRegisterError();
            $error->isLoginError = false;
            $error->fatal = ErrorMessage::USER_CREATE_FAILS->getErrorElement();
            $this->reportError($error);
        }
    }

    /**
     * creates LoginRegisterError object depending on email is valid format, user does exist and password fulfilling
     * requirements amd being correct
     * @param string $email
     * @param string $password
     * @return LoginRegisterError
     */
    private function checkErrorsLogin(string $email, string $password): LoginRegisterError
    {
        $error = new LoginRegisterError();
        if (!$this->checkCorrectEmail($email)) $error->email = ErrorMessage::EMAIL_INCORRECT->getErrorElement($email);
        elseif (!$this->userExists($email)) $error->email = ErrorMessage::USER_DOES_NOT_EXIST->getErrorElement($email);
        if (!$this->validatePassword($password)) $error->password = ErrorMessage::PASSWORD_INVALID->getErrorElement();
        elseif (!$this->checkCorrectPassword($email, $password)) $error->email = ErrorMessage::CREDENTIALS_INVALID->getErrorElement($email);
        if ($error->email === null) $error->email = ErrorMessage::EMAILS_NOT_MATCH->getNoErrorElement($email);
        return $error;
    }

    /**
     * creates LoginRegisterError object depending on email is valid format, user does exist and password fulfilling
     * requirements amd being correct as well as validated input of email and password matching their counterparts
     * @param string $email
     * @param string $emailValidate
     * @param string $password
     * @param string $passwordValidate
     * @param string $userName
     * @return LoginRegisterError
     */
    private function checkErrorsRegister(string $email, string $emailValidate, string $password, string $passwordValidate, string $userName): LoginRegisterError
    {
        $error = new LoginRegisterError();
        $error->isLoginError = false;
        if (!$this->checkCorrectEmail($email) || $this->userExists($email)) $error->email = ErrorMessage::EMAIL_INCORRECT->getErrorElement($email);
        elseif (!$this->validateEqual($email, $emailValidate)) {
            $error->email = ErrorMessage::EMAILS_NOT_MATCH->getErrorElement($email);
            $error->emailValidate = ErrorMessage::EMAILS_NOT_MATCH->getErrorElement($emailValidate);
        }
        if (!$this->validatePassword($password)) $error->password = ErrorMessage::PASSWORD_INVALID->getErrorElement();
        elseif (!$this->validateEqual($password, $passwordValidate)) {
            $error->password = ErrorMessage::PASSWORDS_NOT_MATCH->getErrorElement();
            $error->passwordValidate = ErrorMessage::PASSWORDS_NOT_MATCH->getErrorElement();
        }
        if ($error->username === null) $error->username = ErrorMessage::USER_CREATE_FAILS->getNoErrorElement($userName);
        if ($error->email === null) $error->email = ErrorMessage::EMAILS_NOT_MATCH->getNoErrorElement($email);
        if ($error->emailValidate === null) $error->emailValidate = ErrorMessage::EMAILS_NOT_MATCH->getNoErrorElement($emailValidate);
        return $error;
    }

    /**
     * if error on inputs did occure the LoginRegisterError object is send to login page
     * @param LoginRegisterError $error
     * @return void
     */
    private function reportError(LoginRegisterError $error): void
    {
        $error = json_encode($error);
        $this->view(UseCase::LOGIN_REGISTER->getView(), ['error' => $error]);
    }

    private function validatePassword(string $password): bool
    {

        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        return strlen($password) >=8;
//        return !(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8);
    }

    private function validateEqual(string $first, string $second): bool
    {
        return $first === $second;
    }

    private function checkCorrectEmail(string $email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    private function userExists($email): bool
    {
        $possibleUser = DBHandlerProvider::getUserDBHandler()->findAll(['userEmail' => $email]);
        return $possibleUser !== [];
    }

    private function checkCorrectPassword(string $email, string $password): bool
    {
        $possibleUser = DBHandlerProvider::getUserDBHandler()->findAll(['userEmail' => $email]);
        return !($possibleUser === []) && password_verify($password, $possibleUser[0]['password']);
    }
}