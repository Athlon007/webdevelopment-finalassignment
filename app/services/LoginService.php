<?php
require_once("../repositories/LoginRepository.php");
require_once("../models/Account.php");
require_once("../models/Exceptions/LoginCreationException.php");
require_once("../models/Exceptions/AccountNotFoundException.php");

class LoginService
{
    private LoginRepository $repo;
    public const SALT_LENGTH = 64;
    public const MAX_MINUTES_LOGGED_IN = 60;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->repo = new LoginRepository();
    }

    /**
     * Checks if user is currently logged in.
     * @return bool Returns true, if user is logged in.
     */
    public function isLoggedIn(): bool
    {
        // If the user with provided ID does not exist anymore, log out.
        if (
            !isset($_SESSION["user_id"]) ||
            !isset($_SESSION["login_timestamp"]) ||
            !$this->doesIdExist($_SESSION["user_id"])
        ) {
            $this->logout();
            return false;
        }

        $loginTime = new DateTime($_SESSION["login_timestamp"]);
        $diff = $loginTime->diff(new DateTime(date('Y-m-d H:i:s')));
        $minutesLogged = $diff->days * 24 * 60 + $diff->h * 60 + $diff->i;

        // If user has been logged in for longer than the maximum time, log him out.
        if ($minutesLogged > self::MAX_MINUTES_LOGGED_IN) {
            $this->logout();
            return false;
        }

        return true;
    }

    // Returns true, if one (or more) accounts exists.
    public function isSetup(): bool
    {
        return $this->repo->getRowsCount() > 0;
    }

    public function isPasswordValid($password): bool
    {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        return $uppercase && $lowercase && $number && $specialChars;
    }

    public function doPasswordsMatch($password, $confirmation): bool
    {
        return $password === $confirmation;
    }

    /**
     * Creates a new account.
     */
    public function createAccount(string $username, string $email, string $password, AccountType $accountType)
    {
        $errors = "";
        if ($this->doesUsernameExist($username)) {
            $errors .= "Provided username is already in use.<br>";
        }
        if ($this->doesEmailExist($email)) {
            $errors .= "Provided e-mail is alreay in use.";
        }

        if (strlen($errors) > 0) {
            throw new LoginCreationException($errors);
        }

        $username = htmlspecialchars($username);
        $email = htmlspecialchars($email);
        $password = htmlspecialchars($password);
        $salt = htmlspecialchars($this->generateSalt());
        $hash = htmlspecialchars($this->generatePasswordHash($password, $salt));

        $this->repo->insert($username, $email, $hash, $salt, $accountType);
    }

    /**
     * Generates salt.
     * @return string New salt.
     */
    private function generateSalt(): string
    {
        return random_bytes(self::SALT_LENGTH);
    }

    /**
     * Generates a new password hash using bcrypt.
     * @param string $password Password inputted by the user.
     * @param string $salt Salt that will be added to the password.
     * @return string New password hash.
     */
    private function generatePasswordHash($password, $salt): string
    {
        $options = ['cost' => 11];
        return password_hash($password . $salt, PASSWORD_BCRYPT, $options);
    }

    /**
     * Checks if username exists.
     * @param string $username Username to check for.
     * @return bool True, if username exists. False, if not.
     */
    private function doesUsernameExist(string $username): bool
    {
        $username = htmlspecialchars($username);
        return $this->repo->getRowsCountForUsername($username) > 0;
    }

    /**
     * Checks if email exists.
     * @param string $email Email to look for.
     * @return bool True, if email exists in database, false if not.
     */
    private function doesEmailExist(string $email): bool
    {
        $email = htmlspecialchars($email);
        return $this->repo->getRowsCountForEmail($email) > 0;
    }

    /**
     * Checks if ID exists.
     * @param int $id ID to look for.
     * @return bool True, if ID exists in database, false if not.
     */
    private function doesIdExist(int $id): bool
    {
        return $this->repo->getRowsCountForId($id) > 0;
    }

    /**
     * Returns an account by its email address.
     * @param string $email Email with which user should be found.
     * @return Account account object.
     * @throws AccountNotFoundException Thrown, if the account with provided e-mail does not exist.
     */
    private function getUserByEmail(string $email): Account
    {
        $email = htmlspecialchars($email);

        if (!$this->doesEmailExist($email)) {
            throw new AccountNotFoundException("An e-mail, and/or passwor do not match.");
        }

        return $this->repo->getAccountByEmail($email);
    }

    /**
     * Get the user, if email and passwords match.
     * @param string $email Email to find the account by.
     * @param string $password Password of the account.
     * @return Account Returns an account, if email and passwords match.
     * @throws AccountNotFoundException Thrown, if the account with provided email does not exist.
     */
    public function login(string $email, string $password): Account
    {
        $email = htmlspecialchars($email);
        $password = htmlspecialchars($password);

        $account = $this->getUserByEmail($email);
        if (!$this->verifyPassword($password, $account->getSalt(), $account->getPasswordHash())) {
            throw new AccountNotFoundException("An e-mail, and/or password do not match.");
        }

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION["user_id"] = $account->getID();
        $_SESSION["login_timestamp"] = date("Y-m-d H:i:s");

        return $account;
    }

    /**
     * Ends session and logs the user out.
     */
    public function logout(): void
    {
        unset($_SESSION["user_id"]);
        unset($_SESSION["login_timestamp"]);
        session_destroy();
    }

    /**
     * Verifies the password.
     * @param string $password Password to check.
     * @param string $salt Account salt.
     * @param string $hash Password hash (stored in the database).
     * @return bool Returns true, if password match.
     */
    private function verifyPassword($password, $salt, $hash): bool
    {
        return password_verify($password . $salt, $hash);
    }

    /**
     * Returns the currently logged in user account.
     * @return Account Account that is currently logged-in.
     */
    public function getCurrentlyLoggedInUser(): Account
    {
        require_once("../models/Exceptions/SessionFailException.php");
        if (!isset($_SESSION["user_id"])) {
            $this->logout();
            throw new SessionFailException("You have been logged out.");
        }

        return $this->repo->getAccountById($_SESSION["user_id"]);
    }
}
