<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$pdo = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

function sendJson(mixed $data, int $status = 200): void {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];

switch ($method) {
    case 'GET':
        $query = trim($_GET['q'] ?? '');
        if ($query !== '') {
            $stmt = $pdo->prepare(
                "SELECT id, title, content, is_pinned, updated_at 
                 FROM notes 
                 WHERE title LIKE :q OR content LIKE :q 
                 ORDER BY is_pinned DESC, updated_at DESC"
            );
            $stmt->execute(['q' => "%$query%"]);
        } else {
            $stmt = $pdo->query(
                "SELECT id, title, content, is_pinned, updated_at 
                 FROM notes 
                 ORDER BY is_pinned DESC, updated_at DESC"
            );
        }
        sendJson($stmt->fetchAll());
        break;

    case 'POST':
        $title = trim($input['title'] ?? 'Untitled Note');
        $content = $input['content'] ?? '';
        $stmt = $pdo->prepare("INSERT INTO notes (title, content) VALUES (:title, :content)");
        $stmt->execute([
            'title'   => $title !== '' ? $title : 'Untitled Note',
            'content' => $content
        ]);
        $id = (int)$pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT id, title, content, is_pinned, updated_at FROM notes WHERE id = :id");
        $stmt->execute(['id' => $id]);
        sendJson($stmt->fetch(), 201);
        break;

    case 'PUT':
        $id = isset($input['id']) ? (int)$input['id'] : 0;
        if ($id <= 0) {
            sendJson(['error' => 'Invalid note ID'], 400);
        }

        $fields = [];
        $params = ['id' => $id];

        if (array_key_exists('title', $input)) {
            $title = trim((string)$input['title']);
            $fields[] = "title = :title";
            $params['title'] = $title !== '' ? $title : 'Untitled Note';
        }
        if (array_key_exists('content', $input)) {
            $fields[] = "content = :content";
            $params['content'] = (string)$input['content'];
        }
        if (array_key_exists('is_pinned', $input)) {
            $fields[] = "is_pinned = :is_pinned";
            $params['is_pinned'] = $input['is_pinned'] ? 1 : 0;
        }

        if (empty($fields)) {
            sendJson(['error' => 'No fields to update'], 400);
        }

        $sql = "UPDATE notes SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $stmt = $pdo->prepare("SELECT id, title, content, is_pinned, updated_at FROM notes WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $updated = $stmt->fetch();

        if (!$updated) {
            sendJson(['error' => 'Note not found'], 404);
        }
        sendJson($updated);
        break;

    case 'DELETE':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : (int)($input['id'] ?? 0);
        if ($id <= 0) {
            sendJson(['error' => 'Invalid note ID'], 400);
        }

        $stmt = $pdo->prepare("DELETE FROM notes WHERE id = :id");
        $stmt->execute(['id' => $id]);
        sendJson(['deleted' => true, 'id' => $id]);
        break;

    default:
        sendJson(['error' => 'Method not allowed'], 405);
}