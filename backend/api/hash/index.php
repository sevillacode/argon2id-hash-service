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
    http_response_code(429); // Too Many Requests
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

// 2. Validación de presencia del campo
if (!isset($input['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'El campo "password" es requerido.']);
    exit;
}

$password = $input['password'];

// 3. Validación de tipo estricto (debe ser una cadena de texto)
if (!is_string($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'La contraseña debe ser una cadena de texto válida.']);
    exit;
}

// 4. Validación de contenido no vacío
if (trim($password) === '') {
    http_response_code(400);
    echo json_encode(['error' => 'La contraseña no puede estar vacía o contener solo espacios.']);
    exit;
}

// 5. Límite de longitud máxima para prevenir DoS (128 caracteres)
if (strlen($password) > 128) {
    http_response_code(400);
    echo json_encode(['error' => 'La contraseña excede el límite máximo permitido de 128 caracteres.']);
    exit;
}

// Generar hash seguro con Argon2id
$hash = password_hash($password, PASSWORD_ARGON2ID);

if ($hash === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Fallo al generar el hash.']);
    exit;
}

echo json_encode([
    'success' => true,
    'hash' => $hash,
    'algorithm' => 'Argon2id'
]);
