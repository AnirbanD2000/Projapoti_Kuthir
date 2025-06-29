<?php
require_once('../db.php');

header('Content-Type: application/json');

// Check if admin is logged in
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM biodata_images WHERE approval_status = 'pending' ORDER BY created_at DESC");
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $pending_biodata = [];
    
    while ($row = $result->fetch_assoc()) {
        $pending_biodata[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'age' => $row['age'],
            'height' => $row['height'],
            'education' => $row['education'],
            'caste' => $row['caste'],
            'address' => $row['address'],
            'type' => $row['type'],
            'image_path' => $row['image_path'],
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'pending_biodata' => $pending_biodata,
        'count' => count($pending_biodata)
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_pending_biodata.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 