<?php
session_start();
require_once('../db.php');

// Check if admins table exists, if not create it
$check_table = "SHOW TABLES LIKE 'admins'";
$table_exists = $conn->query($check_table);

if ($table_exists->num_rows == 0) {
    // Create admins table
    $create_table = "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($create_table)) {
        die("Error creating admins table: " . $conn->error);
    }
    
    // Create default admin user
    $username = 'admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    
    $insert_admin = "INSERT INTO admins (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($insert_admin);
    if ($stmt) {
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to check admin credentials
    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                header('Location: dashboard.php');
                exit();
            }
        }
        $stmt->close();
    } else {
        $error = "Database error: " . $conn->error;
    }
    
    if (!isset($error)) {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Projapoti Kuthir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #15283c;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header img {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #FF4D1C;
            border-color: #FF4D1C;
        }
        .btn-primary:hover {
            background-color: #e64417;
            border-color: #e64417;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <img src="../images/logo.jpg" alt="Projapoti Kuthir Logo">
                <h2>Admin Login</h2>
            </div>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <div class="text-center mt-3">
                <a href="../index.php" class="text-decoration-none">Back to Home</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 