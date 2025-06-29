<?php
require_once('../db.php');

header('Content-Type: application/json');

// Check if admin is logged in (you can add session check here)
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$biodata_id = intval($_POST['biodata_id'] ?? 0);
$action = $_POST['action'] ?? ''; // 'approve' or 'reject'

if (!$biodata_id || !in_array($action, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    $status = ($action === 'approve') ? 'approved' : 'rejected';
    
    $stmt = $conn->prepare("UPDATE biodata_images SET approval_status = ? WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    
    $stmt->bind_param("si", $status, $biodata_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Biodata ' . $action . 'd successfully',
            'biodata_id' => $biodata_id,
            'action' => $action,
            'status' => $status
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Biodata not found']);
    }
    
} catch (Exception $e) {
    error_log("Error in approve_biodata.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 