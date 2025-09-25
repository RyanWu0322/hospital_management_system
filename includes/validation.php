<?php
/**
 * Input Validation and Sanitization Functions
 */

// Validate and sanitize username
function validateUsername($username) {
    $username = trim($username);
    if (empty($username)) {
        return false;
    }
    if (strlen($username) < 3 || strlen($username) > 50) {
        return false;
    }
    if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $username)) {
        return false;
    }
    return $username;
}

// Validate password strength
function validatePassword($password) {
    if (empty($password)) {
        return false;
    }
    if (strlen($password) < 6) {
        return false;
    }
    return $password;
}

// Validate patient ID (should be numeric)
function validatePatientId($patient_id) {
    $patient_id = trim($patient_id);
    if (empty($patient_id)) {
        return false;
    }
    if (!is_numeric($patient_id) || $patient_id <= 0) {
        return false;
    }
    return (int)$patient_id;
}

// Validate medical condition
function validateCondition($condition) {
    $condition = trim($condition);
    if (empty($condition)) {
        return false;
    }
    if (strlen($condition) > 100) {
        return false;
    }
    return htmlspecialchars($condition, ENT_QUOTES, 'UTF-8');
}

// Validate remarks
function validateRemarks($remarks) {
    $remarks = trim($remarks);
    if (empty($remarks)) {
        return false;
    }
    if (strlen($remarks) > 1000) {
        return false;
    }
    return htmlspecialchars($remarks, ENT_QUOTES, 'UTF-8');
}

// Validate role selection
function validateRole($role) {
    $allowed_roles = ['doctor', 'nurse', 'admin'];
    if (!in_array($role, $allowed_roles)) {
        return false;
    }
    return $role;
}

// Validate numeric ID
function validateId($id) {
    $id = trim($id);
    if (empty($id)) {
        return false;
    }
    if (!is_numeric($id) || $id <= 0) {
        return false;
    }
    return (int)$id;
}

// Sanitize output for display
function safeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>