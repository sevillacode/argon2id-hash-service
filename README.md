# 🔐 Secure Hash Generator Service

Aplicación web para generar hashes seguros de contraseñas utilizando **Argon2id**, el algoritmo recomendado actualmente por OWASP para el almacenamiento seguro de credenciales.

El proyecto está construido con una arquitectura ligera basada en:

- **Frontend:** React + Vite
- **Backend:** PHP nativo

Este proyecto ha sido construido con una arquitectura ligera basada en un frontend React y un backend en PHP nativo, diseñado para ser fácilmente escalable, dockerizable o refactorizable a frameworks más robustos o consumido como API.
Su objetivo actual es servir como laboratorio técnico.

## ✨ Características Principales

- **🛡️ Hash:** Generación de hashes de contraseña con Argon2id.
- **⚡ Rate Limit:** Incorpora un sistema de control de accesos simple (1 petición por segundo por IP) para prevenir saturación. Funciona mediante un archivo JSON que se **autolimpia**, eliminando registros antiguos sin necesidad de bases de datos externas ni mantenimiento.
- **🎨 Interfaz (UI/UX):** Frontend construido con React y CSS nativo, presentando un diseño simple, transiciones suaves y validaciones en tiempo real.
- **🚀 Ligero y Sin Fricciones:** El backend en PHP nativo no requiere dependencias complejas de Composer ni configurar bases de datos. Simplemente clona y ejecuta.

## 🏗️ Arquitectura y Tecnologías

El proyecto está dividido en dos partes principales:

### 1. Backend (`/backend`)
- **PHP 8+ nativo:** Proporciona la API.
- **`index.php`:** Endpoint principal que gestiona las cabeceras CORS, recibe las peticiones POST y devuelve el hash en formato JSON.
- **`RateLimiter.php`:** Clase encargada de leer/escribir en el archivo `rate_limits.json`, bloqueando IPs abusivas y purgando entradas viejas.

### 2. Frontend (`/frontend`)
- **React 18 + Vite:** Proporciona un entorno de desarrollo ultrarrápido.
- **CSS Nativo:** Estilizado sin librerías externas (sin Tailwind ni Bootstrap), solo con CSS.

## ⚙️ Cómo ejecutarlo localmente

Al no tener dependencias de bases de datos (ni MySQL, ni Redis), levantar el proyecto es sencillo.

### Levantar el Backend (PHP)
Abre una terminal en la raíz del proyecto y arranca el servidor de desarrollo integrado de PHP:
```bash
php -S localhost:8000 -t backend
```
La API quedará escuchando en `http://localhost:8000`.

### Levantar el Frontend (React)
Abre una segunda terminal, navega a la carpeta del frontend, instala las dependencias y arranca Vite:
```bash
cd frontend
npm install
npm run dev
```
La interfaz de usuario estará disponible (generalmente) en `http://localhost:5173`.

## 🔌 Uso de la API (Endpoints)

La API está diseñada para consumirse mediante JSON. 

**Endpoint:** `POST /index.php`
**Headers obligatorios:** `Content-Type: application/json`

### Ejemplo de Petición (cURL)
```bash
curl -X POST http://localhost:8000/index.php \
  -H "Content-Type: application/json" \
  -d '{"password":"mi_contraseña_secreta"}'
```

### Respuestas Esperadas
- **✅ Éxito (200 OK):**
  ```json
  {
    "success": true,
    "hash": "$argon2id$v=19$m=65536,t=4,p=1$...",
    "algorithm": "Argon2id"
  }
  ```
- **❌ Rate Limit (429 Too Many Requests):** (Si haces más de 1 petición por segundo)
  ```json
  {
    "error": "Límite de uso excedido. Por favor, espera 1 segundo."
  }
  ```

## 🛣️ Próximos Pasos (Roadmap)
- [ ] Refactorización del Backend para aumentar la seguridad en los parametros de entrada.
- [ ] Ofrecer url estandar para API.
- [ ] Dockerización completa (contenedores separados para Nginx/PHP-FPM y el build de React).
- [ ] Integración de SQLite o Redis para el Rate Limiting en entornos de producción de alta concurrencia.

## 📄 Licencia
Este proyecto es de código abierto. Siéntete libre de utilizarlo, modificarlo y aprender de él.
