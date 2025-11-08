<?php
require_once('../db_connection.php');
header('Content-Type: application/json');

try {
    if (empty($_POST['request_id']) || empty($_POST['status'])) {
        throw new Exception("Request ID and Status are required.");
    }

    $request_id = intval($_POST['request_id']);
    $status = trim($_POST['status']);
    $valid_statuses = ['Pending', 'In Progress', 'Completed', 'Rejected'];

    if (!in_array($status, $valid_statuses)) {
        throw new Exception("Invalid status value.");
    }

    // Verify request exists
    $check = $conn->prepare("SELECT request_id FROM maintenance WHERE request_id = ?");
    $check->bind_param("i", $request_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        throw new Exception("Maintenance request not found.");
    }
    $check->close();

    // Update status
    $update = $conn->prepare("UPDATE maintenance SET status = ? WHERE request_id = ?");
    $update->bind_param("si", $status, $request_id);

    if ($update->execute()) {
        echo json_encode([
            'success' => true,
            'message' => "Request #$request_id status updated to '$status'"
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
