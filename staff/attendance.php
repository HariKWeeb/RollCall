<?php
session_start();
include('../connect.php');

// Set the default timezone to India
date_default_timezone_set('Asia/Kolkata');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $class_id = $_POST['class_id'];
    $semester = $_POST['semester'];
    $course_id = $_POST['course']; // Use course_id instead of course in the form

    // Fetch other necessary details
    $year = date('Y');

    // Generate a single timestamp for the entire session
    $timestamp = date('Y-m-d H:i:s');

    $scanned_names_json = $_POST["scannedNamesTableBody"];
    $scanned_names = json_decode($scanned_names_json[0], true);

    // Retrieve all student names in the class from the students table
    $selectStudentsQuery = "SELECT student_name FROM students WHERE class_id = ?";

    $stmt = $conn->prepare($selectStudentsQuery);
    $stmt->bind_param("s", $class_id);
    $stmt->execute();
    $stmt->bind_result($student_name_db);

    $all_students = [];
    while ($stmt->fetch()) {
        $all_students[] = $student_name_db;
    }

    // Insert attendance records for scanned names
    foreach ($scanned_names as $scanned_name) {
        // Check if the scanned name is in the list of all students
        if (in_array($scanned_name, $all_students)) {
            $status = 'Present';
        } else {
            $status = 'Absent';
        }

        // Insert the attendance record using the generated timestamp
        $insertQuery = "INSERT INTO class_attendance (class_id, course_id, year, student_name, status, timestamp) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($insertQuery);

        if ($stmt_insert) {
            $stmt_insert->bind_param("ssssss", $class_id, $course_id, $year, $scanned_name, $status, $timestamp);
            $stmt_insert->execute();
            $stmt_insert->close();
        } else {
            echo "Prepare statement error: " . $conn->error;
        }
    }

    // Check for absent students and insert records
    foreach ($all_students as $student_name_db) {
        if (!in_array($student_name_db, $scanned_names)) {
            // Check if the student hasn't been marked present
            $checkPresentQuery = "SELECT 1 FROM class_attendance WHERE class_id = ? AND course_id = ? AND year = ? AND student_name = ? AND status = 'Present' LIMIT 1";

            $stmt_check = $conn->prepare($checkPresentQuery);
            $stmt_check->bind_param("ssss", $class_id, $course_id, $year, $student_name_db);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows == 0) {
                // Student hasn't been marked present, so mark as absent
                $status = 'Absent';

                // Insert the attendance record using the generated timestamp
                $insertQuery = "INSERT INTO class_attendance (class_id, course_id, year, student_name, status, timestamp) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt2 = $conn->prepare($insertQuery);

                if ($stmt2) {
                    $stmt2->bind_param("ssssss", $class_id, $course_id, $year, $student_name_db, $status, $timestamp);
                    $stmt2->execute();
                    $stmt2->close();
                } else {
                    echo "Prepare statement error: " . $conn->error;
                }
            }

            $stmt_check->close();
        }
    }

    // Close the main database connection
    $stmt->close();
    $conn->close();

    // Redirect back to the attendance page after processing
    $_SESSION['attendance_message'] = "Attendance submitted successfully!";
    header("Location: attendance.php");
    exit();
}
?>

