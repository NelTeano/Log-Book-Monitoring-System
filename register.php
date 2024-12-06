<?php
session_start();
require 'dbconn1.php';

class RegisterUser {
    public function register($username, $password, $image) {
        $config = new Config();
        $conn = $config->conn;

        // Check if username already exists
        $query = 'SELECT username FROM users WHERE username = ?';
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                return 'Username already exists';
            }
        } else {
            return $conn->error;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Save the image file
        $targetDir = "uploads/";
        $imagePath = $targetDir . basename($image['name']);
        if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
            return 'Failed to upload image';
        }

        // Insert new user into the database
        $query = 'INSERT INTO users (username, password, image) VALUES (?, ?, ?)';
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('sss', $username, $hashedPassword, $imagePath);
            if ($stmt->execute()) {
                return 'Registration successful';
            } else {
                return $stmt->error;
            }
        } else {
            return $conn->error;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $image = $_FILES['image'];

    // Validate inputs
    if (empty($username) || empty($password) || empty($image['name'])) {
        echo 'Please fill in all fields and upload an image';
    } else {
        $app = new RegisterUser();
        $response = $app->register($username, $password, $image);
        echo $response;
    }
}
?>
