<?php

require_once __DIR__ . '/../bootstrap.php';

initializeApi();

$input = getJsonInput();

$password = requireStringField($input, 'password', 'La contraseña');

if (strlen($password) > 128) {
    sendJsonResponse(['error' => 'La contraseña excede el límite máximo permitido de 128 caracteres.'], 400);
}

$hash = password_hash($password, PASSWORD_ARGON2ID);

if ($hash === false) {
    sendJsonResponse(['error' => 'Fallo al generar el hash.'], 500);
}

sendJsonResponse([
    'success' => true,
    'hash' => $hash,
    'algorithm' => 'Argon2id',
]);