document.addEventListener('DOMContentLoaded', function() {
    // Debug navbar-toggler functionality
    console.log('DOM loaded, checking navbar-toggler...');
    
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (navbarToggler) {
        console.log('Navbar toggler found:', navbarToggler);
        
        // Add click event listener to debug
        navbarToggler.addEventListener('click', function(e) {
            console.log('Navbar toggler clicked!');
            console.log('Current aria-expanded:', this.getAttribute('aria-expanded'));
            console.log('Target element:', document.querySelector('#navbarNav'));
        });
        
        // Check if Bootstrap is loaded
        if (typeof bootstrap !== 'undefined') {
            console.log('Bootstrap is loaded');
            
            // Initialize collapse manually if needed
            const collapseElement = document.querySelector('#navbarNav');
            if (collapseElement) {
                const bsCollapse = new bootstrap.Collapse(collapseElement, {
                    toggle: false
                });
                console.log('Bootstrap collapse initialized');
                
                // Add manual toggle functionality as backup
                navbarToggler.addEventListener('click', function(e) {
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';
                    this.setAttribute('aria-expanded', !isExpanded);
                    
                    if (isExpanded) {
                        collapseElement.classList.remove('show');
                    } else {
                        collapseElement.classList.add('show');
                    }
                });
            }
        } else {
            console.error('Bootstrap is not loaded!');
            
            // Fallback manual toggle functionality
            navbarToggler.addEventListener('click', function(e) {
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !isExpanded);
                
                if (isExpanded) {
                    navbarCollapse.classList.remove('show');
                } else {
                    navbarCollapse.classList.add('show');
                }
            });
        }
    } else {
        console.error('Navbar toggler not found!');
    }

    // Navbar Scroll Effect
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Form submission handling
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Create FormData object
            const formData = new FormData(this);
            
            // Send form data using fetch
            fetch('process_contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Thank you for your message! We will get back to you soon.');
                    contactForm.reset();
                } else {
                    alert('There was an error sending your message. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('There was an error sending your message. Please try again.');
            });
        });
    }

    // Add animation classes on scroll
    const animateOnScroll = () => {
        const elements = document.querySelectorAll('.fade-in');
        elements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < window.innerHeight - elementVisible) {
                element.classList.add('active');
            }
        });
    };

    // Initial check for elements in view
    animateOnScroll();
    
    // Listen for scroll events
    window.addEventListener('scroll', animateOnScroll);

    // Tab switching for custom tab buttons
    setupCustomTabs();

    // Connect new upload button to upload logic
    const imageInputExact = document.getElementById('imageUploadAllExact');
    if (imageInputExact) {
        imageInputExact.addEventListener('change', function(e) {
            handleImageUpload(e, 'allGallery');
        });
    }

    // Hamburger toggle for custom navbar
    var hamburger = document.getElementById('navbarHamburger');
    var menu = document.getElementById('navbarMenu');
    document.addEventListener('click', function(e) {
        if (hamburger.contains(e.target)) {
            menu.classList.toggle('open');
        } else if (!menu.contains(e.target)) {
            menu.classList.remove('open');
        }
    });
});

// Tab switching for custom tab buttons
function setupCustomTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn-exact');
    const allPane = document.getElementById('all');
    const menPane = document.getElementById('men');
    const womenPane = document.getElementById('women');

    if (!tabBtns.length || !allPane || !menPane || !womenPane) {
        console.error('Tab elements not found');
        return;
    }

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            tabBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            // Remove show and active classes from all panes
            [allPane, menPane, womenPane].forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Add show and active classes to the correct pane
            if (this.textContent.trim().toUpperCase() === 'ALL') {
                allPane.classList.add('show', 'active');
            } else if (this.textContent.trim().toUpperCase() === 'BRIDE') {
                womenPane.classList.add('show', 'active');
            } else if (this.textContent.trim().toUpperCase() === 'GROOM') {
                menPane.classList.add('show', 'active');
            }
        });
    });
    
    // Set initial state - show ALL tab by default
    if (tabBtns[0]) {
        tabBtns[0].click();
    }
}

function handleImageUpload(event, galleryId) {
    const fileInput = event.target;
    const files = fileInput.files;

    if (files.length > 0) {
        const formData = new FormData();
        formData.append('image', files[0]);

        // Show loading state
        const uploadBtn = fileInput.nextElementSibling;
        const originalBtnText = uploadBtn.innerHTML;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>আপলোড হচ্ছে...';
        uploadBtn.disabled = true;

        // Debug: log only
        console.log('Uploading image:', files[0]);

        fetch('upload_image.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Raw response:', response);
            return response.json();
        })
        .then(data => {
            console.log('Upload response data:', data);
            if (data.success) {
                // Store the image path and show the biodata form
                document.getElementById('uploadedImagePath').value = data.image_path;
                const biodataModal = new bootstrap.Modal(document.getElementById('biodataFormModal'));
                biodataModal.show();
            } else {
                console.error('ছবি আপলোড করতে সমস্যা হয়েছে: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        })
        .finally(() => {
            // Reset the upload button
            uploadBtn.innerHTML = originalBtnText;
            uploadBtn.disabled = false;
            fileInput.value = ''; // Clear the file input
        });
    }
} 