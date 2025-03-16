<?php
// Check if user is admin
if (!hasRole(ROLE_ADMIN)) {
    redirectWithMessage("index.php", "You don't have permission to access this page.", "error");
}

// Get statistics
$stats = [
    'total_users' => $db->fetchRow("SELECT COUNT(*) as count FROM users")['count'],
    'total_courses' => $db->fetchRow("SELECT COUNT(*) as count FROM courses")['count'],
    'total_enrollments' => $db->fetchRow("SELECT COUNT(*) as count FROM enrollments")['count'],
    'total_revenue' => $db->fetchRow("SELECT SUM(amount) as total FROM payments")['total'] ?? 0
];

// Get recent users
$recentUsers = $db->fetchAll("
    SELECT * FROM users
    ORDER BY created_at DESC
    LIMIT 5
");

// Get recent courses
$recentCourses = $db->fetchAll("
    SELECT c.*, u.name as instructor_name
    FROM courses c
    JOIN users u ON c.instructor_id = u.id
    ORDER BY c.created_at DESC
    LIMIT 5
");

// Get user counts by role
$usersByRole = $db->fetchAll("
    SELECT role, COUNT(*) as count
    FROM users
    GROUP BY role
");

// Format role counts for easier access
$roleCounts = [];
foreach ($usersByRole as $role) {
    $roleCounts[$role['role']] = $role['count'];
}
?>

<div class="admin-dashboard">
    <div class="page-header">
        <h1>Admin Dashboard</h1>
    </div>
    
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-value"><?php echo $stats['total_users']; ?></div>
            <div class="stat-label">Total Users</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div class="stat-value"><?php echo $stats['total_courses']; ?></div>
            <div class="stat-label">Total Courses</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">📝</div>
            <div class="stat-value"><?php echo $stats['total_enrollments']; ?></div>
            <div class="stat-label">Enrollments</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-value">$<?php echo number_format($stats['total_revenue'], 2); ?></div>
            <div class="stat-label">Total Revenue</div>
        </div>
    </div>
    
    <div class="admin-grid">
        <div class="admin-card">
            <h2>User Distribution</h2>
            <div class="user-distribution">
                <div class="distribution-item">
                    <div class="label">Learners</div>
                    <div class="bar">
                        <div class="progress" style="width: <?php echo ($roleCounts[ROLE_LEARNER] / $stats['total_users']) * 100; ?>%"></div>
                    </div>
                    <div class="value"><?php echo $roleCounts[ROLE_LEARNER] ?? 0; ?></div>
                </div>
                
                <div class="distribution-item">
                    <div class="label">Instructors</div>
                    <div class="bar">
                        <div class="progress" style="width: <?php echo ($roleCounts[ROLE_INSTRUCTOR] / $stats['total_users']) * 100; ?>%"></div>
                    </div>
                    <div class="value"><?php echo $roleCounts[ROLE_INSTRUCTOR] ?? 0; ?></div>
                </div>
                
                <div class="distribution-item">
                    <div class="label">Admins</div>
                    <div class="bar">
                        <div class="progress" style="width: <?php echo ($roleCounts[ROLE_ADMIN] / $stats['total_users']) * 100; ?>%"></div>
                    </div>
                    <div class="value"><?php echo $roleCounts[ROLE_ADMIN] ?? 0; ?></div>
                </div>
            </div>
        </div>
        
        <div class="admin-card">
            <h2>Recent Users</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentUsers as $user): ?>
                            <tr>
                                <td><?php echo $user['name']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo $user['role']; ?></td>
                                <td><?php echo formatDate($user['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="index.php?page=manage-users" class="btn btn-outline">View All Users</a>
            </div>
        </div>
        
        <div class="admin-card">
            <h2>Recent Courses</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Instructor</th>
                            <th>Category</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentCourses as $course): ?>
                            <tr>
                                <td><?php echo $course['title']; ?></td>
                                <td><?php echo $course['instructor_name']; ?></td>
                                <td><?php echo $course['category']; ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($course['status']); ?>">
                                        <?php echo $course['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="index.php?page=manage-courses" class="btn btn-outline">View All Courses</a>
            </div>
        </div>
        
        <div class="admin-card">
            <h2>Quick Actions</h2>
            <div class="quick-actions">
                <a href="index.php?page=manage-users" class="btn btn-primary">Manage Users</a>
                <a href="index.php?page=manage-courses" class="btn btn-primary">Manage Courses</a>
                <a href="index.php?page=reports" class="btn btn-primary">View Reports</a>
                <a href="index.php?page=system-settings" class="btn btn-primary">System Settings</a>
            </div>
        </div>
    </div>
</div>