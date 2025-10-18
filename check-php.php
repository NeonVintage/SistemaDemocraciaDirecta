<?php
// Simple PHP diagnostic
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP Version: " . PHP_VERSION . "<br>";
echo "PHP is working!<br>";
echo "Session support: " . (function_exists('session_start') ? 'YES' : 'NO') . "<br>";
echo "JSON support: " . (function_exists('json_encode') ? 'YES' : 'NO') . "<br>";
echo "Mail function: " . (function_exists('mail') ? 'YES' : 'NO') . "<br>";
echo "File write permission: " . (is_writable('.') ? 'YES' : 'NO') . "<br>";

// Try to create data directory
if (!file_exists('data')) {
    $result = @mkdir('data', 0755, true);
    echo "Create data/ directory: " . ($result ? 'YES' : 'NO') . "<br>";
} else {
    echo "data/ directory exists: YES<br>";
}

// Try session
session_start();
echo "Session started: YES<br>";

echo "<br>All basic checks passed!";
?>

