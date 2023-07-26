<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

define('DATABASE_NAME', $_ENV['DATABASE_NAME']);
define('DATABASE_USERNAME', $_ENV['DATABASE_USERNAME']);
define('DATABASE_PASSWORD', $_ENV['DATABASE_PASSWORD']);
define('DATABASE_HOST', $_ENV['DATABASE_HOST']);
define('DATABASE_CHARSET', $_ENV['DATABASE_CHARSET']);
define('_2C2P_HOST', $_ENV['_2C2P_HOST']);
define('_2C2P_MERCHANT_ID', $_ENV['_2C2P_MERCHANT_ID']);
define('_2C2P_SECRET_AUTH_KEY', $_ENV['_2C2P_SECRET_AUTH_KEY']);
define('_2C2P_SECRET_SHA_KEY', $_ENV['_2C2P_SECRET_SHA_KEY']);
define('ENABLED_REDIRECT_CHECKOUT_PAYMENT_URL', $_ENV['ENABLED_REDIRECT_CHECKOUT_PAYMENT_URL'] ?? false);
?>