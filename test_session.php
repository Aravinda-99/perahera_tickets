<?php
/**
 * Session Test Script
 * Use this to diagnose session issues on the server
 */
echo "<h2>Session Diagnostics</h2>";
echo "<pre>";

// Test 1: Session configuration
echo "=== Session Configuration ===\n";
echo "Session save path: " . session_save_path() . "\n";
echo "Session name: " . session_name() . "\n";
echo "Session cookie parameters: " . print_r(session_get_cookie_params(), true) . "\n";

// Test 2: Start session
echo "\n=== Starting Session ===\n";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    echo "Session started: " . (session_status() === PHP_SESSION_ACTIVE ? "Yes" : "No") . "\n";
    echo "Session ID: " . session_id() . "\n";
} else {
    echo "Session already active\n";
}

// Test 3: Check PHP configuration
echo "\n=== PHP Configuration ===\n";
echo "session.auto_start: " . ini_get('session.auto_start') . "\n";
echo "session.use_cookies: " . ini_get('session.use_cookies') . "\n";
echo "session.use_only_cookies: " . ini_get('session.use_only_cookies') . "\n";
echo "session.cookie_httponly: " . ini_get('session.cookie_httponly') . "\n";

// Test 4: Check current session variables
echo "\n=== Current Session Variables ===\n";
if (isset($_SESSION)) {
    echo "Session array:\n";
    print_r($_SESSION);
} else {
    echo "No session data\n";
}

echo "</pre>";

// Test 5: Set a test session variable
$_SESSION['test_var'] = 'test_value_' . time();
echo "<p><strong>Test session variable set!</strong> Refresh the page to verify it persists.</p>";
?>

