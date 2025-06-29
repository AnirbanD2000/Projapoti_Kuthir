<?php
require_once('db.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch all images from the database with error handling
$sql = "SELECT * FROM biodata_images ORDER BY id DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching data: " . $conn->error);
}

$images = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
}

// Separate images by type
$menImages = array_filter($images, function($img) { return $img['type'] === 'men'; });
$womenImages = array_filter($images, function($img) { return $img['type'] === 'women'; });

// Debug information
error_log("Total images fetched: " . count($images));
error_log("Men images: " . count($menImages));
error_log("Women images: " . count($womenImages));

// Debug output
echo "<!-- Debug Info: -->";
echo "<!-- Total images: " . count($images) . " -->";
echo "<!-- Men images: " . count($menImages) . " -->";
echo "<!-- Women images: " . count($womenImages) . " -->";
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>প্রজাপতি কুটির</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;500;600;700&family=Source+Sans+Pro:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
    <!-- Responsive CSS -->
    <link href="css/responsive.css" rel="stylesheet">
    <style>
        body {
            min-height: 200vh; /* Just to show scrolling */
            font-family: Arial, sans-serif;
        }
        .floating-social-icons {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 9999;
    display: flex;
    gap: 10px;
}
        .floating-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #25d366;
            color: #fff;
            font-size: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
            text-decoration: none;
        }
        .floating-icon.facebook {
            background: #4267B2;
        }
        .floating-icon:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.25);
            transform: translateY(-2px) scale(1.08);
            color: #fff;
        }
    </style>
</head>
<body>
    <!-- Pixel-perfect Navbar Start -->
    <nav class="navbar navbar-exact fixed-top">
      <div class="navbar-exact-bg">
        <div class="navbar-exact-logo-area">
          <div class="navbar-exact-logo-red">
            <img src="images/logo.jpg" alt="Butterfly Logo" class="navbar-exact-logo-img">
          </div>
        </div>
        <div class="navbar-exact-right">
          <div class="navbar-exact-lang">
            <span class="navbar-exact-lang-label">English</span>
            <span class="navbar-exact-caret">&#9660;</span>
          </div>
          <button class="navbar-exact-hamburger" id="navbarExactHamburger" aria-label="Toggle navigation">
            <span class="navbar-exact-hamburger-bar"></span>
            <span class="navbar-exact-hamburger-bar"></span>
            <span class="navbar-exact-hamburger-bar"></span>
          </button>
        </div>
      </div>
      <div class="navbar-exact-menu" id="navbarExactMenu">
        <a href="#home" class="navbar-exact-menu-link">Home</a>
        <a href="#portfolios" class="navbar-exact-menu-link">Portfolios</a>
        <a href="#blog" class="navbar-exact-menu-link">Blog</a>
        <a href="#faq" class="navbar-exact-menu-link">FAQ</a>
        <a href="#contact" class="navbar-exact-menu-link">Contact</a>
        <a href="admin/login.php" class="navbar-exact-menu-link admin">Admin Login</a>
      </div>
    </nav>
    <script>
    // Hamburger toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
      var hamburger = document.getElementById('navbarExactHamburger');
      var menu = document.getElementById('navbarExactMenu');
      hamburger.addEventListener('click', function(e) {
        menu.classList.toggle('open');
      });
      document.addEventListener('click', function(e) {
        if (!hamburger.contains(e.target) && !menu.contains(e.target)) {
          menu.classList.remove('open');
        }
      });
    });
    </script>

    <style>
    .admin-login-btn {
        border-color: #FF4D1C;
        color: #FF4D1C;
        transition: all 0.3s ease;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: 500;
        white-space: nowrap;
    }
    .navbar-exact-logo-red {
    width: 215px;
    height: 100%;
    clip-path: polygon(0% 0, 100% 0, 90% 100%, 0% 100%);
}

