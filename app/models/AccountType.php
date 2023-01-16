<?php
require_once("../models/Exceptions/AccountTypeMissingException.php");

class AccountType
{
    const Moderator = "Moderator";
    const Admin = "Admin";

    public static function from(int $i)
    {
        switch ($i) {
            case 0:
                return AccountType::Moderator;
                break;
            case 1:
                return AccountType::Admin;
                break;
            default:
                throw new AccountTypeMissingException("No $i account type exists.");
        }
    }

    public function asString(): string
    {
        return match ($this) {
            AccountType::Moderator => "Moderator",
            AccountType::Admin => "Admin"
        };
    }

    public static function getByString(string $value)
    {
        switch ($value) {
            case "Moderator":
                return AccountType::Moderator;
            case "Admin":
                return AccountType::Admin;
            default:
                throw new AccountTypeMissingException("Account type by the name '$value' does not exist.");
        }
    }

    public static function cases()
    {
        return [AccountType::Moderator, AccountType::Admin];
    }

    public static function asInt($input)
    {
        switch ($input) {
            case AccountType::Moderator:
                return 0;
                break;
            case AccountType::Admin:
                return 1;
                break;
        }

        throw new IllegalOperationException("FUGGG");
    }
}
