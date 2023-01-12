<?php
class HomeController
{
    public function index(): void
    {
        if ($this->isPosting()) {
            $this->handlePost();
        }

        $sortby = "popular";
        if (isset($_GET) && isset($_GET["sortby"]) && $_GET["sortby"] == "new") {
            $sortby = "new";
        }

        require("../views/home/index.php");
    }

    private function isPosting(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    private function reload($message = ""): void
    {
        $str = 'Location: /';
        if (strlen($message) > 0) {
            $str .= "?message=$message";
        }
        header($str);
        die();
    }

    private function handlePost()
    {
        try {
            if (isset($_POST["title"]) && isset($_POST["content"]) && isset($_POST["topicID"])) {
                require_once("../services/OpinionService.php");
                $opinionService = new OpinionService();
                $opinionService->insertOpinion($_POST["topicID"], $_POST["title"], $_POST["content"]);
                $this->reload("Message sent succesfully!");
            } elseif (isset($_POST["actionType"])) {
                $this->reactToOpinion();
                $this->reload("Reaction added!");
            } else {
                throw new ErrorException("Something went wrong while posting a new opinion");
            }
        } catch (Exception $ex) {
            $this->reload($ex->getMessage());
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

    private function getCurrentPage()
    {
        if (!(isset($_GET) && isset($_GET["page"]))) {
            return 1;
        }

        return $_GET["page"];
    }
}
