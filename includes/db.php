<?php

declare(strict_types=1);

function getConnection(array $dbConfig): PDO
{
    $dsn = sprintf(
        'mysql:host=%s;port=3306;dbname=%s;charset=utf8mb4',
        $dbConfig['host'],
        $dbConfig['name']
    );

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        return new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], $options);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        throw new RuntimeException('Database connection failed', 0, $e);
    }
}