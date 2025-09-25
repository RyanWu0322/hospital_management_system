<?php
/**
 * CSRF Protection Utility Functions
 */

// Generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Get CSRF token HTML input
function getCSRFTokenField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

// Check CSRF token for POST requests
function checkCSRFToken() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if (!verifyCSRFToken($token)) {
            die('CSRF token validation failed. Please refresh the page and try again.');
        }
    }
}
?>