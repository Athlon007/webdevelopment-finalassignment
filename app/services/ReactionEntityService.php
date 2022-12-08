<?php
require_once("../models/ReactionEntity.php");
require_once("../repositories/ReactionEntityRepository.php");

class ReactionEntityService
{
    private ReactionEntityRepository $repo;

    public function __construct()
    {
        $this->repo = new ReactionEntityRepository();
    }

    public function getAll() : array
    {
        return $this->repo->getAll();
    }

    public function getById(int $id) : ReactionEntity
    {
        return $this->repo->getById($id);
    }
}