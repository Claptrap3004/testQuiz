<?php

namespace quiz;

use PDO;

class RelationDBHandler extends IdTextDBHandler
{
    public function __construct(KindOf $kindOf)
    {
        parent::__construct($kindOf);
    }

    /**
     * since there is no need to create single relation objects this implementation of findById provides an array of
     * answerIds and the value of isRight of the answerId that refer to a question id instead of providing information
     * of a single relation by its id
     * @param int $id
     * @return array
     */
    public function findById(int $id): array
    {
        $sql = "SELECT * FROM $this->tableName WHERE question_id = :id;";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteAtId(int $id): bool
    {
        $sql = "DELETE FROM $this->tableName WHERE question_id = :id;";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }


    protected function validateArgsCreate(array $args): bool
    {
        return array_key_exists('question_id', $args) &&
            array_key_exists('answer_id', $args) &&
            array_key_exists('is_right', $args);
    }

}