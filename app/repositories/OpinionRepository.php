<?php
require_once("../models/Opinion.php");
require_once("../models/Topic.php");
require_once("Repository.php");

class OpinionRepository extends Repository
{
    private function opinionBuilder(array $arr, Topic $topic) : array
    {
        $output = array();
        foreach ($arr as $row) {
            $id = $row["id"];
            $title = $row["title"];
            $content = $row["content"];
            array_push($output, new Opinion($id, $title, $content, $topic));
        }

        return $output;
    }

    public function getOpinionsForTopic(Topic $topic, bool $descending = false) : array
    {
        $query = "SELECT id, title, content, topicID FROM Opinions WHERE topicID = :topicID";
        if ($descending) {
            $query = $query . " ORDER BY id DESC";
        }

        $stmt = $this->connection->prepare($query);
        $topicID = $topic->getId();
        $stmt->bindParam(":topicID", $topicID, PDO::PARAM_INT);
        $stmt->execute();

        return $this->opinionBuilder($stmt->fetchAll(), $topic);
    }

    public function insertOpinion(int $topicID, string $title, string $content) : void
    {
        $stmt = $this->connection->prepare("INSERT INTO Opinions (title, content, topicID) VALUES (:title, :content, :topicID)");
        $stmt->bindValue(":title", $title, PDO::PARAM_STR);
        $stmt->bindValue(":content", $content, PDO::PARAM_STR);
        $stmt->bindValue(':topicID', $topicID, PDO::PARAM_INT);
        $stmt->execute();
    }
}