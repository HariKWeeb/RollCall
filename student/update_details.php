<?php
session_start();

include('../connect.php');

// Check if the user is logged in
if (!isset($_SESSION["user_email"])) {
    // Redirect to login page if not logged in
    header("Location: ../index.php");
    exit();
}


// Initialize alert messages
$successMessage = $errorMessage = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Assuming you have fields like newPhone, newEmail, newPassword, newAge, newSemester in the form
    $newPhone = $_POST["newPhone"];
    $newEmail = $_POST["newEmail"];
    $newPassword = $_POST["newPassword"];
    $newAge = $_POST["newAge"];
    $newSemester = $_POST["newSemester"];

    // You should perform validation and sanitation of user input here

    // Update the details in the database
    $email = $_SESSION["user_email"];

    // Update phone number if provided
    if (!empty($newPhone)) {
        $updatePhoneQuery = "UPDATE students SET phone = ? WHERE email = ?";
        $stmtPhone = $conn->prepare($updatePhoneQuery);
        $stmtPhone->bind_param("ss", $newPhone, $email);
        if ($stmtPhone->execute()) {
            $successMessage .= "Phone number updated successfully.\n";
        } else {
            $errorMessage .= "Error updating phone number: " . $stmtPhone->error . "\n";
        }
        $stmtPhone->close();
    }

    // Update password if provided
    if (!empty($newPassword)) {
        $updatePasswordQuery = "UPDATE users SET password = ? WHERE email = ?";
        $stmtPassword = $conn->prepare($updatePasswordQuery);
        $stmtPassword->bind_param("ss", $newPassword, $email);
        if ($stmtPassword->execute()) {
            $successMessage .= "Password updated successfully.\n";
        } else {
            $errorMessage .= "Error updating password: " . $stmtPassword->error . "\n";
        }
        $stmtPassword->close();
    }

    // Update age if provided
    if (!empty($newAge)) {
        $updateAgeQuery = "UPDATE students SET age = ? WHERE email = ?";
        $stmtAge = $conn->prepare($updateAgeQuery);
        $stmtAge->bind_param("ss", $newAge, $email);
        if ($stmtAge->execute()) {
            $successMessage .= "Age updated successfully.\n";
        } else {
            $errorMessage .= "Error updating age: " . $stmtAge->error . "\n";
        }
        $stmtAge->close();
    }

    // Update semester if provided
    if (!empty($newSemester)) {
        $updateSemesterQuery = "UPDATE students SET semester = ? WHERE email = ?";
        $stmtSemester = $conn->prepare($updateSemesterQuery);
        $stmtSemester->bind_param("ss", $newSemester, $email);
        if ($stmtSemester->execute()) {
            $successMessage .= "Semester updated successfully.\n";
        } else {
            $errorMessage .= "Error updating semester: " . $stmtSemester->error . "\n";
        }
        $stmtSemester->close();
    }

    // Display alert messages
    if (!empty($successMessage)) {
        echo "alert('$successMessage');";
    }

    if (!empty($errorMessage)) {
        echo "alert('$errorMessage');";
    }

    // Redirect back to the profile page or wherever you want
    header("Location: profile.php");
    exit();
}

// Close the database connection
$conn->close();
?>

