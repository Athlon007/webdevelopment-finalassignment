<?php
class HomeController
{
    public function index() : void
    {
        if ($this->isPosting())
        {
            $this->postOpinion();
            $this->reload();
            die();
        }

        require_once("../services/SettingsService.php");
        require_once("../services/OpinionService.php");
        require_once("../services/ReactionEntityService.php");

        $settingsService = new SettingsService();
        $settings = $settingsService->getSettings();
        $topic = $settings->getSelectedTopic();

        $opinionService = new OpinionService();
        $opinions = $opinionService->getOpinionsForTopic($topic);

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

    private function postOpinion()
    {
        try {
            require_once("../services/OpinionService.php");
            if (isset($_POST["title"]) && isset($_POST["content"]) && isset($_POST["topicID"])) {
                $opinionService = new OpinionService();
                $opinionService->insertOpinion($_POST["topicID"], $_POST["title"], $_POST["content"]);
            } else {
                throw new ErrorException("Something went wrong while posting a new opinion");
            }
        } catch (Exception $ex) {
            # TODO: Improve that bit.
            echo $ex->getMessage();
        }
    }
}