<?php
declare(strict_types=1);

function getDBConnection(): PDO {
    $host = '127.0.0.1';
    $db   = 'coffee_notepad';
    $user = 'root'; // Update with your MySQL username
    $pass = '';     // Update with your MySQL password
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database connection failed.']);
        exit;
    }
}