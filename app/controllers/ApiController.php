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
                default:
                    $this->error("Unrecognized request: $request.");
                    break;
            }
        } catch (MissingArgumentsException $ex) {
            $this->error("Failed ");
        } catch (Throwable $ex) {
            $this->error("Failed to send the report.");
        }
    }

    public function error($msg)
    {
        header('Content-Type: application/json');
        header($_SERVER["SERVER_PROTOCOL"] . " 405 Method Not Allowed", true, 405);

        $reply = ['error_message' => $msg];
        echo json_encode($reply);
    }
}
