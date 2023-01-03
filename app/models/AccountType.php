<?php
require_once("../models/Exceptions/AccountTypeMissingException.php");

enum AccountType: int
{
    case Moderator = 0;
    case Admin = 1;

    public function asString(): string
    {
        return match ($this) {
            AccountType::Moderator => "Moderator",
            AccountType::Admin => "Admin"
        };
    }

    public static function getByString(string $value): AccountType
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
}
