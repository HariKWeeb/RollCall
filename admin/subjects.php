<?php
include('../connect.php');
// Check if the form is submitted
if (isset($_POST['add_course'])) {
    // Include your database connection code here

    if ($conn) {
        // Retrieve form data
        $course_name = mysqli_real_escape_string($conn, $_POST['course_name']);
        $course_code = mysqli_real_escape_string($conn, $_POST['course_code']);
        $course_credits = (int)$_POST['course_credits'];
        $teacher_id = (int)$_POST['teacher_id'];

        // File upload handling
        $targetDirectory = "syllabus/"; // Directory where syllabus files will be stored

        $syllabusFileName = basename($_FILES['syllabus_file']['name']);
        $targetFilePath = $targetDirectory . $syllabusFileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Check if the file is valid and not empty
        if (!empty($_FILES['syllabus_file']['tmp_name'])) {
            if (move_uploaded_file($_FILES['syllabus_file']['tmp_name'], $targetFilePath)) {
                // File uploaded successfully
                // You can now insert the course data and syllabus file path into the database
                $query = "INSERT INTO courses (course_name, course_code, course_credits, teacher_id, syllabus_file) VALUES ('$course_name', '$course_code', $course_credits, $teacher_id, '$targetFilePath')";

                if (mysqli_query($conn, $query)) {
                    // Course and syllabus data inserted successfully
                    // Redirect to a success page or display a success message
                    echo '<script type="text/javascript">alert("Course added successfully");</script>';
                } else {
                    // Handle database insertion error
                    echo '<script type="text/javascript">alert("Error: ' . mysqli_error($conn) . '");</script>';
                }
            } else {
                // Handle file upload error
                echo '<script type="text/javascript">alert("Error uploading file.");</script>';
            }
        } else {
            // Handle empty file error
            echo '<script type="text/javascript">alert("Please select a syllabus file.");</script>';
        }

        // Close the database connection
        mysqli_close($conn);
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course - Admin Page</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include Tailwind CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script type="text/javascript">
        function preventBack(){window.history.forward()};
        setTimeout("preventBack()",0);
        window.onunload=function(){null;}
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
                    <a class="nav-link dropdown-toggle" href="#" id="teacherDropdown" role="button"
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

    <div class="container mt-4">
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="course_name">Course Name:</label>
                <input type="text" name="course_name" id="course_name" required class="form-control" />
            </div>
            <div class="form-group">
                <label for="course_code">Course Code:</label>
                <input type="text" name="course_code" id="course_code" required class="form-control" />
            </div>
            <div class="form-group">
                <label for="syllabus_file">Syllabus File:</label>
                <input type="file" name="syllabus_file" id="syllabus_file" required class="form-control" />
            </div>
            <div class="form-group">
                <label for="course_credits">Course Credits:</label>
                <input type="number" name="course_credits" id="course_credits" required class="form-control" />
            </div>
            <div class="form-group">
                <label for="teacher_id">Teacher:</label>
                <select name="teacher_id" id="teacher_id" class="form-control" required>
                    <!-- Populate this dropdown with teacher_id and teacher_name from your database -->
                    <?php
                    // Include your database connection code here
                    $connection = mysqli_connect("localhost", "root", "", "roll_call_db");

                    if ($connection) {
                        $query = "SELECT teacher_id, teacher_name FROM teachers";
                        $result = mysqli_query($connection, $query);

                        if ($result) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['teacher_id'] . "'>" . $row['teacher_name'] . "</option>";
                            }
                        }

                        mysqli_close($connection);
                    }
                    ?>
                </select>
            </div>
            <button type="submit" name="add_course" class="btn btn-primary">Add Course</button>
        </form>
    </div>

    <!-- Include Bootstrap JavaScript and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
