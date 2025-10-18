<?php
// Enable error logging (not display)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to catch any errors
ob_start();

// Load configuration
if (!file_exists('config.php')) {
    die(json_encode([
        'success' => false,
        'message' => 'Error: config.php no encontrado. Por favor copia config.php de config.example.php y configúralo.'
    ]));
}
require_once 'config.php';

try {
    session_start();
} catch (Exception $e) {
    // Session already started or error
}

header('Content-Type: application/json; charset=utf-8');

// Create data directories if they don't exist
$directories = ['data', 'data/users', 'data/laws', 'data/comments', 'data/votes'];
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Get JSON input
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

// If JSON decode fails, return error
if (json_last_error() !== JSON_ERROR_NONE && !empty($rawInput)) {
    sendResponse(false, 'Invalid JSON input');
}

$action = isset($input['action']) ? $input['action'] : '';

// Response function
function sendResponse($success, $message = '', $data = []) {
    // Clear any output buffer
    if (ob_get_length()) ob_clean();
    
    $response = array_merge(['success' => $success, 'message' => $message], $data);
    echo json_encode($response);
    exit;
}

// Generate unique ID
function generateId() {
    return uniqid() . bin2hex(random_bytes(8));
}

// Encrypt sensitive data (like email)
function encryptData($data) {
    $key = defined('ENCRYPTION_KEY') ? ENCRYPTION_KEY : 'default-key-change-in-config';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

// Decrypt sensitive data
function decryptData($data) {
    $key = defined('ENCRYPTION_KEY') ? ENCRYPTION_KEY : 'default-key-change-in-config';
    $parts = explode('::', base64_decode($data), 2);
    if (count($parts) !== 2) return false;
    list($encrypted_data, $iv) = $parts;
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
}

// Create email hash for searching (deterministic)
function hashEmail($email) {
    return hash('sha256', strtolower(trim($email)));
}

// Email sending function
function sendEmail($to, $subject, $body) {
    $from = SMTP_FROM_EMAIL;
    $fromName = SMTP_FROM_NAME;
    
    $headers = "From: {$fromName} <{$from}>\r\n";
    $headers .= "Reply-To: {$from}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Configure PHP mail settings for SMTP
    if (defined('SMTP_HOST') && SMTP_HOST !== 'mail.tudominio.com') {
        ini_set('SMTP', SMTP_HOST);
        ini_set('smtp_port', SMTP_PORT);
        ini_set('sendmail_from', $from);
    }
    
    // Try to send email
    try {
        $result = @mail($to, $subject, $body, $headers);
        return $result;
    } catch (Exception $e) {
        // Log error but continue
        error_log("Email error: " . $e->getMessage());
        return false;
    }
}

// Load all users
function loadUsers() {
    $usersFile = 'data/users.json';
    if (!file_exists($usersFile)) {
        file_put_contents($usersFile, json_encode([]));
        return [];
    }
    $content = file_get_contents($usersFile);
    $users = json_decode($content, true);
    return is_array($users) ? $users : [];
}

// Save users
function saveUsers($users) {
    file_put_contents('data/users.json', json_encode($users, JSON_PRETTY_PRINT));
}

// Load all laws
function loadLaws() {
    $lawsFile = 'data/laws.json';
    if (!file_exists($lawsFile)) {
        file_put_contents($lawsFile, json_encode([]));
        return [];
    }
    $content = file_get_contents($lawsFile);
    $laws = json_decode($content, true);
    return is_array($laws) ? $laws : [];
}

// Save laws
function saveLaws($laws) {
    file_put_contents('data/laws.json', json_encode($laws, JSON_PRETTY_PRINT));
}

// Load comments for a law
function loadComments($lawId) {
    $commentsFile = "data/comments/" . basename($lawId) . ".json";
    if (!file_exists($commentsFile)) {
        return [];
    }
    $content = file_get_contents($commentsFile);
    $comments = json_decode($content, true);
    return is_array($comments) ? $comments : [];
}

// Save comments for a law
function saveComments($lawId, $comments) {
    $commentsFile = "data/comments/" . basename($lawId) . ".json";
    file_put_contents($commentsFile, json_encode($comments, JSON_PRETTY_PRINT));
}

// Load votes for a law
function loadLawVotes($lawId) {
    $votesFile = "data/votes/law_" . basename($lawId) . ".json";
    if (!file_exists($votesFile)) {
        return [];
    }
    $content = file_get_contents($votesFile);
    $votes = json_decode($content, true);
    return is_array($votes) ? $votes : [];
}

// Save votes for a law
function saveLawVotes($lawId, $votes) {
    $votesFile = "data/votes/law_" . basename($lawId) . ".json";
    file_put_contents($votesFile, json_encode($votes, JSON_PRETTY_PRINT));
}

// Load comment votes
function loadCommentVotes($commentId) {
    $votesFile = "data/votes/comment_" . basename($commentId) . ".json";
    if (!file_exists($votesFile)) {
        return [];
    }
    $content = file_get_contents($votesFile);
    $votes = json_decode($content, true);
    return is_array($votes) ? $votes : [];
}

// Save comment votes
function saveCommentVotes($commentId, $votes) {
    $votesFile = "data/votes/comment_" . basename($commentId) . ".json";
    file_put_contents($votesFile, json_encode($votes, JSON_PRETTY_PRINT));
}

// Get current user
function getCurrentUser() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $users = loadUsers();
    foreach ($users as $user) {
        if ($user['id'] === $_SESSION['user_id']) {
            // Decrypt email for use in the application
            if (isset($user['email_encrypted'])) {
                $user['email'] = decryptData($user['email_encrypted']);
            }
            return $user;
        }
    }
    
    return null;
}

