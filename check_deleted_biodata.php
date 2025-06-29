<?php
require_once('db.php');

header('Content-Type: application/json');

try {
    // Get the list of biodata IDs that should be displayed
    $current_ids = isset($_GET['current_ids']) ? $_GET['current_ids'] : '';
    
    if (empty($current_ids)) {
        echo json_encode(['success' => true, 'deleted_ids' => []]);
        exit;
    }
    
    // Convert comma-separated string to array
    $id_array = explode(',', $current_ids);
    $id_array = array_filter($id_array, 'is_numeric'); // Remove non-numeric values
    
    if (empty($id_array)) {
        echo json_encode(['success' => true, 'deleted_ids' => []]);
        exit;
    }
    
    // Create placeholders for the IN clause
    $placeholders = str_repeat('?,', count($id_array) - 1) . '?';
    
    // Check which IDs from the current list no longer exist in the database
    $sql = "SELECT id FROM biodata_images WHERE id IN ($placeholders) AND approval_status = 'approved'";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    
    // Bind parameters
    $types = str_repeat('i', count($id_array));
    $stmt->bind_param($types, ...$id_array);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Get the IDs that still exist
    $existing_ids = [];
    while ($row = $result->fetch_assoc()) {
        $existing_ids[] = $row['id'];
    }
    
    // Find deleted IDs (IDs that were in the current list but no longer exist)
    $deleted_ids = array_diff($id_array, $existing_ids);
    
    echo json_encode([
        'success' => true,
        'deleted_ids' => array_values($deleted_ids)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 