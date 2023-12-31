<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Taifa_health"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect value of input field and escape potentially dangerous characters
    $name = $conn->real_escape_string($_POST['name']);
    $mobile = $conn->real_escape_string($_POST['mobile']);

    // Your secret encryption key
    $encryptionKey = 'a3b8ebf8f3b4e3d572be03b8d2c2752aeed023d5f34f9bc0b8d6c1f3e8b54913';

    // Prepare an insert statement with AES_ENCRYPT for the Patient table
    $sql = "INSERT INTO Patient (PatientName, MobileNumber) VALUES (?, AES_ENCRYPT(?, UNHEX(SHA2(?,512))))";

    // Prepare and bind for the Patient table
    if ($stmt = $conn->prepare($sql)) {
        // Bind the name and mobile (with the encryption key) to the prepared statement
        $stmt->bind_param("sss", $name, $mobile, $encryptionKey);

        // Attempt to execute the prepared statement for Patient insertion
        if ($stmt->execute()) {
            // Get the last inserted ID for the Patient
            $patientId = $conn->insert_id;

            // Generate a random ticket number between 100 and 200 with "A" prefix
            $max = 200;
            $ticketNumber = mt_rand(100, $max);
            $ticketNumber = "A" . $ticketNumber;

            // Get the current date
            $issueDate = date('Y-m-d');

            // Prepare the SQL statement to insert the ticket data into the Ticket table
            $ticketSql = "INSERT INTO Ticket (PatientID, IssueDate, TicketNumber) VALUES (?, ?, ?)";

            if ($ticketStmt = $conn->prepare($ticketSql)) {
                // Bind the patient ID, issue date, and ticket number to the prepared statement
                $ticketStmt->bind_param("iss", $patientId, $issueDate, $ticketNumber);

                // Execute the statement and check for success
                if ($ticketStmt->execute()) {
                    // Redirect to the ticket receipt page with the generated ticket number
                    header("Location: ticket.php?ticketNumber=$ticketNumber");
                    exit();
                } else {
                    echo "Error: " . $ticketStmt->error;
                }
                // Close the statement for Ticket insertion
                $ticketStmt->close();
            } else {
                echo "Error preparing ticket statement: " . $conn->error;
            }
        } else {
            // Display an error message if the Patient query did not execute
            echo "Error: " . $stmt->error;
        }
        // Close the statement for Patient insertion
        $stmt->close();
    } else {
        // Display an error message if the Patient statement couldn't be prepared
        echo "Error: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>
