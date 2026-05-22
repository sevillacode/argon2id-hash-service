<?php

require_once __DIR__ . '/../bootstrap.php';
$config = require __DIR__ . '/../../config.php';

initializeApi();

$input = getJsonInput();

$password = requireStringField($input, 'password', 'La contraseña');

if (strlen($password) > $config['limits']['max_password_length']) {
    sendJsonResponse(['error' => 'La contraseña excede el límite máximo permitido de '.$config['limits']['max_password_length'].' caracteres.'], 400);
}

$hash = password_hash(
    $password,
    PASSWORD_ARGON2ID,
    $config['argon2id']
);

if ($hash === false) {
    sendJsonResponse(['error' => 'Fallo al generar el hash.'], 500);
}

sendJsonResponse([
    'success' => true,
    'hash' => $hash,
    'algorithm' => 'Argon2id',
]);