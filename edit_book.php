<?php
require 'dbconn1.php';

$config = new Config();
$conn = $config->conn;

$person_id = $_POST['person_id'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$time_in = $_POST['time_in'];
$time_out = $_POST['time_out'];

$query = "UPDATE book SET first_name = ?, last_name = ?, time_in = ?, time_out = ? WHERE person_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ssssi', $first_name, $last_name, $time_in, $time_out, $person_id);

if ($stmt->execute()) {
    echo "Record updated successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
