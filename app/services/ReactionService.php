<?php

require_once("../models/Reaction.php");
require_once("../models/Opinion.php");
require_once("../repositories/ReactionRepository.php");

class ReactionService
{
    private ReactionRepository $repo;

    public function __construct()
    {
        $this->repo = new ReactionRepository();
    }

    public function getAllForOpinion(Opinion $opinion) : array
    {
        return $this->repo->getAllForOpinion($opinion);
    }
}