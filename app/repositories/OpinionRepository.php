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

    public function getOpinionsForTopicByPopularity(Topic $topic, bool $descending) : array
    {
        $query = "SELECT Opinions.id, Opinions.title, Opinions.content, Opinions.topicID, " .
            "IFNULL(SUM(Reactions.count), 0) as reactions " .
            "FROM Opinions " .
            "LEFT JOIN Reactions ON Reactions.opinionID = Opinions.id " .
            "WHERE topicID  = :topicID " .
            "GROUP BY Opinions.id ";

        if ($descending) {
            $query = $query . "ORDER BY reactions DESC";
        }

        $stmt = $this->connection->prepare($query);
        $topicID = $topic->getId();
        $stmt->bindParam(":topicID", $topicID, PDO::PARAM_INT);
        $stmt->execute();

        return $this->opinionBuilder($stmt->fetchAll(), $topic);
    }

    public function insertOpinion(int $topicID, string $title, string $content) : void
    {
        $sql = "INSERT INTO Opinions (title, content, topicID) VALUES (:title, :content, :topicID)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":title", $title, PDO::PARAM_STR);
        $stmt->bindValue(":content", $content, PDO::PARAM_STR);
        $stmt->bindValue(':topicID', $topicID, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getOpinionsForTopicCount(Topic $topic) : int
    {
        $topicID = $topic->getId();
        $sql = "SELECT COUNT(id) AS opinions FROM Opinions WHERE topicID = $topicID";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()["opinions"];
    }
}