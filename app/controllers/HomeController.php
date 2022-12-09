<?php
class HomeController
{
    public function index() : void
    {
        if ($this->isPosting())
        {
            $this->handlePost();
            $this->reload();
            die();
        }

        require_once("../services/SettingsService.php");
        require_once("../services/OpinionService.php");
        require_once("../services/ReactionEntityService.php");

        $sortby = "popular";
        if (isset($_GET) && isset($_GET["sortby"]) && $_GET["sortby"] == "new") {
            $sortby = "new";
        }

        $settingsService = new SettingsService();
        $settings = $settingsService->getSettings();
        $topic = $settings->getSelectedTopic();

        $opinionService = new OpinionService();
        $opinions = $sortby == "popular"
        ? $opinionService->getOpinionsForTopicByPopular($topic)
        : $opinionService->getOpinionsForTopicByNew($topic);

        $currentPage = 1; // TODO: Update current page number.
        $pagesCount = $opinionService->pagesForTopic($topic);

        $reactionEntityService = new ReactionEntityService();
        $reactionEntites = $reactionEntityService->getAll();

        require("../views/home/index.php");
    }

    private function isPosting() : bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    private function reload() : void
    {
        header('Location: /');
        die();
    }

    private function handlePost()
    {
        try {
            if (isset($_POST["title"]) && isset($_POST["content"]) && isset($_POST["topicID"])) {
                require_once("../services/OpinionService.php");
                $opinionService = new OpinionService();
                $opinionService->insertOpinion($_POST["topicID"], $_POST["title"], $_POST["content"]);
            } elseif (isset($_POST["actionType"])) {
                $this->reactToOpinion();
            } else {
                throw new ErrorException("Something went wrong while posting a new opinion");
            }
        } catch (Exception $ex) {
            # TODO: Improve that bit.
            print_r($_POST);
            echo $ex->getMessage();
        }
    }

    private function reactToOpinion()
    {
        if (!(isset($_POST["actionType"]) && isset($_POST["opinionID"]) && isset($_POST["reactionID"]))) {
            throw new ErrorException("Missing parameters for reaction.");
        }

        require_once("../services/ReactionService.php");
        $opinionID = $_POST["opinionID"];
        $reactionID = $_POST["reactionID"];
        $reactionService = new ReactionService();
        $reactionService->addReaction($opinionID, $reactionID);
    }
}