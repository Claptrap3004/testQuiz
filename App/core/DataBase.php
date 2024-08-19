<?php
namespace quiz;

use PDO;

abstract class DataBase
{

    static protected ?PDO $conn = null;
    static protected ?CanConnectDB $connector = null;
    protected function connect():PDO|null
    {
        if (self::$connector === null) self::$connector = new MariaDBConnector();
        if (self::$conn === null) try {
            self::$conn = self::$connector->getConnection();
        } catch (\Exception $e) {
        }
        return self::$conn;
    }

    public function getLastInsertedId():int
    {
        return self::$conn !== null ? self::$conn->lastInsertId() : -1;
    }

    public static function setConnector(?CanConnectDB $connector): void
    {
        self::$connector = $connector;
    }

}