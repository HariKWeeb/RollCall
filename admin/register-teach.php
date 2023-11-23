<?php
session_start();
// Database configuration (change these values accordingly)
include('../connect.php');

$errors = [];
$registrationMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form submitted, process the registration
    $teacher_name = $_POST['teacher_name'];
    $teacher_dept = $_POST['teacher_dept'];
    $teacher_email = $_POST['teacher_email'];
    $teacher_course = $_POST['teacher_course'];
    $password = $_POST['password'];

    // Validate email uniqueness
    $checkEmailQuery = "SELECT teacher_email FROM teachers WHERE teacher_email = ?";
    if ($stmt = $conn->prepare($checkEmailQuery)) {
        $stmt->bind_param("s", $teacher_email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email is already in use. Please use a different email.";
        }
        $stmt->close();
    }

    // If there are no errors, proceed with registration
    if (empty($errors)) {
        // Insert the teacher information into the database
        $insertQuery = "INSERT INTO teachers (teacher_name, teacher_dept, teacher_email, teacher_course) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($insertQuery)) {
            $stmt->bind_param("ssss", $teacher_name, $teacher_dept, $teacher_email, $teacher_course);
            $stmt->execute();
            $stmt->close();
        }

        // Insert the teacher information into the users table
        $user_role = "Teacher"; // Set the role to "Teacher"
        $insertUserQuery = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($insertUserQuery)) {
            $stmt->bind_param("ssss", $teacher_name, $teacher_email, $password, $user_role);
            $stmt->execute();
            $stmt->close();
        }

        // Set the registration message
        $registrationMessage = "Teacher registered successfully!";
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Registration</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script type="text/javascript">
        function preventBack(){window.history.forward()};
        setTimeout("preventBack()",0);
        window.onunload=function(){null;}
    </script>
</head>

<body class="bg-light">
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

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <!-- Teacher Registration Form -->
                <h1 class="text-center">Teacher Registration</h1>
                <?php if (!empty($errors)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php foreach ($errors as $error) : ?>
                            <p><?= $error ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php elseif (!empty($registrationMessage)) : ?>
                    <div class="alert alert-success" role="alert">
                        <?= $registrationMessage ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="" class="p-4">
                    <div class="form-group">
                        <label for="teacher_name">Teacher Name:</label>
                        <input type="text" name="teacher_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="teacher_dept">Department:</label>
                        <input type="text" name="teacher_dept" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="teacher_email">Email:</label>
                        <input type="email" name="teacher_email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="teacher_course">Course:</label>
                        <input type="text" name="teacher_course" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Register Teacher</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Teacher Registration Modal -->
    <div class="modal" id="teacherRegistrationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Teacher Registration</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="modalMessage" class="text-muted"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Function to open the modal and set content
        function openModal(message, isSuccess) {
            const modalMessage = document.getElementById("modalMessage");

            modalMessage.textContent = message;

            if (isSuccess) {
                modalMessage.classList.remove("text-danger");
                modalMessage.classList.add("text-success");
            } else {
                modalMessage.classList.remove("text-success");
                modalMessage.classList.add("text-danger");
            }

            $('#teacherRegistrationModal').modal('show');
        }
    </script>
</body>

</html>
