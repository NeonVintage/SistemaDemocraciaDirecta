<?php
// Email verification page

$token = $_GET['token'] ?? '';

if (empty($token)) {
    die('Token inválido');
}

// Load users
$usersFile = 'data/users.json';
if (!file_exists($usersFile)) {
    die('Error: archivo de usuarios no encontrado');
}

$users = json_decode(file_get_contents($usersFile), true);
$userFound = false;

foreach ($users as &$user) {
    if (isset($user['verification_token']) && $user['verification_token'] === $token) {
        $user['verified'] = true;
        unset($user['verification_token']);
        $userFound = true;
        break;
    }
}

if ($userFound) {
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    $message = '¡Cuenta verificada exitosamente!';
    $success = true;
} else {
    $message = 'Token inválido o expirado';
    $success = false;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Cuenta</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Sistema Democracia Directa Argentina</h1>
            <p class="subtitle">(Versión beta, no ingresar datos reales, la versión final autenticará usando MiArgentina)</p>
        </div>
        
        <div class="auth-container">
            <div style="padding: 2rem; text-align: center;">
                <?php if ($success): ?>
                    <div style="font-size: 4rem; margin-bottom: 1rem;">✅</div>
                    <h2 style="color: #16a34a; margin-bottom: 1rem;"><?php echo $message; ?></h2>
                    <p style="margin-bottom: 1.5rem;">Ya puedes iniciar sesión con tu cuenta.</p>
                    <a href="index.html" class="btn btn-primary" style="display: inline-block; text-decoration: none;">
                        Ir al Login
                    </a>
                <?php else: ?>
                    <div style="font-size: 4rem; margin-bottom: 1rem;">❌</div>
                    <h2 style="color: #dc2626; margin-bottom: 1rem;"><?php echo $message; ?></h2>
                    <p style="margin-bottom: 1.5rem;">Por favor intenta registrarte nuevamente o contacta al soporte.</p>
                    <a href="index.html" class="btn btn-primary" style="display: inline-block; text-decoration: none;">
                        Volver al Inicio
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

