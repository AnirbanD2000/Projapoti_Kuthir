<?php
require_once('../db.php');

header('Content-Type: application/json');

if (!isset($_FILES['image']) || !isset($_POST['tab_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

$file = $_FILES['image'];
$tab_id = (int)$_POST['tab_id'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileError = $file['error'];

// Validate tab exists
$stmt = $conn->prepare("SELECT id FROM gallery_tabs WHERE id = ?");
$stmt->bind_param("i", $tab_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid tab']);
    exit;
}
$stmt->close();

// Create uploads directory if it doesn't exist
$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Generate unique filename
$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$newFileName = uniqid() . '.' . $fileExtension;
$uploadPath = $uploadDir . $newFileName;

// Validate file type
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array($fileExtension, $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type']);
    exit;
}

if ($fileError === 0) {
    if (move_uploaded_file($fileTmpName, $uploadPath)) {
        // Save to database
        $stmt = $conn->prepare("INSERT INTO gallery_images (image_path, tab_id) VALUES (?, ?)");
        $stmt->bind_param("si", $uploadPath, $tab_id);
        
        if ($stmt->execute()) {
            $image_id = $stmt->insert_id;
            echo json_encode([
                'success' => true,
                'image_path' => $uploadPath,
                'image_id' => $image_id
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Database error'
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to move uploaded file'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Upload error: ' . $fileError
    ]);
} 