<?php
require_once("../models/Exceptions/MissingArgumentsException.php");
require_once("../models/Exceptions/IllegalOperationException.php");

class ApiController
{
    public function post($request)
    {
        header('Content-Type: application/json');
        try {
            switch ($request) {
                case "/api/report-opinion":
                    $data = json_decode(file_get_contents("php://input"));
                    if ($data == null) {
                        throw new MissingArgumentsException("Unable to decode JSON.");
                    }

                    $warnings = "";
                    if (!isset($data->opinion_id) || $data->opinion_id == null) {
                        $warnings .= "'opinion_id' is missing.";
                    }
                    if (!isset($data->report_type) || $data->report_type == null) {
                        $warnings .= "'report_type' is missing.";
                    }

                    if (strlen($warnings) > 0) {
                        throw new MissingArgumentsException($warnings);
                    }

                    require_once("../models/ReportType.php");
                    require_once("../services/ReportService.php");
                    require_once("../services/OpinionService.php");
                    $reportType = ReportType::from($data->report_type);

                    $opinionService = new OpinionService();
                    $opinion = $opinionService->getOpinionById($data->opinion_id);

                    $reportService = new ReportService();
                    $reportService->createReport($opinion, $reportType);

                    $reply = ['message' => 'Report has been sent!'];

                    header($_SERVER["SERVER_PROTOCOL"] . " 201 Created", true, 201);
                    echo json_encode($reply);
                    break;
                case "/api/send-opinion":
                    $data = json_decode(file_get_contents("php://input"));
                    if ($data == null) {
                        throw new MissingArgumentsException("Unable to decode JSON.");
                    }

                    $this->postOpinion($data);
                    break;
                case "/api/react-to-opinion":
                    $data = json_decode(file_get_contents("php://input"));
                    if ($data == null) {
                        throw new MissingArgumentsException("Unable to decode JSON.");
                    }

                    $this->reactToOpinion($data);
                    break;
                default:
                    $this->error("Unrecognized request: $request.");
                    break;
            }
        } catch (MissingArgumentsException $ex) {
            $this->error($ex->getMessage());
        } catch (IllegalOperationException $ex) {
            $this->error($ex->getMessage());
        } catch (Throwable $ex) {
            $this->error("Unhandled exception");
        }
    }

    public function error($msg)
    {
        header('Content-Type: application/json');
        header($_SERVER["SERVER_PROTOCOL"] . " 405 Method Not Allowed", true, 405);

        $reply = ['error_message' => $msg];
        echo json_encode($reply);
    }

    private function postOpinion($data)
    {
        $warnings = '';
        if (!isset($data->title) || strlen($data->title) == 0) {
            $warnings .= "Title is missing.\n";
        }

        if (!isset($data->content) || strlen($data->content) == 0) {
            $warnings .= "Content is missing.\n";
        }

        if (strlen($warnings) > 0) {
            throw new MissingArgumentsException($warnings);
        }

        require_once("../services/SettingsService.php");
        require_once("../services/OpinionService.php");
        $settingsService = new SettingsService();
        $settings = $settingsService->getSettings();
        $currentTopic = $settings->getSelectedTopic();

        $opinionService = new OpinionService();
        $opinionService->insertOpinion($currentTopic->getId(), $data->title, $data->content);

        $reply = [
            "message" => "Opinion sent successfully!",
            "topic" => $currentTopic
        ];
        header($_SERVER["SERVER_PROTOCOL"] . " 201 Created", true, 201);
        echo json_encode($reply);
    }

    public function get($request)
    {
        header('Content-Type: application/json');

        try {
            if (str_starts_with($request, "/api/opinions") || str_starts_with($request, "/api/opinions-popular") || str_starts_with($request, "/api/opinions-new")) {
                $sortByNew = str_starts_with($request, "/api/opinions-new");

                $topic = null;
                if (is_numeric(basename($request))) {
                    require_once("../services/TopicService.php");
                    $topicService = new TopicService();

                    if (!$topicService->isTopicWithIdPresent(basename($request))) {
                        $this->error("Topic does not exist or has been removed.");
                        return;
                    }

                    $topic = $topicService->getTopicById(basename($request));
                } else {
                    require_once("../services/SettingsService.php");
                    $settingsService = new SettingsService();
                    $topic = $settingsService->getSettings()->getSelectedTopic();
                }

                $this->printOpinions($topic, $sortByNew);
            } elseif ($request == '/api/topics') {
                $this->printTopics();
            } elseif (str_starts_with($request, '/api/topic')) {
                $this->printTopic($request);
            } elseif (str_starts_with($request, '/api/reaction-entities')) {
                $this->printReactionEntities();
            } elseif (str_starts_with($request, '/api/report-types')) {
                $this->printReportTypes();
            } else {
                $this->error("Unsupported request");
            }
        } catch (MissingArgumentsException $ex) {
            $this->error($ex->getMessage());
        } catch (IllegalOperationException $ex) {
            $this->error($ex->getMessage());
        } catch (Throwable $ex) {
            $this->error("Unhandled error");
        }
    }

    private function printOpinions(Topic $topic, bool $byNew = false)
    {
        require_once("../services/OpinionService.php");

        $opinionService = new OpinionService();
        $opinions = $byNew ? $opinionService->getOpinionsForTopicByNew($topic) : $opinionService->getOpinionsForTopicByPopular($topic);

        $data = [
            "topic" => $topic,
            "pages" => $opinionService->pagesForTopic($topic),
            "opinions" => $opinions
        ];

        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
        echo json_encode($data);
    }

    private function printTopics()
    {
        require_once("../services/TopicService.php");
        $topicService = new TopicService();
        $topics = $topicService->getAll();

        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
        echo json_encode($topics);
    }

    private function printTopic($request)
    {
        $topic = null;
        if (is_numeric(basename($request))) {
            require_once("../services/TopicService.php");
            $topicService = new TopicService();
            $topic = $topicService->getTopicById(basename($request));
        } else {
            require_once("../services/SettingsService.php");
            $settingsService = new SettingsService();
            $topic = $settingsService->getSettings()->getSelectedTopic();
        }

        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
        echo json_encode($topic);
    }

    private function printReactionEntities()
    {
        require_once("../services/ReactionEntityService.php");
        $reactionEntityService = new ReactionEntityService();
        $entities = $reactionEntityService->getAll();

        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
        echo json_encode($entities);
    }

    private function printReportTypes()
    {
        require_once("../models/ReportType.php");
        $reportTypes = ReportType::cases();

        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
        echo json_encode($reportTypes);
    }

    private function reactToOpinion($data)
    {
        if (!isset($data->opinion_id) || !is_numeric($data->opinion_id)) {
            throw new IllegalOperationException("Opinion ID is not set or not a number.");
        }

        if (!isset($data->reaction_id) || !is_numeric($data->reaction_id)) {
            throw new IllegalOperationException("Reaction ID is missing or not a number.");
        }

        require_once("../services/ReactionService.php");
        $reactionService = new ReactionService();
        $opinionID = $data->opinion_id;
        $reactionID = $data->reaction_id;
        $reactionService->addReaction($opinionID, $reactionID);

        require_once("../services/OpinionService.php");
        $opinionService = new OpinionService();
        $opinion = $opinionService->getOpinionById($opinionID);

        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK", true, 200);
        $data = [
            "message" => "Added opinion successfully!",
            "opinion" => $opinion
        ];
        echo json_encode($data);
    }
}
