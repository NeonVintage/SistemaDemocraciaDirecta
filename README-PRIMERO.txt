╔═══════════════════════════════════════════════════════════════════╗
║                                                                   ║
║             ¡BIENVENIDO AL SISTEMA DEMOCRACIA DIRECTA!           ║
║                      🇦🇷 ARGENTINA - VERSIÓN LIBRE 🇦🇷              ║
║                                                                   ║
╚═══════════════════════════════════════════════════════════════════╝

⚠️  LEE ESTO PRIMERO ANTES DE INSTALAR  ⚠️

═══════════════════════════════════════════════════════════════════
 🎯 ¿QUÉ ES ESTE SISTEMA?
═══════════════════════════════════════════════════════════════════

Un sistema web completo de votación para democracia directa donde
cualquier usuario registrado puede:

✓ Proponer nuevas leyes
✓ Votar leyes (a favor o en contra)
✓ Comentar y debatir propuestas
✓ Ver leyes aprobadas y rechazadas

═══════════════════════════════════════════════════════════════════
 ⚙️ PASO 1: CONFIGURACIÓN (OBLIGATORIO)
═══════════════════════════════════════════════════════════════════

⚠️ IMPORTANTE: El sistema NO funcionará hasta que configures email.

¿POR QUÉ?
→ El sistema envía emails para verificar cuentas nuevas
→ Esto previene spam y cuentas falsas
→ Es una medida de seguridad esencial

ACCIÓN REQUERIDA:
─────────────────

1. Copia el archivo "config.example.php" 
2. Renómbralo a "config.php"
3. Abre config.php con tu editor de texto
4. Configura tu servidor SMTP (ver ejemplos abajo)

EJEMPLOS RÁPIDOS:
─────────────────

📧 GMAIL (Recomendado para comenzar):

  define('SMTP_HOST', 'smtp.gmail.com');
  define('SMTP_PORT', 587);
  define('SMTP_USERNAME', 'tu-email@gmail.com');
  define('SMTP_PASSWORD', 'contraseña-de-aplicacion');
  define('SMTP_FROM_EMAIL', 'tu-email@gmail.com');
  define('SITE_URL', 'https://tudominio.com');

  ⚠️ Gmail requiere "Contraseña de aplicación"
  Créala en: https://myaccount.google.com/apppasswords

📧 OUTLOOK:

  define('SMTP_HOST', 'smtp-mail.outlook.com');
  define('SMTP_PORT', 587);
  define('SMTP_USERNAME', 'tu-email@outlook.com');
  define('SMTP_PASSWORD', 'tu-contraseña');
  define('SMTP_FROM_EMAIL', 'tu-email@outlook.com');
  define('SITE_URL', 'https://tudominio.com');

═══════════════════════════════════════════════════════════════════
 📚 PASO 2: LEE LA DOCUMENTACIÓN
═══════════════════════════════════════════════════════════════════

Tenemos MÚLTIPLES guías según tus necesidades:

┌─────────────────────────┬──────────────────────────────────┐
│ ARCHIVO                 │ CONTENIDO                        │
├─────────────────────────┼──────────────────────────────────┤
│ CONFIGURACION.txt       │ ★ GUÍA COMPLETA DE CONFIG        │
│                         │   Todos los proveedores SMTP     │
│                         │   Ejemplos detallados            │
│                         │                                  │
│ INSTALL.txt             │ Instalación paso a paso          │
│                         │   Para principiantes             │
│                         │                                  │
│ README.md               │ Documentación técnica completa   │
│                         │   En inglés                      │
│                         │                                  │
│ LEEME.txt               │ Resumen completo en español      │
│                         │   Características y uso          │
│                         │                                  │
│ CAMBIOS-CODIGO-LIBRE.txt│ Qué cambió en esta versión       │
│                         │   Comparación antes/después      │
└─────────────────────────┴──────────────────────────────────┘

🌟 RECOMENDADO PARA COMENZAR:
   1. CONFIGURACION.txt (configurar email)
   2. INSTALL.txt (instalar el sistema)

═══════════════════════════════════════════════════════════════════
 🚀 PASO 3: INSTALAR
═══════════════════════════════════════════════════════════════════

INSTALACIÓN RÁPIDA (3 minutos):

1. ✓ Configuraste config.php (ver Paso 1)
2. ✓ Sube TODOS los archivos a tu servidor web
3. ✓ Accede a: http://tudominio.com/index.html
4. ✓ ¡Funciona!

═══════════════════════════════════════════════════════════════════
 🧪 PASO 4: PROBAR
═══════════════════════════════════════════════════════════════════

VERIFICACIÓN DEL SISTEMA:

1. http://tudominio.com/check-php.php
   → Verifica que PHP funcione

2. http://tudominio.com/debug-api.php
   → Verifica todos los componentes

3. http://tudominio.com/index.html
   → Regístrate y prueba el sistema

═══════════════════════════════════════════════════════════════════
 ❓ PREGUNTAS FRECUENTES
═══════════════════════════════════════════════════════════════════

P: ¿Por qué necesito configurar email?
R: Para verificar cuentas nuevas y prevenir spam/bots

P: ¿Puedo usar email gratis?
R: ¡Sí! Gmail, Outlook, SendGrid, Mailgun tienen planes gratis

P: ¿Es complicado configurar?
R: No, solo copia y pega 6 líneas de código en config.php

P: ¿Funciona sin base de datos?
R: ¡Sí! Usa archivos JSON, no necesita MySQL ni nada

P: ¿Necesito Node.js, npm, o XAMPP?
R: ¡No! Solo PHP. Funciona en cualquier hosting con PHP

P: ¿Funciona en hosting compartido?
R: ¡Sí! Compatible con cPanel, Plesk, etc.

P: ¿Es realmente gratis?
R: Sí, 100% código libre y gratuito

═══════════════════════════════════════════════════════════════════
 🆘 ¿NECESITAS AYUDA?
═══════════════════════════════════════════════════════════════════

Si tienes problemas:

1. Lee CONFIGURACION.txt (guía completa de configuración)
2. Lee INSTALL.txt (solución de problemas)
3. Usa check-php.php (diagnóstico automático)
4. Revisa SOLUCION-ERROR-500.txt (si ves error 500)

═══════════════════════════════════════════════════════════════════
 ✅ CHECKLIST RÁPIDO
═══════════════════════════════════════════════════════════════════

Antes de instalar, verifica:

□ Tienes acceso a un servidor con PHP 7.4+
□ Tienes un email para usar como SMTP
  (Gmail, Outlook, o el de tu hosting)
□ Copiaste config.example.php a config.php
□ Configuraste las 6 líneas de SMTP en config.php
□ Subiste TODOS los archivos al servidor

Si marcaste todo: ¡Estás listo!

═══════════════════════════════════════════════════════════════════
 🎁 LO QUE OBTIENES
═══════════════════════════════════════════════════════════════════

✨ Sistema completo y funcional
✨ Diseño moderno y responsive
✨ Sin dependencias externas
✨ Sin bases de datos
✨ Fácil de instalar
✨ 100% código libre
✨ Documentación completa
✨ Múltiples idiomas soportados

═══════════════════════════════════════════════════════════════════
 🚦 PRÓXIMOS PASOS
═══════════════════════════════════════════════════════════════════

1. AHORA → Abre CONFIGURACION.txt
2. LUEGO → Configura config.php
3. DESPUÉS → Sube archivos al servidor
4. FINALMENTE → ¡Disfruta tu sistema de democracia directa!

═══════════════════════════════════════════════════════════════════

              ¡Gracias por elegir Código Libre! 🎉

═══════════════════════════════════════════════════════════════════

