<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("../../db_connection.php"); // ✅ correct now (from student/backend → root)

if (!isset($_SESSION['student_id'])) {
    die("<script>alert('Error: Session expired. Please log in again.'); window.location.href='../student-login.php';</script>");
}

if (empty($_POST['complaint_type']) || empty($_POST['description'])) {
    die("<script>alert('All fields are required!'); window.history.back();</script>");
}

$student_id = $_SESSION['student_id'];
$type = trim($_POST['complaint_type']);
$desc = trim($_POST['description']);
$resolution = "Pending review by admin"; // default admin note

$stmt = $conn->prepare("INSERT INTO complaints (student_id, complaint_type, description, resolution) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $student_id, $type, $desc, $resolution);

if ($stmt->execute()) {
    echo "<script>alert('Complaint submitted successfully!'); window.location.href='../student-complaints.php';</script>";
} else {
    echo "<script>alert('Database error: " . addslashes($stmt->error) . "'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
