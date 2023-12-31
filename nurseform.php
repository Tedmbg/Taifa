<?php
session_start();

// Check if the user is logged in and is a doctor.
if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Doctor') {
    header("Location: login.html");
    exit();
}

// Database credentials.
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Taifa_health";

// Establish database connection.
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection.
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the ticket ID is set and valid.
if (!isset($_GET['ticketID']) || empty($_GET['ticketID'])) {
    echo "No ticket selected or invalid ticket ID.";
    exit();
}

$ticketID = $_GET['ticketID'];

// Prepare the SQL statement to fetch patient data based on ticket ID.
$stmt = $conn->prepare("SELECT p.FirstName, p.LastName, p.DateOfBirth, p.Gender, p.Address, p.PhoneNumber, p.Email FROM Patients p INNER JOIN Tickets t ON p.PatientID = t.PatientID WHERE t.TicketID = ?");
$stmt->bind_param("i", $ticketID);

// Execute the prepared statement.
$stmt->execute();

// Get the result.
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Patient not found for the given ticket.";
    exit();
}

// Fetch the data.
$patientData = $result->fetch_assoc();

// Close the statement and connection.
$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Medical Form</title>
    <link rel="stylesheet" href="./nurseform.css">
</head>
<body>
<div class="form-container"><img src="./images/Final.svg" alt="topBar"></div>
<div class="overlay-form">
    <!-- ... rest of your HTML form ... -->

    <!-- Pre-fill the input fields with PHP echo statements -->
    <input type="text" id="fname" name="firstName" value="<?php echo htmlspecialchars($patientData['FirstName']); ?>" style="width: 77%;"><br><br>
    <!-- Repeat for other fields -->

    <!-- Rest of your HTML form -->

    <!-- Done button -->
    <button type="button" onclick="window.location.href='index.html';">Done</button>
</div>

<script type="text/javascript">
    // JavaScript code can be added here if needed
</script>

</body>
</html>
