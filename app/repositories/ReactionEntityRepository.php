<?php
require_once("Repository.php");
require_once("../models/ReactionEntity.php");
require_once("../models/Exceptions/IllegalOperationException.php");

class ReactionEntityRepository extends Repository
{
    private function reactionBuilder(array $pdo): array
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

    public function getAll(): array
    {
        $stmt = $this->connection->prepare("SELECT id, htmlEntity, isNegative FROM ReactionEntities");
        $stmt->execute();
        return $this->reactionBuilder($stmt->fetchAll());
    }

    public function getById(int $id): ReactionEntity
    {
        $stmt = $this->connection->prepare("SELECT id, htmlEntity, isNegative FROM ReactionEntities WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $this->reactionBuilder($stmt->fetchAll())[0];
    }

    public function update(int $id, string $htmlEntity, bool $isNegative)
    {
        if (strlen($htmlEntity) == 0) {
            throw new IllegalOperationException("Cannot set empty reactions.");
        }

        $sql = "UPDATE ReactionEntities SET htmlEntity = :htmlEntity, isNegative = :isNegative WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":htmlEntity", $htmlEntity, PDO::PARAM_STR);
        $stmt->bindValue(":isNegative", $isNegative, PDO::PARAM_BOOL);
        $stmt->execute();
    }

    public function insert(string $htmlEntity, bool $isNegative)
    {
        if (strlen($htmlEntity) == 0) {
            throw new IllegalOperationException("Cannot set empty reactions.");
        }

        $sql = "INSERT INTO ReactionEntities (htmlEntity, isNegative) VALUES (:htmlEntity, :isNegative)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":htmlEntity", $htmlEntity, PDO::PARAM_STR);
        $stmt->bindValue(":isNegative", $isNegative, PDO::PARAM_BOOL);
        $stmt->execute();
    }

    public function delete(int $id)
    {
        $sql = "DELETE FROM ReactionEntities WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
