<?php
require_once("../repositories/ReportRepository.php");
require_once("../models/Opinion.php");

class ReportService
{
    private ReportRepository $repo;

    public function __construct()
    {
        $this->repo = new ReportRepository();
    }

    public function createReport(Opinion $opinion, ReportType $reportType): void
    {
        $this->repo->createReport($opinion, $reportType);
    }
}
