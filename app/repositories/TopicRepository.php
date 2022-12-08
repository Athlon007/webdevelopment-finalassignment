<?php
require_once("Repository.php");
require_once("../models/Topic.php");

class TopicRepository extends Repository
{
    private function topicsBuilder(array $array) : array
    {
        $output = array();
        require_once("../services/OpinionService.php");
        $opinionService = new OpinionService();

        foreach ($array as $row) {
            $id = $row["id"];
            $name = $row["name"];
            array_push($output, new Topic($id, $name));
        }

        return $output;
    }

    public function getAll() : array
    {
        $stmt = $this->connection->prepare("SELECT id, name FROM Topics");
        $stmt->execute();

        return $this->topicsBuilder($stmt->fetchAll());
    }

    public function getNthTopic(int $n) : Topic
    {
        $stmt = $this->connection->prepare("SELECT id, name FROM `Topics` ORDER BY id LIMIT :n,1;");
        $stmt->bindParam(":n", $n, PDO::PARAM_INT);
        $stmt->execute();

        return $this->topicsBuilder($stmt->fetchAll())[0];
    }
}