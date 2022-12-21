<?php
require_once("../models/Opinion.php");
require_once("../repositories/OpinionRepository.php");
require_once("ReactionService.php");
require_once("SettingsService.php");
require_once("../models/Exceptions/OpinionAlterException.php");
require_once("../models/Exceptions/IllegalOperationException.php");

class OpinionService
{
    private OpinionRepository $repo;
    private SettingsService $settingsService;

    public function __construct()
    {
        $this->repo = new OpinionRepository();
        $this->settingsService = new SettingsService();
    }

    public function getOpinionsForTopicByNew(Topic $topic)
    {
        $offset = $this->getPageOffset();
        $limit = $this->getOpinionsLimit();

        $opinions = $this->repo->getOpinionsForTopic($topic, true, $offset, $limit);

        $reactionService = new ReactionService();
        for ($i = 0; $i < count($opinions); $i++) {
            $opinion = $opinions[$i];
            $opinionReactions = $reactionService->getAllForOpinion($opinion);
            $opinion->setAllReactions($opinionReactions);
            $opinions[$i] = $opinion;
        }

        return $opinions;
    }

    public function getOpinionsForTopicByPopular(Topic $topic): array
    {
        $offset = $this->getPageOffset();
        $limit = $this->getOpinionsLimit();

        $opinions = $this->repo->getOpinionsForTopicByPopularity($topic, true, $offset, $limit);

        $reactionService = new ReactionService();
        for ($i = 0; $i < count($opinions); $i++) {
            $opinion = $opinions[$i];
            $opinionReactions = $reactionService->getAllForOpinion($opinion);
            $opinion->setAllReactions($opinionReactions);
            $opinions[$i] = $opinion;
        }

        return $opinions;
    }

    public function insertOpinion(int $topicID, string $title, string $content): void
    {
        $title = htmlspecialchars($title);
        $content = htmlspecialchars($content);

        $this->repo->insertOpinion($topicID, $title, $content);
    }

    // Returns how many pages are there supposed to be for the specific topic.
    public function pagesForTopic(Topic $topic): int
    {
        $settings = $this->settingsService->getSettings();
        return ceil($this->repo->getOpinionsForTopicCount($topic) / $settings->getMaxReactionsPerPage());
    }

    public function getPageOffset()
    {
        $page = 1;
        if (isset($_GET) && isset($_GET["page"])) {
            $page = $_GET["page"];
        }

        $settings = $this->settingsService->getSettings();

        return ($page - 1) * $settings->getMaxReactionsPerPage();
    }

    public function getOpinionsLimit()
    {
        $settings = $this->settingsService->getSettings();
        return $settings->getMaxReactionsPerPage();
    }

    public function deleteById(int $id): void
    {
        $id = htmlspecialchars($id);
        $this->repo->deleteById($id);
    }

    public function updateById(int $id, string $title, string $content, Account $editingUser): void
    {
        if ($editingUser->getAccountType() != AccountType::Admin) {
            throw new IllegalOperationException("Only admins can edit opinions!");
        }

        $id = htmlspecialchars($id);
        $title = htmlspecialchars($title);
        $content = htmlspecialchars($content);

        if (strlen($title) == 0) {
            throw new OpinionAlterException("Title cannot be empty.");
        }
        if (strlen($content) == 0) {
            throw new OpinionAlterException("Content cannot be empty.");
        }

        $this->repo->update($id, $title, $content);
    }
}
