<?php

require_once __DIR__ . '/../bootstrap.php';

initializeApi();

$input = getJsonInput();

$password = requireStringField($input, 'password', 'La contraseña');
$hash = requireStringField($input, 'hash', 'El hash');

if (strlen($password) > 128) {
    sendJsonResponse(['error' => 'La contraseña excede el límite máximo permitido de 128 caracteres.'], 400);
}

if (strlen($hash) > 255) {
    sendJsonResponse(['error' => 'El hash excede el límite máximo permitido de 255 caracteres.'], 400);
}

$hashInfo = password_get_info($hash);

if (($hashInfo['algoName'] ?? 'unknown') !== 'argon2id') {
    sendJsonResponse(['error' => 'El hash proporcionado no corresponde al algoritmo Argon2id.'], 400);
}

sendJsonResponse([
    'success' => true,
    'valid' => password_verify($password, $hash),
    'algorithm' => 'Argon2id',
]);