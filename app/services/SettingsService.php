<?php
require_once("../repositories/SettingsRepository.php");

class SettingsService
{
    private SettingsRepository $repo;

    public function __construct()
    {
        $this->repo = new SettingsRepository();
    }

    public function getSettings() : Settings
    {
        return $this->repo->getSettings();
    }

    public function isTimeToChangeTopic() : bool
    {
        $today = date('y-m-d');
        $settings = $this->getSettings();
        $lastTimeUpdated = $settings->getDateLastTopicSelected();
        return $today > $lastTimeUpdated;
    }
}