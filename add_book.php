<?php
require 'dbconn1.php';

$config = new Config();
$conn = $config->conn;

$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$time_in = $_POST['time_in'];
$time_out = $_POST['time_out'];

$query = "INSERT INTO book (first_name, last_name, time_in, time_out) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param('ssss', $first_name, $last_name, $time_in, $time_out);

if ($stmt->execute()) {
    echo "Record added successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
