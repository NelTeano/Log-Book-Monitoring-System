<?php
session_start();
require 'dbconn1.php';

class RegisterUser {
    public function register($username, $password) {
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

        // Insert new user into the database
        $query = 'INSERT INTO users (username, password) VALUES (?, ?)';
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('ss', $username, $hashedPassword);
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

    // Validate inputs
    if (empty($username) || empty($password)) {
        echo 'Please fill in all fields';
    } else {
        $app = new RegisterUser();
        $response = $app->register($username, $password);
        echo $response;
    }
}
?>
