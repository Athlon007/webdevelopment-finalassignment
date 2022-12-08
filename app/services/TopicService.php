<?php
require_once("../repositories/TopicRepository.php");

class TopicService {
    private TopicRepository $repo;

    public function __construct()
    {
        $this->repo = new TopicRepository();
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function getNthTopic($n) : Topic
    {
        return $this->repo->getNthTopic($n);
    }
}
