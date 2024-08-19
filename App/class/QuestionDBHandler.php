<?php

namespace quiz;


use PDO;

class QuestionDBHandler extends IdTextDBHandler
{

    public function __construct(KindOf $kindOf)
    {
        parent::__construct($kindOf);
    }


    /**
     * without param simply calls parent find all method to provide all question data sets including following keys:
     * 'id', 'category_id', 'user_id' and 'text'
     * filter array may contain 'categoryIds' or 'userIds' key which hold array of appropriate ids; use Filters enum
     * for proper creation
     * @param array $filters
     * @return array listed keys
     */
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

        if (array_key_exists('categoryIds', $filters)){

            $required = Filters::CATEGORY->createWhereClauseAndBindings($filters['categoryIds']);
            $sql = "SELECT * FROM $this->tableName" . $required['sql'];
            $stmt = $this->connection->prepare($sql);
            foreach ($required['binding'] as $key => $value) $stmt->bindParam("$key", $value);
            $stmt->execute();
        }
        elseif (array_key_exists('userIds', $filters)){
            $required = Filters::USER->createWhereClauseAndBindings($filters['userIds']);
            $sql = "SELECT * FROM $this->tableName" . $required['sql'];
            $stmt = $this->connection->prepare($sql);
            foreach ($required['binding'] as $key => $value) $stmt->bindParam("$key", $value);
            $stmt->execute();
        }
        elseif (array_key_exists('question_by_category', $filters)){
            $sql = "SELECT c.id,c.text, COUNT(q.category_id) AS number FROM question q LEFT JOIN category c ON q.category_id = c.id GROUP BY q.category_id ;" ;
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
        }
        else return [];
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }


    /**
     * checks against existence of necessary keys in args.
     * required keys are 'text','category_id' and 'user_id'
     * @param array $args
     * @return bool
     */
    protected function validateArgsCreate(array $args): bool
    {
        return array_key_exists('text', $args) &&
            array_key_exists('explanation', $args) &&
            array_key_exists('category_id', $args) &&
            array_key_exists('user_id', $args);
    }

}