<?php

require_once __DIR__ . '/../RateLimiter.php';

function sendJsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

function initializeApi(): void
{
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Content-Type: application/json");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJsonResponse(['error' => 'Method Not Allowed'], 405);
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $limiter = new RateLimiter(__DIR__ . '/../rate_limits.json');

    if (!$limiter->allowAccess($ip)) {
        sendJsonResponse(['error' => 'Límite de uso excedido. Por favor, espera 1 segundo.'], 429);
    }
}

function getJsonInput(): array
{
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($input)) {
        sendJsonResponse(['error' => 'Formato JSON inválido.'], 400);
    }

    return $input;
}

function requireStringField(array $input, string $fieldName, string $label): string
{
    if (!isset($input[$fieldName])) {
        sendJsonResponse(['error' => sprintf('El campo "%s" es requerido.', $fieldName)], 400);
    }

    $value = $input[$fieldName];

    if (!is_string($value)) {
        sendJsonResponse(['error' => sprintf('%s debe ser una cadena de texto válida.', $label)], 400);
    }

    if (trim($value) === '') {
        sendJsonResponse(['error' => sprintf('%s no puede estar vacío o contener solo espacios.', $label)], 400);
    }

    return $value;
}