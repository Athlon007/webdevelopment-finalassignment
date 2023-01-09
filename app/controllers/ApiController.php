<?php
require_once("../models/Exceptions/MissingArgumentsException.php");

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
                default:
                    $this->error("Unrecognized request: $request.");
                    break;
            }
        } catch (MissingArgumentsException $ex) {
            $this->error($ex->getMessage());
        } catch (Throwable $ex) {
            $this->error("Unhandled error");
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
            $warnings .= "Title is missing.";
        }

        if (!isset($data->content) || strlen($data->content) == 0) {
            $warnings .= "Content is missing.";
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

        $reply = ["message" => "Opinion added successfully!"];
        header($_SERVER["SERVER_PROTOCOL"] . " 201 Created", true, 201);
        echo json_encode($reply);
    }

    public function get($request)
    {
        header('Content-Type: application/json');

        try {
            if (str_starts_with($request, "/api/opinions") || str_starts_with($request, "/api/opinions-popular") || str_starts_with($request, "/api/opinions-new")) {
                $sortByNew = str_starts_with($request, "/api/opinion-new");

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
            } else {
                $this->error("Unsupported request");
            }
        } catch (MissingArgumentsException $ex) {
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

        echo json_encode($opinions);
    }
}
