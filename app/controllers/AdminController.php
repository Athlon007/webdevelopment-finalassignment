<?php
require_once("../services/LoginService.php");
require_once("../services/OpinionService.php");
require_once("../models/Exceptions/NotLoggedInException.php");

class AdminController
{
    private LoginService $loginService;
    private ?Account $activeUser;

    public function __construct()
    {
        $this->loginService = new LoginService();
        $this->activeUser = $this->loginService->getCurrentlyLoggedInUser();
    }

    public function opinionsPanel()
    {
        $warnings = "";

        require_once("../services/SettingsService.php");
        $settingService = new SettingsService();

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

        $settings = $settingService->getSettings();
        $topic = null;
        try {
            if ($currentTopic == -1) {
                $topic = $settings->getSelectedTopic();
                if ($topic != null) {
                    $currentTopic = $topic->getId();
                }
            } else {
                $topic = $topicService->getTopicById($currentTopic);
            }
        } catch (IllegalOperationException $ex) {
            $warnings .= $ex->getMessage();
        }

        $opinions = array();
        if ($topic != null) {
            $opinionService = new OpinionService();
            $opinions = $opinionService->getOpinionsForTopicByNew($topic, 0, 9999999);
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
        if ($this->activeUser->getAccountType() == AccountType::Moderator) {
            header("Location: /admin");
            die();
        }
        $warnings = "";

        require_once("../services/TopicService.php");
        $topicService = new TopicService();
        $settingsService = new SettingsService();

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
                    case "force-next-topic":
                        $settingsService->forceNextTopic();
                        header("Location: /admin/topics");
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

        $activeTopic = $settingsService->getSettings()->getSelectedTopic();

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
        if ($this->activeUser->getAccountType() == AccountType::Moderator) {
            header("Location: /admin");
            die();
        }

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
                        header("Location: /admin/reactions");
                        die();
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

    public function usersPanel()
    {
        if ($this->activeUser->getAccountType() == AccountType::Moderator) {
            header("Location: /admin");
            die();
        }

        require_once("../services/LoginService.php");
        $loginService = new LoginService();
        $accounts = $loginService->getAll();
        $warnings = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["action"])) {
            if (!$this->loginService->isLoggedIn()) {
                echo "Cannot perform operation, if not logged in!";
            }

            try {
                switch ($_POST["action"]) {
                    case "edit-account":
                        if (!isset($_POST["username"]) || strlen($_POST["username"]) == 0) {
                            $warnings .= "Username is missing\n";
                        }
                        if (!isset($_POST["email"]) || strlen($_POST["email"]) == 0) {
                            $warnings .= "Email is missing\n";
                        }

                        if (!isset($_POST["type"]) || strlen($_POST["type"]) == 0) {
                            $warnings .= "Type is missing\n";
                        }

                        if (strlen($warnings) > 0) {
                            break;
                        }

                        $accountType = AccountType::getByString($_POST["type"]);
                        $loginService->editAccount($_POST["account-id"], $_POST["username"], $_POST["email"], $accountType);

                        if (isset($_POST["password"]) && strlen($_POST["password"]) > 0) {
                            $loginService->updatePassword($_POST["account-id"], $_POST["password"]);
                        }

                        header("Location: /admin/users");
                        die();
                        break;
                    case "add-account":
                        if (!isset($_POST["username"]) || strlen($_POST["username"]) == 0) {
                            $warnings .= "Username is missing\n";
                        }
                        if (!isset($_POST["email"]) || strlen($_POST["email"]) == 0) {
                            $warnings .= "Email is missing\n";
                        }

                        if (!isset($_POST["password"]) || strlen($_POST["password"]) == 0) {
                            $warnings .= "Password is missing\n";
                        }

                        if (!isset($_POST["type"]) || strlen($_POST["type"]) == 0) {
                            $warnings .= "Type is missing\n";
                        }

                        if (strlen($warnings) > 0) {
                            break;
                        }

                        $accountType = AccountType::getByString($_POST["type"]);
                        $loginService->createAccount($_POST["username"], $_POST["email"], $_POST["password"], $accountType);
                        header("Location: /admin/users");
                        die();
                        break;
                    case "delete-account":
                        if (!isset($_POST['account-id'])) {
                            $warnings .= "Account ID is missing.";
                            break;
                        }
                        $loginService->deleteAccount($_POST["account-id"]);
                        header("Location: /admin/users");
                        die();
                        break;
                    default:
                        $this->globalActions();
                        break;
                }
            } catch (IllegalOperationException $ex) {
                $warnings .= $ex->getMessage();
            } catch (PDOException $ex) {
                $warnings .= $ex->getMessage();
            }
        }

        $accountTypes = AccountType::cases();

        $activeUser = $this->activeUser;
        require("../views/admin/panel-users.php");
    }

