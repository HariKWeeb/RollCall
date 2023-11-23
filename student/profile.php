<?php
session_start(); // Start the session

include('../connect.php');

if (isset($_SESSION["user_email"])) {
    $email = $_SESSION["user_email"];
} else {
    // Redirect to login page or handle the case when the session is not set
    header("Location: login.php");
    exit();
}

// Fetch student details from the database using the email and join with the classes table
$studentQuery = "SELECT s.*, c.class_name FROM students s JOIN class_details c ON s.class_id = c.class_id WHERE s.email = ?";
$stmt = $conn->prepare($studentQuery);

// Check if the prepare statement succeeded
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Check if the execute statement succeeded
if (!$result) {
    die("Execute failed: " . $stmt->error);
}

// Check if the student exists
if ($result->num_rows > 0) {
    $studentDetails = $result->fetch_assoc();

    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    // Handle the case where the student does not exist
    $stmt->close();
    $conn->close();
    die("Student not found");
}
?>
<!-- The rest of your HTML code remains unchanged -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ID Card</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .id-card {
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .id-card img {
            max-width: 100px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .id-card p {
            margin: 5px 0;
        }

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


                <li class="nav-item">
                    <a class="nav-link" href="./attendance.php">Attendance</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>


            </ul>
        </div>
    </nav>
    <div class="container">
        <div class="id-card" id="idCard">
            <img src="<?php echo $studentDetails['qr_code_path']; ?>" alt="Student Photo" id="studentPhoto"
                class="img-fluid rounded-circle">
            <p id="studentName" class="lead">
                <?php echo $studentDetails['student_name']; ?>
            </p>
            <p id="rollNo">Roll No:
                <?php echo $studentDetails['rollno']; ?>
            </p>
            <p id="phone">Phone:
                <?php echo $studentDetails['phone']; ?>
            </p>
            <p id="email">Email:
                <?php echo $studentDetails['email']; ?>
            </p>
            <p id="gender">Gender:
                <?php echo $studentDetails['gender']; ?>
            </p>
            <p id="age">Age:
                <?php echo $studentDetails['age']; ?>
            </p>
            <p id="semester">Semester:
                <?php echo $studentDetails['semester']; ?>
            </p>
            <p id="year">Year:
                <?php echo $studentDetails['year']; ?>
            </p>
            <p id="className">Class Name:
                <?php echo $studentDetails['class_name']; ?>
            </p>


            <button onclick="exportAsImage()" class="btn btn-primary mt-3">Export as Image</button>
        </div>
    </div>


    <div class="container mt-4">
        <h2>Update Details</h2>
        <!-- Add a form for updating details -->
        <form action="update_details.php" method="post">
            <!-- You can include input fields for the details you want to update -->
            <div class="form-group">
                <label for="newPhone">Phone:</label>
                <input type="text" name="newPhone" class="form-control" placeholder="Enter new phone number">
            </div>

            <div class="form-group">
                <label for="newPassword">New Password:</label>
                <input type="password" name="newPassword" class="form-control" placeholder="Enter new password">
            </div>

            <div class="form-group">
                <label for="newAge">Age:</label>
                <input type="text" name="newAge" class="form-control" placeholder="Enter new age">
            </div>

            <div class="form-group">
                <label for="newSemester">Semester:</label>
                <input type="text" name="newSemester" class="form-control" placeholder="Enter new semester">
            </div>

            <button type="submit" class="btn btn-primary">Update Details</button>
        </form>
    </div>



    <!-- Include Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Include html2canvas library -->
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

    <script>
        // Function to export the ID card as an image
        function exportAsImage() {
            var idCard = document.getElementById("idCard");
            html2canvas(idCard).then(function (canvas) {
                var imgData = canvas.toDataURL('image/png');
                var img = new Image();
                img.src = imgData;
                var link = document.createElement('a');
                link.href = imgData;
                link.download = 'student_id_card.png';
                link.click();
            });
        }
    </script>
</body>

</html>