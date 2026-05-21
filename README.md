# 🔐 Secure Hash Generator Service

Aplicación web para generar hashes seguros de contraseñas utilizando **Argon2id**, el algoritmo recomendado actualmente por OWASP para el almacenamiento seguro de credenciales.

Este proyecto ha sido construido con una arquitectura ligera basada en un frontend React y un backend en PHP nativo, diseñado para ser fácilmente escalable, dockerizable, refactorizable a frameworks más robustos o consumido como API.

Su objetivo actual es servir como laboratorio técnico.

## ✨ Características Principales

- **🛡️ Hash:** Generación de hashes de contraseña con Argon2id.
- **⚡ Rate Limit:** Sistema simple de control de accesos por IP para reducir saturación. Funciona mediante un archivo JSON que se autolimpia, eliminando registros antiguos sin necesidad de base de datos externa.
- **🎨 Interfaz (UI/UX):** Frontend construido con React y CSS nativo, con diseño simple, transiciones suaves y validaciones en tiempo real.
- **🚀 Ligero y sin fricciones:** Backend en PHP nativo, sin dependencias complejas de Composer ni base de datos.

## 🏗️ Arquitectura y Tecnologías

### Backend (`/backend`)

- **PHP 8+ nativo:** Proporciona la API.
- **`api/hash/index.php`:** Endpoint principal que gestiona CORS, recibe peticiones POST y devuelve el hash en formato JSON.
- **`RateLimiter.php`:** Clase encargada de leer/escribir en `rate_limits.json`, bloqueando peticiones abusivas y purgando entradas antiguas.

### Frontend (`/frontend`)

- **React 18 + Vite:** Entorno de desarrollo rápido para la interfaz.
- **CSS nativo:** Estilizado sin librerías externas como Tailwind o Bootstrap.

## ⚙️ Cómo ejecutarlo localmente

No requiere base de datos ni servicios externos.

### Levantar el Backend

```bash
php -S localhost:8000 -t backend
```

La API quedará disponible en:

```text
http://localhost:8000
```

### Levantar el Frontend

```bash
cd frontend
npm install
npm run dev
```

La interfaz estará disponible normalmente en:

```text
http://localhost:5173
```

## 🔌 Uso de la API

La API está diseñada para consumirse mediante JSON.

**Endpoint:** `POST /api/hash`  
**Headers obligatorios:** `Content-Type: application/json`

### Ejemplo con cURL

```bash
curl -X POST http://localhost:8000/api/hash \
  -H "Content-Type: application/json" \
  -d '{"password":"mi_contraseña_secreta"}'
```

### Respuesta satisfactoria

```json
{
  "success": true,
  "hash": "$argon2id$v=19$m=65536,t=4,p=1$...",
  "algorithm": "Argon2id"
}
```

### Rate limit excedido

```json
{
  "error": "Límite de uso excedido. Por favor, espera 1 segundo."
}
```

## 🔐 Consideraciones de seguridad

- Uso de `password_hash()` con `PASSWORD_ARGON2ID`.
- Validación básica de entrada.
- Límite de longitud para evitar entradas excesivas.
- Rate limiting simple por IP.
- Manejo de preflight CORS mediante petición `OPTIONS`.

> Este proyecto tiene finalidad educativa y demostrativa. Para producción sería recomendable añadir HTTPS, configuración por variables de entorno, un sistema de rate limiting más robusto y pruebas automatizadas.

## 🛣️ Próximos Pasos (Roadmap)

- [x] Refactorización del backend para mejorar la seguridad en los parámetros de entrada.
- [x] Exponer una URL estándar para la API: `POST /api/hash`.
- [ ] Dockerización completa.
- [ ] Integración de SQLite o Redis para el rate limiting en entornos de mayor concurrencia.
- [ ] Configuración parametrizable de Argon2id.
- [ ] Tests automatizados.

## 📄 Licencia

Este proyecto es de código abierto. Puedes utilizarlo, modificarlo y aprender de él.
