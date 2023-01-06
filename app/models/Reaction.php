<?php
require_once("ReactionEntity.php");
require_once("Opinion.php");

class Reaction implements JsonSerializable
{
    private int $id;
    private ReactionEntity $reactionEntity;
    private Opinion $opinion;
    private int $count;

    public function __construct(int $id, ReactionEntity $reactionEntity, Opinion $opinion, int $count)
    {
        $this->id = $id;
        $this->reactionEntity = $reactionEntity;
        $this->opinion = $opinion;
        $this->count = $count;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $value): void
    {
        $this->id = $value;
    }

    public function getReactionEntity(): ReactionEntity
    {
        return $this->reactionEntity;
    }

    public function setReactionEntity(ReactionEntity $value): void
    {
        $this->reactionEntity = $value;
    }

    public function getOpinion(): Opinion
    {
        return $this->opinion;
    }

    public function setOpinion(Opinion $value): void
    {
        $this->opinion = $value;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $value): void
    {
        $this->count = $value;
    }

    public function jsonSerialize(): mixed
    {
        return [
            "id" => $this->id,
            "reaction_entity" => $this->reactionEntity,
            "count" => $this->count
        ];
    }
}
