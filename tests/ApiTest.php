<?php
// Run: php tests/ApiTest.php
$baseUrl = 'http://localhost/CoffeePad/api/notes.php';

function request(string $method, string $url, ?array $data = null): array {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => json_decode($response, true)];
}

// 1. Test Create
$created = request('POST', $baseUrl, ['title' => 'Espresso Test', 'content' => 'Dialing in 18g in, 36g out.']);
assert($created['code'] === 201 && $created['body']['title'] === 'Espresso Test', 'Failed POST test');
$id = $created['body']['id'];

// 2. Test Update & Pin
$updated = request('PUT', $baseUrl, ['id' => $id, 'is_pinned' => 1]);
assert($updated['code'] === 200 && (int)$updated['body']['is_pinned'] === 1, 'Failed PUT test');

// 3. Test Search
$searched = request('GET', $baseUrl . '?q=Espresso');
assert($searched['code'] === 200 && count($searched['body']) >= 1, 'Failed GET search test');

// 4. Test Delete
$deleted = request('DELETE', $baseUrl . '?id=' . $id);
assert($deleted['code'] === 200 && $deleted['body']['deleted'] === true, 'Failed DELETE test');

echo "All Superpowers API verification checks passed (GREEN).\n";