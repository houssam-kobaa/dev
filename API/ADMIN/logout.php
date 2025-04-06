<?php
require_once '../../config.php';

header("Content-Type: application/json");

// 1. Initialize Session Securely
session_start([
    'cookie_httponly' => true,    // Prevent JavaScript access
    'cookie_secure' => true,      // Requires HTTPS
    'use_strict_mode' => true     // Prevent session fixation
]);

// 2. Destroy Session Completely
$_SESSION = []; // Clear session data

// Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

// 3. Security Headers
header("Clear-Site-Data: \"cache\", \"cookies\", \"storage\"");
header("X-Content-Type-Options: nosniff");

// 4. Response
echo json_encode([
    'success' => true,
    'message' => 'Logged out successfully'
]);

// 5. Force Immediate Exit
exit;
?>