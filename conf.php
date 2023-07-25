<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

define('DATABASE_NAME', $_ENV['DATABASE_NAME']);
define('DATABASE_USERNAME', $_ENV['DATABASE_USERNAME']);
define('DATABASE_PASSWORD', $_ENV['DATABASE_PASSWORD']);
define('DATABASE_HOST', $_ENV['DATABASE_HOST']);
define('DATABASE_CHARSET', $_ENV['DATABASE_CHARSET']);
define('2C2P_MERCHANT_ID', $_ENV['2C2P_MERCHANT_ID']);
define('2C2P_SECRET_KEY', $_ENV['2C2P_SECRET_KEY']);
define('2C2P_HOST', $_ENV['2C2P_HOST']);
?>