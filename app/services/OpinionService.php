<?php
require_once("../models/Opinion.php");
require_once("../repositories/OpinionRepository.php");
require_once("ReactionService.php");

class OpinionService
{
    private OpinionRepository $repo;

    public function __construct()
    {
        $this->repo = new OpinionRepository();
    }

    public function getOpinionsForTopic(Topic $topic)
    {
        $opinions = $this->repo->getOpinionsForTopic($topic, true);

        $reactionService = new ReactionService();
        for ($i = 0; $i < count($opinions); $i++) {
            $opinion = $opinions[$i];
            $opinionReactions = $reactionService->getAllForOpinion($opinion);
            $opinion->setAllReactions($opinionReactions);
            $opinions[$i] = $opinion;
        }

        return $opinions;
    }

    public function insertOpinion(int $topicID, string $title, string $content) : void
    {
        $title = htmlspecialchars($title);
        $content = htmlspecialchars($content);

        $this->repo->insertOpinion($topicID, $title, $content);
    }
}