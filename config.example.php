<?php
/**
 * Configuración del Sistema de Democracia Directa - EJEMPLO
 * 
 * INSTRUCCIONES:
 * 1. Copia este archivo y renómbralo a "config.php"
 * 2. Configura los valores según tu servidor
 * 3. Guarda los cambios
 * 4. ¡No subas config.php a repositorios públicos!
 */

// ========================================
// CONFIGURACIÓN DE EMAIL / SMTP
// ========================================

// Servidor SMTP
define('SMTP_HOST', 'mail.tudominio.com');          // Ejemplo: mail.tudominio.com, smtp.gmail.com
define('SMTP_PORT', 25);                             // Puertos comunes: 25, 465 (SSL), 587 (TLS)
define('SMTP_USE_TLS', false);                       // true para STARTTLS, false para sin cifrado

// Credenciales SMTP
define('SMTP_USERNAME', 'tu-email@tudominio.com');   // Tu email completo
define('SMTP_PASSWORD', 'tu-contraseña-aqui');       // Tu contraseña de email

// Información del remitente
define('SMTP_FROM_EMAIL', 'tu-email@tudominio.com'); // Email que aparecerá como remitente
define('SMTP_FROM_NAME', 'Sistema Democracia Directa'); // Nombre del remitente

// ========================================
// CONFIGURACIÓN DEL SISTEMA
// ========================================

// URL base del sitio (sin barra al final)
// Ejemplo: 'https://tudominio.com' o 'https://tudominio.com/democracia'
define('SITE_URL', 'http://localhost');

// Nombre del sistema
define('SITE_NAME', 'Sistema Democracia Directa Argentina');

// ========================================
// CRITERIOS DE VOTACIÓN
// ========================================

// Mínimo de votos para determinar si una ley es aprobada/rechazada
define('VOTING_MIN_VOTES', 10);

// Porcentaje necesario para aprobar una ley (%)
define('APPROVAL_THRESHOLD', 60);

// Porcentaje o menor para rechazar una ley (%)
define('REJECTION_THRESHOLD', 40);

// ========================================
// CONFIGURACIÓN DE SEGURIDAD
// ========================================

// Clave de encriptación para datos sensibles (cambiar por una única)
// Genera una clave aleatoria de 32 caracteres
define('ENCRYPTION_KEY', 'cambiar-por-clave-aleatoria-32-caracteres');

// Tiempo de sesión en segundos (3600 = 1 hora)
define('SESSION_TIMEOUT', 3600);

// Longitud mínima de contraseña
define('PASSWORD_MIN_LENGTH', 6);

// ========================================
// RUTAS DEL SISTEMA (No modificar)
// ========================================

define('DATA_DIR', 'data');
define('USERS_FILE', DATA_DIR . '/users.json');
define('LAWS_FILE', DATA_DIR . '/laws.json');

// ========================================
// EJEMPLOS DE CONFIGURACIÓN
// ========================================

/*
──────────────────────────────────────────────────────────────
GMAIL
──────────────────────────────────────────────────────────────
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USE_TLS', true);
define('SMTP_USERNAME', 'tu-email@gmail.com');
define('SMTP_PASSWORD', 'tu-contraseña-de-aplicacion');
define('SMTP_FROM_EMAIL', 'tu-email@gmail.com');
define('SMTP_FROM_NAME', 'Sistema Democracia');

NOTA: Debes habilitar "Verificación en 2 pasos" y crear una 
"Contraseña de aplicación" en tu cuenta de Google.
https://myaccount.google.com/apppasswords

──────────────────────────────────────────────────────────────
OUTLOOK / HOTMAIL
──────────────────────────────────────────────────────────────
define('SMTP_HOST', 'smtp-mail.outlook.com');
define('SMTP_PORT', 587);
define('SMTP_USE_TLS', true);
define('SMTP_USERNAME', 'tu-email@outlook.com');
define('SMTP_PASSWORD', 'tu-contraseña');
define('SMTP_FROM_EMAIL', 'tu-email@outlook.com');
define('SMTP_FROM_NAME', 'Sistema Democracia');

──────────────────────────────────────────────────────────────
CPANEL / HOSTING COMPARTIDO
──────────────────────────────────────────────────────────────
define('SMTP_HOST', 'mail.tudominio.com');
define('SMTP_PORT', 25);
define('SMTP_USE_TLS', false);
define('SMTP_USERNAME', 'sistema@tudominio.com');
define('SMTP_PASSWORD', 'tu-contraseña-de-email');
define('SMTP_FROM_EMAIL', 'sistema@tudominio.com');
define('SMTP_FROM_NAME', 'Sistema Democracia');

──────────────────────────────────────────────────────────────
SENDGRID
──────────────────────────────────────────────────────────────
define('SMTP_HOST', 'smtp.sendgrid.net');
define('SMTP_PORT', 587);
define('SMTP_USE_TLS', true);
define('SMTP_USERNAME', 'apikey');
define('SMTP_PASSWORD', 'SG.tu-api-key-de-sendgrid');
define('SMTP_FROM_EMAIL', 'sistema@tudominio.com');
define('SMTP_FROM_NAME', 'Sistema Democracia');

──────────────────────────────────────────────────────────────
MAILGUN
──────────────────────────────────────────────────────────────
define('SMTP_HOST', 'smtp.mailgun.org');
define('SMTP_PORT', 587);
define('SMTP_USE_TLS', true);
define('SMTP_USERNAME', 'postmaster@tudominio.mailgun.org');
define('SMTP_PASSWORD', 'tu-contraseña-de-mailgun');
define('SMTP_FROM_EMAIL', 'sistema@tudominio.com');
define('SMTP_FROM_NAME', 'Sistema Democracia');

──────────────────────────────────────────────────────────────
AMAZON SES
──────────────────────────────────────────────────────────────
define('SMTP_HOST', 'email-smtp.us-east-1.amazonaws.com');
define('SMTP_PORT', 587);
define('SMTP_USE_TLS', true);
define('SMTP_USERNAME', 'tu-smtp-username-de-aws');
define('SMTP_PASSWORD', 'tu-smtp-password-de-aws');
define('SMTP_FROM_EMAIL', 'sistema@tudominio.com');
define('SMTP_FROM_NAME', 'Sistema Democracia');

*/
?>
