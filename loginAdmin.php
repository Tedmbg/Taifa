<?php
session_start();

// Check if we have form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database credentials
    $servername = "localhost";
    $username = "root"; // Replace with your database username
    $password = ""; // Replace with your database password
    $dbname = "Taifa_health"; // Replace with your database name

    // Create database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve form data
    $form_username = $_POST['username'];
    $form_password = $_POST['password']; // In a real application, you'd hash this

    // Prepare and bind
    $stmt = $conn->prepare("SELECT UserID, Username, Password, Role FROM User WHERE Username = ?");
    $stmt->bind_param("s", $form_username);

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify user exists and password matches
    if ($user && hash('sha256', $form_password) === $user['Password']) {
        // Store user data in session variables
        $_SESSION['UserID'] = $user['UserID'];
        $_SESSION['Username'] = $user['Username'];
        $_SESSION['Role'] = $user['Role'];

        // Redirect the user to different pages based on role
        if ($user['Role'] === 'Admin') {
            header("Location: admin_dashboard.php"); // Path to the admin dashboard
        } elseif ($user['Role'] === 'Doctor') {
            header("Location: loginDoctor.php"); // Path to the doctor dashboard
        } elseif ($user['Role'] === 'Nurse') {
            header("Location: loginNurse.php"); // Path to the nurse dashboard
        }
        exit();
    } else {
        echo "Username or password is incorrect.";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
