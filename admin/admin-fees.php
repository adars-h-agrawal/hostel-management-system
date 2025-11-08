require_once('includes/auth_check.php');

<?php
require_once('../db_connection.php');
header('Content-Type: application/json');

try {
    if (empty($_POST['student_id']) || empty($_POST['reminder'])) {
        throw new Exception("Student ID and reminder message are required");
    }

    $student_id = intval($_POST['student_id']);
    $reminder = trim($_POST['reminder']);

    // Insert reminder into reminders table
    $stmt = $conn->prepare("INSERT INTO reminders (student_id, type, message) VALUES (?, 'Fee Reminder', ?)");
    if (!$stmt) {
        throw new Exception("Database prepare error: " . $conn->error);
    }

    $stmt->bind_param("is", $student_id, $reminder);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'message' => "Reminder added successfully!"
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt) && $stmt instanceof mysqli_stmt) $stmt->close();
    $conn->close();
}
?>
