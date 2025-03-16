<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'learning_platform');

// Application configuration
define('SITE_NAME', 'Learning Course Platform');
define('SITE_URL', 'http://localhost/learning-platform');
define('UPLOAD_DIR', 'uploads/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'ppt', 'pptx', 'mp4', 'webm']);
define('MAX_FILE_SIZE', 10485760); // 10MB

// User roles
define('ROLE_ADMIN', 'admin');
define('ROLE_INSTRUCTOR', 'instructor');
define('ROLE_LEARNER', 'learner');

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);