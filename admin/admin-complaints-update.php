<?php
require_once('../db_connection.php');
header('Content-Type: application/json');

try {
    if (empty($_POST['complaint_id']) || empty($_POST['status'])) {
        throw new Exception("Complaint ID and Status are required.");
    }

    $complaint_id = intval($_POST['complaint_id']);
    $status = trim($_POST['status']);
    $resolution = trim($_POST['resolution'] ?? '');
    $valid_statuses = ['Pending', 'In Progress', 'Resolved'];

    if (!in_array($status, $valid_statuses)) {
        throw new Exception("Invalid status value.");
    }

    // Check if complaint exists
    $check = $conn->prepare("SELECT complaint_id FROM complaints WHERE complaint_id = ?");
    $check->bind_param("i", $complaint_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        throw new Exception("Complaint not found.");
    }
    $check->close();

    // Update complaint status
    $update = $conn->prepare("UPDATE complaints SET status = ?, description = CONCAT(description, '\n\nResolution: ', ?) WHERE complaint_id = ?");
    $update->bind_param("ssi", $status, $resolution, $complaint_id);

    if ($update->execute()) {
        echo json_encode([
            'success' => true,
            'message' => "Complaint #$complaint_id marked as '$status'."
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