// Handle actions
switch ($action) {
    case 'register':
        $nombre = isset($input['nombre']) ? trim($input['nombre']) : '';
        $apellido = isset($input['apellido']) ? trim($input['apellido']) : '';
        $email = isset($input['email']) ? trim($input['email']) : '';
        $password = isset($input['password']) ? $input['password'] : '';
        
        if (empty($nombre) || empty($apellido) || empty($email) || empty($password)) {
            sendResponse(false, 'Todos los campos son obligatorios');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendResponse(false, 'Correo electrónico inválido');
        }
        
        $users = loadUsers();
        
        // Check if email already exists (using hash)
        $emailHash = hashEmail($email);
        foreach ($users as $user) {
            if ($user['email_hash'] === $emailHash) {
                sendResponse(false, 'Este correo electrónico ya está registrado');
            }
        }
        
        // Create new user
        $userId = generateId();
        $verificationToken = bin2hex(random_bytes(32));
        
        // Store encrypted email and email hash
        $newUser = [
            'id' => $userId,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email_encrypted' => encryptData($email),  // Email encriptado
            'email_hash' => $emailHash,                // Hash para búsquedas
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'verified' => false,
            'verification_token' => $verificationToken,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $users[] = $newUser;
        saveUsers($users);
        
        // Send verification email
        $baseUrl = defined('SITE_URL') ? SITE_URL : (
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . 
            "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF'])
        );
        $verificationUrl = $baseUrl . "/verify.php?token=" . $verificationToken;
        
        $emailBody = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; }
                .content { background: #f8f9fa; padding: 20px; }
                .button { display: inline-block; padding: 12px 30px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; color: #666; font-size: 12px; padding: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Sistema Democracia Directa Argentina</h1>
                </div>
                <div class='content'>
                    <h2>¡Bienvenido/a, {$nombre}!</h2>
                    <p>Gracias por registrarte en el Sistema de Democracia Directa Argentina.</p>
                    <p>Para activar tu cuenta, por favor haz click en el siguiente enlace:</p>
                    <p style='text-align: center;'>
                        <a href='{$verificationUrl}' class='button'>Verificar mi cuenta</a>
                    </p>
                    <p>O copia y pega este enlace en tu navegador:</p>
                    <p style='word-break: break-all; background: white; padding: 10px; border-radius: 5px;'>{$verificationUrl}</p>
                    <p><strong>Nota:</strong> Esta es una versión beta. No ingreses datos reales. La versión final autenticará usando MiArgentina.</p>
                </div>
                <div class='footer'>
                    <p>Sistema Democracia Directa Argentina - Versión Beta</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        sendEmail($email, 'Confirma tu cuenta - Sistema Democracia Directa', $emailBody);
        
        sendResponse(true, 'Registro exitoso. Por favor revisa tu correo para confirmar tu cuenta.');
        break;
        
    case 'login':
        $email = isset($input['email']) ? trim($input['email']) : '';
        $password = isset($input['password']) ? $input['password'] : '';
        
        if (empty($email) || empty($password)) {
            sendResponse(false, 'Todos los campos son obligatorios');
        }
        
        $users = loadUsers();
        
        // Search by email hash
        $emailHash = hashEmail($email);
        foreach ($users as $user) {
            // Support both old format (email) and new format (email_hash)
            $userEmailHash = isset($user['email_hash']) ? $user['email_hash'] : (isset($user['email']) ? hashEmail($user['email']) : '');
            
            if ($userEmailHash === $emailHash) {
                if (!$user['verified']) {
                    sendResponse(false, 'Por favor verifica tu cuenta primero. Revisa tu correo electrónico.');
                }
                
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    
                    // Decrypt email for session (not stored in JSON)
                    $decryptedEmail = isset($user['email_encrypted']) ? decryptData($user['email_encrypted']) : (isset($user['email']) ? $user['email'] : '');
                    
                    sendResponse(true, 'Inicio de sesión exitoso', [
                        'user' => [
                            'id' => $user['id'],
                            'nombre' => $user['nombre'],
                            'apellido' => $user['apellido'],
                            'email' => $decryptedEmail  // Email desencriptado solo para la sesión
                        ]
                    ]);
                } else {
                    sendResponse(false, 'Contraseña incorrecta');
                }
            }
        }
        
        sendResponse(false, 'Usuario no encontrado');
        break;
        
    case 'check_auth':
        $user = getCurrentUser();
        if (!$user) {
            sendResponse(false, 'No autenticado');
        }
        
        // Email already decrypted by getCurrentUser()
        sendResponse(true, 'Autenticado', [
            'user' => [
                'id' => $user['id'],
                'nombre' => $user['nombre'],
                'apellido' => $user['apellido'],
                'email' => isset($user['email']) ? $user['email'] : ''
            ]
        ]);
        break;
        
    case 'logout':
        session_destroy();
        sendResponse(true, 'Sesión cerrada');
        break;
        
    case 'create_law':
        $user = getCurrentUser();
        if (!$user) {
            sendResponse(false, 'No autenticado');
        }
        
        $title = isset($input['title']) ? trim($input['title']) : '';
        $description = isset($input['description']) ? trim($input['description']) : '';
        
        if (empty($title) || empty($description)) {
            sendResponse(false, 'Título y descripción son obligatorios');
        }
        
        $laws = loadLaws();
        
        $newLaw = [
            'id' => generateId(),
            'title' => $title,
            'description' => $description,
            'author_id' => $user['id'],
            'author_name' => $user['nombre'] . ' ' . $user['apellido'],
            'status' => 'propuesta',
            'votes_yes' => 0,
            'votes_no' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $laws[] = $newLaw;
        saveLaws($laws);
        
        sendResponse(true, 'Ley propuesta exitosamente', ['law' => $newLaw]);
        break;
        
    case 'get_laws':
        $user = getCurrentUser();
        if (!$user) {
            sendResponse(false, 'No autenticado');
        }
        
        $laws = loadLaws();
        
        // Update law statuses based on votes
        $minVotes = defined('VOTING_MIN_VOTES') ? VOTING_MIN_VOTES : 10;
        $approvalThreshold = defined('APPROVAL_THRESHOLD') ? APPROVAL_THRESHOLD : 60;
        $rejectionThreshold = defined('REJECTION_THRESHOLD') ? REJECTION_THRESHOLD : 40;
        
        foreach ($laws as &$law) {
            if ($law['status'] === 'propuesta') {
                $totalVotes = $law['votes_yes'] + $law['votes_no'];
                if ($totalVotes >= $minVotes) {
                    $percentage = ($law['votes_yes'] / $totalVotes) * 100;
                    if ($percentage >= $approvalThreshold) {
                        $law['status'] = 'aprobada';
                    } elseif ($percentage <= $rejectionThreshold) {
                        $law['status'] = 'rechazada';
                    }
                }
            }
        }
        saveLaws($laws);
        
        sendResponse(true, '', ['laws' => $laws]);
        break;
        
    case 'get_law_details':
        $user = getCurrentUser();
        if (!$user) {
            sendResponse(false, 'No autenticado');
        }
        
        $lawId = isset($input['law_id']) ? $input['law_id'] : '';
        $laws = loadLaws();
        
        $law = null;
        foreach ($laws as $l) {
            if ($l['id'] === $lawId) {
                $law = $l;
                break;
            }
        }
        
        if (!$law) {
            sendResponse(false, 'Ley no encontrada');
        }
        
        // Check if user has voted
        $votes = loadLawVotes($lawId);
        $userVote = null;
        foreach ($votes as $vote) {
            if ($vote['user_id'] === $user['id']) {
                $userVote = $vote['vote'];
                break;
            }
        }
        $law['user_vote'] = $userVote;
        
        // Load comments
        $comments = loadComments($lawId);
        
        // Add user vote info to comments
        foreach ($comments as &$comment) {
            $commentVotes = loadCommentVotes($comment['id']);
            $userCommentVote = null;
            foreach ($commentVotes as $vote) {
                if ($vote['user_id'] === $user['id']) {
                    $userCommentVote = $vote['vote'];
                    break;
                }
            }
            $comment['user_vote'] = $userCommentVote;
        }
        
        sendResponse(true, '', ['law' => $law, 'comments' => $comments]);
        break;
        
    case 'vote_law':
        $user = getCurrentUser();
        if (!$user) {
            sendResponse(false, 'No autenticado');
        }
        
        $lawId = isset($input['law_id']) ? $input['law_id'] : '';
        $vote = isset($input['vote']) ? $input['vote'] : '';
        
        if (!in_array($vote, ['yes', 'no'])) {
            sendResponse(false, 'Voto inválido');
        }
        
        $laws = loadLaws();
        $lawIndex = null;
        
        foreach ($laws as $index => $l) {
            if ($l['id'] === $lawId) {
                $lawIndex = $index;
                break;
            }
        }
        
        if ($lawIndex === null) {
            sendResponse(false, 'Ley no encontrada');
        }
        
        if ($laws[$lawIndex]['status'] !== 'propuesta') {
            sendResponse(false, 'Esta ley ya no acepta votos');
        }
        
        // Check if user already voted
        $votes = loadLawVotes($lawId);
        foreach ($votes as $v) {
            if ($v['user_id'] === $user['id']) {
                sendResponse(false, 'Ya has votado en esta ley');
            }
        }
        
        // Add vote
        $votes[] = [
            'user_id' => $user['id'],
            'vote' => $vote,
            'created_at' => date('Y-m-d H:i:s')
        ];
        saveLawVotes($lawId, $votes);
        
        // Update vote count
        if ($vote === 'yes') {
            $laws[$lawIndex]['votes_yes']++;
        } else {
            $laws[$lawIndex]['votes_no']++;
        }
        saveLaws($laws);
        
        sendResponse(true, 'Voto registrado exitosamente');
        break;
        
    case 'add_comment':
        $user = getCurrentUser();
        if (!$user) {
            sendResponse(false, 'No autenticado');
        }
        
        $lawId = isset($input['law_id']) ? $input['law_id'] : '';
        $text = isset($input['text']) ? trim($input['text']) : '';
        
        if (empty($text)) {
            sendResponse(false, 'El comentario no puede estar vacío');
        }
        
        $laws = loadLaws();
        $lawExists = false;
        foreach ($laws as $l) {
            if ($l['id'] === $lawId) {
                $lawExists = true;
                break;
            }
        }
        
        if (!$lawExists) {
            sendResponse(false, 'Ley no encontrada');
        }
        
        $comments = loadComments($lawId);
        
        $newComment = [
            'id' => generateId(),
            'law_id' => $lawId,
            'author_id' => $user['id'],
            'author_name' => $user['nombre'] . ' ' . $user['apellido'],
            'text' => $text,
            'upvotes' => 0,
            'downvotes' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $comments[] = $newComment;
        saveComments($lawId, $comments);
        
        sendResponse(true, 'Comentario publicado exitosamente');
        break;
        
    case 'vote_comment':
        $user = getCurrentUser();
        if (!$user) {
            sendResponse(false, 'No autenticado');
        }
        
        $commentId = isset($input['comment_id']) ? $input['comment_id'] : '';
        $vote = isset($input['vote']) ? $input['vote'] : '';
        
        if (!in_array($vote, ['up', 'down'])) {
            sendResponse(false, 'Voto inválido');
        }
        
        // Find the comment
        $laws = loadLaws();
        $comment = null;
        $lawId = null;
        
        foreach ($laws as $law) {
            $comments = loadComments($law['id']);
            foreach ($comments as $index => $c) {
                if ($c['id'] === $commentId) {
                    $comment = $c;
                    $lawId = $law['id'];
                    break 2;
                }
            }
        }
        
        if (!$comment) {
            sendResponse(false, 'Comentario no encontrado');
        }
        
        // Check if user already voted
        $commentVotes = loadCommentVotes($commentId);
        $existingVoteIndex = null;
        foreach ($commentVotes as $index => $v) {
            if ($v['user_id'] === $user['id']) {
                $existingVoteIndex = $index;
                break;
            }
        }
        
        // Load all comments to update
        $comments = loadComments($lawId);
        $commentIndex = null;
        foreach ($comments as $index => $c) {
            if ($c['id'] === $commentId) {
                $commentIndex = $index;
                break;
            }
        }
        
        if ($existingVoteIndex !== null) {
            $oldVote = $commentVotes[$existingVoteIndex]['vote'];
            
            // Remove old vote count
            if ($oldVote === 'up') {
                $comments[$commentIndex]['upvotes']--;
            } else {
                $comments[$commentIndex]['downvotes']--;
            }
            
            // If same vote, remove it (toggle)
            if ($oldVote === $vote) {
                unset($commentVotes[$existingVoteIndex]);
                $commentVotes = array_values($commentVotes);
            } else {
                // Change vote
                $commentVotes[$existingVoteIndex]['vote'] = $vote;
                if ($vote === 'up') {
                    $comments[$commentIndex]['upvotes']++;
                } else {
                    $comments[$commentIndex]['downvotes']++;
                }
            }
        } else {
            // Add new vote
            $commentVotes[] = [
                'user_id' => $user['id'],
                'vote' => $vote,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            if ($vote === 'up') {
                $comments[$commentIndex]['upvotes']++;
            } else {
                $comments[$commentIndex]['downvotes']++;
            }
        }
        
        saveCommentVotes($commentId, $commentVotes);
        saveComments($lawId, $comments);
        
        sendResponse(true, 'Voto registrado');
        break;
        
    default:
        sendResponse(false, 'Acción no válida');
}
?>
