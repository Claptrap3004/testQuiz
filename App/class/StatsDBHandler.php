<?php

namespace quiz;


use PDO;

class StatsDBHandler extends IdTextDBHandler
{

    public function __construct(KindOf $kindOf)
    {
        parent::__construct($kindOf);
    }
    public function findAll(array $filters = []): array
    {
        if ($filters !== []) return $this->findFiltered($filters);
        return parent::findAll();
    }

    /**
     * returns question data sets matching filter containing keys listed in findAll DOC
     * @param array $filters
     * @return array
     */
    private function findFiltered(array $filters): array
    {

        if (array_key_exists('userId', $filters)){
            $sql = "SELECT * FROM $this->tableName WHERE user_id = :user_id";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([':user_id' => $filters['userId']]);
        }
        else return [];
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    /**
     * $id in this case represents the question id the stats data is related to, provides stats data set for question
     * depending on actual user
     * data set contains following keys: 'id', 'user_id', 'question_id', 'times_asked' and 'times_right'
     * @param int $id
     * @return array
     */
    public function findById(int $id): array
    {
        $sql = "SELECT * FROM $this->tableName WHERE question_id = :question_id AND user_id = :user_id;";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':question_id'=> $id,':user_id'=> $_SESSION['UserId']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            $id = $this->create(['question_id'=> $id, 'user_id' => $_SESSION['UserId'],'times_asked'=>0,'times_right' =>0]);
            $result = ['id'=>$id,'question_id'=> $id, 'user_id' => $_SESSION['UserId'],'times_asked'=>0,'times_right' =>0];
        }
        return $result;
    }

    /**
     * expects array containing 'id', 'times_asked' and 'times_right'. 'user_id' and 'question_id' cannot be changed
     * through this update method
     * @param array $args
     * @return bool
     */
    public function update(array $args): bool
    {
        if ($this->validateArgsUpdate($args)) {
            $sql = "UPDATE $this->tableName SET times_asked = :times_asked, times_right = :times_right WHERE id = :id;";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':id', $args['id']);
            $stmt->bindParam(':times_asked', $args['times_asked']);
            $stmt->bindParam(':times_right', $args['times_right']);
            return $stmt->execute();
        }
        return false;
    }

    public function deleteAll():bool
    {
        $sql = "DELETE FROM $this->tableName WHERE user_id = :user_id;";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':user_id', $_SESSION['UserId']);
        return $stmt->execute();
    }

    protected function validateArgsCreate(array $args): bool
    {
        return array_key_exists('question_id', $args) &&
            array_key_exists('user_id', $args) &&
            array_key_exists('times_asked', $args) &&
            array_key_exists('times_right', $args);
    }


}