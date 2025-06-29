<?php
require_once('db.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Log the incoming request
error_log("Received biodata save request: " . print_r($_POST, true));

// Validate required fields
$required_fields = ['name', 'age', 'address', 'type', 'image_path'];
$missing_fields = [];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    error_log("Missing required fields: " . implode(', ', $missing_fields));
    echo json_encode([
        'success' => false, 
        'message' => 'Missing required fields: ' . implode(', ', $missing_fields)
    ]);
    exit;
}

// Validate image path exists
if (!file_exists($_POST['image_path'])) {
    error_log("Image file not found: " . $_POST['image_path']);
    echo json_encode([
        'success' => false,
        'message' => 'Image file not found'
    ]);
    exit;
}

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Handle caste as a checkbox array
    $caste = '';
    if (isset($_POST['caste'])) {
        if (is_array($_POST['caste'])) {
            $caste = implode(',', $_POST['caste']);
        } else {
            $caste = $_POST['caste'];
        }
    } else {
        $caste = '';
    }
    
    // Check if record already exists
    $check_stmt = $conn->prepare("SELECT id FROM biodata_images WHERE image_path = ?");
    if (!$check_stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    
    $check_stmt->bind_param("s", $_POST['image_path']);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Update existing record
        $stmt = $conn->prepare("UPDATE biodata_images SET name = ?, age = ?, height = ?, education = ?, caste = ?, address = ?, type = ?, approval_status = 'pending' WHERE image_path = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssssssss", 
            $_POST['name'],
            $_POST['age'],
            $_POST['height'],
            $_POST['education'],
            $caste,
            $_POST['address'],
            $_POST['type'],
            $_POST['image_path']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $image_id = $check_result->fetch_assoc()['id'];
    } else {
        // Insert new record
        $stmt = $conn->prepare("INSERT INTO biodata_images (image_path, name, age, height, education, caste, address, type, approval_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssssssss", 
            $_POST['image_path'],
            $_POST['name'],
            $_POST['age'],
            $_POST['height'],
            $_POST['education'],
            $caste,
            $_POST['address'],
            $_POST['type']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $image_id = $conn->insert_id;
    }
    
    // Format serial number to start from 01
    $formattedId = str_pad($image_id, 2, '0', STR_PAD_LEFT);
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'image_id' => $image_id,
        'image_path' => $_POST['image_path'],
        'name' => $_POST['name'],
        'age' => $_POST['age'],
        'height' => $_POST['height'],
        'education' => $_POST['education'],
        'caste' => $caste,
        'address' => $_POST['address'],
        'type' => $_POST['type'],
        'serial_number' => 'S.No ' . $formattedId
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    error_log("Error in save_biodata.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 