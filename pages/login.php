<?php
// Process login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'dashboard';
    
    // Validate input
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        // Check if user exists
        $user = $db->fetchRow("SELECT * FROM users WHERE email = ?", [$email]);
        
        if ($user && verifyPassword($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Update last login time
            $db->query("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);
            
            // Redirect to dashboard or requested page
            header("Location: index.php?page=$redirect");
            exit;
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>

<div class="auth-container">
    <div class="auth-form">
        <h2>Login to Your Account</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <div class="checkbox">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                <a href="index.php?page=forgot-password" class="forgot-password">Forgot Password?</a>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </div>
        </form>
        
        <div class="auth-footer">
            <p>Don't have an account? <a href="index.php?page=register">Register</a></p>
        </div>
    </div>
</div>