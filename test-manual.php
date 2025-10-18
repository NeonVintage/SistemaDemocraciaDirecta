<?php
/**
 * Manual Testing Script
 * 
 * This script helps you test the email functionality and create test users
 * Access: http://yoursite.com/test-manual.php
 * 
 * IMPORTANT: Delete this file in production!
 */

session_start();

// Create data directory if needed
if (!file_exists('data')) {
    mkdir('data', 0755, true);
    mkdir('data/users', 0755, true);
    mkdir('data/laws', 0755, true);
    mkdir('data/comments', 0755, true);
    mkdir('data/votes', 0755, true);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Pruebas - Sistema Democracia Directa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .panel {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { color: #2563eb; }
        h2 { color: #333; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover { background: #1d4ed8; }
        .success { color: #16a34a; font-weight: bold; }
        .error { color: #dc2626; font-weight: bold; }
        .info { background: #dbeafe; padding: 15px; border-radius: 5px; margin: 10px 0; }
        code {
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #f8fafc;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>🧪 Panel de Pruebas del Sistema</h1>
    
    <div class="warning">
        <strong>⚠️ IMPORTANTE:</strong> Este archivo es solo para pruebas. Elimínalo en producción.
    </div>

    <div class="panel">
        <h2>1. Verificación de Sistema</h2>
        <table>
            <tr>
                <th>Componente</th>
                <th>Estado</th>
            </tr>
            <tr>
                <td>Versión PHP</td>
                <td class="<?php echo version_compare(PHP_VERSION, '7.4.0', '>=') ? 'success' : 'error'; ?>">
                    <?php echo PHP_VERSION; ?>
                    <?php echo version_compare(PHP_VERSION, '7.4.0', '>=') ? '✓' : '✗ (Requiere 7.4+)'; ?>
                </td>
            </tr>
            <tr>
                <td>Directorio data/</td>
                <td class="<?php echo is_dir('data') ? 'success' : 'error'; ?>">
                    <?php echo is_dir('data') ? '✓ Existe' : '✗ No existe'; ?>
                </td>
            </tr>
            <tr>
                <td>Permisos de escritura</td>
                <td class="<?php echo is_writable('.') ? 'success' : 'error'; ?>">
                    <?php echo is_writable('.') ? '✓ Correcto' : '✗ Sin permisos'; ?>
                </td>
            </tr>
            <tr>
                <td>Función mail()</td>
                <td class="<?php echo function_exists('mail') ? 'success' : 'error'; ?>">
                    <?php echo function_exists('mail') ? '✓ Disponible' : '✗ No disponible'; ?>
                </td>
            </tr>
            <tr>
                <td>Sesiones PHP</td>
                <td class="<?php echo session_status() === PHP_SESSION_ACTIVE ? 'success' : 'error'; ?>">
                    <?php echo session_status() === PHP_SESSION_ACTIVE ? '✓ Activas' : '✗ Inactivas'; ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="panel">
        <h2>2. Archivos del Sistema</h2>
        <div class="info">
            <?php
            $files = ['index.html', 'dashboard.html', 'api.php', 'verify.php', 'auth.js', 'app.js', 'styles.css', '.htaccess'];
            echo "<strong>Archivos encontrados:</strong><br>";
            foreach ($files as $file) {
                $exists = file_exists($file);
                echo ($exists ? '✓' : '✗') . " $file<br>";
            }
            ?>
        </div>
    </div>

    <div class="panel">
        <h2>3. Crear Usuario de Prueba</h2>
        <p>Este botón crea un usuario verificado para pruebas inmediatas:</p>
        <button onclick="createTestUser()">Crear Usuario de Prueba</button>
        <div id="test-user-result" style="margin-top: 10px;"></div>
        
        <div class="info" style="margin-top: 15px;">
            <strong>Credenciales de prueba:</strong><br>
            Email: <code>prueba@test.com</code><br>
            Contraseña: <code>prueba123</code>
        </div>
    </div>

    <div class="panel">
        <h2>4. Crear Leyes de Ejemplo</h2>
        <p>Crea algunas leyes de ejemplo para probar el sistema:</p>
        <button onclick="createSampleLaws()">Crear Leyes de Ejemplo</button>
        <div id="sample-laws-result" style="margin-top: 10px;"></div>
    </div>

    <div class="panel">
        <h2>5. Ver Usuarios Registrados</h2>
        <button onclick="viewUsers()">Ver Usuarios</button>
        <div id="users-list" style="margin-top: 10px;"></div>
    </div>

    <div class="panel">
        <h2>6. Limpiar Datos</h2>
        <p class="error">⚠️ Esto eliminará todos los usuarios, leyes y votos.</p>
        <button onclick="if(confirm('¿Estás seguro?')) clearData()" style="background: #dc2626;">
            Limpiar Todos los Datos
        </button>
        <div id="clear-result" style="margin-top: 10px;"></div>
    </div>

    <script>
        async function createTestUser() {
            const result = document.getElementById('test-user-result');
            result.innerHTML = 'Creando usuario...';
            
            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'register',
                        nombre: 'Usuario',
                        apellido: 'Prueba',
                        email: 'prueba@test.com',
                        password: 'prueba123'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Manually verify the user
                    const verifyResponse = await fetch('test-manual.php?verify_last=1');
                    result.innerHTML = '<span class="success">✓ Usuario creado y verificado</span><br>Email: prueba@test.com<br>Contraseña: prueba123';
                } else {
                    result.innerHTML = '<span class="error">Error: ' + data.message + '</span>';
                }
            } catch (error) {
                result.innerHTML = '<span class="error">Error de conexión</span>';
            }
        }

        async function createSampleLaws() {
            const result = document.getElementById('sample-laws-result');
            result.innerHTML = 'Creando leyes de ejemplo...';
            
            const laws = [
                {
                    title: 'Ley de Energías Renovables',
                    description: 'Propuesta para incrementar el uso de energías renovables en todo el país. Se propone que para el año 2030, al menos el 50% de la energía consumida provenga de fuentes renovables como solar, eólica e hidroeléctrica.'
                },
                {
                    title: 'Ley de Educación Digital',
                    description: 'Implementación de tecnología en todas las escuelas públicas del país. Cada estudiante recibirá una tablet o computadora portátil para su uso educativo, junto con acceso a internet de alta velocidad.'
                },
                {
                    title: 'Ley de Transparencia Gubernamental',
                    description: 'Todos los gastos del gobierno deben ser publicados en tiempo real en un portal web accesible para todos los ciudadanos. Se incluyen salarios, contrataciones, y todo gasto público mayor a $10,000.'
                }
            ];
            
            result.innerHTML = 'Por favor, crea un usuario de prueba primero y luego inicia sesión.';
        }

        async function viewUsers() {
            const result = document.getElementById('users-list');
            result.innerHTML = 'Cargando...';
            
            try {
                const response = await fetch('test-manual.php?action=list_users');
                const text = await response.text();
                result.innerHTML = text;
            } catch (error) {
                result.innerHTML = '<span class="error">Error al cargar usuarios</span>';
            }
        }

        async function clearData() {
            const result = document.getElementById('clear-result');
            result.innerHTML = 'Limpiando datos...';
            
            try {
                const response = await fetch('test-manual.php?action=clear_data');
                const text = await response.text();
                result.innerHTML = text;
            } catch (error) {
                result.innerHTML = '<span class="error">Error</span>';
            }
        }
    </script>

    <?php
    // Handle AJAX requests
    if (isset($_GET['verify_last'])) {
        $usersFile = 'data/users.json';
        if (file_exists($usersFile)) {
            $users = json_decode(file_get_contents($usersFile), true);
            if (!empty($users)) {
                $lastUser = &$users[count($users) - 1];
                $lastUser['verified'] = true;
                unset($lastUser['verification_token']);
                file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
            }
        }
        exit;
    }

    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'list_users') {
            $usersFile = 'data/users.json';
            if (file_exists($usersFile)) {
                $users = json_decode(file_get_contents($usersFile), true);
                if (empty($users)) {
                    echo '<p>No hay usuarios registrados</p>';
                } else {
                    echo '<table><tr><th>Nombre</th><th>Email</th><th>Verificado</th></tr>';
                    foreach ($users as $user) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) . '</td>';
                        echo '<td>' . htmlspecialchars($user['email']) . '</td>';
                        echo '<td>' . ($user['verified'] ? '✓' : '✗') . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                }
            } else {
                echo '<p>No hay usuarios registrados</p>';
            }
            exit;
        }
        
        if ($_GET['action'] === 'clear_data') {
            $files = ['data/users.json', 'data/laws.json'];
            foreach ($files as $file) {
                if (file_exists($file)) {
                    file_put_contents($file, json_encode([]));
                }
            }
            
            // Clear comments and votes
            $dirs = ['data/comments', 'data/votes'];
            foreach ($dirs as $dir) {
                if (is_dir($dir)) {
                    $files = glob($dir . '/*');
                    foreach ($files as $file) {
                        if (is_file($file)) unlink($file);
                    }
                }
            }
            
            echo '<span class="success">✓ Datos limpiados exitosamente</span>';
            exit;
        }
    }
    ?>

    <div class="panel" style="background: #f8fafc;">
        <h2>📚 Guía Rápida</h2>
        <ol>
            <li>Verifica que todos los componentes estén en verde</li>
            <li>Crea un usuario de prueba</li>
            <li>Ve a <a href="index.html">index.html</a> e inicia sesión</li>
            <li>Prueba crear leyes y votar</li>
            <li><strong>Elimina este archivo en producción</strong></li>
        </ol>
    </div>
</body>
</html>

