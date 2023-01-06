<?php
// This class stores information about one of allowed reactions.
class ReactionEntity implements JsonSerializable
{
    private int $id;
    private string $htmlEntity;
    private bool $isNegativeOpinion;

    public function __construct(int $id, string $htmlEntity, bool $isNegativeOpinion)
    {
        $this->id = $id;
        $this->htmlEntity = $htmlEntity;
        $this->isNegativeOpinion = $isNegativeOpinion;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $value): void
    {
        $this->id = $value;
    }

    public function getHtmlEntity(): string
    {
        return $this->htmlEntity;
    }

    public function setHtmlEntity(string $value): void
    {
        $this->htmlEntity = $value;
    }

    public function getIsNegativeOpinion(): bool
    {
        return $this->isNegativeOpinion;
    }

    public function setIsNegativeOpinion(bool $value): void
    {
        $this->isNegativeOpinion = $value;
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
