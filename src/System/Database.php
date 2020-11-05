<?php

namespace Src\System;

class Database
{
    private $connection;

    public function __construct()
    {
        $driver = $_ENV['DB_DRIVER'];
        $host   = $_ENV['DB_HOST'];
        $port   = $_ENV['DB_PORT'];
        $db     = $_ENV['DB_DATABASE'];
        $user   = $_ENV['DB_USERNAME'];
        $pass   = $_ENV['DB_PASSWORD'];

        try {
            $this->connection = new \PDO(
                "$driver:host=$host;port=$port;dbname=$db",
                $user,
                $pass
            );

            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
