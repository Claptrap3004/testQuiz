<?php


namespace quiz;

class User
{
    private int $id;
    private string $username;
    private string $email;
    private string $pwhash;

    /**
     * @param int $id
     * @param string $username
     * @param string $email
     * @param string $pwhash
     */
    public function __construct(int $id, string $username, string $email, string $pwhash)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->pwhash = $pwhash;
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->pwhash = password_hash($password, PASSWORD_BCRYPT);
    }



}