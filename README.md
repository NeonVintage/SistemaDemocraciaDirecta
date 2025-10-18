# Sistema Democracia Directa Argentina

**Versión Beta** - No ingresar datos reales. La versión final autenticará usando MiArgentina.

## Descripción

Sistema web de votación para democracia directa donde todos los usuarios registrados pueden proponer y votar leyes.

## Características

- 📝 **Registro y Login** con verificación por email
- 🗳️ **Propuesta de Leyes** - Cualquier usuario puede proponer nuevas leyes
- ✅ **Votación** - Sistema de votación a favor o en contra
- 💬 **Comentarios** - Sistema de comentarios con votación (los más votados aparecen primero)
- 📊 **Seguimiento** - Leyes propuestas, aprobadas y rechazadas en menús separados
- 🔒 **Seguridad** - Almacenamiento seguro con archivos JSON individuales

## Requisitos

- Servidor web con PHP 7.4 o superior
- Soporte para función `mail()` de PHP o SMTP configurado
- Permisos de escritura en el directorio del proyecto

## Instalación

### 1. Subir archivos al servidor
Sube todos los archivos a tu directorio web (por ejemplo: public_html, www, htdocs)

### 2. Configurar Email (IMPORTANTE)
**El sistema requiere configuración de email para funcionar:**

```bash
# Copia el archivo de configuración de ejemplo
cp config.example.php config.php
```

Luego edita `config.php` y configura tus credenciales de email:

```php
// Servidor SMTP
define('SMTP_HOST', 'mail.tudominio.com');
define('SMTP_PORT', 25);
define('SMTP_USERNAME', 'tu-email@tudominio.com');
define('SMTP_PASSWORD', 'tu-contraseña');
define('SMTP_FROM_EMAIL', 'tu-email@tudominio.com');
define('SMTP_FROM_NAME', 'Sistema Democracia Directa');

// URL de tu sitio
define('SITE_URL', 'https://tudominio.com');
```

**Ejemplos de configuración para proveedores comunes:**

**Gmail:**
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'tu-email@gmail.com');
define('SMTP_PASSWORD', 'tu-contraseña-de-aplicacion'); // No tu contraseña normal
```
*Nota: Debes crear una "Contraseña de aplicación" en tu cuenta de Google*

**Outlook/Hotmail:**
```php
define('SMTP_HOST', 'smtp-mail.outlook.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'tu-email@outlook.com');
define('SMTP_PASSWORD', 'tu-contraseña');
```

**cPanel/Hosting compartido:**
```php
define('SMTP_HOST', 'mail.tudominio.com');
define('SMTP_PORT', 25);
define('SMTP_USERNAME', 'sistema@tudominio.com');
define('SMTP_PASSWORD', 'contraseña-del-email');
```

Ver más ejemplos en `config.example.php`

### 3. Configurar permisos
- El servidor necesita permisos de escritura en el directorio del proyecto
- El sistema creará automáticamente el directorio `data/` y subdirectorios

### 4. Acceder al sistema
- Navega a `http://tudominio.com/index.html`
- ¡Listo! El sistema funcionará inmediatamente

## Estructura del Proyecto

```
democracy/
├── index.html              # Página de login/registro
├── dashboard.html          # Panel principal
├── verify.php             # Verificación de cuentas por email
├── api.php                # Backend API (maneja todas las operaciones)
├── auth.js                # JavaScript para autenticación
├── app.js                 # JavaScript del dashboard
├── styles.css             # Estilos CSS
├── .htaccess              # Configuración de seguridad
├── data/                  # Directorio de datos (creado automáticamente)
│   ├── users.json         # Usuarios registrados
│   ├── laws.json          # Leyes propuestas
│   ├── comments/          # Comentarios por ley
│   └── votes/             # Votos de leyes y comentarios
└── README.md              # Este archivo
```

## Uso

### Registro
1. Completa el formulario con nombre, apellido y email
2. Recibirás un email con un enlace de verificación
3. Haz click en el enlace para activar tu cuenta

### Proponer una Ley
1. Inicia sesión
2. Ve a "Nueva Propuesta"
3. Completa título y descripción
4. Publica la propuesta

### Votar
1. Navega a "Leyes Propuestas"
2. Haz click en una ley para ver detalles
3. Vota a favor o en contra
4. Tu voto se registra y se actualiza el conteo

### Comentar
1. Abre una ley propuesta
2. Escribe tu comentario
3. Los usuarios pueden votar comentarios
4. Los más votados aparecen primero

## Criterios de Aprobación/Rechazo

Una ley pasa de "Propuesta" a "Aprobada" o "Rechazada" cuando:
- Se alcanzan al menos 10 votos totales
- Si el 60% o más votan a favor → **Aprobada**
- Si el 40% o menos votan a favor → **Rechazada**
- Entre 40% y 60% → Permanece como **Propuesta**

## Configuración SMTP

El sistema requiere un servidor SMTP para enviar emails de verificación.

**Archivo de configuración:** `config.php`

Copia `config.example.php` a `config.php` y configura:

- **SMTP_HOST:** Tu servidor SMTP
- **SMTP_PORT:** Puerto (25, 465, 587)
- **SMTP_USERNAME:** Tu usuario de email
- **SMTP_PASSWORD:** Tu contraseña
- **SMTP_FROM_EMAIL:** Email remitente
- **SMTP_FROM_NAME:** Nombre del remitente
- **SITE_URL:** URL completa de tu sitio

**Proveedores soportados:**
- Gmail (smtp.gmail.com:587)
- Outlook (smtp-mail.outlook.com:587)
- SendGrid (smtp.sendgrid.net:587)
- Mailgun (smtp.mailgun.org:587)
- Amazon SES
- Cualquier servidor SMTP compatible

Ver ejemplos completos en `config.example.php`

## Seguridad

- **Emails encriptados:** Los emails se almacenan encriptados (AES-256-CBC) en el JSON
- **Email hashing:** Se usa SHA-256 para búsquedas sin exponer emails
- **Contraseñas hasheadas:** Con `password_hash()` de PHP (bcrypt)
- **Protección de datos:** Directorio `data/` protegido mediante `.htaccess`
- **Validación de sesiones:** En cada petición al API
- **Protección XSS:** Escaping de HTML en frontend
- **Headers de seguridad:** X-Frame-Options, X-XSS-Protection, etc.
- **Clave de encriptación:** Configurable en `config.php` (no se sube a Git)

Ver más detalles en: `SEGURIDAD-EMAILS.txt`

## Compatibilidad

- ✅ Funciona en cualquier servidor con PHP
- ✅ No requiere Node.js
- ✅ No requiere instalación de dependencias
- ✅ No requiere base de datos
- ✅ Responsive (móvil y desktop)

## Soporte

Para reportar problemas o sugerencias:
- Email: sistemademocraciadirecta@neonvintage.com.ar

## Licencia

Software Beta - Uso con fines de prueba y evaluación.

---

**Nota importante:** Esta es una versión beta para pruebas. No ingreses datos personales reales. La versión final implementará autenticación mediante MiArgentina.

