<?php
require_once("ReportType.php");
require_once("Opinion.php");

class Report
{
    private int $id;
    private Opinion $opinion;
    private $reportType;

    public function __construct(int $id, Opinion $opinion, $reportType)
    {
        $this->id = $id;
        $this->opinion = $opinion;
        $this->reportType = $reportType;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $value): void
    {
        $this->id = $value;
    }

    public function getOpinion(): Opinion
    {
        return $this->opinion;
    }

    public function setOpinion(Opinion $opinion): void
    {
        $this->opinion = $opinion;
    }

    public function getReportType()
    {
        return $this->reportType;
    }

    public function setReportType($value): void
    {
        $this->reportType = $value;
    }
}
