<?php
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the selected class and selected subjects from the form
    $classId = $_POST["selected_class"];
    $selectedSubjects = $_POST["selected_subjects"];

    // Insert the selected subjects into the class_subjects table
    foreach ($selectedSubjects as $subjectId) {
        $insertSql = "INSERT INTO class_subjects (class_id, course_id) VALUES ('$classId', '$subjectId')";
        if ($conn->query($insertSql) === true) {
            // Course assigned successfully
        } else {
            // Error occurred while assigning course
        }
    }
}

// Close the database connection
$conn->close();
?>
