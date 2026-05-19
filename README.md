# 🔐 Servicio de Cifrado (Secure Hash Generator)

Una aplicación web moderna y segura diseñada para generar hashes criptográficos irreversibles utilizando el algoritmo **Argon2id** (el estándar actual recomendado para almacenamiento de contraseñas).

Este proyecto ha sido construido con una arquitectura ligera basada en un frontend moderno (React) y un backend eficiente (PHP nativo), diseñado desde cero para ser fácilmente escalable, dockerizable o refactorizable a frameworks más robustos como Symfony en el futuro.

## ✨ Características Principales

- **🛡️ Cifrado Fuerte:** Utiliza `PASSWORD_ARGON2ID` de PHP para ofrecer la máxima resistencia contra ataques de fuerza bruta y diccionarios.
- **⚡ Rate Limiting Inteligente:** Incorpora un sistema de control de accesos (1 petición por segundo por IP) para prevenir ataques de denegación de servicio (DoS) y saturación. Funciona mediante un archivo JSON que se **autolimpia**, eliminando registros antiguos sin necesidad de bases de datos externas ni mantenimiento.
- **🎨 Interfaz Premium (UI/UX):** Frontend construido con React y CSS nativo, presentando un diseño *Dark Mode* con efectos *Glassmorphism*, transiciones suaves y validaciones en tiempo real.
- **🚀 Ligero y Sin Fricciones:** El backend en PHP nativo no requiere dependencias complejas de Composer ni configurar bases de datos. Simplemente clona y ejecuta.

## 🏗️ Arquitectura y Tecnologías

El proyecto está dividido en dos partes principales:

### 1. Backend (`/backend`)
- **PHP 8+ nativo:** Proporciona la API RESTful.
- **`index.php`:** Endpoint principal que gestiona las cabeceras CORS, recibe las peticiones POST y devuelve el hash en formato JSON.
- **`RateLimiter.php`:** Clase encargada de leer/escribir en el archivo `rate_limits.json`, bloqueando IPs abusivas y purgando entradas viejas.

### 2. Frontend (`/frontend`)
- **React 18 + Vite:** Proporciona un entorno de desarrollo ultrarrápido.
- **CSS Nativo:** Estilizado sin librerías externas (sin Tailwind ni Bootstrap), demostrando que se puede lograr un aspecto premium ("WOW factor") solo con CSS moderno.

## ⚙️ Cómo ejecutarlo localmente

Al no tener dependencias de bases de datos (ni MySQL, ni Redis), levantar el proyecto es extremadamente sencillo.

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

## 🛣️ Próximos Pasos (Roadmap)
- [ ] Refactorización del Backend a **Symfony** para estructurar mejor la API.
- [ ] Dockerización completa (contenedores separados para Nginx/PHP-FPM y el build de React).
- [ ] Integración de SQLite o Redis para el Rate Limiting en entornos de producción de alta concurrencia.

## 📄 Licencia
Este proyecto es de código abierto. Siéntete libre de utilizarlo, modificarlo y aprender de él.
