<?php
include('../connect.php');
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_user"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $pwd = $_POST["password"];
    $role = $_POST["role"];


    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $pwd, $role);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        echo '<script>alert("User added successfully"); window.location.href = "users.php";</script>';
        exit();
    } else {
        // Error handling
        echo '<script>alert("Email already registered"); window.location.href = "users.php";</script>';
        // echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
