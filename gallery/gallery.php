<?php
require_once('../db.php');

// Create gallery_images table if not exists
$sql = "CREATE TABLE IF NOT EXISTS gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    tab_id INT NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Create gallery_tabs table if not exists
$sql = "CREATE TABLE IF NOT EXISTS gallery_tabs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tab_name VARCHAR(100) NOT NULL,
    display_order INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Insert default tab if none exists
$result = $conn->query("SELECT COUNT(*) as count FROM gallery_tabs");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $conn->query("INSERT INTO gallery_tabs (tab_name, display_order) VALUES ('Default Gallery', 1)");
}

// Get all tabs
$tabs = [];
$result = $conn->query("SELECT * FROM gallery_tabs ORDER BY display_order ASC");
while ($row = $result->fetch_assoc()) {
    $tabs[] = $row;
}

// Get active tab
$active_tab = isset($_GET['tab']) ? (int)$_GET['tab'] : $tabs[0]['id'];

// Get common heading from settings
$heading = "Gallery Images"; // Default heading
$result = $conn->query("SELECT common_heading FROM gallery_settings ORDER BY id DESC LIMIT 1");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $heading = $row['common_heading'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .gallery-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .tab {
            padding: 10px 20px;
            background: #f5f5f5;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }
        .tab:hover {
            background: #e0e0e0;
        }
        .tab.active {
            background: #2196F3;
            color: white;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .gallery-item {
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }
        .gallery-item h3 {
            margin: 10px 0;
            color: #333;
        }
        .gallery-item .form-link {
            display: inline-block;
            margin: 10px 0;
            padding: 8px 15px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }
        .gallery-item .form-link:hover {
            background: #45a049;
        }
        .upload-section {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        .upload-btn {
            padding: 10px 20px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        .upload-btn:hover {
            background: #1976D2;
        }
        #imageUpload {
            display: none;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="gallery-container">
        <h2><?php echo htmlspecialchars($heading); ?></h2>
        
        <div class="tabs">
            <?php foreach ($tabs as $tab): ?>
                <button class="tab <?php echo $tab['id'] == $active_tab ? 'active' : ''; ?>" 
                        onclick="switchTab(<?php echo $tab['id']; ?>)">
                    <?php echo htmlspecialchars($tab['tab_name']); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="upload-section">
            <input type="file" id="imageUpload" accept="image/*" multiple>
            <button class="upload-btn" onclick="document.getElementById('imageUpload').click()">Upload Image</button>
        </div>
        
        <?php foreach ($tabs as $tab): ?>
            <div class="tab-content <?php echo $tab['id'] == $active_tab ? 'active' : ''; ?>" 
                 id="tab-content-<?php echo $tab['id']; ?>">
                <div class="gallery-grid">
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM gallery_images WHERE tab_id = ? ORDER BY upload_date DESC");
                    $stmt->bind_param("i", $tab['id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="gallery-item">';
                        echo '<h3>' . htmlspecialchars($heading) . '</h3>';
                        echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="Gallery Image">';
                        echo '<a href="form.php?image_id=' . $row['id'] . '" class="form-link">Fill the Form</a>';
                        echo '</div>';
                    }
                    $stmt->close();
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        function switchTab(tabId) {
            // Update URL without refreshing
            const url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.pushState({}, '', url);

            // Update active tab
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelector(`.tab[onclick="switchTab(${tabId})"]`).classList.add('active');

            // Update active content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(`tab-content-${tabId}`).classList.add('active');
        }

        document.getElementById('imageUpload').addEventListener('change', function(e) {
            const files = e.target.files;
            const activeTabId = document.querySelector('.tab.active').getAttribute('onclick').match(/\d+/)[0];

            for (let i = 0; i < files.length; i++) {
                const formData = new FormData();
                formData.append('image', files[i]);
                formData.append('tab_id', activeTabId);

                fetch('upload.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const galleryGrid = document.querySelector(`#tab-content-${activeTabId} .gallery-grid`);
                        const newItem = document.createElement('div');
                        newItem.className = 'gallery-item';
                        newItem.innerHTML = `
                            <h3><?php echo htmlspecialchars($heading); ?></h3>
                            <img src="${data.image_path}" alt="Gallery Image">
                            <a href="form.php?image_id=${data.image_id}" class="form-link">Fill the Form</a>
                        `;
                        galleryGrid.insertBefore(newItem, galleryGrid.firstChild);
                    } else {
                        alert('Error uploading image: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error uploading image');
                });
            }
        });
    </script>
</body>
</html> 