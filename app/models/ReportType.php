<?php

require_once("Exceptions/ReportTypeMissingException.php");

class ReportType implements JsonSerializable
{
    const Hateful = "Hateful";
    const Harassment = "Harassment";
    const Misinformation = "Misinformation";
    const Spam = "Spam";

    public function asString(): string
    {
        return match ($this) {
            ReportType::Hateful => "Hateful or abusive content",
            ReportType::Harassment => "Harassment or bulying",
            ReportType::Misinformation => "Misinformation",
            ReportType::Spam => "Spam or misleading"
        };
    }

    public static function getByString(string $value)
    {
        switch ($value) {
            case "Hateful or abusive content":
                return ReportType::Hateful;
            case "Harassment or bulying":
                return ReportType::Harassment;
            case "Misinformation":
                return ReportType::Misinformation;
            case "Spam or misleading":
                return ReportType::Spam;
            default:
                throw new ReportTypeMissingException("Report type by the name '$value' does not exist.");
        }
    }

    public function jsonSerialize(): mixed
    {
        return [
            "name" => $this->asString(),
            "value" => $this
        ];
    }

    public static function asInt($input)
    {
        switch ($input) {
            case ReportType::Hateful:
                return 0;
                break;
            case ReportType::Harassment:
                return 1;
                break;
            case ReportType::Misinformation:
                return 2;
                break;
            case ReportType::Spam:
                return 3;
                break;
        }
    }

    public static function cases()
    {
        return [ReportType::Hateful, ReportType::Harassment, ReportType::Misinformation, ReportType::Spam];
    }

    public static function from(int $i)
    {
        switch ($i) {
            case 0:
                return ReportType::Hateful;
                break;
            case 1:
                return ReportType::Harassment;
                break;
            case 2:
                return ReportType::Misinformation;
                break;
            case 3:
                return ReportType::Spam;
                break;
        }
    }
}