    public function reportsPanel()
    {
        require("../models/ReportType.php");
        require("../services/ReportService.php");
        $reportTypes = ReportType::cases();

        $reportService = new ReportService();
        $warnings = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["action"])) {
            if (!$this->loginService->isLoggedIn()) {
                echo "Cannot perform operation, if not logged in!";
            }

            try {
                switch ($_POST["action"]) {
                    case "dismiss-report":
                        if (!isset($_POST["opinion-id"]) || strlen($_POST["opinion-id"]) == 0) {
                            $warnings .= "Opinion-id is missing.";
                            break;
                        }

                        $reportService->deleteReportsForOpinion($_POST["opinion-id"]);
                        header("Location: /admin/reports");
                        die();
                        break;
                    case "delete-opinion":
                        require_once("../services/OpinionService.php");
                        $opinionService = new OpinionService();
                        $opinionService->deleteById($_POST["opinion-id"]);
                        header("Location: /admin/reports");
                        die();
                        break;
                    default:
                        $this->globalActions();
                        break;
                }
            } catch (IllegalOperationException $ex) {
                $warnings .= $ex->getMessage();
            } catch (PDOException $ex) {
                $warnings .= $ex->getMessage();
            }
        }

        $opinions = $reportService->getOpinionsWithReports();

        $opinionsWithReportsCount = [];
        foreach ($opinions as $opinion) {
            $value = [
                "opinion" => $opinion
            ];

            foreach ($reportTypes as $reportType) {
                $value[$reportType->name] = $reportService->countReportsForOpinionByType($opinion, $reportType);
            }

            array_push($opinionsWithReportsCount, $value);
        }

        $activeUser = $this->activeUser;
        require("../views/admin/panel-reports.php");
    }

    public function configPanel()
    {
        if ($this->activeUser->getAccountType() == AccountType::Moderator) {
            header("Location: /admin");
            die();
        }
        $warnings = '';

        require_once("../services/SettingsService.php");
        $settingsService = new SettingsService();
        $settings = $settingsService->getSettings();

        $activeUser = $this->activeUser;
        require("../views/admin/panel-config.php");
    }

    public function settingsPanel()
    {
        if ($this->activeUser->getAccountType() == AccountType::Moderator) {
            header("Location: /admin");
            die();
        }
        $warnings = "";

        require_once("../services/SettingsService.php");
        $settingsService = new SettingsService();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->loginService->isLoggedIn()) {
                echo "Cannot perform operation, if not logged in!";
            }

            try {
                if (isset($_POST["showMaxOpinions"])) {
                    $settingsService->setMaxReactionsPerPage($_POST["showMaxOpinions"]);
                    header("Location: /admin/settings");
                    die();
                }
            } catch (IllegalOperationException $ex) {
                $warnings .= $ex->getMessage();
            } catch (PDOException $ex) {
                $warnings .= $ex->getMessage();
            }
        }

        $settings = $settingsService->getSettings();

        $activeUser = $this->activeUser;
        require("../views/admin/panel-settings.php");
    }
}
