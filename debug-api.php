<?php
// Debug version of API to see actual errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debugging API</h2>";

// Test 1: Session
echo "<h3>Test 1: Session</h3>";
try {
    session_start();
    echo "✓ Session started successfully<br>";
} catch (Exception $e) {
    echo "✗ Session error: " . $e->getMessage() . "<br>";
}

// Test 2: Create directories
echo "<h3>Test 2: Create Directories</h3>";
$directories = ['data', 'data/users', 'data/laws', 'data/comments', 'data/votes'];
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        $result = @mkdir($dir, 0755, true);
        echo ($result ? "✓" : "✗") . " Created: $dir<br>";
    } else {
        echo "✓ Exists: $dir<br>";
    }
}

// Test 3: Read JSON input
echo "<h3>Test 3: Read JSON Input</h3>";
$rawInput = file_get_contents('php://input');
echo "Raw input length: " . strlen($rawInput) . "<br>";
if (!empty($rawInput)) {
    $input = json_decode($rawInput, true);
    echo "JSON decoded: " . (is_array($input) ? "✓ Yes" : "✗ No") . "<br>";
    if (is_array($input)) {
        echo "Action: " . (isset($input['action']) ? $input['action'] : 'none') . "<br>";
    }
} else {
    echo "No input provided (use POST request)<br>";
}

// Test 4: Generate ID
echo "<h3>Test 4: Generate ID</h3>";
try {
    $id = uniqid() . bin2hex(random_bytes(8));
    echo "✓ Generated ID: " . $id . "<br>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

// Test 5: Password hash
echo "<h3>Test 5: Password Hash</h3>";
try {
    $hash = password_hash("test123", PASSWORD_DEFAULT);
    echo "✓ Password hashed successfully<br>";
    $verify = password_verify("test123", $hash);
    echo ($verify ? "✓" : "✗") . " Password verification<br>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

// Test 6: File operations
echo "<h3>Test 6: File Operations</h3>";
try {
    $testFile = 'data/test.json';
    $testData = ['test' => 'data'];
    file_put_contents($testFile, json_encode($testData));
    echo "✓ File written<br>";
    
    $content = file_get_contents($testFile);
    echo "✓ File read<br>";
    
    $decoded = json_decode($content, true);
    echo (is_array($decoded) ? "✓" : "✗") . " JSON decoded from file<br>";
    
    unlink($testFile);
    echo "✓ File deleted<br>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

// Test 7: JSON response
echo "<h3>Test 7: JSON Response</h3>";
echo "About to send JSON response...<br>";
echo "<pre>";
$response = ['success' => true, 'message' => 'Test successful', 'data' => ['test' => 123]];
echo json_encode($response, JSON_PRETTY_PRINT);
echo "</pre>";

echo "<hr>";
echo "<h3>All tests completed!</h3>";
echo "<p>If you see this, PHP is working. Now try the actual API endpoint.</p>";
?>

