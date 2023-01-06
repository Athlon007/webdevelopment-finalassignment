<?php
require_once("Repository.php");
require_once("../models/Topic.php");

class TopicRepository extends Repository
{
    private function topicsBuilder(array $array): array
    {
        $output = array();

        foreach ($array as $row) {
            $id = $row["id"];
            $name = $row["name"];
            array_push($output, new Topic($id, $name));
        }

        return $output;
    }

    public function getAll(): array
    {
        $stmt = $this->connection->prepare("SELECT id, name FROM Topics");
        $stmt->execute();

        return $this->topicsBuilder($stmt->fetchAll());
    }

    public function getNthTopic(int $n): ?Topic
    {
        $stmt = $this->connection->prepare("SELECT id, name FROM `Topics` ORDER BY id LIMIT :n,1;");
        $stmt->bindParam(":n", $n, PDO::PARAM_INT);
        $stmt->execute();

        $value = $stmt->fetchAll();
        if (count($value) == 0) {
            return null;
        }

        return $this->topicsBuilder($value)[0];
    }

    public function getCount(): int
    {
        $stmt = $this->connection->prepare("SELECT COUNT(id) as count FROM Topics");
        $stmt->execute();
        return $stmt->fetch()["count"];
    }

    public function getCountById(int $id): int
    {
        $stmt = $this->connection->prepare("SELECT COUNT(id) AS count FROM Topics WHERE id = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch()["count"];
    }

    public function update(int $id, string $name): void
    {
        $sql = "UPDATE Topics SET name = :name WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function insert(string $name): void
    {
        $sql = "INSERT INTO Topics (name) VALUES (:name)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function delete(int $id): void
    {
        $sql = "DELETE FROM Topics WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getById(int $id): Topic
    {
        $sql = "SELECT id, name FROM Topics WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $this->topicsBuilder($stmt->fetchAll())[0];
    }
}
