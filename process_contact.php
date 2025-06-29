<?php
// Include database configuration
require_once 'db_config.php';

// Set header for JSON response
header('Content-Type: application/json');

// Initialize response array
$response = array('success' => false, 'message' => '');

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING);
    $message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format';
        echo json_encode($response);
        exit;
    }

    // Prepare SQL statement
    $sql = "INSERT INTO contacts (name, email, phone, message) VALUES (?, ?, ?, ?)";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement
        mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $phone, $message);
        
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            $response['success'] = true;
            $response['message'] = 'Message sent successfully';
        } else {
            $response['message'] = 'Error: Could not execute query';
        }

        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        $response['message'] = 'Error: Could not prepare query';
    }

    // Close connection
    mysqli_close($conn);
} else {
    $response['message'] = 'Invalid request method';
}

// Send JSON response
echo json_encode($response); 