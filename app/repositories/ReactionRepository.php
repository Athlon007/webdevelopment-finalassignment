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

    public function createNewReaction(int $opinionID, int $reactionID) : void
    {
        $sql = "INSERT INTO Reactions (reactionID, opinionID, count) VALUES (:reactionID, :opinionID, 1)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":reactionID", $reactionID, PDO::PARAM_INT);
        $stmt->bindParam(":opinionID", $opinionID, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function increaseCountOfExistingOpinion(int $opinionID, int $reactionID) : void
    {
        $sql = "UPDATE Reactions SET count = count + 1 WHERE opinionID = :opinionID AND reactionID = :reactionID";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":reactionID", $reactionID, PDO::PARAM_INT);
        $stmt->bindParam(":opinionID", $opinionID, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Returns number of specific reactions for specific opinion.
    public function getReactionCount(int $opinionID, int $reactionID) : int
    {
        $sql = "SELECT count(id) AS reactionCount FROM Reactions " .
                "WHERE opinionID = :opinionID AND reactionID = :reactionID";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":reactionID", $reactionID, PDO::PARAM_INT);
        $stmt->bindParam(":opinionID", $opinionID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch()["reactionCount"];
    }

    public function getReactionsCount(int $opinionID) : int
    {
        $sql = "SELECT IFNULL(SUM(Reactions.count), 0) as reactionCount FROM Opinions " .
        "LEFT JOIN Reactions on Reactions.opinionID = Opinions.id WHERE Opinions.id = :opinionID;";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":opinionID", $opinionID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch()["reactionCount"];
    }
}