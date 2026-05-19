<?php
// Configuración de CORS para permitir que el frontend se comunique
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Manejo de la petición preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

require_once 'RateLimiter.php';

$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$limiter = new RateLimiter(__DIR__ . '/rate_limits.json');

if (!$limiter->allowAccess($ip)) {
    http_response_code(429); // Too Many Requests
    echo json_encode(['error' => 'Límite de uso excedido. Por favor, espera 1 segundo.']);
    exit;
}

// Leer y decodificar el cuerpo JSON
$input = json_decode(file_get_contents('php://input'), true);
$password = $input['password'] ?? '';

if (empty($password)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'La contraseña es requerida.']);
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
