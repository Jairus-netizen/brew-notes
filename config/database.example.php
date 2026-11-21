<?php
declare(strict_types=1);

function getDBConnection(): PDO {
    $host = '127.0.0.1';
    $db   = 'coffee_notepad';
    $user = 'YOUR_DB_USER';
    $pass = 'YOUR_DB_PASSWORD';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    return new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
}