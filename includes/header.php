<header class="main-header">
    <div class="container">
        <div class="logo">
            <a href="index.php">
                <h1><?php echo SITE_NAME; ?></h1>
            </a>
        </div>
        
        <nav class="main-nav">
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php">Home</a></li>
                <li><a href="index.php?page=courses">Courses</a></li>
                
                <?php if (isLoggedIn()): ?>
                    <li><a href="index.php?page=dashboard">Dashboard</a></li>
                    
                    <?php if (hasRole(ROLE_ADMIN)): ?>
                        <li><a href="index.php?page=admin">Admin Panel</a></li>
                    <?php endif; ?>
                    
                    <?php if (hasRole(ROLE_INSTRUCTOR) || hasRole(ROLE_ADMIN)): ?>
                        <li><a href="index.php?page=create-course">Create Course</a></li>
                    <?php endif; ?>
                    
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle">
                            <?php echo $_SESSION['user_name']; ?> <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="index.php?page=profile">Profile</a></li>
                            <li><a href="index.php?page=my-courses">My Courses</a></li>
                            <li><a href="index.php?page=logout">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="index.php?page=login">Login</a></li>
                    <li><a href="index.php?page=register">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<?php echo displayMessage(); ?>