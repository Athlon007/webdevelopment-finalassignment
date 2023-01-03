<?php
require_once("../models/ReactionEntity.php");
require_once("../repositories/ReactionEntityRepository.php");

class ReactionEntityService
{
    private ReactionEntityRepository $repo;

    public function __construct()
    {
        $this->repo = new ReactionEntityRepository();
    }

    public function getAll(): array
    {
        return $this->repo->getAll();
    }

    public function getById(int $id): ReactionEntity
    {
        return $this->repo->getById($id);
    }

    public function editReaction(int $id, string $emoji, bool $isNegative): void
    {
        $id = htmlspecialchars($id);
        $emoji = $this->convertEmojiToHtmlEntity($emoji);
        $isNegative = htmlspecialchars($isNegative);

        $this->repo->update($id, $emoji, $isNegative);
    }

    // Taken from: https://stackoverflow.com/questions/34956163/htmlentites-not-working-for-emoji
    private function convertEmojiToHtmlEntity($emoji)
    {
        $hex = preg_replace_callback('/[\x{80}-\x{10FFFF}]/u', function ($m) {
            $char = current($m);
            $utf = iconv('UTF-8', 'UCS-4', $char);
            return sprintf("&#x%s;", ltrim(strtoupper(bin2hex($utf)), "0"));
        }, $emoji);

        return $hex;
    }

    public function addReaction(string $emoji, bool $isNegative): void
    {
        $emoji = $this->convertEmojiToHtmlEntity($emoji);
        $isNegative = htmlspecialchars($isNegative);
        $this->repo->insert($emoji, $isNegative);
    }

    public function deleteReaction(int $id): void
    {
        $id = htmlspecialchars($id);
        $this->repo->delete($id);
    }
}
