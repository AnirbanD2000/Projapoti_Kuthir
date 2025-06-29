<?php
// Database connection parameters
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'wedding_website');

// Create connection without database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    $conn->select_db(DB_NAME);
} else {
    die("Error creating database: " . $conn->error);
}

// Create contacts table
$sql = "CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (!$conn->query($sql)) {
    die("Error creating contacts table: " . $conn->error);
}

// Create subscribers table
$sql = "CREATE TABLE IF NOT EXISTS subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (!$conn->query($sql)) {
    die("Error creating subscribers table: " . $conn->error);
}

// Create biodata_images table
$sql = "CREATE TABLE IF NOT EXISTS biodata_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    name VARCHAR(100) NULL,
    age VARCHAR(50) NULL,
    height VARCHAR(50) NULL,
    education VARCHAR(100) NULL,
    caste VARCHAR(100) NULL,
    address TEXT NULL,
    type ENUM('men', 'women') NULL,
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (!$conn->query($sql)) {
    die("Error creating biodata_images table: " . $conn->error);
}

// Create gallery_images table
$sql = "CREATE TABLE IF NOT EXISTS gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    tab_id INT NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (!$conn->query($sql)) {
    die("Error creating gallery_images table: " . $conn->error);
}

// Create gallery_tabs table
$sql = "CREATE TABLE IF NOT EXISTS gallery_tabs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tab_name VARCHAR(100) NOT NULL,
    display_order INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (!$conn->query($sql)) {
    die("Error creating gallery_tabs table: " . $conn->error);
}

// Create gallery_settings table
$sql = "CREATE TABLE IF NOT EXISTS gallery_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    common_heading VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (!$conn->query($sql)) {
    die("Error creating gallery_settings table: " . $conn->error);
}

// Create admins table
$sql = "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (!$conn->query($sql)) {
    die("Error creating admins table: " . $conn->error);
}

// Insert default gallery tab if none exists
$result = $conn->query("SELECT COUNT(*) as count FROM gallery_tabs");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $conn->query("INSERT INTO gallery_tabs (tab_name, display_order) VALUES ('Default Gallery', 1)");
}

// Insert default admin user if none exists
$result = $conn->query("SELECT COUNT(*) as count FROM admins");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $username = 'admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $stmt->close();
    }
}

// Set character set
$conn->set_charset("utf8mb4");

