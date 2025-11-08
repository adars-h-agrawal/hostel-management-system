<?php
require_once('../db_connection.php');
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo json_encode(['success' => false, 'error' => 'Invalid ID']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM attendance WHERE attendance_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Attendance deleted successfully']);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
