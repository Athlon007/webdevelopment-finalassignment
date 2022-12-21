<?php
require_once("../repositories/SettingsRepository.php");

class SettingsService
{
    private SettingsRepository $repo;

    public function __construct()
    {
        $this->repo = new SettingsRepository();
        if ($this->isTimeToChangeTopic()) {
            $this->changeTopicToNext();
        }
    }

    public function getSettings(): Settings
    {
        return $this->repo->getSettings();
    }

    public function isTimeToChangeTopic(): bool
    {
        $today = DateTime::createFromFormat('y-m-d', date('y-m-d'));
        $settings = $this->getSettings();
        $lastTimeUpdated = $settings->getDateLastTopicSelected();
        $result = $today > $lastTimeUpdated;
        return $result;
    }

    private function changeTopicToNext()
    {
        $settings = $this->getSettings();
        require_once("TopicService.php");
        $topicService = new TopicService();

        $nextID = $this->repo->getSelectedNthTopic() + 1;
        $topicsCount = $topicService->getTopicCount();
        if ($nextID >= $topicsCount) {
            // Do not overflow the selected opinion, instead go back to the beginning.
            $nextID = 0;
        }

        $this->repo->setSelectedNthTopic($nextID, date('y-m-d'));
    }
}
