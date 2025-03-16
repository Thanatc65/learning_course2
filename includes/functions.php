<?php
// Utility functions for the application

// Sanitize input data
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Generate a secure password hash
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Verify password against hash
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check user role
function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == $role;
}

// Generate a random token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Format date
function formatDate($date, $format = 'd M Y') {
    $timestamp = strtotime($date);
    return date($format, $timestamp);
}

// Calculate progress percentage
function calculateProgress($completed, $total) {
    if ($total == 0) return 0;
    return round(($completed / $total) * 100);
}

// Get file extension
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

// Check if file extension is allowed
function isAllowedExtension($extension) {
    return in_array($extension, ALLOWED_EXTENSIONS);
}

// Generate a unique filename
function generateUniqueFilename($filename) {
    $extension = getFileExtension($filename);
    return uniqid() . '.' . $extension;
}

// Redirect with a message
function redirectWithMessage($url, $message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header("Location: $url");
    exit;
}

// Display flash message
function displayMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'];
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        return "<div class='alert alert-$type'>$message</div>";
    }
    return '';
}