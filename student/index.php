<?php
session_start(); // Start the session

include('../connect.php');
if (isset($_SESSION["user_email"])) {
    $email = $_SESSION["user_email"];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script type="text/javascript">
        function preventBack(){window.history.forward()};
        setTimeout("preventBack()",0);
        window.onunload=function(){null;}
    </script>
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

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="text-center">
                    <h1 class="display-4 mb-4">Welcome to RollCall</h1>
                </div>
                <p class="lead text-center">
                    "RollCall is your online dashboard for managing courses and attendance. It's designed to make your
                    teaching experience easier and more organized."
                </p>
                <p>
                    To get started, use the navigation bar at the top to explore the different features and manage your
                    classes effectively.
                </p>
            </div>
        </div>
    </div>
</body>

</html>