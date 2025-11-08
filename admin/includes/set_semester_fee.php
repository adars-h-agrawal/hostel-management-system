<?php
session_start();
require_once('../../db_connection.php');
header('Content-Type: text/plain');

// Security check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    die("Unauthorized access.");
}

if (empty($_POST['room_type']) || empty($_POST['amount'])) {
    http_response_code(400);
    die("Room type and amount are required.");
}

$room_type = trim($_POST['room_type']);
$amount = floatval($_POST['amount']);

try {
    $sql = "INSERT INTO fee_structure (room_type, amount)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE amount = VALUES(amount)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sd", $room_type, $amount);

    if ($stmt->execute()) {
        echo "Fee structure updated successfully.";
    } else {
        echo "Error updating fee structure.";
    }

} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
} finally {
    $conn->close();
}
?>
