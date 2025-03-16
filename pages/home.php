<section class="hero">
    <div class="hero-content">
        <h1>Welcome to the Learning Course Platform</h1>
        <p>Enhance your skills with our comprehensive courses</p>
        <a href="index.php?page=courses" class="btn btn-primary">Browse Courses</a>
    </div>
</section>

<section class="features">
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">📚</div>
            <h3>Diverse Course Catalog</h3>
            <p>Access a wide range of courses across various disciplines</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">🎓</div>
            <h3>Expert Instructors</h3>
            <p>Learn from industry professionals with real-world experience</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Track Your Progress</h3>
            <p>Monitor your learning journey with detailed progress tracking</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">🏆</div>
            <h3>Earn Certificates</h3>
            <p>Receive certificates upon successful course completion</p>
        </div>
    </div>
</section>

<section class="featured-courses">
    <h2>Featured Courses</h2>
    
    <div class="course-grid">
        <?php
        // Fetch featured courses from database
        $featuredCourses = $db->fetchAll("
            SELECT c.*, u.name as instructor_name 
            FROM courses c
            JOIN users u ON c.instructor_id = u.id
            WHERE c.is_featured = 1
            LIMIT 4
        ");
        
        if (count($featuredCourses) > 0) {
            foreach ($featuredCourses as $course) {
                ?>
                <div class="course-card">
                    <div class="course-image">
                        <img src="<?php echo $course['image_path'] ? $course['image_path'] : 'assets/images/course-placeholder.jpg'; ?>" alt="<?php echo $course['title']; ?>">
                    </div>
                    <div class="course-content">
                        <h3><?php echo $course['title']; ?></h3>
                        <p class="instructor">By <?php echo $course['instructor_name']; ?></p>
                        <p class="description"><?php echo substr($course['description'], 0, 100) . '...'; ?></p>
                        <a href="index.php?page=course-details&id=<?php echo $course['id']; ?>" class="btn btn-secondary">View Course</a>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p>No featured courses available at the moment.</p>';
        }
        ?>
    </div>
    
    <div class="view-all">
        <a href="index.php?page=courses" class="btn btn-outline">View All Courses</a>
    </div>
</section>

<section class="testimonials">
    <h2>What Our Students Say</h2>
    
    <div class="testimonial-slider" id="testimonialSlider">
        <div class="testimonial">
            <div class="testimonial-content">
                <p>"The courses on this platform have significantly improved my skills and helped me advance in my career."</p>
            </div>
            <div class="testimonial-author">
                <p><strong>John Doe</strong> - Web Developer</p>
            </div>
        </div>
        
        <div class="testimonial">
            <div class="testimonial-content">
                <p>"The instructors are knowledgeable and the content is well-structured. I highly recommend this platform."</p>
            </div>
            <div class="testimonial-author">
                <p><strong>Jane Smith</strong> - Project Manager</p>
            </div>
        </div>
        
        <div class="testimonial">
            <div class="testimonial-content">
                <p>"I've completed three courses so far, and each one has provided valuable insights and practical knowledge."</p>
            </div>
            <div class="testimonial-author">
                <p><strong>Michael Johnson</strong> - Data Analyst</p>
            </div>
        </div>
    </div>
</section>