<!-- The rest of your HTML remains unchanged -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management System</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <!-- Include Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <!-- Include Bootstrap JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Include Instascan library -->
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
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
        function preventBack() { window.history.forward() };
        setTimeout("preventBack()", 0);
        window.onunload = function () { null; }

        <?php
        if (isset($_SESSION['attendance_message'])) {
            echo "alert('" . $_SESSION['attendance_message'] . "');";
            unset($_SESSION['attendance_message']); // Clear the session variable
        }
        ?>
    </script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <a class="navbar-brand" href="index.php">
            RollCall
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link" href="./classes" id="studentsDropdown" role="button">
                        Classes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./manage.php">Manage</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="./attendance.php">Attendance</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./summary.php">Report</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>

            </ul>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="row">
            <!-- QR Code Scanner -->
            <!-- QR Code Scanner -->
            <div class="col-md-6">
                <h3>QR Code Scanner</h3>
                <video id="scanner" width="100%"></video>
                <!-- Include Instascan library -->
                <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

                <script>
                    // Variable to store scanned names
                    let scannedNames = [];

                    let scanner = new Instascan.Scanner({ video: document.getElementById('scanner') });
                    scanner.addListener('scan', function (content) {
                        // Handle scanned QR code content
                        console.log(content);

                        // Add the scanned content to the table dynamically
                        let newRow = document.createElement('tr');
                        let newNameCell = document.createElement('td');
                        newNameCell.textContent = content;
                        newRow.appendChild(newNameCell);
                        document.getElementById('scannedNamesTableBody').appendChild(newRow);

                        // Add the scanned name to the array
                        scannedNames.push(content);

                        // Call the function to update the hidden input field
                        updateScannedNamesField();
                    });

                    Instascan.Camera.getCameras().then(function (cameras) {
                        if (cameras.length > 0) {
                            scanner.start(cameras[0]);
                        } else {
                            console.error('No cameras found.');
                        }
                    }).catch(function (e) {
                        console.error(e);
                    });

                    // Function to update the form field with scanned names
                    function updateScannedNamesField() {
                        // Extract names from the array and store them as strings
                        let stringNames = scannedNames.map(name => Array.isArray(name) ? name[0] : name);

                        // Convert the array to JSON and assign it to the hidden input field
                        document.getElementById('scannedNamesField').value = JSON.stringify(stringNames);
                    }
                </script>

            </div>



            <!-- Attendance Form -->
            <div class="col-md-6">
                <h3>Add Attendance</h3>
                <form method="POST" action="attendance.php">
                    <div class="form-group">
                        <label for="class_id">Class Name:</label>
                        <?php
                        // Your PHP code to fetch class names goes here
                        $conn = new mysqli("localhost", "root", "", "roll_call_db");
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        $classQuery = "SELECT class_id, class_name FROM class_details";
                        $classResult = $conn->query($classQuery);
                        if ($classResult->num_rows > 0) {
                            echo '<select name="class_id" class="form-control">';
                            while ($row = $classResult->fetch_assoc()) {
                                echo "<option value='" . $row["class_id"] . "'>" . $row["class_name"] . "</option>";
                            }
                            echo '</select>';
                        } else {
                            echo '<p>No classes available.</p>';
                        }

                        $conn->close();
                        ?>
                    </div>
                    <div class="form-group">
                        <label for="semester">Semester:</label>
                        <!-- Enter semester manually -->
                        <input type="text" name="semester" class="form-control" placeholder="Enter Semester">
                    </div>

                    <div class="form-group">
                        <label for="course">Course:</label>
                        <!-- Select course from courses table -->
                        <?php
                        // Your PHP code to fetch courses goes here
                        $conn = new mysqli("localhost", "root", "", "roll_call_db");
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        $courseQuery = "SELECT course_id, course_name FROM courses";
                        $courseResult = $conn->query($courseQuery);
                        if ($courseResult->num_rows > 0) {
                            echo '<select name="course" class="form-control">';
                            while ($row = $courseResult->fetch_assoc()) {
                                echo "<option value='" . $row["course_id"] . "'>" . $row["course_name"] . "</option>";
                            }
                            echo '</select>';
                        } else {
                            echo '<p>No courses available.</p>';
                        }

                        $conn->close();
                        ?>

                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Scanned QR Code Names</th>
                            </tr>
                        </thead>
                        <tbody id="scannedNamesTableBody">
                            <!-- Display scanned QR code names here -->
                        </tbody>
                    </table>
                    <!-- Hidden input field to store scanned names -->
                    <input type="hidden" name="scannedNamesTableBody[]" id="scannedNamesField" value="">

                    <!-- Submit button -->
                    <button type="submit" class="btn btn-primary">Submit Attendance</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>