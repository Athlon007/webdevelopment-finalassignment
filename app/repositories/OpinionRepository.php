<?php
require_once("../models/Opinion.php");
require_once("../models/Topic.php");
require_once("Repository.php");

class OpinionRepository extends Repository
{
    private function opinionBuilder(array $arr, Topic $topic): array
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

    public function getOpinionsForTopic(
        Topic $topic,
        bool $descending = false,
        int $offset = -1,
        int $limit = -1
    ): array {
        $query = "SELECT id, title, content, topicID FROM Opinions WHERE topicID = :topicID";
        if ($descending) {
            $query = $query . " ORDER BY id DESC";
        }
        if ($limit >= 0) {
            $query = $query . " LIMIT $limit";
        }
        if ($offset >= 0) {
            $query = $query . " OFFSET $offset";
        }

        $stmt = $this->connection->prepare($query);
        $topicID = $topic->getId();
        $stmt->bindParam(":topicID", $topicID, PDO::PARAM_INT);
        $stmt->execute();

        return $this->opinionBuilder($stmt->fetchAll(), $topic);
    }

    public function getOpinionsForTopicByPopularity(
        Topic $topic,
        bool $descending,
        int $offset = -1,
        int $limit = -1
    ): array {
        $query = "SELECT Opinions.id, Opinions.title, Opinions.content, Opinions.topicID, " .
            "IFNULL(SUM(Reactions.count), 0) as reactions " .
            "FROM Opinions " .
            "LEFT JOIN Reactions ON Reactions.opinionID = Opinions.id " .
            "WHERE topicID  = :topicID " .
            "GROUP BY Opinions.id ";

        $query .= "ORDER BY reactions";
        if ($descending) {
            $query .= " DESC";
        }

        $query .= ", id DESC";

        if ($limit >= 0) {
            $query = $query . " LIMIT $limit";
        }
        if ($offset >= 0) {
            $query = $query . " OFFSET $offset";
        }

        $stmt = $this->connection->prepare($query);
        $topicID = $topic->getId();
        $stmt->bindParam(":topicID", $topicID, PDO::PARAM_INT);
        $stmt->execute();

        return $this->opinionBuilder($stmt->fetchAll(), $topic);
    }

    public function insertOpinion(int $topicID, string $title, string $content): void
    {
        $sql = "INSERT INTO Opinions (title, content, topicID) VALUES (:title, :content, :topicID)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":title", $title, PDO::PARAM_STR);
        $stmt->bindValue(":content", $content, PDO::PARAM_STR);
        $stmt->bindValue(':topicID', $topicID, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getOpinionsForTopicCount(Topic $topic): int
    {
        $topicID = $topic->getId();
        $sql = "SELECT COUNT(id) AS opinions FROM Opinions WHERE topicID = $topicID";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()["opinions"];
    }

    public function deleteById(int $id): void
    {
        $sql = "DELETE FROM Opinions WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function update(int $id, string $title, string $content)
    {
        $sql = "UPDATE Opinions SET title = :title, content = :content WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":title", $title, PDO::PARAM_STR);
        $stmt->bindValue(":content", $content, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function selectById(int $opinionID): Opinion
    {
        $sql = "SELECT id, title, content FROM Opinions WHERE id = :opinionID";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":opinionID", $opinionID, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        $opinion = new Opinion($row["id"], $row["title"], $row["content"], new Topic(-1, ""));
        return $opinion;
    }
}
