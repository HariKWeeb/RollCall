<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

// Start the session
session_start();


include 'connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $role = $_POST["role"];

    

    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password' AND role = '$role'";
    $result = $conn->query($sql);


    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Store user information in session variables
        $_SESSION['user_email'] = $row['email'];
        $_SESSION['user_role'] = $row['role'];


        // Replace $teacherName with the actual name value



        // Redirect based on user role
        if ($row['role'] == 'Admin') {
            header("Location: admin/index.php");
        } elseif ($row['role'] == 'Teacher') {
            header("Location: staff/index.php");
        } elseif ($row['role'] == 'Student') {
            header("Location: student/index.php");
        }
        exit();
    } else {
        // Invalid credentials, show an error message or handle it as needed
        $_SESSION['error_message'] = "Invalid login credentials.";
    }

    $conn->close();
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - RollCall</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.7/dist/tailwind.min.css" rel="stylesheet">
    <script type="text/javascript">
        function preventBack() { window.history.forward() };
        setTimeout("preventBack()", 0);
        window.onunload = function () { null; }
    </script>
</head>

<body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="container mx-auto max-w-md">
        <div class="bg-white p-6 rounded shadow-md">
            <h3 class="text-2xl text-center mb-4">Login</h3>
            <?php
            // Display error message if set
            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert alert-danger mt-3 text-center'>" . $_SESSION['error_message'] . "</div>";
                unset($_SESSION['error_message']);
            }
            ?>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                    <input type="email" name="email" id="email" required class="w-full px-3 py-2 border rounded-lg" />
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-3 py-2 border rounded-lg" />
                </div>
                <div class="mb-4">
                    <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Role:</label>
                    <select name="role" id="role" class="w-full px-3 py-2 border rounded-lg">
                        <option value="Admin">Admin</option>
                        <option value="Teacher">Teacher</option>
                        <option value="Student">Student</option>
                    </select>
                </div>
                <button type="submit" name="login"
                    class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring focus:border-blue-300">
                    Login
                </button>
            </form>
        </div>
    </div>
</body>

</html>