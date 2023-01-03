<?php
require_once("../services/LoginService.php");
require_once("../services/OpinionService.php");
require_once("../models/Exceptions/NotLoggedInException.php");
class AdminController
{
    private LoginService $loginService;
    private Account $activeUser;

    public function __construct()
    {
        $this->loginService = new LoginService();

        try {
            $this->activeUser = $this->loginService->getCurrentlyLoggedInUser();
        } catch (SessionFailException $ex) {
            $this->login();
        }
    }

    public function opinionsPanel()
    {
        $warnings = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["action"])) {
            if (!$this->loginService->isLoggedIn()) {
                echo "Cannot perform operation, if not logged in!";
            }

            try {
                switch ($_POST["action"]) {
                    case "delete-opinion":
                        if (isset($_POST["opinion-id"])) {
                            $opinionService = new OpinionService();
                            $opinionService->deleteById($_POST["opinion-id"]);
                            header("Location: /admin");
                            die();
                        } else {
                            $warnings .= "Opinion ID is missing.";
                        }
                        break;
                    case "edit-opinion":
                        if (!isset($_POST["opinion-id"]) || !isset($_POST["title"]) || !isset($_POST["content"])) {
                            $warnings .= "Title and/or content is missing.";
                        } else {
                            $opinionService = new OpinionService();
                            $opinionService->updateById($_POST["opinion-id"], $_POST["title"], $_POST["content"], $this->activeUser);
                            header("Location: /admin");
                            die();
                        }
                        break;
                    default:
                        $this->globalActions();
                        break;
                }
            } catch (OpinionAlterException $ex) {
                $warnings .= $ex->getMessage();
            } catch (IllegalOperationException $ex) {
                $warnings .= $ex->getMessage();
            }
        }

        // Get current mode.
        $mode = "opinions";
        if (isset($_GET) && isset($_GET["mode"])) {
            $mode = $_GET["mode"];
        }

        $currentTopic = -1;
        if ($mode == "opinions" && isset($_GET) && isset($_GET["topic"]) && strlen($_GET["topic"]) > 0) {
            $currentTopic = $_GET["topic"];
        }

        require_once("../services/TopicService.php");
        $topicService = new TopicService();
        $topics = $topicService->getAll();

        require_once("../services/SettingsService.php");
        $settingService = new SettingsService();
        $settings = $settingService->getSettings();
        $topic = null;
        try {
            if ($currentTopic == -1) {
                $topic = $settings->getSelectedTopic();
                $currentTopic = $topic->getId();
            } else {
                $topic = $topicService->getTopicById($currentTopic);
            }
        } catch (IllegalOperationException $ex) {
            $warnings .= $ex->getMessage();
        }

        $opinions = array();
        if ($topic != null) {
            $opinionService = new OpinionService();
            $opinions = $opinionService->getOpinionsForTopicByNew($topic);
        }

        $activeUser = $this->activeUser;
        require("../views/admin/panel.php");
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $warns = "";
            if (!isset($_POST["email"])) {
                $warns .= "Email is missing.";
            } else {
                $email = $_POST["email"];
            }

            if (!isset($_POST["password"])) {
                $warns .= "Password is missing.";
            } else {
                $password = $_POST["password"];
            }

            if (strlen($warns) > 0) {
                $warnings = $warns;
            } else {
                try {
                    $this->loginService->login($email, $password);
                    header('Location: /admin');
                    die();
                } catch (AccountNotFoundException $ex) {
                    $warnings = $ex->getMessage();
                }
            }
        }

        require("../views/admin/login.php");
    }

    public function setup()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $warns = "";
            if (!isset($_POST['username'])) {
                $warns .= "Username is not present.<br>";
            } else {
                $username = $_POST["username"];
            }

            if (!isset($_POST['email'])) {
                $warns .= "E-mail is not present.<br>";
            } else {
                $email = $_POST["email"];
            }

            if (!isset($_POST["password"])) {
                $warns .= "Password is not present.<br>";
            } else {
                $password = $_POST["password"];
            }

            if (!isset($_POST["confirm-password"])) {
                $warns .= "Password confirmation is not present.<br>";
            } else {
                $confirmPassword = $_POST["confirm-password"];
            }

            if (!$this->loginService->isPasswordValid($password)) {
                $warns .= "Password does not meet the criteria!<br>";
            }
            if (!$this->loginService->doPasswordsMatch($password, $confirmPassword)) {
                $warns .= "Passwords do not match!<br>";
            }

            if (strlen($warns) > 0) {
                $warnings = $warns;
            } else {
                require_once("../models/AccountType.php");
                try {
                    $service->createAccount($username, $email, $password, AccountType::Admin);
                    header('Location: /admin/setup-ready');
                    die();
                } catch (LoginCreationException $e) {
                    $warnings = $e->getMessage();
                }
            }
        }
        require("../views/admin/setup.php");
    }

    /**
     * Show the setup complete screen.
     */
    public function setupComplete()
    {
        require("../views/admin/setup-ready.php");
    }

    public function topicsPanel()
    {
        $warnings = "";

        require_once("../services/TopicService.php");
        $topicService = new TopicService();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["action"])) {
            if (!$this->loginService->isLoggedIn()) {
                echo "Cannot perform operation, if not logged in!";
            }

            try {
                switch ($_POST["action"]) {
                    case "edit-topic":
                        if (!isset($_POST["topic-id"]) || !isset($_POST["title"])) {
                            $warnings .= "Topic or title is missing";
                            break;
                        }
                        $topicService->editTopicTitle($_POST["topic-id"], $_POST["title"], $this->activeUser);
                        header("Location: /admin/topics");
                        die();
                        break;
                    case "add-topic":
                        if (!isset($_POST["title"])) {
                            $warnings .= "Title is missing.";
                            break;
                        }
                        $topicService->addTopic($_POST["title"]);
                        header("Location: /admin/topics");
                        die();
                        break;
                    case "delete-topic":
                        if (!isset($_POST['topic-id'])) {
                            $warnings .= "Topic ID is missing.";
                            break;
                        }
                        $topicService->deleteById($_POST["topic-id"]);
                        header("Location: /admin/topics");
                        die();
                        break;
                    default:
                        $this->globalActions();
                        break;
                }
            } catch (IllegalOperationException $ex) {
                $warnings .= $ex->getMessage();
            }
        }

        $topics = $topicService->getAll();
        $activeUser = $this->activeUser;

        require("../views/admin/panel-topics.php");
    }

    /**
     * This function handless global actions (such as loging-out).
     */
    private function globalActions()
    {
        switch ($_POST["action"]) {
            case "logout":
                $this->loginService->logout();
                header("Location: /admin");
                die();
                break;
        }
    }

    public function reactionsPanel()
    {
        require_once("../services/ReactionEntityService.php");
        $reactionService = new ReactionEntityService();

        $warnings = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["action"])) {
            if (!$this->loginService->isLoggedIn()) {
                echo "Cannot perform operation, if not logged in!";
            }

            try {
                switch ($_POST["action"]) {
                    case "edit-reaction":
                        if (!isset($_POST["reaction-id"]) || !isset($_POST["emoji"])) {
                            $warnings .= "Emoji or 'Is Negative' is missing";
                            break;
                        }
                        $reactionService->editReaction($_POST["reaction-id"], $_POST["emoji"], isset($_POST["isNegative"]));
                        header("Location: /admin/reactions");
                        die();
                        break;
                    case "add-reaction":
                        if (!isset($_POST["emoji"])) {
                            $warnings .= "Emoji or 'Is Negative' is missing.";
                            break;
                        }
                        $reactionService->addReaction($_POST["emoji"], isset($_POST["isNegative"]));
                        header("Location: /admin/reactions");
                        die();
                        break;
                    case "delete-reaction":
                        if (!isset($_POST['reaction-id'])) {
                            $warnings .= "Reaction ID is missing.";
                            break;
                        }
                        $reactionService->deleteReaction($_POST["reaction-id"]);
                        break;
                    default:
                        $this->globalActions();
                        break;
                }
            } catch (IllegalOperationException $ex) {
                $warnings .= $ex->getMessage();
            } catch (PDOException $ex) {
                $warnings .= "Cannot delete already used reactions.";
            }
        }

        $reactions = $reactionService->getAll();

        $activeUser = $this->activeUser;
        require("../views/admin/panel-reactions.php");
    }
}
