<?php

declare(strict_types=1);

require_once '../autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '../vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . DIRECTORY_SEPARATOR . "..");
$dotenv->load();

session_start();

require_once '../routes/web.php';

// require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . '../routes' . DIRECTORY_SEPARATOR . 'web.php';