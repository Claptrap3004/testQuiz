<?php

namespace quiz;


use PDO;

class UserDBHandler extends IdTextDBHandler
{
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

        if (array_key_exists('userEmail', $filters)){
            $sql = "SELECT * FROM $this->tableName WHERE email = :email";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([':email' => $filters['userEmail']]);
        }
        else return [];
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }



    protected function validateArgsCreate(array $args): bool
    {
        return array_key_exists('username', $args) &&
            array_key_exists('email', $args) &&
            array_key_exists('password', $args);
    }
}