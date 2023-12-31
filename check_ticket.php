<?php
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
    // Escape user inputs for security
    $name = $conn->real_escape_string($_POST['name']);
    $ticketno = $conn->real_escape_string($_POST['ticketno']);

    // First, try to find the patient by name
    if (!empty($name)) {
        $query = "SELECT PatientID FROM Patient WHERE PatientName = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // If a patient with the given name exists, redirect to nurseform.html
            header("Location: nurseform.html");
            exit();
        }
    }

    // Then, try to find the ticket by TicketNumber
    if (!empty($ticketno)) {
        $query = "SELECT TicketID FROM Ticket WHERE TicketNumber = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $ticketno); // Use 's' to denote string type
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // If a ticket with the given number exists, redirect to nurseform.html
            header("Location: nurseform.html");
            exit();
        }
    }

    // If neither a matching name nor ticket number was found, handle the error appropriately
    echo "No matching records found.";
}

$conn->close();
?>
