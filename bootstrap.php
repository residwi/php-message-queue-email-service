<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/routes.php';

use Dotenv\Dotenv;
use Src\System\Database;

$dotenv = DotEnv::createImmutable(__DIR__);
$dotenv->load();

$dbConnection = (new Database())->getConnection();