.navbar-exact-logo-img {
    width: 36px;
    height: 36px;
}
    .admin-login-btn:hover {
        background-color: #FF4D1C;
        color: white;
        border-color: #FF4D1C;
    }
    @media (max-width: 991.98px) {
        .navbar-collapse {
    margin-top: 10px;
    margin-bottom: 20px;
    width: 100%;
}
        .admin-login-btn {
    border-color: #FF4D1C;
    color: #FF4D1C;
    transition: all 0.3s ease;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: 500;
    white-space: nowrap;
}
        .nav-right {
            margin-top: 10px;
            justify-content: center;
        }
        .container {
            position: relative;
        }
    }
    .banner-section {
    padding: 50px 0 0;
}
.banner-section .container {
    padding: 0;
}
.banner-video {
    background: transparent;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgb(0 0 0 / 0%);
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
}
.banner-video video {
    border-radius: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.10);
    background: #f8f9fa00;
    position: relative;
    z-index: 1;
    border: 0;
    padding: 0 20px;
}
    .banner-info-texts {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        margin-top: 12px;
    }
    .banner-title {
        background: #e53935;
        color: #fff;
        font-weight: bold;
        font-size: 1.3rem;
        padding: 6px 22px;
        border-radius: 6px;
        letter-spacing: 1px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.10);
    }
    .banner-link {
        background: #8e24aa;
        color: #fff;
        font-weight: 600;
        font-size: 1.1rem;
        padding: 4px 18px;
        border-radius: 6px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.10);
    }
    .banner-link a {
        color: #fff;
        text-decoration: none;
    }
    .banner-link a:hover {
        text-decoration: underline;
    }
    .banner-location {
        background: #222;
        color: #fff;
        font-weight: 600;
        font-size: 1rem;
        padding: 4px 18px;
        border-radius: 6px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.10);
    }
    .biodata-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        border: 2px solid #e53935;
        overflow: hidden;
        position: relative;
        margin-bottom: 24px;
        padding: 0;
    }
    .banner-green-text {
    background: #2ecc40;
    color: #fff;
    width: 100%;
    height: 200px;
    text-align: center;
    font-size: 16px;
    font-weight: bold;
    padding: 156px 0 0;
    letter-spacing: 2px;
    position: absolute;
    top: 236px;
    z-index: 0;
}
    .biodata-card .biodata-photo {
        width: 100%;
        height: 320px;
        object-fit: cover;
        display: block;
        border-bottom: 2px solid #e53935;
    }
    .biodata-card .biodata-cta {
        background: #e53935;
        color: #fff;
        font-size: 1.1rem;
        font-weight: 600;
        text-align: center;
        padding: 8px 0;
        letter-spacing: 0.5px;
    }
    .biodata-card .biodata-info {
        padding: 18px 18px 12px 18px;
        background: #fff;
    }
    .biodata-card .biodata-info-row {
        margin-bottom: 7px;
        font-size: 1.08rem;
    }
    .biodata-card .biodata-label {
        color: #e53935;
        font-weight: 700;
        margin-right: 4px;
    }
    .biodata-card .biodata-value {
        color: #222;
        font-weight: 600;
    }
    .biodata-card .biodata-value.address {
        color: #111;
        font-weight: 700;
    }
    @media (max-width: 575.98px) {
        .biodata-card .biodata-photo {
            height: 220px;
        }
        .biodata-card .biodata-info {
            padding: 12px 8px 8px 8px;
        }
    }
    .biodata-login-bar {
        width: 100%;
        background: #e53935;
        color: #fff;
        font-size: 2rem;
        font-weight: 600;
        text-align: center;
        padding: 10px 0 10px 0;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 48px;
        border-radius: 0;
        overflow: visible;
    }
    .biodata-login-bar .login-bar-content {
        flex: 1;
        text-align: center;
        z-index: 2;
    }
    .biodata-login-bar .login-bar-whitecut {
        position: absolute;
        right: 0;
        top: 0;
        height: 100%;
        width: 120px;
        background: #fff;
        clip-path: polygon(40% 0, 100% 0, 100% 100%, 0% 100%);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        z-index: 3;
    }
    .biodata-login-bar .login-bar-decor {
        display: flex;
        align-items: center;
        height: 32px;
        margin-right: 16px;
    }
    .biodata-login-bar .dots {
        display: flex;
        align-items: center;
        margin-left: 8px;
    }
    .biodata-login-bar .dot {
        width: 9px;
        height: 9px;
        background: #1a237e;
        border-radius: 50%;
        margin-left: 5px;
    }
    @media (max-width: 575.98px) {
        .biodata-login-bar {
            font-size: 1.2rem;
            min-height: 36px;
        }
        .biodata-login-bar .login-bar-whitecut {
            width: 60px;
        }
        .biodata-login-bar .login-bar-decor {
            height: 20px;
            margin-right: 6px;
        }
        .biodata-login-bar .dot {
            width: 6px;
            height: 6px;
            margin-left: 3px;
        }
    }
    .biodata-checkbox-list {
    background: #15283c;
    color: #fff;
    border-radius: 8px;
    padding: 16px 12px;
    margin-top: 18px;
}
    .biodata-checkbox-list label {
        display: flex;
        align-items: center;
        margin-bottom: 6px;
        font-size: 1em;
    }
    .biodata-checkbox-list input[type='checkbox'] {
        margin-right: 8px;
        accent-color: #fff;
        width: 18px;
        height: 18px;
    }
    .nav-tabs {
        border-bottom: none !important;
        display: flex;
        gap: 18px;
        justify-content: center;
        background: transparent;
        margin-bottom: 0;
    }
    .nav-tabs .nav-link {
        background: #a259f7;
        color: #fff !important;
        font-weight: bold;
        font-size: 1.35rem;
        border: none !important;
        border-radius: 16px !important;
        padding: 10px 28px !important;
        margin-bottom: 0 !important;
        transition: background 0.2s, color 0.2s;
        box-shadow: none;
        outline: none;
        letter-spacing: 1px;
        font-family: 'Nunito Sans', 'Source Sans Pro', sans-serif;
    }
    .nav-tabs .nav-link.active, .nav-tabs .nav-link:focus, .nav-tabs .nav-link:hover {
        background: #7c2ae8 !important;
        color: #fff !important;
        border: none !important;
        box-shadow: none;
    }
    </style>

    <!-- Banner Section -->
    <section id="home" class="banner-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="banner-content">
                        <h1 class="display-4">Perfect Agency For Innovative Business</h1>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="banner-video">
                        <video autoplay loop controls class="w-100">
                            <source src="images/pro_banner.mp4" type="video/mp4">
                        </video>
                        <!-- Custom Green Banner -->
                    <div class="banner-green-text">
                        www.marriagebureau.com
                    </div>
                        <div class="banner-title-wedding">PROJAPATI KUTHIR</div>
                        <div class="banner-tab">
                         <div class="banner-marriage-bureau">(Marriage Bureau)</div>
                         <div class="banner-desc-box">
                            Mtrimmonial Site For Prospective<br>
                            Indian Bride & Groom Perfect Match
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tabbed Content Section -->
    <section class="content-section tab-upload-section-exact">
        <div class="container tab-upload-container-exact">
            <!-- Login Button -->
            <div class="login-btn-wrapper" style="text-align:center; background-color: #ffffcd">
                <a href="https://docs.google.com/forms/d/1vFDn3oRZ2x3-ZooE4UT5tyMrT2CUGkfivNmnHONZIsg/viewform?edit_requested=true" class="custom-login-btn" target="_blank">LOGIN</a>
            </div>
            <div class="tab-btn-group-exact">
                <button class="tab-btn-exact active">ALL</button>
                <button class="tab-btn-exact">BRIDE</button>
                <button class="tab-btn-exact">GROOM</button>
            </div>
            <div class="upload-section-exact">
                <div class="upload-icons-row">
                    <span class="upload-icon-left">
                        <img src="images/logo-1.jpg" alt="logo">
                    </span>
                    <input type="file" name="image" id="imageUploadAllExact" accept="image/*" style="display: none;">
                    <button class="upload-btn-exact" type="button" onclick="document.getElementById('imageUploadAllExact').click()"><i class="fas fa-upload me-2"></i>UPLOAD IMAGE</button>
                    <span class="upload-icon-right">
                        <img src="images/image.png" alt="image">
                    </span>
                </div>
            </div>
            <!-- Image Slider -->
            <div class="custom-slider-wrapper">
                <button class="slider-arrow slider-arrow-left" onclick="moveSlide(-1)">&#10094;</button>
                <div class="custom-slider">
                    <img src="images/slide-1.jpg" class="slider-image" style="display:block;">
                    <img src="images/slide-2.jpg" class="slider-image" style="display:none;">
                    <img src="images/slide-3.jpg" class="slider-image" style="display:none;">
                    <img src="images/slide-4.jpg" class="slider-image" style="display:none;">
                    <img src="images/slide-5.jpg" class="slider-image" style="display:none;">
                    <img src="images/slide-6.jpg" class="slider-image" style="display:none;">
                </div>
                <button class="slider-arrow slider-arrow-right" onclick="moveSlide(1)">&#10095;</button>
            </div>
            <div class="tab-content mt-4">
                <!-- All Tab -->
                <div class="tab-pane fade show active" id="all" role="tabpanel">
                    <div class="row g-4" id="allGallery">
                        <?php displayBiodataCards(); ?>
                    </div>
                </div>

                <!-- Men Tab (পাত্র) -->
                <div class="tab-pane fade" id="men" role="tabpanel">
                    <div class="row g-4" id="menGallery">
                        <?php displayBiodataCards('men'); ?>
                    </div>
                </div>

                <!-- Women Tab (পাত্রী) -->
                <div class="tab-pane fade" id="women" role="tabpanel">
                    <div class="row g-4" id="womenGallery">
                        <?php displayBiodataCards('women'); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
    .upload-section {
        padding: 20px;
    }
    .upload-btn {
        border-radius: 30px !important;
        padding: 10px 25px;
        background-color: #FF4D1C;
        color: white;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .upload-btn:hover {
    background-color: #ff4d1cad;
    color: #fff;
}
.upload-btn:hover {
    background-color: #ff4d1cad;
    color: #fff;
}
button:focus:not(:focus-visible) {
    outline: 0;
    box-shadow: none;
}
    .biodata-card {
        position: relative;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .biodata-card img {
        width: 100%;
        height: 300px;
        object-fit: cover;
        display: block;
    }
    .biodata-info {
        padding: 15px;
        background: white;
    }
    .fill-form-btn {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        background-color: #FF4D1C;
        color: white;
        padding: 15px 20px;
        text-decoration: none;
        opacity: 1;
        transition: all 0.3s ease;
        z-index: 3;
        text-align: center;
        font-weight: 500;
        border-radius: 8px 8px 0 0;
        box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
    }
    .fill-form-btn:hover {
        color: white;
        background-color: #FF4D1C;
        box-shadow: 0 -4px 8px rgba(0,0,0,0.2);
    }
    .card-menu {
        position: absolute;
        top: 65px;
        right: 10px;
        z-index: 3;
    }
    .card-menu-btn {
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    .card-menu-content {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 5px;
        margin-top: 5px;
    }
    .card-menu-content.show {
        display: block;
    }
    .delete-btn {
        background: none;
        border: none;
        color: #dc3545;
        padding: 5px 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        width: 100%;
        text-align: left;
        transition: all 0.3s ease;
    }
    .delete-btn:hover {
        background: #f8f9fa;
    }
    .serial-number {
        position: absolute;
        top: 65px;
        left: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.9em;
        z-index: 2;
    }
    .biodata-info h3 {
        margin: 0 0 10px 0;
        font-size: 1.2em;
    }
    .biodata-info p {
        margin: 5px 0;
        color: #666;
    }
    .location {
        color: #666;
        font-size: 0.9em;
    }
    .modal-content {
        border-radius: 15px;
    }
    .modal-header {
    border-bottom: 1px solid #dee2e6;
    background-color: #15283c;
    border-radius: 15px 15px 0 0;
}
.modal-footer {
    border-top: 1px solid #dee2e6;
    background-color: #15283c;
    border-radius: 0 0 15px 15px;
}
#saveBiodataBtn {
    color: #fff;
    background: #e53935;
}
.form-control {
    border-radius: 8px;
    border: 1px solid #ced4da;
    padding: 10px;
    background: #ffcbf0;
}
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        background: #ffcbf0;
    }
    .form-label {
    font-weight: 500;
    color: #ffffff;
}
    .loading {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    .form-select {
    --bs-form-select-bg-img: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    display: block;
    width: 100%;
    padding: .375rem 2.25rem .375rem .75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: var(--bs-body-color);
    background-color: #ffcbf0;
    background-image: var(--bs-form-select-bg-img),var(--bs-form-select-bg-icon,none);
    background-repeat: no-repeat;
    background-position: right .75rem center;
    background-size: 16px 12px;
    border: var(--bs-border-width) solid var(--bs-border-color);
    border-radius: var(--bs-border-radius);
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}
    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
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

                fetch('upload_image.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Store the image path and show the biodata form
                        document.getElementById('uploadedImagePath').value = data.image_path;
                        const biodataModal = new bootstrap.Modal(document.getElementById('biodataFormModal'));
                        biodataModal.show();
                    } else {
                        alert('ছবি আপলোড করতে সমস্যা হয়েছে: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('ছবি আপলোড করতে সমস্যা হয়েছে');
                })
                .finally(() => {
                    // Reset the upload button
                    uploadBtn.innerHTML = originalBtnText;
                    uploadBtn.disabled = false;
                    fileInput.value = ''; // Clear the file input
                });
            }
        }

        // Add event listeners to all file inputs
        document.getElementById('imageUploadAll').addEventListener('change', (e) => handleImageUpload(e, 'allGallery'));
        document.getElementById('imageUploadMen').addEventListener('change', (e) => handleImageUpload(e, 'menGallery'));
        document.getElementById('imageUploadWomen').addEventListener('change', (e) => handleImageUpload(e, 'womenGallery'));
    });

    // Close all menus when clicking outside
    document.addEventListener('click', function(event) {
        const menus = document.querySelectorAll('.card-menu-content.show');
        menus.forEach(menu => {
            if (!menu.contains(event.target) && !event.target.classList.contains('card-menu-btn')) {
                menu.classList.remove('show');
            }
        });
    });

    function toggleMenu(button) {
        const menu = button.nextElementSibling;
        const allMenus = document.querySelectorAll('.card-menu-content.show');
        
        // Close all other menus
        allMenus.forEach(m => {
            if (m !== menu) {
                m.classList.remove('show');
            }
        });
        
        // Toggle current menu
        menu.classList.toggle('show');
    }

    function deleteCard(button, imageId) {
        if (confirm('Are you sure you want to delete this card?')) {
            // Send delete request to server
            fetch('delete_image.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'image_id=' + imageId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the card from all galleries
                    const card = button.closest('.biodata-card').parentElement;
                    ['allGallery', 'menGallery', 'womenGallery'].forEach(galleryId => {
                        const gallery = document.getElementById(galleryId);
                        if (gallery) {
                            const cardToRemove = gallery.querySelector(`[data-image-id="${imageId}"]`);
                            if (cardToRemove) {
                                cardToRemove.remove();
                            }
                        }
                    });
                } else {
                    alert('Error deleting card: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting card');
            });
        }
    }

    // Update the submitBiodata function
    function submitBiodata() {
        const form = document.getElementById('biodataForm');
        const saveBtn = document.getElementById('saveBiodataBtn');
        
        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const formData = new FormData(form);
        
        // Show loading state
        const originalBtnText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>সংরক্ষণ করা হচ্ছে...';
        saveBtn.disabled = true;

        fetch('save_biodata.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('biodataFormModal'));
                modal.hide();

                // Reset the form
                form.reset();
                
                // Show success message for pending approval
                showSuccessMessage('আপনার বায়োডাটা সফলভাবে জমা হয়েছে! এটি অ্যাডমিন অনুমোদনের জন্য অপেক্ষা করছে।');
            } else {
                alert('বায়োডাটা সংরক্ষণ করতে সমস্যা হয়েছে: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('বায়োডাটা সংরক্ষণ করতে সমস্যা হয়েছে');
        })
        .finally(() => {
            // Reset the submit button
            saveBtn.innerHTML = originalBtnText;
            saveBtn.disabled = false;
        });
    }

    // Function to show success message
    function showSuccessMessage(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
        successDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 400px; max-width: 500px;';
        successDiv.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(successDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (successDiv.parentNode) {
                successDiv.remove();
            }
        }, 5000);
    }

    // Use event delegation for biodata-card image click
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('biodata-photo')) {
            const img = event.target;
            document.getElementById('biodataDetailImage').src = img.src;
            document.getElementById('biodataDetailImage').style.height = '220px'; // Make image smaller in modal
            document.getElementById('detailName').textContent = img.getAttribute('data-name') || '';
            document.getElementById('detailAge').textContent = img.getAttribute('data-age') || '';
            document.getElementById('detailHeight').textContent = img.getAttribute('data-height') || '';
            document.getElementById('detailEducation').textContent = img.getAttribute('data-education') || '';
            document.getElementById('detailCaste').textContent = img.getAttribute('data-caste') || '';
            document.getElementById('detailAddress').textContent = img.getAttribute('data-address') || '';
            const biodataDetailsModal = new bootstrap.Modal(document.getElementById('biodataDetailsModal'));
            biodataDetailsModal.show();
        }
    });
    </script>
    
    <!-- Footer -->
    <footer class="footer-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="footer-content">
                        <p>Power of choice is untrammelled & when nothing prevents our being able</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="newsletter-section">
                        <h3>SUBSCRIBE FOR NEWSLETTER</h3>
                        <form class="newsletter-form">
                            <input type="email" placeholder="Email Address" required>
                            <button type="submit">Subscribe</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Scroll to Top Button -->
            <button class="scroll-to-top">
                <i class="fas fa-chevron-up"></i>
            </button>
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="policy-links">
                    <a href="#">Terms & Conditions</a>
                    <a href="#">Privacy Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Card Manager JS -->
    <script src="js/card_manager.js"></script>
    <!-- Custom JS -->
    <script src="js/script.js"></script>
    <!-- Custom JS -->
    <script>
        // Scroll to Top functionality
        document.addEventListener('DOMContentLoaded', function() {
            const scrollBtn = document.querySelector('.scroll-to-top');
            
            // Smooth scroll to top when clicked
            scrollBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>

    <!-- Biodata Form Modal -->
    <div class="modal fade" id="biodataFormModal" tabindex="-1" aria-labelledby="biodataFormModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="biodataFormModalLabel">বায়োডাটা তথ্য</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="biodataForm" onsubmit="return false;">
                        <input type="hidden" id="uploadedImagePath" name="image_path">
                        <div class="mb-3">
                            <label for="name" class="form-label">নাম</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="age" class="form-label">বয়স</label>
                            <input type="text" class="form-control" id="age" name="age" required>
                        </div>
                        <div class="mb-3">
                            <label for="height" class="form-label">উচ্চতা</label>
                            <input type="text" class="form-control" id="height" name="height" placeholder="যেমন: ৫'৬&quot;">
                        </div>
                        <div class="mb-3">
                            <label for="education" class="form-label">শিক্ষা</label>
                            <input type="text" class="form-control" id="education" name="education" placeholder="যেমন: এস.এস.সি">
                        </div>
                        <div class="mb-3 biodata-checkbox-list">
                            <div style="font-weight: bold; font-size: 1.1em; margin-bottom: 8px; color: #fff;">All Collection</div>
                            <div>
                                <label><input type="checkbox" name="caste[]" value="ব্রাহ্মণ"> <span>ব্রাহ্মণ</span></label><br>
                                <label><input type="checkbox" name="caste[]" value="কায়স্থ"> <span>কায়স্থ</span></label><br>
                                <label><input type="checkbox" name="caste[]" value="সদগোপ"> <span>সদগোপ</span></label><br>
                                <label><input type="checkbox" name="caste[]" value="নাপিত"> <span>নাপিত</span></label><br>
                                <label><input type="checkbox" name="caste[]" value="গোয়ালা"> <span>গোয়ালা</span></label><br>
                                <label><input type="checkbox" name="caste[]" value="তন্তুবয়"> <span>তন্তুবয়</span></label><br>
                                <label><input type="checkbox" name="caste[]" value="মাহিষ্য"> <span>মাহিষ্য</span></label><br>
                                <label><input type="checkbox" name="caste[]" value="পঞ্চ খত্রিয়"> <span>পঞ্চ খত্রিয়</span></label><br>
                                <label><input type="checkbox" name="caste[]" value="স্বর্ণকার"> <span>স্বর্ণকার</span></label><br>
                                <label><input type="checkbox" name="caste[]" value="সুবর্ণ বনিক"> <span>সুবর্ণ বনিক</span></label><br>
                                <label><input type="checkbox" name="caste[]" value="গন্ধ বনিক"> <span>গন্ধ বনিক</span></label><br>
                                <label><input type="checkbox" name="caste[]" value="কর্মকার"> <span>কর্মকার</span></label><br>
                                <label><input type="checkbox" name="caste[]" value="বর্গ খত্রিয়"> <span>বর্গ খত্রিয়</span></label><br>
                                <label><input type="checkbox" name="caste[]" value="রাজপুত খত্রিয়"> <span>রাজপুত খত্রিয়</span></label><br>
                                <label><input type="checkbox" name="caste[]" value="ময়রা"> <span>ময়রা</span></label><br>
                                <label><input type="checkbox" name="caste[]" value="SC / ST"> <span>SC / ST</span></label><br>
                                <label><input type="checkbox" name="caste[]" value="মুসলিম"> <span>মুসলিম</span></label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">ঠিকানা</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">ধরন</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">নির্বাচন করুন</option>
                                <option value="men">পাত্র</option>
                                <option value="women">পাত্রী</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বাতিল</button>
                    <button type="button" class="btn" id="saveBiodataBtn" onclick="submitBiodata()">সংরক্ষণ করুন</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Biodata Details Modal -->
    <div class="modal fade" id="biodataDetailsModal" tabindex="-1" aria-labelledby="biodataDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
            <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-body p-0">
                    <div class="biodata-card m-0" style="box-shadow:none; border-radius:0;">
                        <div class="biodata-modal-flex" style="display: flex; align-items: flex-start; gap: 24px; padding: 24px; flex-wrap: wrap;">
                            <img id="biodataDetailImage" src="" alt="Biodata Image" style="width: 120px; height: 120px; object-fit: cover; border-radius: 10px; border: 1.5px solid #e53935; background: #f8f8f8;">
                            <div class="biodata-info" style="flex: 1 1 120px; min-width: 120px;">
                                <div class="biodata-info-row"><span class="biodata-label">নাম :</span> <span class="biodata-value" id="detailName"></span></div>
                                <div class="biodata-info-row"><span class="biodata-label">বয়স :</span> <span class="biodata-value" id="detailAge"></span></div>
                                <div class="biodata-info-row"><span class="biodata-label">উচ্চতা :</span> <span class="biodata-value" id="detailHeight"></span></div>
                                <div class="biodata-info-row"><span class="biodata-label">শিক্ষা :</span> <span class="biodata-value" id="detailEducation"></span></div>
                                <div class="biodata-info-row"><span class="biodata-label">জাতি :</span> <span class="biodata-value" id="detailCaste"></span></div>
                                <div class="biodata-info-row"><span class="biodata-label">ঠিকানা :</span> <span class="biodata-value address" id="detailAddress"></span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .biodata-details {
        padding: 15px;
    }
    .biodata-details p {
        margin-bottom: 10px;
        font-size: 16px;
    }
    .biodata-details strong {
        color: #FF4D1C;
        margin-right: 10px;
    }
    #biodataDetailImage {
        width: 100% !important;
        height: 300px !important;
        object-fit: cover;
        border-radius: 8px;
    }
    @media (max-width: 600px) {
        .modal-dialog {
            max-width: 98vw !important;
            margin: 0 20px !important;
        }
        .biodata-modal-flex {
            flex-direction: column !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 12px !important;
        }
    }
    </style>

    <script>
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('biodata-photo')) {
            const img = event.target;
            document.getElementById('biodataDetailImage').src = img.src;
            document.getElementById('biodataDetailImage').style.height = '220px'; // Make image smaller in modal
            document.getElementById('detailName').textContent = img.getAttribute('data-name') || '';
            document.getElementById('detailAge').textContent = img.getAttribute('data-age') || '';
            document.getElementById('detailHeight').textContent = img.getAttribute('data-height') || '';
            document.getElementById('detailEducation').textContent = img.getAttribute('data-education') || '';
            document.getElementById('detailCaste').textContent = img.getAttribute('data-caste') || '';
            document.getElementById('detailAddress').textContent = img.getAttribute('data-address') || '';
            const biodataDetailsModal = new bootstrap.Modal(document.getElementById('biodataDetailsModal'));
            biodataDetailsModal.show();
        }
    });
    </script>

    <script>
    // Hamburger toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        var hamburger = document.getElementById('navbarHamburger');
        var menu = document.getElementById('navbarMenu');
        hamburger.addEventListener('click', function() {
            menu.classList.toggle('open');
        });
    });
    </script>

    <!-- Floating Social Icons -->
    <div class="floating-social-icons">
        <a href="https://wa.me/919999999999" target="_blank" class="floating-icon whatsapp" title="WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="https://facebook.com/yourpage" target="_blank" class="floating-icon facebook" title="Facebook">
            <i class="fab fa-facebook-f"></i>
        </a>
    </div>

    <style>
    .custom-slider-wrapper {
        width: 100%;
        max-width: 500px;
        margin: 20px auto 0 auto;
        position: relative;
        background: #fefecc;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .custom-slider {
    width: 100%;
    height: 240px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    margin: 0 10px;
}
    .slider-image {
        width: 100%;
        height: 240px;
        object-fit: cover;
        border-radius: 0;
        box-shadow: none;
        position: absolute;
        left: 0;
        top: 0;
        transition: opacity 0.3s;
    }
    .slider-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: #a259f7cc;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        font-size: 1.7rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        transition: background 0.2s, box-shadow 0.2s;
        z-index: 2;
        opacity: 0.85;
    }
    .slider-arrow-left {
        left: 16px;
    }
    .slider-arrow-right {
        right: 16px;
    }
    .slider-arrow:hover {
        background: #7c2ae8;
        box-shadow: 0 4px 16px rgba(160, 89, 247, 0.25);
        opacity: 1;
    }
    </style>
    <script>
    let currentSlide = 0;
    const slides = document.getElementsByClassName('slider-image');
    function showSlide(idx) {
        if (idx < 0) idx = slides.length - 1;
        if (idx >= slides.length) idx = 0;
        for (let i = 0; i < slides.length; i++) {
            slides[i].style.display = 'none';
        }
        slides[idx].style.display = 'block';
        currentSlide = idx;
    }
    function moveSlide(dir) {
        showSlide(currentSlide + dir);
    }
    // Initialize
    showSlide(0);
    </script>

    <script>
    // Real-time updates for approved biodata cards
    let lastApprovedId = 0;
    let pollingInterval;

    // Function to get the highest biodata ID currently displayed
    function getHighestDisplayedId() {
        const cards = document.querySelectorAll('.biodata-card-exact');
        let maxId = 0;
        cards.forEach(card => {
            const cardId = card.getAttribute('data-biodata-id');
            if (cardId && parseInt(cardId) > maxId) {
                maxId = parseInt(cardId);
            }
        });
        return maxId;
    }

    // Function to get all current biodata IDs
    function getCurrentBiodataIds() {
        const cards = document.querySelectorAll('.biodata-card-exact');
        const ids = [];
        cards.forEach(card => {
            const cardId = card.getAttribute('data-biodata-id');
            if (cardId) {
                ids.push(cardId);
            }
        });
        return ids;
    }

    // Function to check for deleted biodata
    function checkForDeletedBiodata() {
        const currentIds = getCurrentBiodataIds();
        
        if (currentIds.length === 0) {
            return; // No cards to check
        }
        
        const idsParam = currentIds.join(',');
        console.log('Checking for deleted biodata. Current IDs:', currentIds);
        
        fetch('check_deleted_biodata.php?current_ids=' + idsParam)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.deleted_ids.length > 0) {
                    console.log('Deleted biodata found:', data.deleted_ids);
                    
                    // Remove deleted cards from all tabs
                    data.deleted_ids.forEach(deletedId => {
                        console.log('Removing biodata card with ID:', deletedId);
                        removeBiodataCardFromPage(deletedId);
                    });
                    
                    // Show notification
                    if (data.deleted_ids.length === 1) {
                        showNotification('A biodata card has been removed.', 'info');
                    } else {
                        showNotification(`${data.deleted_ids.length} biodata cards have been removed.`, 'info');
                    }
                } else {
                    console.log('No deleted biodata found');
                }
            })
            .catch(error => {
                console.error('Error checking for deleted biodata:', error);
            });
    }

    // Function to remove a biodata card from the page
    function removeBiodataCardFromPage(biodataId) {
        // Find and remove the card from all galleries
        const galleries = ['allGallery', 'menGallery', 'womenGallery'];
        
        galleries.forEach(galleryId => {
            const gallery = document.getElementById(galleryId);
            if (gallery) {
                const cardContainer = gallery.querySelector(`[data-biodata-id="${biodataId}"]`);
                if (cardContainer) {
                    // Fade out the card
                    cardContainer.style.opacity = '0';
                    cardContainer.style.transition = 'opacity 0.5s ease-in-out';
                    
                    // Remove after fade out
                    setTimeout(() => {
                        if (cardContainer.parentNode) {
                            cardContainer.remove();
                        }
                    }, 500);
                }
            }
        });
    }

    // Function to check for new approved biodata
    function checkForNewApprovedBiodata() {
        const currentMaxId = getHighestDisplayedId();
        
        fetch('check_new_approved.php?last_id=' + currentMaxId)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.new_biodata.length > 0) {
                    console.log('New approved biodata found:', data.new_biodata);
                    
                    // Add new biodata cards to all relevant tabs
                    data.new_biodata.forEach(biodata => {
                        addBiodataCardToPage(biodata);
                    });
                    
                    // Show notification
                    showNotification('New biodata card has been approved and added!', 'success');
                }
            })
            .catch(error => {
                console.error('Error checking for new approved biodata:', error);
            });
    }

    // Function to check for real-time updates (both new and deleted)
    function checkForRealTimeUpdates() {
        // Check for new approved biodata
        checkForNewApprovedBiodata();
        
        // Check for deleted biodata only if there are cards displayed
        const currentIds = getCurrentBiodataIds();
        if (currentIds.length > 0) {
            checkForDeletedBiodata();
        }
    }

    // Function to add a biodata card to the page
    function addBiodataCardToPage(biodata) {
        const cardHtml = generateBiodataCardHtml(biodata);
        
        // Function to add card to a specific gallery
        function addCardToGallery(galleryId) {
            const gallery = document.getElementById(galleryId);
            if (gallery) {
                const cardContainer = document.createElement('div');
                cardContainer.className = 'col-md-4';
                cardContainer.style.opacity = '0';
                cardContainer.style.transition = 'opacity 0.5s ease-in-out';
                cardContainer.innerHTML = cardHtml;
                gallery.appendChild(cardContainer);
                
                // Ensure the new card has proper styling
                const newCard = cardContainer.querySelector('.biodata-card-exact');
                if (newCard) {
                    // Force a reflow to ensure CSS is applied
                    newCard.offsetHeight;
                    
                    // Add click event listener for modal
                    const photo = newCard.querySelector('.biodata-photo');
                    if (photo) {
                        photo.addEventListener('click', function() {
                            document.getElementById('biodataDetailImage').src = this.src;
                            document.getElementById('biodataDetailImage').style.height = '220px';
                            document.getElementById('detailName').textContent = this.getAttribute('data-name') || '';
                            document.getElementById('detailAge').textContent = this.getAttribute('data-age') || '';
                            document.getElementById('detailHeight').textContent = this.getAttribute('data-height') || '';
                            document.getElementById('detailEducation').textContent = this.getAttribute('data-education') || '';
                            document.getElementById('detailCaste').textContent = this.getAttribute('data-caste') || '';
                            document.getElementById('detailAddress').textContent = this.getAttribute('data-address') || '';
                            const biodataDetailsModal = new bootstrap.Modal(document.getElementById('biodataDetailsModal'));
                            biodataDetailsModal.show();
                        });
                    }
                    
                    // Fade in the card after a short delay
                    setTimeout(() => {
                        cardContainer.style.opacity = '1';
                    }, 100);
                }
            }
        }
        
        // Add to ALL tab
        addCardToGallery('allGallery');
        
        // Add to specific tab (BRIDE or GROOM)
        if (biodata.type === 'women') {
            addCardToGallery('womenGallery');
        } else if (biodata.type === 'men') {
            addCardToGallery('menGallery');
        }
    }

    // Function to generate biodata card HTML
    function generateBiodataCardHtml(biodata) {
        const header = (biodata.type === 'men') ? 'পাত্রী চাই' : 'পাত্র চাই';
        const formattedId = String(biodata.id).padStart(2, '0');
        
        return `
            <div class="biodata-card-exact" data-biodata-id="${biodata.id}">
                <div class="biodata-card-header-exact">
                    <span class="biodata-card-header-text-exact">${header}</span>
                    <span class="biodata-card-header-triangle">
                        <img src="images/logo.jpg" alt="Butterfly">
                    </span>
                </div>
                <div class="biodata-card-photo-wrap-exact">
                    <img src="${biodata.image_path}" class="biodata-card-photo-exact biodata-photo" alt="Biodata Image" 
                        data-name="${biodata.name || ''}"
                        data-age="${biodata.age || ''}"
                        data-height="${biodata.height || ''}"
                        data-education="${biodata.education || ''}"
                        data-caste="${biodata.caste || ''}"
                        data-address="${biodata.address || ''}">
                </div>
                <div class="biodata-card-info-bar-exact">বিশদ জানতে ফটোর উপর টাচ করুন</div>
                <div class="biodata-card-details-exact">
                    <div><span class="biodata-label-red-exact">নাম :</span> <span class="biodata-value-blue-exact">${biodata.name || ''}</span></div>
                    <div><span class="biodata-label-red-exact">বয়স / উচ্চতা / কাস্ট :</span> <span class="biodata-value-blue-exact">${biodata.age || ''} / ${biodata.height || ''} / ${biodata.caste || ''}</span></div>
                    <div><span class="biodata-label-red-exact">শিক্ষা :</span> <span class="biodata-value-blue-exact">${biodata.education || ''}</span></div>
                    <div><span class="biodata-label-red-exact">ঠিকানা :</span> <span class="biodata-value-blue-exact">${biodata.address || ''}</span></div>
                </div>
                <div style="background: #a259f7; color: #fff; text-align: center; font-size: 14px; font-weight: bold; padding: 8px 0; border-radius: 0 0 8px 8px; border-right: 2px solid #d90429; border-left: 2px solid #d90429; border-bottom: 2px solid #d90429;">যোগাযোগ করতে ফটো স্ক্রীনশট ওয়াটসঅ্যাপ করুন ।</div>
            </div>
        `;
    }

    // Function to show notification
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : 'info'} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 400px; max-width: 500px;';
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }

    // Start polling when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize last approved ID
        lastApprovedId = getHighestDisplayedId();
        
        // Start polling after a short delay to ensure page is fully loaded
        setTimeout(() => {
            // Start polling every 1.5 seconds to ensure deletions are detected within 2 seconds
            pollingInterval = setInterval(checkForRealTimeUpdates, 1500);
            console.log('Real-time biodata updates started. Polling every 1.5 seconds...');
        }, 1000);
    });

    // Stop polling when page is hidden (to save resources)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(pollingInterval);
            console.log('Polling paused (page hidden)');
        } else {
            pollingInterval = setInterval(checkForRealTimeUpdates, 1500);
            console.log('Polling resumed (page visible)');
        }
    });
    </script>
</body>
</html> 