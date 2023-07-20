<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('DB_NAME', $_ENV['DATABASE_NAME']);
define('DB_USER', $_ENV['DATABASE_USERNAME']);
define('DB_PASSWORD', $_ENV['DATABASE_PASSWORD']);
define('DB_HOST', $_ENV['DATABASE_HOST']);
define('DB_CHARSET', $_ENV['DATABASE_CHARSET']);