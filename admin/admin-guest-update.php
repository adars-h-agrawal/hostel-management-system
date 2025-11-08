<?php
require_once('../db_connection.php');
header('Content-Type: application/json');

try {
    if (empty($_POST['student_id']) || empty($_POST['guest_id']) || empty($_POST['status'])) {
        throw new Exception("All fields are required.");
    }

    $student_id = intval($_POST['student_id']);
    $guest_id = intval($_POST['guest_id']);
    $status = trim($_POST['status']);

    $valid_statuses = ['Pending', 'Approved', 'Rejected'];
    if (!in_array($status, $valid_statuses)) {
        throw new Exception("Invalid status value.");
    }

    // Verify guest record
    $check = $conn->prepare("SELECT guest_id FROM guest_log WHERE guest_id = ? AND student_id = ?");
    $check->bind_param("ii", $guest_id, $student_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        throw new Exception("Guest record not found for the given student.");
    }
    $check->close();

    // Update guest status
    $update = $conn->prepare("UPDATE guest_log SET status = ? WHERE guest_id = ? AND student_id = ?");
    $update->bind_param("sii", $status, $guest_id, $student_id);

    if ($update->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Guest status updated successfully.'
        ]);
    } else {
        throw new Exception("Database error: " . $update->error);
    }

    $update->close();
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>
