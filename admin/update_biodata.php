<?php
require_once('../db.php');
$id = intval($_POST['id']);
$name = $_POST['name'];
$age = $_POST['age'];
$type = $_POST['type'];
$address = $_POST['address'];

$image_path = null;
if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    $image_path = "uploads/" . basename($_FILES["image"]["name"]);
}

if($image_path){
    $stmt = $conn->prepare("UPDATE biodata_images SET name=?, age=?, type=?, address=?, image_path=? WHERE id=?");
    $stmt->bind_param("sssssi", $name, $age, $type, $address, $image_path, $id);
} else {
    $stmt = $conn->prepare("UPDATE biodata_images SET name=?, age=?, type=?, address=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $age, $type, $address, $id);
}
$success = $stmt->execute();
echo json_encode(['success' => $success]);
?> 