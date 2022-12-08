<?php
class Topic
{
    private int $id;
    private string $name;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function setId($value) : void
    {
        $this->id = $value;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName($value): void
    {
        $this->name = $value;
    }
}