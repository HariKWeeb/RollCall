<?php
session_start();
include('../connect.php');

// Simulated user authentication (replace this with your authentication logic)
function authenticateUser()
{
    if (isset($_SESSION["user_email"])) {
        return $_SESSION["user_email"];
    } else {
        // Handle the case where the email session variable is not set
        echo "Email session variable not set.";
        return null; // Return null or handle the error as needed
    }
}

// Simulated user authentication
$teacherEmail = authenticateUser();

if ($teacherEmail !== null) {
    // Database connection information
    

    // Function to retrieve teacher's classes from the 'class_details' table
    function getTeacherClasses($conn, $teacherId)
    {
        $sql = "SELECT class_name FROM class_details WHERE teacher_id = $teacherId";
        $result = $conn->query($sql);

        $teacherClasses = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $teacherClasses[] = $row["class_name"];
            }
        }

        return $teacherClasses;
    }

    // Function to retrieve teacher's subjects from the 'courses' table
    function getTeacherSubjects($conn, $teacherId)
    {
        $sql = "SELECT course_name, course_code FROM courses WHERE teacher_id = $teacherId";
        $result = $conn->query($sql);

        $teacherSubjects = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $teacherSubjects[] = $row;
            }

            return $teacherSubjects;
        } else {
            return array(); // Return an empty array if no subjects are found
        }
    }

    // Get the teacher_id using the teacher_email
    $sql = "SELECT teacher_id FROM teachers WHERE teacher_email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $teacherEmail);
        $stmt->execute();
        $stmt->bind_result($teacherId);
        $stmt->fetch();
        $stmt->close();

        if (!empty($teacherId)) {
            // Teacher_id found, now you can use it to retrieve classes and subjects
            $teacherClasses = getTeacherClasses($conn, $teacherId);
            $teacherSubjects = getTeacherSubjects($conn, $teacherId);

            // Close the database connection
            $conn->close();
        } else {
            echo "Teacher not found.";
        }
    } else {
        echo "Prepared statement creation failed: " . $conn->error;
    }
} else {
    echo "Email session variable not set.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Classes and Subjects</title>
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
    <h1 class="text-center mt-4">Teacher Classes and Subjects</h1>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Class</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($teacherClasses as $class) {
                            echo "<tr><td>$class</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="col-md-6">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Subject</th>
                            <th>Course Code</th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        foreach ($teacherSubjects as $subject) {
                            echo "<tr>";
                            echo "<td>{$subject['course_name']}</td>";
                            echo "<td>{$subject['course_code']}</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>