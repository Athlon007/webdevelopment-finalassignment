<?php

require_once("Exceptions/ReportTypeMissingException.php");

enum ReportType: int
{
    case Hateful = 0;
    case Harassment = 1;
    case Misinformation = 2;
    case Spam = 3;

    public function asString(): string
    {
        return match ($this) {
            ReportType::Hateful => "Hateful or abusive content",
            ReportType::Harassment => "Harassment or bulying",
            ReportType::Misinformation => "Missinformation",
            ReportType::Spam => "Spam or misleading"
        };
    }

    public static function getByString(string $value): ReportType
    {
        switch ($value) {
            case "Hateful or abusive content":
                return ReportType::Hateful;
            case "Harassment or bulying":
                return ReportType::Harassment;
            case "Missinformation":
                return ReportType::Misinformation;
            case "Spam or misleading":
                return ReportType::Spam;
            default:
                throw new ReportTypeMissingException("Report type by the name '$value' does not exist.");
        }
    }
}
