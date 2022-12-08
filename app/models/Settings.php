<?php

require("Topic.php");

class Settings
{
    private Topic $selectedTopic;
    private $dateLastTopicSelected;
    private int $hideOptionsWithNReports;

    public function __construct(Topic $selectedTopic, $dateLastTopicSelected, int $hideOptionsWithNReports)
    {
        $this->selectedTopic = $selectedTopic;
        $this->dateLastTopicSelected = $dateLastTopicSelected;
        $this->hideOptionsWithNReports = $hideOptionsWithNReports;
    }

    public function getSelectedTopic() : Topic
    {
        return $this->selectedTopic;
    }

    public function setSelectedTopic(Topic $value) : void
    {
        $this->selectedTopic = $value;
    }

    public function getDateLastTopicSelected() : DateTime
    {
        return $this->dateLastTopicSelected;
    }

    public function setDateLastTopicSelected(DateTime $value) : void
    {
        $this->dateLastTopicSelected = $value;
    }

    public function getHideOptionsWithNReports() : int
    {
        return $this->hideOptionsWithNReports;
    }

    public function setHideOptionsWithNReports(int $value) : void
    {
        $this->hideOptionsWithNReports = $value;
    }
}