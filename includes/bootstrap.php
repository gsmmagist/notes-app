<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/auth_helpers.php';
require_once __DIR__ . '/note_helpers.php';

$configPath = dirname(__DIR__) . '/config.php';

if (!is_file($configPath)) {
    exit('Файл config.php не найден. Скопируйте config.example.php в config.php и укажите данные для подключения к БД.');
}

$config = require $configPath;
$pdo = getConnection($config['db']);
