<?php
// Start session for user authentication
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $isLoggedIn ? $_SESSION['user_role'] : '';

// Get current page from URL parameter, default to home
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Security: Validate page parameter to prevent directory traversal
$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);

// Restrict access to certain pages based on authentication
$restricted_pages = ['dashboard', 'profile', 'courses', 'admin', 'instructor'];
if (in_array($page, $restricted_pages) && !$isLoggedIn) {
    // Redirect to login if trying to access restricted page while not logged in
    header('Location: index.php?page=login&redirect=' . $page);
    exit;
}

// Role-based access control
$admin_pages = ['admin', 'reports'];
$instructor_pages = ['create-course', 'manage-courses'];

if (in_array($page, $admin_pages) && $userRole != 'admin') {
    // Redirect if trying to access admin pages without admin role
    header('Location: index.php?page=dashboard');
    exit;
}

if (in_array($page, $instructor_pages) && $userRole != 'instructor' && $userRole != 'admin') {
    // Redirect if trying to access instructor pages without proper role
    header('Location: index.php?page=dashboard');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Course Platform</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Add responsive design meta tag -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <!-- Header section -->
    <?php include 'includes/header.php'; ?>

    <!-- Main content -->
    <main class="container">
        <?php
        // Include the requested page
        $file_path = 'pages/' . $page . '.php';
        if (file_exists($file_path)) {
            include $file_path;
        } else {
            // Fallback to 404 page if requested page doesn't exist
            include 'pages/404.php';
        }
        ?>
    </main>

    <!-- Footer section -->
    <?php include 'includes/footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
</body>
</html>