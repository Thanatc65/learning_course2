document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.getElementById('navMenu');
    
    if (mobileMenuToggle && navMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }
    
    // Testimonial slider
    const testimonialSlider = document.getElementById('testimonialSlider');
    if (testimonialSlider) {
        let currentSlide = 0;
        const slides = testimonialSlider.querySelectorAll('.testimonial');
        const totalSlides = slides.length;
        
        // Hide all slides except the first one
        for (let i = 1; i < totalSlides; i++) {
            slides[i].style.display = 'none';
        }
        
        // Auto-rotate slideses every 5 seconds
        setInterval(function() {
            slides[currentSlide].style.display = 'none';
            currentSlide = (currentSlide + 1) % totalSlides;
            slides[currentSlide].style.display = 'block';
        }, 5000);
    }
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                event.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
    
    // Password confirmation validation
    const passwordFields = document.querySelectorAll('input[type="password"][id="password"]');
    passwordFields.forEach(function(passwordField) {
        const form = passwordField.closest('form');
        const confirmPasswordField = form.querySelector('input[id="confirm_password"]');
        
        if (confirmPasswordField) {
            form.addEventListener('submit', function(event) {
                if (passwordField.value !== confirmPasswordField.value) {
                    event.preventDefault();
                    alert('Passwords do not match.');
                    confirmPasswordField.classList.add('is-invalid');
                }
            });
        }
    });
    
    // Course progress bars animation
    const progressBars = document.querySelectorAll('.progress');
    progressBars.forEach(function(bar) {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(function() {
            bar.style.width = width;
            bar.style.transition = 'width 1s ease-in-out';
        }, 100);
    });
    
    // File input preview for course image upload
    const imageInput = document.getElementById('image');
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewContainer = document.createElement('div');
                    previewContainer.className = 'image-preview';
                    previewContainer.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                    
                    const existingPreview = imageInput.parentElement.querySelector('.image-preview');
                    if (existingPreview) {
                        existingPreview.remove();
                    }
                    
                    imageInput.parentElement.appendChild(previewContainer);
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Course content editor (for create/edit course pages)
    const addModuleBtn = document.getElementById('addModuleBtn');
    const modulesContainer = document.getElementById('modulesContainer');
    
    if (addModuleBtn && modulesContainer) {
        let moduleCount = document.querySelectorAll('.module').length;
        
        addModuleBtn.addEventListener('click', function() {
            moduleCount++;
            
            const moduleHtml = `
                <div class="module" data-module-id="${moduleCount}">
                    <div class="module-header">
                        <h3>Module ${moduleCount}</h3>
                        <button type="button" class="btn btn-sm btn-outline remove-module">Remove</button>
                    </div>
                    <div class="form-group">
                        <label for="module_title_${moduleCount}">Module Title</label>
                        <input type="text" id="module_title_${moduleCount}" name="modules[${moduleCount}][title]" required>
                    </div>
                    <div class="form-group">
                        <label for="module_description_${moduleCount}">Module Description</label>
                        <textarea id="module_description_${moduleCount}" name="modules[${moduleCount}][description]" rows="3"></textarea>
                    </div>
                    <div class="module-items">
                        <h4>Module Content</h4>
                        <div class="module-items-container" data-module-id="${moduleCount}"></div>
                        <div class="module-actions">
                            <button type="button" class="btn btn-sm btn-outline add-lesson" data-module-id="${moduleCount}">Add Lesson</button>
                            <button type="button" class="btn btn-sm btn-outline add-quiz" data-module-id="${moduleCount}">Add Quiz</button>
                            <button type="button" class="btn btn-sm btn-outline add-assignment" data-module-id="${moduleCount}">Add Assignment</button>
                        </div>
                    </div>
                </div>
            `;
            
            modulesContainer.insertAdjacentHTML('beforeend', moduleHtml);
        });
        
        // Event delegation for dynamically added elements
        modulesContainer.addEventListener('click', function(event) {
            // Remove module
            if (event.target.classList.contains('remove-module')) {
                const module = event.target.closest('.module');
                if (confirm('Are you sure you want to remove this module?')) {
                    module.remove();
                }
            }
            
            // Add lesson
            if (event.target.classList.contains('add-lesson')) {
                const moduleId = event.target.getAttribute('data-module-id');
                const itemsContainer = document.querySelector(`.module-items-container[data-module-id="${moduleId}"]`);
                const itemCount = itemsContainer.querySelectorAll('.module-item').length + 1;
                
                const lessonHtml = `
                    <div class="module-item lesson" data-item-id="${itemCount}">
                        <div class="module-item-header">
                            <h5>Lesson ${itemCount}</h5>
                            <button type="button" class="btn btn-sm btn-outline remove-item">Remove</button>
                        </div>
                        <div class="form-group">
                            <label for="module_${moduleId}_lesson_${itemCount}_title">Lesson Title</label>
                            <input type="text" id="module_${moduleId}_lesson_${itemCount}_title" name="modules[${moduleId}][items][${itemCount}][title]" required>
                        </div>
                        <div class="form-group">
                            <label for="module_${moduleId}_lesson_${itemCount}_content">Lesson Content</label>
                            <textarea id="module_${moduleId}_lesson_${itemCount}_content" name="modules[${moduleId}][items][${itemCount}][content]" rows="5"></textarea>
                        </div>
                        <input type="hidden" name="modules[${moduleId}][items][${itemCount}][type]" value="lesson">
                    </div>
                `;
                
                itemsContainer.insertAdjacentHTML('beforeend', lessonHtml);
            }
            
            // Add quiz
            if (event.target.classList.contains('add-quiz')) {
                const moduleId = event.target.getAttribute('data-module-id');
                const itemsContainer = document.querySelector(`.module-items-container[data-module-id="${moduleId}"]`);
                const itemCount = itemsContainer.querySelectorAll('.module-item').length + 1;
                
                const quizHtml = `
                    <div class="module-item quiz" data-item-id="${itemCount}">
                        <div class="module-item-header">
                            <h5>Quiz ${itemCount}</h5>
                            <button type="button" class="btn btn-sm btn-outline remove-item">Remove</button>
                        </div>
                        <div class="form-group">
                            <label for="module_${moduleId}_quiz_${itemCount}_title">Quiz Title</label>
                            <input type="text" id="module_${moduleId}_quiz_${itemCount}_title" name="modules[${moduleId}][items][${itemCount}][title]" required>
                        </div>
                        <div class="form-group">
                            <label for="module_${moduleId}_quiz_${itemCount}_description">Quiz Description</label>
                            <textarea id="module_${moduleId}_quiz_${itemCount}_description" name="modules[${moduleId}][items][${itemCount}][description]" rows="3"></textarea>
                        </div>
                        <div class="quiz-questions" data-module-id="${moduleId}" data-item-id="${itemCount}">
                            <h6>Questions</h6>
                            <div class="quiz-questions-container"></div>
                            <button type="button" class="btn btn-sm btn-outline add-question" data-module-id="${moduleId}" data-item-id="${itemCount}">Add Question</button>
                        </div>
                        <input type="hidden" name="modules[${moduleId}][items][${itemCount}][type]" value="quiz">
                    </div>
                `;
                
                itemsContainer.insertAdjacentHTML('beforeend', quizHtml);
            }
            
            // Add assignment
            if (event.target.classList.contains('add-assignment')) {
                const moduleId = event.target.getAttribute('data-module-id');
                const itemsContainer = document.querySelector(`.module-items-container[data-module-id="${moduleId}"]`);
                const itemCount = itemsContainer.querySelectorAll('.module-item').length + 1;
                
                const assignmentHtml = `
                    <div class="module-item assignment" data-item-id="${itemCount}">
                        <div class="module-item-header">
                            <h5>Assignment ${itemCount}</h5>
                            <button type="button" class="btn btn-sm btn-outline remove-item">Remove</button>
                        </div>
                        <div class="form-group">
                            <label for="module_${moduleId}_assignment_${itemCount}_title">Assignment Title</label>
                            <input type="text" id="module_${moduleId}_assignment_${itemCount}_title" name="modules[${moduleId}][items][${itemCount}][title]" required>
                        </div>
                        <div class="form-group">
                            <label for="module_${moduleId}_assignment_${itemCount}_description">Assignment Description</label>
                            <textarea id="module_${moduleId}_assignment_${itemCount}_description" name="modules[${moduleId}][items][${itemCount}][description]" rows="5"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="module_${moduleId}_assignment_${itemCount}_points">Points</label>
                            <input type="number" id="module_${moduleId}_assignment_${itemCount}_points" name="modules[${moduleId}][items][${itemCount}][points]" min="0" value="10">
                        </div>
                        <input type="hidden" name="modules[${moduleId}][items][${itemCount}][type]" value="assignment">
                    </div>
                `;
                
                itemsContainer.insertAdjacentHTML('beforeend', assignmentHtml);
            }
            
            // Remove item
            if (event.target.classList.contains('remove-item')) {
                const item = event.target.closest('.module-item');
                if (confirm('Are you sure you want to remove this item?')) {
                    item.remove();
                }
            }
            
            // Add question
            if (event.target.classList.contains('add-question')) {
                const moduleId = event.target.getAttribute('data-module-id');
                const itemId = event.target.getAttribute('data-item-id');
                const questionsContainer = event.target.closest('.quiz-questions').querySelector('.quiz-questions-container');
                let questionCount = questionsContainer.querySelectorAll('.quiz-question').length + 1;
                
                const questionHtml = `
                    <div class="quiz-question" data-question-id="${questionCount}">
                        <div class="question-header">
                            <h6>Question ${questionCount}</h6>
                            <button type="button" class="btn btn-sm btn-outline remove-question">Remove</button>
                        </div>
                        <div class="form-group">
                            <label for="module_${moduleId}_item_${itemId}_question_${questionCount}_text">Question Text</label>
                            <textarea id="module_${moduleId}_item_${itemId}_question_${questionCount}_text" name="modules[${moduleId}][items][${itemId}][questions][${questionCount}][text]" rows="2" required></textarea>
                        </div>
                        <div class="question-options" data-module-id="${moduleId}" data-item-id="${itemId}" data-question-id="${questionCount}">
                            <div class="question-options-container"></div>
                            <button type="button" class="btn btn-sm btn-outline add-option" data-module-id="${moduleId}" data-item-id="${itemId}" data-question-id="${questionCount}">Add Option</button>
                        </div>
                    </div>
                `;
                
                questionsContainer.insertAdjacentHTML('beforeend', questionHtml);
            }
            
            // Remove question
            if (event.target.classList.contains('remove-question')) {
                const question = event.target.closest('.quiz-question');
                if (confirm('Are you sure you want to remove this question?')) {
                    question.remove();
                }
            }
            
            // Add option
            if (event.target.classList.contains('add-option')) {
                const moduleId = event.target.getAttribute('data-module-id');
                const itemId = event.target.getAttribute('data-item-id');
                const questionId = event.target.getAttribute('data-question-id');
                const optionsContainer = event.target.closest('.question-options').querySelector('.question-options-container');
                let optionCount = optionsContainer.querySelectorAll('.question-option').length + 1;
                
                const optionHtml = `
                    <div class="question-option" data-option-id="${optionCount}">
                        <div class="form-group">
                            <div class="option-row">
                                <input type="text" name="modules[${moduleId}][items][${itemId}][questions][${questionId}][options][${optionCount}][text]" placeholder="Option ${optionCount}" required>
                                <div class="checkbox">
                                    <input type="checkbox" id="module_${moduleId}_item_${itemId}_question_${questionId}_option_${optionCount}_correct" name="modules[${moduleId}][items][${itemId}][questions][${questionId}][options][${optionCount}][correct]" value="1">
                                    <label for="module_${moduleId}_item_${itemId}_question_${questionId}_option_${optionCount}_correct">Correct</label>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline remove-option">Remove</button>
                            </div>
                        </div>
                    </div>
                `;
                
                optionsContainer.insertAdjacentHTML('beforeend', optionHtml);
            }
            
            // Remove option
            if (event.target.classList.contains('remove-option')) {
                const option = event.target.closest('.question-option');
                option.remove();
            }
        });
    }
});