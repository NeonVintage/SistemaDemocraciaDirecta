<?php
/**
 * Script de migración de emails
 * 
 * Este script migra emails en texto plano a formato encriptado
 * 
 * USO:
 * 1. Asegúrate de tener config.php configurado con ENCRYPTION_KEY
 * 2. Haz backup de data/users.json
 * 3. Ejecuta: php migrate-emails.php
 * 4. Borra este archivo después de usarlo
 * 
 * ⚠️ ADVERTENCIA: Haz backup antes de ejecutar
 */

// Load configuration
if (!file_exists('config.php')) {
    die("ERROR: config.php no encontrado. Configúralo primero.\n");
}
require_once 'config.php';

// Load encryption functions
if (!file_exists('api.php')) {
    die("ERROR: api.php no encontrado.\n");
}

// Define only the necessary functions if not already defined
if (!function_exists('encryptData')) {
    function encryptData($data) {
        $key = defined('ENCRYPTION_KEY') ? ENCRYPTION_KEY : 'default-key-change-in-config';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }
}

if (!function_exists('hashEmail')) {
    function hashEmail($email) {
        return hash('sha256', strtolower(trim($email)));
    }
}

echo "═══════════════════════════════════════════════════════════════════\n";
echo "   SCRIPT DE MIGRACIÓN DE EMAILS\n";
echo "   Sistema Democracia Directa Argentina\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

// Check if ENCRYPTION_KEY is configured
if (!defined('ENCRYPTION_KEY') || ENCRYPTION_KEY === 'cambiar-por-clave-aleatoria-32-caracteres') {
    die("ERROR: Debes configurar ENCRYPTION_KEY en config.php primero.\n");
}

// Load users
$usersFile = 'data/users.json';
if (!file_exists($usersFile)) {
    die("ERROR: data/users.json no encontrado. No hay usuarios para migrar.\n");
}

echo "Cargando usuarios...\n";
$users = json_decode(file_get_contents($usersFile), true);

if (!is_array($users) || empty($users)) {
    die("No hay usuarios en el sistema.\n");
}

echo "Usuarios encontrados: " . count($users) . "\n\n";

// Create backup
$backupFile = 'data/users.json.backup.' . date('Y-m-d-H-i-s');
copy($usersFile, $backupFile);
echo "✓ Backup creado: $backupFile\n\n";

// Migrate users
$migrated = 0;
$skipped = 0;
$errors = 0;

foreach ($users as &$user) {
    $userId = isset($user['id']) ? $user['id'] : 'unknown';
    
    // Check if already migrated
    if (isset($user['email_encrypted']) && isset($user['email_hash'])) {
        echo "⊘ Usuario {$userId}: Ya migrado\n";
        $skipped++;
        continue;
    }
    
    // Check if has email in plain text
    if (!isset($user['email']) || empty($user['email'])) {
        echo "✗ Usuario {$userId}: No tiene email\n";
        $errors++;
        continue;
    }
    
    $email = $user['email'];
    
    try {
        // Encrypt email
        $user['email_encrypted'] = encryptData($email);
        $user['email_hash'] = hashEmail($email);
        
        // Optionally remove plain text email
        // Uncomment next line to remove plain text email from JSON
        // unset($user['email']);
        
        echo "✓ Usuario {$userId}: Email encriptado\n";
        $migrated++;
        
    } catch (Exception $e) {
        echo "✗ Usuario {$userId}: Error - " . $e->getMessage() . "\n";
        $errors++;
    }
}

// Save migrated users
if ($migrated > 0) {
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    echo "\n✓ Cambios guardados en $usersFile\n";
}

// Summary
echo "\n═══════════════════════════════════════════════════════════════════\n";
echo "   RESUMEN DE MIGRACIÓN\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";
echo "Total usuarios:      " . count($users) . "\n";
echo "Migrados:            $migrated\n";
echo "Ya migrados:         $skipped\n";
echo "Errores:             $errors\n";
echo "\n";

if ($migrated > 0) {
    echo "✓ MIGRACIÓN EXITOSA\n\n";
    echo "Próximos pasos:\n";
    echo "1. Verifica que el sistema funcione correctamente\n";
    echo "2. Prueba hacer login con usuarios migrados\n";
    echo "3. Si todo funciona, puedes borrar el backup\n";
    echo "4. BORRA ESTE SCRIPT (migrate-emails.php)\n";
} else {
    echo "ℹ NO SE MIGRÓ NINGÚN USUARIO\n\n";
    echo "Posibles razones:\n";
    echo "- Todos los usuarios ya estaban migrados\n";
    echo "- No hay usuarios con emails en texto plano\n";
}

echo "\n═══════════════════════════════════════════════════════════════════\n";
echo "NOTA: Si deseas remover los emails en texto plano del JSON,\n";
echo "      descomenta la línea 'unset(\$user['email']);' en este script\n";
echo "      y ejecuta nuevamente.\n";
echo "═══════════════════════════════════════════════════════════════════\n";
?>

