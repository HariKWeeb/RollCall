<?php
session_start();

include('../connect.php');
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user_id"])) {
    // Get the user ID from the POST data
    $userId = $_POST["user_id"];

    // Delete the user from the database
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // Close the database connection
    $conn->close();
}
