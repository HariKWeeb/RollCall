<?php
session_start(); // Start the session

if (isset($_SESSION["user_email"])) {
    $email = $_SESSION["user_email"];
}
include('../connect.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance System</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
    <style>
        <style>body {
            padding-top: 56px;
            /* Adjusted to accommodate fixed navbar */
        }

        .navbar {
            background-color: #343a40;
            /* Dark background color */
        }

        .navbar-dark .navbar-brand {
            color: #ffffff;
            /* White text for brand */
        }

        .navbar-dark .navbar-nav .nav-link {
            color: #ffffff;
            /* White text for nav links */
        }

        .navbar-dark .navbar-toggler-icon {
            background-color: #ffffff;
            /* White color for the toggler icon */
        }
    </style>
    <script type="text/javascript">
        function preventBack(){window.history.forward()};
        setTimeout("preventBack()",0);
        window.onunload=function(){null;}
    </script>
</head>

<body class="container mt-5">

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top mb-4">
        <a class="navbar-brand" href="index.php">
            RollCall
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">



                <li class="nav-item">
                    <a class="nav-link" href="./attendance.php">Attendance</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>

            </ul>
        </div>
    </nav>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="mt-5">
        <div class="form-group" style="margin-top: 100px">
            <label>Select Course:</label>
            <select class="form-control mb-4" name="course" id="course">
                <?php
                include('../connect.php');

                // Fetch courses from the database
                $courseQuery = "SELECT * FROM courses";
                $courseResult = $conn->query($courseQuery);

                // Populate the dropdown with courses
                while ($courseRow = $courseResult->fetch_assoc()) {
                    echo "<option value='{$courseRow['course_id']}'>{$courseRow['course_name']}</option>";
                }

                $conn->close();
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mb-4">View Attendance</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if the course is selected
        if (isset($_POST['course'])) {
            $selectedCourse = $_POST["course"];

            include('../connect.php');


            // Use prepared statement to fetch student name and class_id based on email
            $stmt = $conn->prepare("SELECT student_name, class_id FROM students WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($name_student, $class_id);
            $stmt->fetch();
            $stmt->close();

            // Use prepared statement to fetch course_name based on course_id
            $stmt1 = $conn->prepare("SELECT course_name FROM courses WHERE course_id = ?");
            $stmt1->bind_param("s", $selectedCourse);
            $stmt1->execute();
            $stmt1->bind_result($course_name);
            $stmt1->fetch();
            $stmt1->close();

            // Use prepared statement to fetch class_name based on class_id
            $stmt2 = $conn->prepare("SELECT class_name FROM class_details WHERE class_id = ?");
            $stmt2->bind_param("s", $class_id);
            $stmt2->execute();
            $stmt2->bind_result($class_name);
            $stmt2->fetch();
            $stmt2->close();

            // Display course_name and class_name
            echo "<h2>Course Details:</h2>";
            echo "<p><strong>Course Name:</strong> {$course_name}</p>";
            echo "<p><strong>Class Name:</strong> {$class_name}</p>";

            // Use prepared statement to fetch attendance records for the selected course and current user
            $attendanceQuery = "SELECT * FROM class_attendance WHERE course_id = ? AND student_name = ?";

            // Prepare the statement
            $stmt = $conn->prepare($attendanceQuery);

            // Bind parameters
            $stmt->bind_param("ss", $selectedCourse, $name_student);

            // Execute the statement
            $stmt->execute();

            // Get result
            $attendanceResult = $stmt->get_result();

            if ($attendanceResult->num_rows > 0) {
                echo "<h2>Attendance Records for Selected Course:</h2>";
                echo "<table class='table table-bordered' id='attendanceTable'>";
                echo "<tr><th>Attendance ID</th><th>Student Name</th><th>Status</th><th>Date&Time</th></tr>";

                while ($attendanceRow = $attendanceResult->fetch_assoc()) {
                    // Format the timestamp as desired (adjust 'Y-m-d H:i:s' to your desired format)
                    $formattedTimestamp = date('Y-m-d H:i:s', strtotime($attendanceRow['timestamp']));

                    echo "<tr><td>{$attendanceRow['attendance_id']}</td><td>{$attendanceRow['student_name']}</td><td>{$attendanceRow['status']}</td><td>{$formattedTimestamp}</td></tr>";
                }

                echo "</table>";
            } else {
                echo "No attendance records found for the selected course.";
            }

            // Close statement and connection
            $stmt->close();
            $conn->close();
        }
    }
    ?>
</body>

</html>