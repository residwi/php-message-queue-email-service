<?php

namespace Src\Repositories;

use Src\System\Database;

class UserRepository
{
    private $dbConnection;

    public function __construct()
    {
        $this->dbConnection = (new Database())->getConnection();
    }

    public function login($value)
    {
        $query = "
            SELECT
                id, name, username, password
            FROM
                users
            WHERE username = ?;
        ";

        try {
            $statement = $this->dbConnection->prepare($query);
            $statement->execute([$value['username']]);

            $result = $statement->fetch();

            if (!password_verify($value['password'], $result['password'])) {
                return false;
            }

            return true;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function insert($input)
    {
        $query = "
            INSERT INTO users
                (name, username, password)
            VALUES
                (:name, :username, :password);
        ";

        try {
            $statement = $this->dbConnection->prepare($query);
            $result = $statement->execute([
                'name' => $input['name'],
                'username'  => $input['username'],
                'password'  => password_hash($input['password'], PASSWORD_BCRYPT),
            ]);

            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}
