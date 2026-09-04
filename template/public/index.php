<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$logger = require dirname(__DIR__) . '/config/logger.php';
$logger->info('Request handled');

echo ($_ENV['APP_NAME'] ?? 'App') . ' is up and running';
