<?php
require_once("Repository.php");
require_once("../models/Settings.php");

class SettingsRepository extends Repository
{
    public function getSettings(): Settings
    {
        require_once("../services/TopicService.php");
        $stmt = $this->connection->prepare("SELECT * FROM Settings LIMIT 1");
        $stmt->execute();

        $topicService = new TopicService();

        while ($row = $stmt->fetch()) {
            $selectedNthTopic = $topicService->getNthTopic($row["selectedNthTopic"]);
            $dateLastTopicSelected = DateTime::createFromFormat("Y-m-d", $row["dateLastTopicSelected"]);
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

    public function getSelectedNthTopic(): int
    {
        $stmt = $this->connection->prepare("SELECT selectedNthTopic FROM Settings LIMIT 1");
        $stmt->execute();
        return $stmt->fetch()["selectedNthTopic"];
    }

    public function setSelectedNthTopic(int $id, string $today): void
    {
        $stmt = $this->connection->prepare("UPDATE Settings SET selectedNthTopic = :id, dateLastTopicSelected = :today");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue("today", $today);
        $stmt->execute();
    }
}
