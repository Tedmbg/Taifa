<?php
session_start();

// Check if the user is logged in and is a nurse.
// if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Nurse') {
//     header("Location: nurse.html");
//     exit();
// }

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Taifa_health";
// $encryption_key = '3b1e8caf7f20235b0d2f0e69c3b07f5d6a7fefe82e3a17366b8b8c74deb35f44';
$encryption_key = "39ed10598bc90576637315fc891a7e2e745f797b6f868e124636e1745c709765";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Encrypt and insert patient data
$sql = "INSERT INTO Patients (FirstName, LastName, DateOfBirth, Gender, Address, PhoneNumber, Email) VALUES (AES_ENCRYPT(?, UNHEX(?)), AES_ENCRYPT(?, UNHEX(?)), AES_ENCRYPT(?, UNHEX(?)), AES_ENCRYPT(?, UNHEX(?)), AES_ENCRYPT(?, UNHEX(?)), AES_ENCRYPT(?, UNHEX(?)), AES_ENCRYPT(?, UNHEX(?)))";
$stmt = $conn->prepare($sql);

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$dateOfBirth = $_POST['dateOfBirth'];
$gender = $_POST['gender'];
$address = $_POST['address'];
$phoneNumber = $_POST['phoneNumber'];
$email = $_POST['email'];

$stmt->bind_param("ssssssssssssss", 
    $firstName, $encryption_key,
    $lastName, $encryption_key,
    $dateOfBirth, $encryption_key,
    $gender, $encryption_key,
    $address, $encryption_key,
    $phoneNumber, $encryption_key,
    $email, $encryption_key
);

if (!$stmt->execute()) {
    error_log("Execute error: " . $stmt->error);
    // Handle error
}

$patientID = $stmt->insert_id;
$stmt->close();

// Insert into EmergencyContacts table
$sql = "INSERT INTO EmergencyContacts (PatientID, Name, Relationship, PhoneNumber) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

$emergencyName = $_POST['emergencyName'];
$relationship = $_POST['relationship'];
$emergencyPhone = $_POST['emergencyPhone'];

$stmt->bind_param("isss", $patientID, $emergencyName, $relationship, $emergencyPhone);

if (!$stmt->execute()) {
    error_log("Execute error: " . $stmt->error);
    // Handle error
}
$stmt->close();

// Encrypt and insert into Consultations table
$sql = "INSERT INTO Consultations (PatientID, ConsultationDate, Reason) VALUES (?, CURDATE(), AES_ENCRYPT(?, UNHEX(?)))";
$stmt = $conn->prepare($sql);

$reasonForConsultation = $_POST['reasonForConsultation'];

$stmt->bind_param("iss", $patientID, $reasonForConsultation, $encryption_key);

if (!$stmt->execute()) {
    error_log("Execute error: " . $stmt->error);
    // Handle error
}
$stmt->close();

$conn->close();

// Redirect to index.html
header("Location: index.html");
exit();
?>
