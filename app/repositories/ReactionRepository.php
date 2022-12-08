<?php
require_once("../models/Reaction.php");
require_once("../models/Opinion.php");
require_once("ReactionEntityRepository.php");
require_once("Repository.php");

class ReactionRepository extends Repository
{
    private function reactionBuilder(array $input, Opinion $opinion): array
    {
        $reactionEntityService = new ReactionEntityRepository();

        $output = array();
        foreach ($input as $row) {
            $id = $row["id"];
            $reaction = $reactionEntityService->getById($row["reactionID"]);
            $count = $row["count"];
            array_push($output, new Reaction($id, $reaction, $opinion, $count));
        }
        return $output;
    }

    public function getAllForOpinion(Opinion $opinion) : array
    {
        $stmt = $this->connection->prepare("SELECT id, reactionID, 'name', opinionID, count FROM Reactions WHERE opinionID = :opinionID");
        $opinionID = $opinion->getId();
        $stmt->bindParam(":opinionID", $opinionID, PDO::PARAM_INT);
        $stmt->execute();
        return $this->reactionBuilder($stmt->fetchAll(), $opinion);
    }
}