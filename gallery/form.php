<?php
require_once('../db.php');

$image_id = isset($_GET['image_id']) ? (int)$_GET['image_id'] : 0;

// Fetch image details
$stmt = $conn->prepare("SELECT * FROM gallery_images WHERE id = ?");
$stmt->bind_param("i", $image_id);
$stmt->execute();
$result = $stmt->get_result();
$image = $result->fetch_assoc();
$stmt->close();

if (!$image) {
    die("Image not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fill Form for Image</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .form-image {
            max-width: 300px;
            margin: 0 auto 20px;
            display: block;
            border-radius: 4px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-group textarea {
            height: 100px;
        }
        .submit-btn {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .submit-btn:hover {
            background: #45a049;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #2196F3;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Selected Image" class="form-image">
        
        <form action="process_form.php" method="POST">
            <input type="hidden" name="image_id" value="<?php echo $image_id; ?>">
            
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" required></textarea>
            </div>
            
            <button type="submit" class="submit-btn">Submit Form</button>
        </form>
        
        <a href="gallery.php" class="back-link">← Back to Gallery</a>
    </div>
</body>
</html> 