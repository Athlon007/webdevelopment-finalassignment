<?php
require_once("Repository.php");
require_once("../models/Report.php");
require_once("../models/ReportType.php");

class ReportRepository extends Repository
{
    private function reportBuilder($array, Opinion $opinion)
    {
        $output = array();
        foreach ($array as $row) {
            $report = new Report($row["id"], $opinion, ReportType::from($row["reportType"]));
            array_push($output, $report);
        }

        return $output;
    }

    public function selectAllForOpinion(Opinion $opinion): array
    {
        $sql = "SELECT id, reportType FROM Reports WHERE opinionID = :opinionID";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":opinionID", $opinion->getId(), PDO::PARAM_INT);
        $stmt->execute();
        return $this->reportBuilder($stmt->fetchAll(), $opinion);
    }

    public function createReport(Opinion $opinion, ReportType $reportType): void
    {
        $sql = "INSERT INTO Reports (opinionID, reportType) VALUES (:opinionID, :reportType)";
        $stmt = $this->connection->prepare($sql);
        $id = $opinion->getId();
        $stmt->bindParam(":opinionID", $id, PDO::PARAM_INT);
        $reportTypeID = $reportType->value;
        $stmt->bindParam(":reportType", $reportTypeID, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getOpinionsWithReports(): array
    {
        $sql = "SELECT UNIQUE(r.opinionID) AS OpinionID, "
            . "o.title AS OpinionTitle, o.content AS OpinionContent, "
            . "t.id AS TopicID, t.name AS TopicName, "
            . "COUNT(r.opinionID) AS reportCount "
            . "FROM Reports r "
            . "JOIN Opinions o ON r.opinionID = o.id "
            . "JOIN Topics t ON t.id = o.topicID "
            . "HAVING reportCount > 0 "
            . "ORDER BY reportCount";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $this->buildOpinions($stmt->fetchAll());
    }

    private function buildOpinions($array): array
    {
        if (count($array) == 0) {
            return array();
        }

        require_once("../models/Opinion.php");
        require_once("../models/Topic.php");

        $topic;

        $output = array();
        foreach ($array as $row) {
            if (!isset($topic)) {
                $topic = new Topic($row["TopicID"], $row["TopicName"]);
            }

            $opinion = new Opinion($row["OpinionID"], $row["OpinionTitle"], $row["OpinionContent"], $topic);
            array_push($output, $opinion);
        }

        return $output;
    }

    public function countReportsForOpinionByType(Opinion $opinion, ReportType $reportType)
    {
        $sql = "SELECT COUNT(id) AS occurences FROM Reports WHERE reportType = :reportType AND opinionID = :opinionID";
        $stmt = $this->connection->prepare($sql);
        $reportTypeAsNumber = $reportType->value;
        $opinionID = $opinion->getId();
        $stmt->bindParam(":reportType", $reportTypeAsNumber, PDO::PARAM_INT);
        $stmt->bindParam(":opinionID", $opinionID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch()["occurences"];
    }

    public function countReportsForOpinion(Opinion $opinion)
    {
        $sql = "SELECT COUNT(id) AS occurences FROM Reports WHERE opinionID = :opinionID";
        $stmt = $this->connection->prepare($sql);
        $opinionID = $opinion->getId();
        $stmt->bindParam(":opinionID", $opinionID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch()["occurences"];
    }

    public function deleteReportsForOpinion(int $opinionID)
    {
        $sql = "DELETE FROM Reports WHERE opinionID = :opinionID";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":opinionID", $opinionID, PDO::PARAM_INT);
        $stmt->execute();
    }
}
