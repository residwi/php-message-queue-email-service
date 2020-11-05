<?php
require __DIR__ . '/bootstrap.php';

$statement = <<<EOS
CREATE TABLE public.logs (
	id serial NOT NULL,
	email varchar NOT NULL,
	subject text NOT NULL,
	body text NOT NULL,
	CONSTRAINT logs_pk PRIMARY KEY (id)
);

CREATE TABLE public.users (
	id serial NOT NULL,
	username varchar NOT NULL,
	name varchar NOT NULL,
	password varchar NOT NULL,
	CONSTRAINT users_pk PRIMARY KEY (id)
);
EOS;

try {
    $createTable = $dbConnection->exec($statement);
    echo "Success!\n";
} catch (\PDOException $e) {
    exit($e->getMessage());
}
