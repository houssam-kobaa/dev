<?php
// Security Headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// Database Constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'pizza_delivery');
define('DB_USER', 'restricted_user');
define('DB_PASS', 'complex_password_!234');

// Admin Credentials
define('ADMIN_USERNAME', 'admin@pizza');
define('ADMIN_PASSWORD_HASH', password_hash('SecureP@ss123', PASSWORD_BCRYPT));

// Rate Limiting
define('API_RATE_LIMIT', 100); // Requests per minute
?>