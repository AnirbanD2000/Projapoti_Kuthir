<?php
require_once('db.php');

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug logging
error_log("Upload request received");
error_log("FILES: " . print_r($_FILES, true));

if (!isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'message' => 'No image uploaded']);
    exit;
}

$file = $_FILES['image'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileError = $file['error'];
$fileSize = $file['size'];

// Validate file size (5MB max)
$maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
if ($fileSize > $maxFileSize) {
    echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit']);
    exit;
}

// Create uploads directory if it doesn't exist
$uploadDir = 'uploads';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
        exit;
    }
    error_log("Created uploads directory");
}

// Generate unique filename
$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$newFileName = uniqid() . '.' . $fileExtension;
$uploadPath = $uploadDir . '/' . $newFileName;

// Validate file type
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array($fileExtension, $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed types: ' . implode(', ', $allowedTypes)]);
    exit;
}

if ($fileError === 0) {
    error_log("Attempting to move uploaded file to: " . $uploadPath);
    
    if (move_uploaded_file($fileTmpName, $uploadPath)) {
        error_log("File moved successfully");
        
        try {
            // Get next serial number
            $result = $conn->query("SELECT MAX(id) as last_id FROM biodata_images");
            if (!$result) {
                throw new Exception("Database query failed: " . $conn->error);
            }
            
            $row = $result->fetch_assoc();
            $nextId = ($row['last_id'] ?? 0) + 1;
            
            // Save to database with empty biodata fields
            $stmt = $conn->prepare("INSERT INTO biodata_images (image_path, name, age, address, type) VALUES (?, '', '', '', '')");
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }
            
            $stmt->bind_param("s", $uploadPath);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $image_id = $stmt->insert_id;
            error_log("Image saved to database with ID: " . $image_id);
            
            echo json_encode([
                'success' => true,
                'image_path' => $uploadPath,
                'image_id' => $image_id,
                'serial_number' => 'S.No ' . $nextId
            ]);
            
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            // Delete the uploaded file if database insert fails
            if (file_exists($uploadPath)) {
                unlink($uploadPath);
            }
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    } else {
        error_log("Failed to move uploaded file. Upload error code: " . $fileError);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to move uploaded file. Error code: ' . $fileError
        ]);
    }
} else {
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
    ];
    
    $errorMessage = $errorMessages[$fileError] ?? 'Unknown upload error';
    error_log("Upload error: " . $errorMessage);
    
    echo json_encode([
        'success' => false,
        'message' => 'Upload error: ' . $errorMessage
    ]);
} 