<?php

namespace Src\Repositories;

use Src\System\Database;

class EmailRepository
{
    private $dbConnection;

    public function __construct()
    {
        $this->dbConnection = (new Database())->getConnection();
    }

    public function insert($input)
    {
        $query = "
            INSERT INTO logs
                (email, subject, body)
            VALUES
                (:email, :subject, :body);
        ";

        try {
            $statement = $this->dbConnection->prepare($query);
            $result = $statement->execute([
                'email' => $input['email'],
                'subject'  => $input['emailSubject'],
                'body'  => $input['emailBody'],
            ]);

            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}
