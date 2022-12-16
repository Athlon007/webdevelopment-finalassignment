<?php
require_once("../services/LoginService.php");
require_once("../services/OpinionService.php");
require_once("../models/Exceptions/NotLoggedInException.php");
class AdminController
{
    public function index()
    {
        $service = new LoginService();
        $activeUser = $service->getCurrentlyLoggedInUser();

        $warnings = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["action"])) {
            if (!$service->isLoggedIn()) {
                echo "Cannot perform operation, if not logged in!";
            }

            try {
                switch ($_POST["action"]) {
                    case "logout":
                        $service->logout();
                        header("Location: /admin");
                        die();
                        break;
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
                            $opinionService->updateById($_POST["opinion-id"], $_POST["title"], $_POST["content"]);
                            header("Location: /admin");
                            die();
                        }
                        break;
                }
            } catch (OpinionAlterException $ex) {
                $warnings .= $ex->getMessage();
            }
        }

        // Get current mode.
        $mode = "opinions";
        if (isset($_GET) && isset($_GET["mode"])) {
            $mode = $_GET["mode"];
        }

        $currentTopic = -1;
        if ($mode == "opinions" && isset($_GET) && isset($_GET["topic"])) {
            $currentTopic = $_GET["topic"];
        }

        require_once("../services/TopicService.php");
        $topicService = new TopicService();
        $topics = $topicService->getAll();

        $topic = null;
        if ($currentTopic == -1) {
            require_once("../services/SettingsService.php");
            $settingService = new SettingsService();
            $settings = $settingService->getSettings();
            $topic = $settings->getSelectedTopic();
        } else {
            // TODO
        }
        $opinionService = new OpinionService();
        $opinions = $opinionService->getOpinionsForTopicByNew($topic);

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
                $service = new LoginService();
                try {
                    $service->login($email, $password);
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
        $service = new LoginService();

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

            if (!$service->isPasswordValid($password)) {
                $warns .= "Password does not meet the criteria!<br>";
            }
            if (!$service->doPasswordsMatch($password, $confirmPassword)) {
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
}
