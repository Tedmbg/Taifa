<?php
// Always start by initializing the session.
session_start();

// Define your database credentials.
$servername = "localhost";
$username = "root"; // Replace with your username
$password = ""; // Replace with your password
$dbname = "Taifa_health"; // Replace with your database name

// Process the login form submission.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establish database connection using MySQLi.
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection.
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the username and password from the form.
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    // Hash the password using SHA-256
    $hashed_password = hash('sha256', $input_password);

    // Prepare the SQL statement to prevent SQL injection.
    $stmt = $conn->prepare("SELECT UserID, Role, Password FROM User WHERE Username=?");

    // Bind the input parameters to the prepared statement.
    $stmt->bind_param("s", $input_username);

    // Execute the prepared statement.
    $stmt->execute();

    // Get the result of the query.
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        // Fetch the user's data.
        $row = $result->fetch_assoc();

        // Verify the hashed password.
        if ($hashed_password == $row['Password']) {
            // Check the user's role.
            if ($row['Role'] === 'Doctor') {
                // Store the user's data in the session.
                $_SESSION['UserID'] = $row['UserID'];
                $_SESSION['Role'] = $row['Role'];

                // Redirect to select_patient.php.
                header("Location: select_patient.php");
                exit();
            } else {
                // User is not a doctor, deny access.
                echo "Access denied. Only doctors can login here.";
            }
        } else {
            // The username or password was invalid.
            echo "Invalid username or password.";
        }
    } else {
        // The username or password was invalid.
        echo "Invalid username or password.";
    }

    // Close the statement and the connection.
    $stmt->close();
    $conn->close();
}
?>
