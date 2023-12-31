<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Taifa_health";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputUsername = $conn->real_escape_string($_POST['username']);
    $inputPassword = $conn->real_escape_string($_POST['password']);
    
    // Hash the entered password with SHA-2 (SHA-256) for comparison
    $hashedInputPassword = hash('sha256', $inputPassword);

    $sql = "SELECT UserID, Username, Password, Role FROM User WHERE Username = ?";
    
    // Prepare and bind
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $inputUsername);
        
        // Execute the statement
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Compare the entered password hash with the stored hash
                if ($hashedInputPassword === $user['Password']) {
                    // Check if the role is Nurse
                    if ($user['Role'] === 'Nurse') {
                        // Authentication successful, redirect to ticket.html
                        header("Location: ticket.html");
                        exit();
                    } else {
                        // User is not a nurse
                        echo "Access denied. You are not authorized as a Nurse.";
                    }
                } else {
                    // Password is not correct
                    echo "Invalid password for username: {$inputUsername}.";
                }
            } else {
                // No user found
                echo "No user found with username: {$inputUsername}.";
            }
        } else {
            echo "Error executing query: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

$conn->close();
?>
