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

    public function createReport(Opinion $opinion, ReportType $reportType)
    {
        $sql = "INSERT INTO Reports (opinionID, reportType) VALUES (:opinionID, :reportType)";
        $stmt = $this->connection->prepare($sql);
        $id = $opinion->getId();
        $stmt->bindParam(":opinionID", $id, PDO::PARAM_INT);
        $reportTypeID = $reportType->value;
        $stmt->bindParam(":reportType", $reportTypeID, PDO::PARAM_INT);
        $stmt->execute();
    }
}
