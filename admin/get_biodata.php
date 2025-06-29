<?php
require_once('../db.php');

header('Content-Type: application/json');

try {
    $id = intval($_GET['id']);
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT * FROM biodata_images WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Biodata not found']);
        exit;
    }
    
    $biodata = $result->fetch_assoc();
    echo json_encode(['success' => true, 'data' => $biodata]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?> 