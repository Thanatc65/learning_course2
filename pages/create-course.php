<?php
// Get user information
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'];

// Get user's enrolled courses
$enrolledCourses = $db->fetchAll("
    SELECT c.*, e.progress, e.last_accessed
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    WHERE e.user_id = ?
    ORDER BY e.last_accessed DESC
", [$userId]);

// Get user's course statistics
$stats = [
    'total_courses' => count($enrolledCourses),
    'completed_courses' => 0,
    'in_progress_courses' => 0,
    'not_started_courses' => 0
];

foreach ($enrolledCourses as $course) {
    if ($course['progress'] == 100) {
        $stats['completed_courses']++;
    } elseif ($course['progress'] > 0) {
        $stats['in_progress_courses']++;
    } else {
        $stats['not_started_courses']++;
    }
}

// For instructors, get their created courses
$createdCourses = [];
if ($userRole == ROLE_INSTRUCTOR || $userRole == ROLE_ADMIN) {
    $createdCourses = $db->fetchAll("
        SELECT c.*, COUNT(e.id) as enrollment_count
        FROM courses c
        LEFT JOIN enrollments e ON c.id = e.course_id
        WHERE c.instructor_id = ?
        GROUP BY c.id
        ORDER BY c.created_at DESC
    ", [$userId]);
    
    // Get instructor statistics
    $instructorStats = [
        'total_courses' => count($createdCourses),
        'total_students' => 0,
        'average_rating' => 0
    ];
    
    $totalRating = 0;
    $ratedCourses = 0;
    
    foreach ($createdCourses as $course) {
        $instructorStats['total_students'] += $course['enrollment_count'];
        
        if ($course['rating'] > 0) {
            $totalRating += $course['rating'];
            $ratedCourses++;
        }
    }
    
    if ($ratedCourses > 0) {
        $instructorStats['average_rating'] = round($totalRating / $ratedCourses, 1);
    }
}
?>

<div class="dashboard">
    <h1>Dashboard</h1>
    
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['total_courses']; ?></div>
            <div class="stat-label">Enrolled Courses</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['completed_courses']; ?></div>
            <div class="stat-label">Completed</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['in_progress_courses']; ?></div>
            <div class="stat-label">In Progress</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['not_started_courses']; ?></div>
            <div class="stat-label">Not Started</div>
        </div>
    </div>
    
    <?php if ($userRole == ROLE_INSTRUCTOR || $userRole == ROLE_ADMIN): ?>
        <div class="section">
            <h2>Instructor Statistics</h2>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $instructorStats['total_courses']; ?></div>
                    <div class="stat-label">Created Courses</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-value"><?php echo $instructorStats['total_students']; ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-value"><?php echo $instructorStats['average_rating']; ?></div>
                    <div class="stat-label">Average Rating</div>
                </div>
            </div>
            
            <h2>Your Courses</h2>
            
            <div class="action-bar">
                <a href="index.php?page=create-course" class="btn btn-primary">Create New Course</a>
            </div>
            
            <?php if (count($createdCourses) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Students</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($createdCourses as $course): ?>
                                <tr>
                                    <td><?php echo $course['title']; ?></td>
                                    <td><?php echo $course['category']; ?></td>
                                    <td><?php echo $course['enrollment_count']; ?></td>
                                    <td><?php echo $course['rating'] > 0 ? $course['rating'] : 'Not rated'; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($course['status']); ?>">
                                            <?php echo $course['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="index.php?page=edit-course&id=<?php echo $course['id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                                            <a href="index.php?page=course-details&id=<?php echo $course['id']; ?>" class="btn btn-sm btn-outline">View</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>You haven't created any courses yet.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <div class="section">
        <h2>Your Learning Progress</h2>
        
        <?php if (count($enrolledCourses) > 0): ?>
            <div class="course-progress-list">
                <?php foreach ($enrolledCourses as $course): ?>
                    <div class="course-progress-item">
                        <div class="course-info">
                            <h3><?php echo $course['title']; ?></h3>
                            <p class="course-meta">
                                Last accessed: <?php echo formatDate($course['last_accessed']); ?>
                            </p>
                        </div>
                        
                        <div class="progress-container">
                            <div class="progress-bar">
                                <div class="progress" style="width: <?php echo $course['progress']; ?>%"></div>
                            </div>
                            <div class="progress-value"><?php echo $course['progress']; ?>%</div>
                        </div>
                        
                        <div class="course-actions">
                            <a href="index.php?page=course-content&id=<?php echo $course['id']; ?>" class="btn btn-primary btn-sm">Continue</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>You are not enrolled in any courses yet.</p>
            <a href="index.php?page=courses" class="btn btn-primary">Browse Courses</a>
        <?php endif; ?>
    </div>
</div>

```html project="Learning Course Platform" file="pages/courses.php" type="code"
<?php
// Get filter parameters
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$sort = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'newest';

// Build query
$query = "
    SELECT c.*, u.name as instructor_name, 
    COUNT(e.id) as enrollment_count
    FROM courses c
    JOIN users u ON c.instructor_id = u.id
    LEFT JOIN enrollments e ON c.id = e.course_id
    WHERE c.status = 'Published'
";

$params = [];

if (!empty($category)) {
    $query .= " AND c.category = ?";
    $params[] = $category;
}

if (!empty($search)) {
    $query .= " AND (c.title LIKE ? OR c.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " GROUP BY c.id";

// Add sorting
switch ($sort) {
    case 'popular':
        $query .= " ORDER BY enrollment_count DESC";
        break;
    case 'rating':
        $query .= " ORDER BY c.rating DESC";
        break;
    case 'oldest':
        $query .= " ORDER BY c.created_at ASC";
        break;
    case 'newest':
    default:
        $query .= " ORDER BY c.created_at DESC";
        break;
}

// Get courses
$courses = $db->fetchAll($query, $params);

// Get categories for filter
$categories = $db->fetchAll("
    SELECT DISTINCT category 
    FROM courses 
    WHERE status = 'Published'
    ORDER BY category
");
?>

<div class="courses-page">
    <div class="page-header">
        <h1>Course Catalog</h1>
    </div>
    
    <div class="filter-section">
        <form method="get" action="index.php" class="filter-form">
            <input type="hidden" name="page" value="courses">
            
            <div class="form-group">
                <input type="text" name="search" placeholder="Search courses..." value="<?php echo $search; ?>">
            </div>
            
            <div class="form-group">
                <select name="category">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['category']; ?>" <?php echo $category == $cat['category'] ? 'selected' : ''; ?>>
                            <?php echo $cat['category']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <select name="sort">
                    <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest</option>
                    <option value="popular" <?php echo $sort == 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                    <option value="rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                    <option value="oldest" <?php echo $sort == 'oldest' ? 'selected' : ''; ?>>Oldest</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="index.php?page=courses" class="btn btn-outline">Reset</a>
        </form>
    </div>
    
    <div class="courses-grid">
        <?php if (count($courses) > 0): ?>
            <?php foreach ($courses as $course): ?>
                <div class="course-card">
                    <div class="course-image">
                        <img src="<?php echo $course['image_path'] ? $course['image_path'] : 'assets/images/course-placeholder.jpg'; ?>" alt="<?php echo $course['title']; ?>">
                        <?php if ($course['is_featured']): ?>
                            <div class="featured-badge">Featured</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="course-content">
                        <div class="course-category"><?php echo $course['category']; ?></div>
                        <h3 class="course-title"><?php echo $course['title']; ?></h3>
                        <p class="course-instructor">By <?php echo $course['instructor_name']; ?></p>
                        
                        <div class="course-meta">
                            <div class="course-rating">
                                <?php
                                $rating = $course['rating'];
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating) {
                                        echo '<span class="star filled">★</span>';
                                    } else {
                                        echo '<span class="star">☆</span>';
                                    }
                                }
                                ?>
                                <span class="rating-value"><?php echo number_format($rating, 1); ?></span>
                            </div>
                            
                            <div class="course-students">
                                <span class="icon">👥</span>
                                <?php echo $course['enrollment_count']; ?> students
                            </div>
                        </div>
                        
                        <p class="course-description"><?php echo substr($course['description'], 0, 100) . '...'; ?></p>
                        
                        <div class="course-footer">
                            <div class="course-price">
                                <?php if ($course['price'] > 0): ?>
                                    $<?php echo number_format($course['price'], 2); ?>
                                <?php else: ?>
                                    <span class="free-label">Free</span>
                                <?php endif; ?>
                            </div>
                            
                            <a href="index.php?page=course-details&id=<?php echo $course['id']; ?>" class="btn btn-secondary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">
                <p>No courses found matching your criteria.</p>
                <a href="index.php?page=courses" class="btn btn-primary">View All Courses</a>
            </div>
        <?php endif; ?>
    </div>
</div>