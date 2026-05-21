<?php

// Configuración de CORS para permitir que el frontend se comunique
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Manejo de la petición preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

require_once __DIR__ . '/../../RateLimiter.php';

$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$limiter = new RateLimiter(__DIR__ . '/../../rate_limits.json');

if (!$limiter->allowAccess($ip)) {
    http_response_code(429);
    echo json_encode(['error' => 'Límite de uso excedido. Por favor, espera 1 segundo.']);
    exit;
}

// Leer y decodificar el cuerpo JSON
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

// 1. Validación de JSON bien formado
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Formato JSON inválido.']);
    exit;
}

// 2. Validación de presencia de campos
if (!isset($input['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'El campo "password" es requerido.']);
    exit;
}

if (!isset($input['hash'])) {
    http_response_code(400);
    echo json_encode(['error' => 'El campo "hash" es requerido.']);
    exit;
}

$password = $input['password'];
$hash = $input['hash'];

// 3. Validación de tipo estricto
if (!is_string($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'La contraseña debe ser una cadena de texto válida.']);
    exit;
}

if (!is_string($hash)) {
    http_response_code(400);
    echo json_encode(['error' => 'El hash debe ser una cadena de texto válida.']);
    exit;
}

// 4. Validación de contenido no vacío
if (trim($password) === '') {
    http_response_code(400);
    echo json_encode(['error' => 'La contraseña no puede estar vacía o contener solo espacios.']);
    exit;
}

if (trim($hash) === '') {
    http_response_code(400);
    echo json_encode(['error' => 'El hash no puede estar vacío o contener solo espacios.']);
    exit;
}

// 5. Límites de longitud
if (strlen($password) > 128) {
    http_response_code(400);
    echo json_encode(['error' => 'La contraseña excede el límite máximo permitido de 128 caracteres.']);
    exit;
}

if (strlen($hash) > 255) {
    http_response_code(400);
    echo json_encode(['error' => 'El hash excede el límite máximo permitido de 255 caracteres.']);
    exit;
}

// 6. Validación básica del algoritmo esperado
$hashInfo = password_get_info($hash);

if (($hashInfo['algoName'] ?? 'unknown') !== 'argon2id') {
    http_response_code(400);
    echo json_encode(['error' => 'El hash proporcionado no corresponde al algoritmo Argon2id.']);
    exit;
}

// Verificar contraseña contra hash
$isValid = password_verify($password, $hash);

echo json_encode([
    'success' => true,
    'valid' => $isValid,
    'algorithm' => 'Argon2id'
]);
