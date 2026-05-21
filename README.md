# 🔐 Secure Hash Generator Service

Aplicación web para generar y verificar hashes seguros de contraseñas utilizando **Argon2id**, el algoritmo recomendado actualmente por OWASP para el almacenamiento seguro de credenciales.

Este proyecto ha sido construido con una arquitectura ligera basada en un frontend React y un backend en PHP nativo, diseñado para ser fácilmente escalable, dockerizable, refactorizable a frameworks más robustos o consumido como API.

Su objetivo actual es servir como laboratorio técnico para practicar seguridad aplicada, diseño de APIs HTTP, separación frontend/backend y despliegue básico con Docker.

## ✨ Características Principales

- **🛡️ Hash:** Generación de hashes de contraseña con Argon2id.
- **✅ Verify:** Verificación de una contraseña contra un hash Argon2id existente mediante `password_verify()`.
- **⚙️ Configuración centralizada:** Parámetros de Argon2id y límites de entrada definidos en un archivo de configuración del backend.
- **⚡ Rate Limit:** Sistema simple de control de accesos por IP para reducir saturación. Funciona mediante un archivo JSON que se autolimpia, eliminando registros antiguos sin necesidad de base de datos externa.
- **🎨 Interfaz (UI/UX):** Frontend construido con React y CSS nativo, con diseño simple, transiciones suaves y validaciones en tiempo real.
- **🔌 API HTTP con JSON:** Endpoints separados para generación y verificación.
- **🐳 Docker:** Entorno preparado para ejecutarse mediante contenedores.
- **🚀 Ligero y sin fricciones:** Backend en PHP nativo, sin dependencias complejas de Composer ni base de datos.

## 🏗️ Arquitectura y Tecnologías

### Backend (`/backend`)

- **PHP 8+ nativo:** Proporciona la API.
- **`api/bootstrap.php`:** Inicialización común de la API: cabeceras CORS, preflight `OPTIONS`, validación de método HTTP, rate limiting y utilidades JSON.
- **`api/hash/index.php`:** Endpoint para generar hashes Argon2id.
- **`api/verify/index.php`:** Endpoint para verificar una contraseña contra un hash Argon2id.
- **`RateLimiter.php`:** Clase encargada de leer/escribir en `rate_limits.json`, bloqueando peticiones abusivas y purgando entradas antiguas.
- **`config.php`:** Configuración centralizada de parámetros Argon2id y límites de entrada.

### Frontend (`/frontend`)

- **React 18 + Vite:** Entorno de desarrollo rápido para la interfaz.
- **CSS nativo:** Estilizado sin librerías externas como Tailwind o Bootstrap.
- **Variables de entorno:** Configuración de la URL base de la API mediante `.env`.

## 📁 Estructura general

```text
.
├── backend/
│   ├── api/
│   │   ├── bootstrap.php
│   │   ├── hash/
│   │   │   └── index.php
│   │   └── verify/
│   │       └── index.php
│   ├── config.php
│   ├── RateLimiter.php
│   ├── rate_limits.example.json
│   └── Dockerfile
├── frontend/
│   ├── src/
│   ├── .env.example
│   ├── Dockerfile
│   └── package.json
└── docker-compose.yml
```

## ⚙️ Cómo ejecutarlo localmente sin Docker

No requiere base de datos ni servicios externos.

### 1. Configurar el frontend

El archivo `frontend/.env.example` sirve como plantilla. Debe copiarse o renombrarse a `frontend/.env`:

```bash
cp frontend/.env.example frontend/.env
```

Ejemplo de contenido:

```env
VITE_API_BASE_URL=http://localhost:8000
```

> El archivo `.env` contiene configuración local y no debería subirse al repositorio. El archivo `.env.example` sí debe versionarse como referencia.

### 2. Levantar el Backend

Desde la raíz del proyecto:

```bash
php -S localhost:8000 -t backend
```

La API quedará disponible en:

```text
http://localhost:8000
```

### 3. Levantar el Frontend

En otra terminal:

```bash
cd frontend
npm install
npm run dev
```

La interfaz estará disponible normalmente en:

```text
http://localhost:5173
```

## 🐳 Cómo ejecutarlo con Docker

Desde la raíz del proyecto:

```bash
docker compose up --build
```

Servicios disponibles:

```text
Frontend: http://localhost:5173
Backend:  http://localhost:8000
```

Para detener los contenedores:

```bash
docker compose down
```

## 🔌 Uso de la API

La API está diseñada para consumirse mediante JSON.

### Generar hash

**Endpoint:** `POST /api/hash`  
**Headers obligatorios:** `Content-Type: application/json`

#### Ejemplo con cURL

```bash
curl -X POST http://localhost:8000/api/hash \
  -H "Content-Type: application/json" \
  -d '{"password":"mi_contraseña_secreta"}'
```

#### Respuesta satisfactoria

```json
{
  "success": true,
  "hash": "$argon2id$v=19$m=65536,t=4,p=1$...",
  "algorithm": "Argon2id"
}
```

### Verificar contraseña contra hash

**Endpoint:** `POST /api/verify`  
**Headers obligatorios:** `Content-Type: application/json`

#### Ejemplo con cURL

```bash
curl -X POST http://localhost:8000/api/verify \
  -H "Content-Type: application/json" \
  -d '{
    "password":"mi_contraseña_secreta",
    "hash":"$argon2id$v=19$m=65536,t=4,p=1$..."
  }'
```

#### Respuesta satisfactoria

```json
{
  "success": true,
  "valid": true,
  "algorithm": "Argon2id"
}
```

### Rate limit excedido

Si se supera el límite de peticiones permitido:

```json
{
  "error": "Límite de uso excedido. Por favor, espera 1 segundo."
}
```

## ⚙️ Configuración Argon2id

Los parámetros de Argon2id se definen en el backend, no se reciben como input desde el frontend.

Ejemplo:

```php
return [
    'argon2id' => [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 1,
    ],
    'limits' => [
        'max_password_length' => 128,
        'max_hash_length' => 255,
    ],
];
```

Esto evita que un usuario pueda forzar parámetros demasiado costosos y saturar el servidor.

## 🔐 Consideraciones de seguridad

- Uso de `password_hash()` con `PASSWORD_ARGON2ID`.
- Uso de `password_verify()` para verificar contraseñas contra hashes existentes.
- Parámetros Argon2id centralizados en configuración del servidor.
- Validación básica de entrada.
- Límite de longitud para evitar entradas excesivas.
- Rate limiting simple por IP.
- Manejo de preflight CORS mediante petición `OPTIONS`.
- Archivo `rate_limits.json` excluido del repositorio.
- Archivo `.env` excluido del repositorio.

> Este proyecto tiene finalidad educativa y demostrativa. Para producción sería recomendable añadir HTTPS, configuración más estricta de CORS, un sistema de rate limiting más robusto, almacenamiento persistente adecuado y pruebas automatizadas.

## 🛣️ Roadmap

- [x] Refactorización del backend para mejorar la seguridad en los parámetros de entrada.
- [x] Exponer una URL estándar para la API: `POST /api/hash`.
- [x] Añadir endpoint de verificación: `POST /api/verify`.
- [x] Extraer inicialización común de API.
- [x] Configuración parametrizable de Argon2id.
- [x] Dockerización básica del entorno.
- [x] Configuración del frontend mediante `.env`.
- [ ] Integración de SQLite o Redis para el rate limiting en entornos de mayor concurrencia.
- [ ] Tests automatizados.

## 📄 Licencia

Este proyecto es de código abierto. Puedes utilizarlo, modificarlo y aprender de él.
