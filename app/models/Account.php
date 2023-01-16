<?php
require_once("AccountType.php");

class Account
{
    private int $id;
    private string $username;
    private string $email;
    private string $passwordHash;
    private string $salt;
    private $accountType;

    public function __construct(
        int $id,
        string $username,
        string $email,
        string $passwordHash,
        string $salt,
        $accountType
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->salt = $salt;
        $this->accountType = $accountType;
    }

    public function getID(): int
    {
        return $this->id;
    }

    public function setID(int $id): void
    {
        $this->id = $id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $value): void
    {
        $this->username = $value;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $value): void
    {
        $this->email = $value;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $value): void
    {
        $this->passwordHash = $value;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }

    public function setSalt(string $value): void
    {
        $this->salt = $value;
    }

    public function getAccountType()
    {
        return $this->accountType;
    }

    public function setAccountType($value): void
    {
        $this->accountType = $value;
    }
}
