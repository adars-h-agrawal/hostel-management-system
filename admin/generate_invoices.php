<?php
session_start();
require_once('../db_connection.php');
header('Content-Type: text/plain');

// Security check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    die("Unauthorized access.");
}

try {
    // Get all students
    $students = $conn->query("SELECT student_id, full_name, room_type FROM students");

    // Get fee structure
    $feeStructure = [];
    $fees = $conn->query("SELECT * FROM fee_structure");
    while ($row = $fees->fetch_assoc()) {
        $feeStructure[$row['room_type']] = $row['amount'];
    }

    $count = 0;

    // Generate invoices
    while ($student = $students->fetch_assoc()) {
        $amount = $feeStructure[$student['room_type']] ?? 0;
        if ($amount > 0) {
            $due_date = date('Y-m-d', strtotime('+30 days'));
            $sql = "INSERT INTO payments (student_id, amount, due_date, status) 
                    VALUES (?, ?, ?, 'Pending')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ids", $student['student_id'], $amount, $due_date);
            $stmt->execute();
            $count++;
        }
    }

    echo "$count invoices generated successfully.";

} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
} finally {
    $conn->close();
}
?>
