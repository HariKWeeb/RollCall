<?php
session_start();
include('../connect.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User - Admin Page</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script type="text/javascript">
        function preventBack() { window.history.forward() };
        setTimeout("preventBack()", 0);
        window.onunload = function () { null; }
    </script>
    <style>
        #messageModal .modal-dialog {
            max-width: 400px;
        }

        #messageModal .modal-content {
            padding: 20px;
        }
    </style>
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
                        <a class="nav-link dropdown-toggle" href="#" id="teachersDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Teachers
                        </a>
                        <div class="dropdown-menu" aria-labelledby="teachersDropdown">
                            <a class="dropdown-item" href="./register-teach.php">Register</a>
                            <a class="dropdown-item" href="./view-teach.php">View</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./manage.php">Manage</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="classesDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Courses
                        </a>
                        <div class="dropdown-menu" aria-labelledby="classesDropdown">
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
        <h2>Create Class</h2>
        <form method="post" action="">
            <div class="mb-3">
                <label for="class_name" class="form-label">Class Name:</label>
                <input type="text" name="class_name" id="class_name" required class="form-control" />
            </div>
            <div class="mb-3">
                <label for="teacher_id" class="form-label">Teacher:</label>
                <select name="teacher_id" id="teacher_id" class="form-control" required>
                    <?php
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
            <button type="submit" name="create_class" class="btn btn-primary">Create Class</button>
        </form>
    </div>

    <?php
    $connection = mysqli_connect("localhost", "root", "", "roll_call_db");

    if ($connection) {
        if (isset($_POST['create_class'])) {
            $class_name = mysqli_real_escape_string($connection, $_POST['class_name']);
            $teacher_id = $_POST['teacher_id'];

            // Check if the class already exists
            $check_query = "SELECT * FROM class_details WHERE class_name = '$class_name' AND teacher_id = $teacher_id";
            $check_result = mysqli_query($connection, $check_query);

            if ($check_result && mysqli_num_rows($check_result) > 0) {
                echo "<script>alert('Error: Class already exists.');</script>";
            } else {
                // Retrieve teacher_name based on the selected teacher_id
                $teacher_query = "SELECT teacher_name FROM teachers WHERE teacher_id = $teacher_id";
                $teacher_result = mysqli_query($connection, $teacher_query);

                if ($teacher_result && mysqli_num_rows($teacher_result) > 0) {
                    $row = mysqli_fetch_assoc($teacher_result);
                    $teacher_name = $row['teacher_name'];

                    // Insert the class details into the class_details table
                    $insert_query = "INSERT INTO class_details (class_name, teacher_id, teacher_name) VALUES ('$class_name', $teacher_id, '$teacher_name')";
                    $insert_result = mysqli_query($connection, $insert_query);

                    if ($insert_result) {
                        echo "<script>alert('Success: Class created successfully!');</script>";
                    } else {
                        echo "<script>alert('Error: " . mysqli_error($connection) . "');</script>";
                    }
                } else {
                    echo "<script>alert('Error: Teacher not found.');</script>";
                }
            }
        }

        mysqli_close($connection);
    }
    ?>

    <!-- Include Bootstrap JavaScript and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>