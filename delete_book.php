

<?php
session_start();
require 'dbconn1.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode the incoming JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    $personId = $data['person_id'];

    if (!isset($personId)) {
        echo "Invalid Request";
        exit();
    }

    $config = new Config();
    $conn = $config->conn;

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Fetch the record from the 'book' table
        $fetchQuery = "SELECT * FROM book WHERE person_id = ?";
        $stmt = $conn->prepare($fetchQuery);
        $stmt->bind_param("i", $personId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $record = $result->fetch_assoc();

            // Insert the record into the 'history' table
            $insertQuery = "INSERT INTO history (person_id, first_name, last_name, time_in, time_out) VALUES (?, ?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param(
                "issss",
                $record['person_id'],
                $record['first_name'],
                $record['last_name'],
                $record['time_in'],
                $record['time_out']
            );
            $insertStmt->execute();

            // Delete the record from the 'book' table
            $deleteQuery = "DELETE FROM book WHERE person_id = ?";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bind_param("i", $personId);
            $deleteStmt->execute();

            // Commit the transaction
            $conn->commit();
            echo "Record successfully moved to history and deleted from the book.";
        } else {
            echo "Record not found.";
        }
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    } finally {
        $stmt->close();
        $conn->close();
    }
} else {
    echo "Invalid Request Method";
}
?>

