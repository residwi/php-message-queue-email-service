<?php

require __DIR__ . '/bootstrap.php';

use Src\Services\EmailQueue;

$emailQueue = new EmailQueue();
$emailQueue->consume();
