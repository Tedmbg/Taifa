<?php
// Database credentials
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

// Check if the ticket number is passed via GET
if (isset($_GET['ticketNumber'])) {
    $randomTicketNumber = htmlspecialchars($_GET['ticketNumber']);
    
    // Prepare a select statement to retrieve the issue date and time for the ticket
    $sql = "SELECT IssueDate FROM Ticket WHERE TicketNumber = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("s", $randomTicketNumber);
        
        // Execute the statement
        if ($stmt->execute()) {
            // Bind result variables
            $stmt->bind_result($issueDate);
            
            // Fetch value
            if ($stmt->fetch()) {
                // Convert the date to a datetime string if you want to include time
                $issueDateTime = date('Y-m-d H:i:s', strtotime($issueDate));
            } else {
                echo "No ticket found with that number.";
                exit;
            }
        } else {
            echo "Error retrieving ticket: " . $stmt->error;
            exit;
        }
        
        // Close statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
        exit;
    }
    
    // Close connection
    $conn->close();
    
} else {
    echo "No ticket number provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Receipt</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="ticket-container">
        <h1>Taifa Health Ticket Receipt</h1>
        <p>Ticket Number: <?php echo $randomTicketNumber; ?></p>
        <p>Issue Date and Time: <?php echo $issueDateTime; ?></p>
        <button onclick="window.location.href='index.html';">Return Home</button>
    </div>
</body>
</html>
