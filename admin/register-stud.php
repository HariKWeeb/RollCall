<?php
require_once '../phpqrcode/qrlib.php';
include('../connect.php');

$errors = [];
$registrationMessage = '';
$qrcode_path = '';

$classes = [];
$result = $conn->query("SELECT * FROM class_details");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
    $result->free_result();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form submitted, process the registration
    $student_name = $_POST['student_name'];
    if (!preg_match("/^[a-zA-Z\s]+$/", $student_name)) {
        $errors[] = "Name should contain only letters and spaces.";
    }

    $phone = $_POST['phone'];
    $phone = $_POST['phone'];
    if (!ctype_digit($phone)) {
        $errors[] = "Phone should contain only digits.";
    }
    $email = $_POST['email'];
    $email = $_POST['email'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $age = $_POST['age'];
    if (!ctype_digit($age) || $age <= 0) {
        $errors[] = "Age should be a positive integer.";
    }
    $class_id = $_POST['class_id'];
    $semester = $_POST['semester'];
    $year = $_POST['year'];
    if (!preg_match("/^[a-zA-Z0-9\s]+$/", $semester) || !preg_match("/^[a-zA-Z0-9\s]+$/", $year)) {
        $errors[] = "Invalid semester or year format.";
    }
    $role = "Student";
    $password = isset($_POST["password"]) ? $_POST["password"] : '';

    // Validate email uniqueness
    $checkEmailQuery = "SELECT email FROM students WHERE email = ?";
    if ($stmt = $conn->prepare($checkEmailQuery)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email is already in use. Please use a different email.";
        }
        $stmt->close();
    }

    // If there are no errors, proceed with registration
    if (empty($errors)) {
        // Generate a unique QR code for the student with the student's name as content
        $qrcode_path = '../images/' . time() . '.png';
        QRcode::png($student_name, $qrcode_path, 'H', 4, 4);

        // Insert the student information and QR code path into the database
        $insertQuery = "INSERT INTO students (student_name, phone, email, gender, age, class_id, semester, qr_code_path, year) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($insertQuery)) {
            $stmt->bind_param("sssssssss", $student_name, $phone, $email, $gender, $age, $class_id, $semester, $qrcode_path, $year);
            $stmt->execute();
            $stmt->close();
        }

        $insertUserQuery = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($insertUserQuery)) {
            $stmt->bind_param("ssss", $student_name, $email, $password, $role);
            $stmt->execute();
            $stmt->close();
        }


        // Set the registration message
        $registrationMessage = "Student registered successfully!";
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
    <title>Student Registration Form</title>
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
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
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
                    <a class="nav-link dropdown-toggle" href="#" id="coursesDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Courses
                    </a>
                    <div class="dropdown-menu" aria-labelledby="coursesDropdown">
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
        <h2>Student Registration Form</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <?php foreach ($errors as $error): ?>
                    <?php echo $error; ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($registrationMessage)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $registrationMessage; ?>
            </div>
        <?php endif; ?>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" class="my-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="student_name">Student Name:</label>
                        <input type="text" name="student_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="text" name="phone" class="form-control" pattern="[0-9]{10}" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select name="gender" class="form-control" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="age">Age:</label>
                        <input type="number" name="age" class="form-control" min="1" required>
                    </div>

                    <div class="form-group">
                        <label for="class_id">Select Class:</label>
                        <select name="class_id" class="form-control" required>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo $class['class_id']; ?>">
                                    <?php echo $class['class_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="semester">Semester:</label>
                        <input type="text" name="semester" class="form-control" pattern="[a-zA-Z0-9\s]+" required>
                    </div>

                    <div class="form-group">
                        <label for="year">Year:</label>
                        <input type="text" name="year" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Register</button>
        </form>

        <?php
        // Check if a QR code path is available
        if (!empty($qrcode_path)) {
            echo '<div class="mt-3"><h3>Generated QR Code</h3>';
            echo '<img src="' . $qrcode_path . '" alt="QR Code">';
            echo '</div>';
        }
        ?>
    </div>

    <!-- Include Bootstrap JS via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>