<?php
require_once("Repository.php");
require_once("../models/Settings.php");

class SettingsRepository extends Repository {
    public function getSettings() : Settings {
        require_once("../services/TopicService.php");
        $stmt = $this->connection->prepare("SELECT * FROM Settings LIMIT 1");
        $stmt->execute();

        $topicService = new TopicService();

        while ($row = $stmt->fetch()) {
            $selectedNthTopic = $topicService->getNthTopic($row["selectedNthTopic"]);
            $dateLastTopicSelected = $row["dateLastTopicSelected"];
            $hideOpinionsWithNReports = $row["hideOpinionsWithNReports"];
            $maxReactionsPerPage = $row["maxReactionsPerPage"];
        }

        return new Settings(
            $selectedNthTopic,
            $dateLastTopicSelected,
            $hideOpinionsWithNReports,
            $maxReactionsPerPage
        );
    }
}