// Function to display biodata cards
function displayBiodataCards($type = null) {
    global $conn;
    
    // Check if approval_status column exists, if not add it
    $result = $conn->query("SHOW COLUMNS FROM biodata_images LIKE 'approval_status'");
    if ($result->num_rows == 0) {
        // Add the column
        $conn->query("ALTER TABLE biodata_images ADD COLUMN approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
        // Update existing records to approved
        $conn->query("UPDATE biodata_images SET approval_status = 'approved'");
    }
    
    $sql = "SELECT * FROM biodata_images WHERE approval_status = 'approved'";
    if ($type) {
        $sql .= " AND type = ?";
    }
    $sql .= " ORDER BY id DESC";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return;
    }
    
    if ($type) {
        $stmt->bind_param("s", $type);
    }
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return;
    }
    
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        // Determine card header
        $header = 'পাত্র চাই';
        if ($type === 'men') {
            $header = 'পাত্রী চাই';
        } else if ($type === 'women') {
            $header = 'পাত্র চাই';
        } else if (!empty($row['type'])) {
            $header = ($row['type'] === 'men') ? 'পাত্রী চাই' : 'পাত্র চাই';
        }
        echo '<div class="col-md-4">';
        echo '<div class="biodata-card-exact" data-biodata-id="' . $row['id'] . '">';
        // Header
        echo '<div class="biodata-card-header-exact">';
        echo '<span class="biodata-card-header-text-exact">' . $header . '</span>';
        echo '<span class="biodata-card-header-triangle">'
            . '<img src="images/logo.jpg" alt="Butterfly">'
            . '</span>';
        echo '</div>';
        // Photo
        echo '<div class="biodata-card-photo-wrap-exact">';
        echo '<img src="' . htmlspecialchars($row['image_path'] ?? '') . '" class="biodata-card-photo-exact biodata-photo" alt="Biodata Image" 
            data-name="' . htmlspecialchars($row['name'] ?? '') . '"
            data-age="' . htmlspecialchars($row['age'] ?? '') . '"
            data-height="' . htmlspecialchars($row['height'] ?? '') . '"
            data-education="' . htmlspecialchars($row['education'] ?? '') . '"
            data-caste="' . htmlspecialchars($row['caste'] ?? '') . '"
            data-address="' . htmlspecialchars($row['address'] ?? '') . '">';
        echo '</div>';
        // Purple info bar
        echo '<div class="biodata-card-info-bar-exact">বিশদ জানতে ফটোর উপর টাচ করুন</div>';
        // Details
        echo '<div class="biodata-card-details-exact">';
        echo '<div><span class="biodata-label-red-exact">নাম :</span> <span class="biodata-value-blue-exact">' . htmlspecialchars($row['name'] ?? '') . '</span></div>';
        echo '<div><span class="biodata-label-red-exact">বয়স / উচ্চতা / কাস্ট :</span> <span class="biodata-value-blue-exact">' . htmlspecialchars($row['age'] ?? '') . ' / ' . htmlspecialchars($row['height'] ?? '') . ' / ' . htmlspecialchars($row['caste'] ?? '') . '</span></div>';
        echo '<div><span class="biodata-label-red-exact">শিক্ষা :</span> <span class="biodata-value-blue-exact">' . htmlspecialchars($row['education'] ?? '') . '</span></div>';
        echo '<div><span class="biodata-label-red-exact">ঠিকানা :</span> <span class="biodata-value-blue-exact">' . htmlspecialchars($row['address'] ?? '') . '</span></div>';
        echo '</div>';
        // Purple title bar
        echo '<div style="background: #a259f7; color: #fff; text-align: center; font-size: 14px; font-weight: bold; padding: 8px 0; border-radius: 0 0 8px 8px; border-right: 2px solid #d90429; border-left: 2px solid #d90429; border-bottom: 2px solid #d90429;">যোগাযোগ করতে ফটো স্ক্রীনশট ওয়াটসঅ্যাপ করুন ।</div>';
        // Social icons
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}

// Function to get biodata images
function getBiodataImages() {
    global $conn;
    
    try {
        $sql = "SELECT * FROM biodata_images ORDER BY id DESC";
        $result = $conn->query($sql);
        
        if (!$result) {
            error_log("Error fetching biodata images: " . $conn->error);
            return [];
        }
        
        $images = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $images[] = $row;
            }
        }
        
        error_log("Successfully fetched " . count($images) . " images");
        return $images;
        
    } catch (Exception $e) {
        error_log("Exception in getBiodataImages: " . $e->getMessage());
        return [];
    }
}

// Function to save biodata
function saveBiodata($data) {
    global $conn;
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Update existing record instead of creating a new one
        $stmt = $conn->prepare("UPDATE biodata_images SET name = ?, age = ?, address = ?, type = ? WHERE image_path = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        
        $stmt->bind_param("sssss", 
            $data['name'],
            $data['age'],
            $data['address'],
            $data['type'],
            $data['image_path']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        // Get the updated record
        $stmt = $conn->prepare("SELECT id FROM biodata_images WHERE image_path = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        
        $stmt->bind_param("s", $data['image_path']);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $image_id = $row['id'];
        
        // Commit transaction
        $conn->commit();
        
        error_log("Successfully updated biodata with ID: " . $image_id);
        
        return [
            'success' => true,
            'image_id' => $image_id,
            'image_path' => $data['image_path'],
            'name' => $data['name'],
            'age' => $data['age'],
            'address' => $data['address'],
            'type' => $data['type'],
            'serial_number' => $image_id
        ];
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        error_log("Error in saveBiodata: " . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

// Function to delete biodata
function deleteBiodata($imageId) {
    global $conn;
    
    try {
        // First get the image path
        $stmt = $conn->prepare("SELECT image_path FROM biodata_images WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $imageId);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row) {
            // Delete the file
            $file_path = $row['image_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            // Delete from database
            $stmt = $conn->prepare("DELETE FROM biodata_images WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }
            
            $stmt->bind_param("i", $imageId);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            error_log("Successfully deleted biodata with ID: " . $imageId);
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'Image not found'];
        
    } catch (Exception $e) {
        error_log("Error in deleteBiodata: " . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
?> 