<?php
require_once('db.php');

header('Content-Type: application/json');

if (!isset($_POST['image_id'])) {
    echo json_encode(['success' => false, 'message' => 'No image ID provided']);
    exit;
}

$image_id = intval($_POST['image_id']);

try {
    // Get the image path before deleting
    $stmt = $conn->prepare("SELECT image_path FROM biodata_images WHERE id = ?");
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $image_path = $row['image_path'];
        
        // Delete from database
        $stmt = $conn->prepare("DELETE FROM biodata_images WHERE id = ?");
        $stmt->bind_param("i", $image_id);
        
        if ($stmt->execute()) {
            // Delete the actual file
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Failed to delete from database");
        }
    } else {
        throw new Exception("Image not found");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 