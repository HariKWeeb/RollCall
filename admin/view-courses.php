<?php
include('../connect.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>
    <!-- Include Bootstrap CSS -->
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


    <?php

    // Check if the connection is successful
    if ($conn) {
        // Fetch courses from the database
        $query = "SELECT * FROM courses";
        $result = mysqli_query($conn, $query);

        // Check if any courses were found
        if ($result && mysqli_num_rows($result) > 0) {
            // Start building the HTML table
            echo '<div class="container mt-4">';
            echo '<h2>Courses</h2>';
            echo '<table class="table">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Course Name</th>';
            echo '<th>Course Code</th>';
            echo '<th>Credits</th>';
            // echo '<th>Actions</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $row['course_name'] . '</td>';
                echo '<td>' . $row['course_code'] . '</td>';
                echo '<td>' . $row['course_credits'] . '</td>';
                echo '<td><a href="' . $row['syllabus_file'] . '" target="_blank">View Syllabus</a></td>';
                echo '</tr>';
            }


            // Finish building the table and close the container
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            echo '<div class="container mt-4">';
            echo '<h2>No courses found.</h2>';
            echo '</div>';
        }

        // Close the database connection
        mysqli_close($conn);
    }
    ?>








    <!-- Include Bootstrap JavaScript and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>

</body>

</html>