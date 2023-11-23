<?php
session_start();
include('../connect.php');
$alert_message = ""; // Variable to store the alert message

if (isset($_POST['assign_course'])) {
    $class_id = $_POST['class_id'];
    $course_ids = $_POST['course_ids'];

    foreach ($course_ids as $course_id) {
        $sql = "INSERT INTO class_subjects (class_id, course_id) VALUES ('$class_id', '$course_id')";
        $result = $conn->query($sql);
    }

    // Check if the query was successful
    if ($result) {
        $alert_message = "Courses assigned successfully!";
    } else {
        $alert_message = "Error assigning courses. Please try again.";
    }
}

$sql_class = "SELECT * FROM class_details";
$result_class = $conn->query($sql_class);

$sql_course = "SELECT * FROM courses";
$result_course = $conn->query($sql_course);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Assign Course to Class</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 56px; /* Adjusted to accommodate fixed navbar */
        }

        .navbar {
            background-color: #343a40; /* Dark background color */
        }

        .navbar-dark .navbar-brand {
            color: #ffffff; /* White text for brand */
        }

        .navbar-dark .navbar-nav .nav-link {
            color: #ffffff; /* White text for nav links */
        }

        .navbar-dark .navbar-toggler-icon {
            background-color: #ffffff; /* White color for the toggler icon */
        }
    </style>
    <script type="text/javascript">
        function preventBack(){window.history.forward()};
        setTimeout("preventBack()",0);
        window.onunload=function(){null;}

        <?php
        if (!empty($alert_message)) {
            echo "alert('$alert_message');";
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

    <div class="container mt-5">
        <h2>Assign Course to Class</h2>
        <form method="post" action="">
            <div class="form-group">
                <label>Select Class:</label>
                <select name="class_id" class="form-control">
                    <?php
                    if ($result_class->num_rows > 0) {
                        while ($row = $result_class->fetch_assoc()) {
                            echo "<option value='" . $row['class_id'] . "'>" . $row['class_name'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No class available</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Select Courses:</label>
                <?php
                if ($result_course->num_rows > 0) {
                    while ($row = $result_course->fetch_assoc()) {
                        echo "<div class='form-check'>";
                        echo "<input type='checkbox' name='course_ids[]' value='" . $row['course_id'] . "' class='form-check-input'>";
                        echo "<label class='form-check-label'>" . $row['course_name'] . "</label>";
                        echo "</div>";
                    }
                } else {
                    echo "No courses available";
                }
                ?>
            </div>

            <div class="form-group">
                <input type="submit" name="assign_course" value="Assign Course" class="btn btn-primary">
            </div>
        </form>
    </div>

    <!-- Include Bootstrap JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>