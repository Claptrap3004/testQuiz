<?php
// Implementation for MariaDB access
// if not working in test environment maybe the port needs to be changed. See ddev describe
namespace quiz;

use Exception;
use PDO;

class MariaDBConnector implements CanConnectDB
{
    private string $servername;
    private string $username;
    private string $password;
    private string $dbname;
    private static ?\PDO $connection = null;


    public function __construct(string $dbname = null)
    {
        $this->servername = getenv('DB_HOST') ? getenv('DB_HOST') : 'ddev-AbfrageProgramm-db';
        $this->dbname = getenv('DB_NAME') ? getenv('DB_NAME') : 'abfrageprogramm';
        $this->username = getenv('DB_USER') ? getenv('DB_USER') : 'root';;
        $this->password = getenv('DB_PASSWORD') ? getenv('DB_PASSWORD') : 'root';;
    }

    /**
     * @throws Exception
     */
    public function getConnection(): \PDO
    {
        if (!self::$connection) {
            try {
                self::$connection = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            } catch (Exception $e) {
                throw new Exception('FEHLER : ' . $e->getMessage() . '<br> Datei : ' . $e->getFile() . '<br> Zeile : ' . $e->getLine() . '<br> Trace : ' . $e->getTraceAsString());
            }

        }
        return self::$connection;
    }
}