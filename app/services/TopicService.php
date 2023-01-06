<?php
require_once("../repositories/TopicRepository.php");

class TopicService
{
    private TopicRepository $repo;

    public function __construct()
    {
        $this->repo = new TopicRepository();
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function getNthTopic($n): ?Topic
    {
        return $this->repo->getNthTopic($n);
    }

    public function isAnyTopicPresent(): bool
    {
        return $this->repo->getCount() > 0;
    }

    public function editTopicTitle(int $id, string $title, Account $editingUser): void
    {
        require_once("SettingsService.php");
        $settingService = new SettingsService();
        if ($settingService->getSettings()->getSelectedTopic()->getId() == $id) {
            require_once("../models/Exceptions/IllegalOperationException.php");
            throw new IllegalOperationException("Unable to edit currently active topic.");
        }

        $id = htmlspecialchars($id);
        $title = htmlspecialchars($title);
        $this->repo->update($id, $title);
    }

    public function addTopic(string $title): void
    {
        $title = htmlspecialchars($title);

        if (strlen($title) == 0) {
            throw new IllegalOperationException("Cannot add empty topics.");
        }

        $this->repo->insert($title);
    }

    public function getTopicById(int $id): Topic
    {
        $id = htmlspecialchars($id);
        if ($this->repo->getCountById($id) == 0) {
            require_once("../models/Exceptions/IllegalOperationException.php");
            throw new IllegalOperationException("Topic $id does not exist.");
        }
        return $this->repo->getById($id);
    }

    public function deleteById(int $id): void
    {
        require_once("SettingsService.php");
        $settingsService = new SettingsService();
        if ($settingsService->getSettings()->getSelectedTopic()->getId() == $id) {
            require_once("../models/Exceptions/IllegalOperationException.php");
            throw new IllegalOperationException("Unable to delete currently active topic.");
        }
        $id = htmlspecialchars($id);
        $this->repo->delete($id);
    }

    public function getTopicCount()
    {
        return $this->repo->getCount();
    }
}
