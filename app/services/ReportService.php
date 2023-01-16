<?php
require_once("../repositories/ReportRepository.php");
require_once("../models/Opinion.php");
require_once("../models/ReportType.php");

class ReportService
{
    private ReportRepository $repo;

    public function __construct()
    {
        $this->repo = new ReportRepository();
    }

    public function createReport(Opinion $opinion, $reportType): void
    {
        $reportType = ReportType::asInt($reportType);
        $this->repo->createReport($opinion, $reportType);
    }

    public function getOpinionsWithReports(): array
    {
        return $this->repo->getOpinionsWithReports();
    }

    public function countReportsForOpinionByType(Opinion $opinion, $reportType): int
    {
        $reportType = ReportType::asInt($reportType);
        return $this->repo->countReportsForOpinionByType($opinion, $reportType);
    }

    public function countReportsForOpinion(Opinion $opinion): int
    {
        return $this->repo->countReportsForOpinion($opinion);
    }

    public function deleteReportsForOpinion(int $opinionID)
    {
        $opinionID = htmlspecialchars($opinionID);
        $this->repo->deleteReportsForOpinion($opinionID);
    }
}
