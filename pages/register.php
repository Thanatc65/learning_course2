<?php
// Process registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = isset($_POST['role']) ? sanitize($_POST['role']) : ROLE_LEARNER;
    
    // Validate input
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!isValidEmail($email)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // Check if email already exists
    $existingUser = $db->fetchRow("SELECT id FROM users WHERE email = ?", [$email]);
    if ($existingUser) {
        $errors[] = "Email already in use";
    }
    
    // If no errors, create user
    if (empty($errors)) {
        $hashedPassword = hashPassword($password);
        $token = generateToken();
        
        $result = $db->query(
            "INSERT INTO users (name, email, password, role, verification_token, created_at) VALUES (?, ?, ?, ?, ?, NOW())",
            [$name, $email, $hashedPassword, $role, $token]
        );
        
        if ($result) {
            // Send verification email (in a real application)
            // For now, just redirect to login
            redirectWithMessage("index.php?page=login", "Registration successful! Please login to continue.", "success");
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>

<div class="auth-container">
    <div class="auth-form">
        <h2>Create an Account</h2>
        
        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <small>Password must be at least 8 characters long</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <label>Register as:</label>
                <div class="radio-group">
                    <div class="radio">
                        <input type="radio" id="role_learner" name="role" value="<?php echo ROLE_LEARNER; ?>" checked>
                        <label for="role_learner">Learner</label>
                    </div>
                    <div class="radio">
                        <input type="radio" id="role_instructor" name="role" value="<?php echo ROLE_INSTRUCTOR; ?>">
                        <label for="role_instructor">Instructor</label>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </div>
        </form>
        
        <div class="auth-footer">
            <p>Already have an account? <a href="index.php?page=login">Login</a></p>
        </div>
    </div>
</div>