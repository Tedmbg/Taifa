<?php
session_start();

// Check if the user is logged in and is an admin.
if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Admin') {
    header("Location: login.html");
    exit();
}

// Database credentials.
$servername = "localhost";
$dbUsername = "root"; // Replace with your database username
$dbPassword = ""; // Replace with your database password
$dbname = "Taifa_health"; // Replace with your database name

$message = '';

// Check if we have a password change submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changePassword'])) {
    // Establish database connection.
    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

    // Check connection.
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $_POST['username']; // Use the posted username
    $newPassword = hash('sha256', $_POST['newPassword']); // Hash the new password using SHA-256

    // Prepare and bind.
    $stmt = $conn->prepare("UPDATE User SET Password = ? WHERE Username = ?");
    $stmt->bind_param("ss", $newPassword, $username);

    // Execute the update.
    if ($stmt->execute()) {
        $message = "Password updated successfully for user $username.";
    } else {
        $message = "Error updating password for user $username.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <!-- Link to your stylesheet here -->
</head>
<body>
    <h1>Admin Dashboard</h1>
    <?php if (!empty($message)) echo "<p>$message</p>"; ?>

    <!-- Form to change the password based on username -->
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        
        <label for="newPassword">New Password:</label>
        <input type="password" id="newPassword" name="newPassword" required>
        
        <input type="submit" name="changePassword" value="Change Password">
    </form>

    <!-- Button to return to the homepage -->
    <button onclick="window.location.href='index.html';">Return to Home</button>
</body>
</html>
