<?php
require_once("Topic.php");

class Opinion implements JsonSerializable
{
    private int $id;
    private string $title;
    private string $content;
    private Topic $topic;
    private array $reactions;

    public function __construct(int $id, string $title, string $content, Topic $topic)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->topic = $topic;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $value): void
    {
        $this->id = $value;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $value): void
    {
        $this->title = $value;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $value): void
    {
        $this->content = $value;
    }

    public function getTopic(): Topic
    {
        return $this->topic;
    }

    public function setTopic(Topic $value): void
    {
        $this->topic = $value;
    }

    public function getAllReactions(): array
    {
        return $this->reactions;
    }

    public function setAllReactions(array $value): void
    {
        $this->reactions = $value;
    }

    public function jsonSerialize(): mixed
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "content" => $this->content,
            "reactions" => $this->reactions
        ];
    }
}
