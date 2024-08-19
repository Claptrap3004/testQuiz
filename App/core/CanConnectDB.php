<?php

// Interface to be able to integrate different DBMS
// Only Implementation for now ist the MariaDBConnector

namespace quiz;

interface CanConnectDB
{
    public function getConnection():\PDO;
}