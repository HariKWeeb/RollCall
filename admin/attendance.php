<?php
// Assuming you have a database connection
include('../connect.php');

// Fetch class details
$class_query = "SELECT * FROM class_details";
$class_result = $conn->query($class_query);

// Fetch course details
$course_query = "SELECT * FROM courses";
$course_result = $conn->query($course_query);

// Function to calculate attendance percentage
function calculateAttendance($classId, $courseId, $conn)
{
    $attendance_query = "SELECT student_name, 
                                (SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) / COUNT(status)) * 100 AS attendance_percentage 
                         FROM class_attendance 
                         WHERE class_id = $classId AND course_id = $courseId 
                         GROUP BY student_name";

    $attendance_result = $conn->query($attendance_query);

    // Check for query errors
    if (!$attendance_result) {
        die("Attendance query failed: " . $conn->error);
    }

    // Fetch the results
    $attendanceData = $attendance_result->fetch_all(MYSQLI_ASSOC);

    return $attendanceData;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedClass = $_POST["class"];
    $selectedCourse = $_POST["course"];

    $attendanceData = calculateAttendance($selectedClass, $selectedCourse, $conn);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script type="text/javascript">
        function preventBack() { window.history.forward() };
        setTimeout("preventBack()", 0);
        window.onunload = function () { null; }
    </script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">
            RollCall
        </a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="./users.php">Users</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="studentsDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Students
                    </a>
                    <div class="dropdown-menu" aria-labelledby="studentsDropdown">
                        <a class="dropdown-item" href="./register-stud.php">Register</a>
                        <a class="dropdown-item" href="./view-stud.php">View</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="studentsDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Teachers
                    </a>
                    <div class="dropdown-menu" aria-labelledby="studentsDropdown">
                        <a class="dropdown-item" href="./register-teach.php">Register</a>
                        <a class="dropdown-item" href="./view-teach.php">View</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./manage.php">Manage</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="teacherDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Courses
                    </a>
                    <div class="dropdown-menu" aria-labelledby="studentsDropdown">
                        <a class="dropdown-item" href="./subjects.php">Add Course</a>
                        <a class="dropdown-item" href="./view-courses.php">View</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./attendance.php">Attendance</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./classes.php">Classes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h2 class="mt-3">Attendance Report</h2>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="mt-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="class" class="form-label">Select Class:</label><br>
                    <select name="class" class="form-select" required>
                        <?php
                        $class_result->data_seek(0);
                        while ($class_row = $class_result->fetch_assoc()) {
                            $selected = ($class_row['class_id'] == $selectedClass) ? "selected" : "";
                            echo "<option value='" . $class_row['class_id'] . "' $selected>" . $class_row['class_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="course" class="form-label">Select Course:</label>
                    <select name="course" class="form-select" required>
                        <?php
                        $course_result->data_seek(0);
                        while ($course_row = $course_result->fetch_assoc()) {
                            $selected = ($course_row['course_id'] == $selectedCourse) ? "selected" : "";
                            echo "<option value='" . $course_row['course_id'] . "' $selected>" . $course_row['course_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="submit" value="Generate Report" class="btn btn-primary">
                </div>
            </div>
        </form>
        <?php
        // Display attendance report table
        if (isset($attendanceData)) {
            echo '<button id="exportButton" class="btn btn-primary mb-4 mt-4" onclick="exportData()">Export Data</button>';

            echo "<table id='attendanceTable' class='table table-bordered mt-2'>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Attendance Percentage</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($attendanceData as $attendanceRow) {
                echo "<tr>
                        <td>" . $attendanceRow['student_name'] . "</td>
                        <td>" . number_format($attendanceRow['attendance_percentage'], 2) . "%</td>
                      </tr>";
            }

            echo "</tbody></table>";
        }
        ?>

    </div>



    <!-- Include Bootstrap JavaScript and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>
    <script>
        function exportData() {
        // Get the table data
        var table = document.getElementById('attendanceTable');
        var data = XLSX.utils.table_to_sheet(table, { raw: true });

        // Create a workbook
        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, data, 'Attendance Report');

        // Save the workbook as an Excel file
        XLSX.writeFile(wb, 'Attendance_Report.xlsx');
    }
    </script>
</body>

</html>