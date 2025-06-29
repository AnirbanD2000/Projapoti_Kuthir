<?php
require_once('db.php');

header('Content-Type: application/json');

// Get the last ID that was displayed on the page
$last_id = intval($_GET['last_id'] ?? 0);

try {
    // Get newly approved biodata since the last check
    $stmt = $conn->prepare("SELECT * FROM biodata_images WHERE id > ? AND approval_status = 'approved' ORDER BY id ASC");
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $last_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $new_biodata = [];
    
    while ($row = $result->fetch_assoc()) {
        $new_biodata[] = [
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
        'new_biodata' => $new_biodata,
        'count' => count($new_biodata),
        'last_checked_id' => $last_id
    ]);
    
} catch (Exception $e) {
    error_log("Error in check_new_approved.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 