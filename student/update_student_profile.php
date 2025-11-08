<?php
session_start();
include("../db_connection.php");

header('Content-Type: text/plain');

if (!isset($_SESSION['student_id'])) {
    echo "error: not_logged_in";
    exit();
}

$student_id = $_SESSION['student_id'];

$full_name   = trim($_POST['full_name'] ?? '');
$email       = trim($_POST['email'] ?? '');
$phone       = trim($_POST['phone'] ?? '');
$address     = trim($_POST['address'] ?? '');
$dob         = trim($_POST['dob'] ?? '');
$blood_group = trim($_POST['blood_group'] ?? '');

if (empty($full_name) || empty($email) || empty($phone)) {
    echo "error: missing_fields";
    exit();
}

$query = "UPDATE students 
          SET full_name = ?, email = ?, phone = ?, address = ?, dob = ?, blood_group = ?
          WHERE student_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ssssssi", $full_name, $email, $phone, $address, $dob, $blood_group, $student_id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error: db_failed";
}

$stmt->close();
$conn->close();
?>
