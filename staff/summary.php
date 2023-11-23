<?php
// Assuming you have a database connection
include('../connect.php');

// Fetch class details
$class_query = "SELECT * FROM class_Details";
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
    <title>Attendance Report</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
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

        .low-attendance {
            background-color: #ffdddd;
        }

        .high-attendance {
            background-color: #d4edda;
            /* Light green */
        }
    </style>
    <script type="text/javascript">
        function preventBack() { window.history.forward() };
        setTimeout("preventBack()", 0);
        window.onunload = function () { null; }
    </script>
</head>

<body>
    <!-- Your existing navbar code with custom styling -->
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
                // Add a class based on attendance percentage
                $attendanceClass = ($attendanceRow['attendance_percentage'] < 70) ? 'low-attendance' : 'high-attendance';

                $formattedPercentage = number_format($attendanceRow['attendance_percentage'], 2);

                echo "<tr class='$attendanceClass'>
                                <td>" . $attendanceRow['student_name'] . "</td>
                                <td>" . $formattedPercentage. "%</td>
                              </tr>";
            }

            echo "</tbody></table>";
        }
        ?>

    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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