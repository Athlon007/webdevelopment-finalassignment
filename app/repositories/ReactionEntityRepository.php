<?php
require_once("Repository.php");
require_once("../models/ReactionEntity.php");

class ReactionEntityRepository extends Repository
{
    private function reactionBuilder(array $pdo) : array
    {
        $output = array();
        foreach ($pdo as $row) {
            $id = $row["id"];
            $htmlEntity = $row["htmlEntity"];
            $isNegativeOpinion = $row["isNegative"];
            array_push($output, new ReactionEntity($id, $htmlEntity, $isNegativeOpinion));
        }
        return $output;
    }

    public function getAll() : array
    {
        $stmt = $this->connection->prepare("SELECT id, htmlEntity, isNegative FROM ReactionEntities");
        $stmt->execute();
        return $this->reactionBuilder($stmt->fetchAll());
    }

    public function getById(int $id) : ReactionEntity
    {
        $stmt = $this->connection->prepare("SELECT id, htmlEntity, isNegative FROM ReactionEntities WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $this->reactionBuilder($stmt->fetchAll())[0];
    }
}