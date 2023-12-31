<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Uncomment the following lines if session checks are implemented.
/*
if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Doctor') {
    header("Location: loginDoctor.html");
    exit();
}
*/

$consultations = [];
$feedback = '';
$encryption_key = '339ed10598bc90576637315fc891a7e2e745f797b6f868e124636e1745c709765';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Taifa_health";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['patientName'])) {
    $patientName = trim($_POST['patientName']);

    // Encrypt patientName
    $stmt = $conn->prepare("SELECT AES_ENCRYPT(?, UNHEX(?)) AS EncryptedPatientName");
    $stmt->bind_param("ss", $patientName, $encryption_key);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $encryptedPatientName = $row['EncryptedPatientName'];
    echo $encryptedPatientName;

    // Prepare the SQL statement for search
    $stmt = $conn->prepare("
        SELECT 
            AES_DECRYPT(p.FirstName, UNHEX(?)) AS DecryptedFirstName,
            AES_DECRYPT(p.LastName, UNHEX(?)) AS DecryptedLastName,
            AES_DECRYPT(c.Reason, UNHEX(?)) AS DecryptedReason
        FROM Patients p 
        INNER JOIN Consultations c ON p.PatientID = c.PatientID
        WHERE p.FirstName = ?
        OR p.LastName = ?
    ");

    // Bind the parameters to the SQL query
    $stmt->bind_param("sssss", 
        $encryption_key, $encryption_key, $encryption_key, 
        $encryptedPatientName, $encryptedPatientName
    );

    // Execute the query and process the results
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $consultations[] = $row;
        }
    } else {
        $feedback = "No consultations found for the patient name provided.";
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Patient</title>
    <link rel="stylesheet" href="./select_patient.css">
</head>
<body>
    <div class="container">
        <div class="patient-section">
            <h1>Select Patient for Consultation</h1>
            <form class="form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <label for="patientName">Enter Patient Name:</label>
                <input type="text" id="patientName" name="patientName" required>
                <input class="search" type="submit" value="Search">
            </form>
            <button class="btn" onclick="window.location.href='index.html';">Return to Home</button>
        </div>
    </div>

    <?php
    if (!empty($feedback)) {
        echo "<p class='feedback'>$feedback</p>";
    }

    if (!empty($consultations)) {
        echo "<h2>Consultation Details</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Patient Name</th><th>Reason for Consultation</th></tr>";
        foreach ($consultations as $consultation) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($consultation['DecryptedFirstName']) . " " . htmlspecialchars($consultation['DecryptedLastName']) . "</td>";
            echo "<td>" . htmlspecialchars($consultation['DecryptedReason']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        phpinfo();
    }
    ?>
</body>
</html>
