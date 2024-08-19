<?php
// provides CRUD functionality for all IdText - Objects, all objects that derive from IdText class are going to have
// CanHandleDB - Interface - Implementations that derive from this class because read and delete operations always work
// the same way, only  update and create operations need to be implemented by polymorphism
// As specified in the Interface the arrays as parameters are generell, validation of the content ist happening in the
// implementations of the interface. Therefor the two validation methods (validateCreate and validateUpdate) need to
// be overwritten (Polymorphism again).

namespace quiz;

use PDO;

class IdTextDBHandler extends DataBase implements CanHandleDB
{
    protected string $tableName;
    protected PDO $connection;

    /**
     * expects enum entry to contain appropriate table name
     * @param KindOf $kindOf
     */
    public function __construct(KindOf $kindOf)
    {

        $this->tableName = $kindOf->getTableName();
        $this->connection = $this->connect();

    }


    /**
     * very general implementation for IdText objects and child classes
     * args array must contain all properties of object where key must match property name
     * returns id in db, on fail returns -1
     * @param array $args
     * @return int
     */
    public function create(array $args): int
    {
        if ($this->validateArgsCreate($args)){
        $items = '';
        $values = '';
        foreach ($args as $key => $value) {
            $items .= $key . ', ';
            $values .= ':' . $key . ', ';
        }
        $items = rtrim($items, ', ');
        $values = rtrim($values, ', ');
        $sql = "INSERT INTO $this->tableName (" . $items . ") VALUES (" . $values . ")";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($args);
        return $this->connection->lastInsertId();
        }
        return -1;
    }

    /**
     * provides data stored in db for TextId object or child class object by id in db
     * @param int $id
     * @return array
     */
    public function findById(int $id): array
    {
        $sql = "SELECT * FROM $this->tableName WHERE id = :id;";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id'=> $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * provides data for all TextId objects or child class objects stored in db
     * filters array may include keys categoryIds or userIds storing int[] with appropriate ids but filtering only takes
     * place in certain child classes
     * @param array $filters
     * @return array
     */
    public function findAll(array $filters = []): array
    {
        $sql = "SELECT * FROM $this->tableName;";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * very general implementation for IdText objects and child classes
     * args array must contain all properties of object where key must match property name
     * @param array $args
     * @return bool
     */
    public function update(array $args): bool
    {
        if ($this->validateArgsUpdate($args)) {
            $args['id'] = (int)$args['id'];
            $items = '';
            foreach ($args as $key => $value) {
            if ($key == 'id') continue;
            $items .= $key . '=:' . $key . ', ';
            }
            $items = rtrim($items, ', ');
            $sql = "UPDATE $this->tableName SET " . $items . " WHERE id = :id;";
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($args);
        }
        return false;
    }

    /**
     * simply deletes entry in db where id in db matches param $id
     * @param int $id
     * @return bool
     */
    public function deleteAtId(int $id): bool
    {
        $sql = "DELETE FROM $this->tableName WHERE id = :id;";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * must be overwritten in child classes to have reliable validation of expected array $args keys
     * validateArgsUpdate will in most cases rely on this validation
     * @param array $args
     * @return bool
     */
    protected function validateArgsCreate(array $args): bool
    {
        return array_key_exists('text', $args);
    }

    /**
     * checks validateArgsCreate plus existence of key 'id' in $args array
     * @param array $args
     * @return bool
     */
    protected function validateArgsUpdate(array $args): bool
    {
        return $this->validateArgsCreate($args) && array_key_exists('id', $args);
    }
}