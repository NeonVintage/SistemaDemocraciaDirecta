<?php
// Simplified API for debugging - Shows actual PHP errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "API Simple Debug Version\n\n";

// Try to start session
echo "Starting session... ";
try {
    session_start();
    echo "OK\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    die();
}

// Set header
echo "Setting JSON header... ";
header('Content-Type: application/json');
echo "OK\n";

// Create directories
echo "Creating directories... ";
$directories = ['data', 'data/users', 'data/laws', 'data/comments', 'data/votes'];
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        if (!@mkdir($dir, 0755, true)) {
            echo "ERROR: Cannot create $dir\n";
            die();
        }
    }
}
echo "OK\n";

// Read input
echo "Reading input... ";
$rawInput = file_get_contents('php://input');
echo "Length: " . strlen($rawInput) . "\n";

// Decode JSON
echo "Decoding JSON... ";
if (empty($rawInput)) {
    echo "NO INPUT - Sending test response\n\n";
    echo json_encode(['success' => true, 'message' => 'API is working! No input provided.', 'php_version' => PHP_VERSION]);
    exit;
}

$input = json_decode($rawInput, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSON ERROR: " . json_last_error_msg() . "\n";
    echo json_encode(['success' => false, 'message' => 'Invalid JSON: ' . json_last_error_msg()]);
    exit;
}
echo "OK\n";

// Get action
$action = isset($input['action']) ? $input['action'] : '';
echo "Action: " . $action . "\n\n";

// Simple test response
echo json_encode([
    'success' => true,
    'message' => 'API Simple is working!',
    'received_action' => $action,
    'php_version' => PHP_VERSION,
    'time' => date('Y-m-d H:i:s')
]);
?>

