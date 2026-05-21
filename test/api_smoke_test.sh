#!/usr/bin/env bash

set -euo pipefail

API_BASE_URL="${API_BASE_URL:-http://localhost:8000}"

echo "Ejecutando pruebas básicas contra: $API_BASE_URL"

fallar() {
  echo "ERROR: $1"
  exit 1
}

ok() {
  echo "OK: $1"
}

contiene() {
  local respuesta="$1"
  local texto_esperado="$2"
  local mensaje="$3"

  if [[ "$respuesta" != *"$texto_esperado"* ]]; then
    echo "Respuesta recibida:"
    echo "$respuesta"
    fallar "$mensaje"
  fi

  ok "$mensaje"
}

estado_http() {
  local estado="$1"
  local esperado="$2"
  local mensaje="$3"

  if [[ "$estado" != "$esperado" ]]; then
    fallar "$mensaje. Esperado: $esperado, recibido: $estado"
  fi

  ok "$mensaje"
}

echo "Probando generación de hash..."

RESPUESTA_HASH="$(curl -s -X POST "$API_BASE_URL/api/hash" \
  -H "Content-Type: application/json" \
  -d '{"password":"prueba123"}')"

contiene "$RESPUESTA_HASH" '"success":true' "El endpoint /api/hash devuelve success=true"
contiene "$RESPUESTA_HASH" '"algorithm":"Argon2id"' "El endpoint /api/hash devuelve el algoritmo Argon2id"
contiene "$RESPUESTA_HASH" '$argon2id$' "El endpoint /api/hash devuelve un hash Argon2id"

HASH="$(php -r '$data=json_decode($argv[1], true); echo $data["hash"] ?? "";' "$RESPUESTA_HASH")"

if [[ -z "$HASH" ]]; then
  fallar "No se ha podido extraer el hash generado"
fi

sleep 1

echo "Probando verificación con contraseña correcta..."

RESPUESTA_VERIFY_OK="$(curl -s -X POST "$API_BASE_URL/api/verify" \
  -H "Content-Type: application/json" \
  -d "{\"password\":\"prueba123\",\"hash\":\"$HASH\"}")"

contiene "$RESPUESTA_VERIFY_OK" '"success":true' "El endpoint /api/verify devuelve success=true"
contiene "$RESPUESTA_VERIFY_OK" '"valid":true' "El endpoint /api/verify valida la contraseña correcta"

sleep 1

echo "Probando verificación con contraseña incorrecta..."

RESPUESTA_VERIFY_KO="$(curl -s -X POST "$API_BASE_URL/api/verify" \
  -H "Content-Type: application/json" \
  -d "{\"password\":\"incorrecta\",\"hash\":\"$HASH\"}")"

contiene "$RESPUESTA_VERIFY_KO" '"success":true' "El endpoint /api/verify responde correctamente con contraseña incorrecta"
contiene "$RESPUESTA_VERIFY_KO" '"valid":false' "El endpoint /api/verify rechaza la contraseña incorrecta"

sleep 1

echo "Probando validación de JSON inválido..."

ESTADO_JSON_INVALIDO="$(curl -s -o /dev/null -w "%{http_code}" -X POST "$API_BASE_URL/api/hash" \
  -H "Content-Type: application/json" \
  -d '{"password":')"

estado_http "$ESTADO_JSON_INVALIDO" "400" "El endpoint /api/hash rechaza JSON inválido con HTTP 400"

echo "Probando método no permitido..."

ESTADO_GET_HASH="$(curl -s -o /dev/null -w "%{http_code}" -X GET "$API_BASE_URL/api/hash")"

estado_http "$ESTADO_GET_HASH" "405" "El endpoint /api/hash rechaza GET con HTTP 405"

echo "Probando endpoint antiguo..."

ESTADO_ENDPOINT_ANTIGUO="$(curl -s -o /dev/null -w "%{http_code}" "$API_BASE_URL/index.php")"

estado_http "$ESTADO_ENDPOINT_ANTIGUO" "404" "El endpoint antiguo /index.php ya no está disponible"

echo "Pruebas finalizadas correctamente"