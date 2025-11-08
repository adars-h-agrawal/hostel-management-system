<?php
include("../db_connection.php"); // path corrected for admin folder
$conn->begin_transaction();

try {
    // Required fields validation
    $required_fields = ['registration_number', 'full_name', 'email', 'phone', 'block', 'room_number', 'room_type'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("All fields are required");
        }
    }

    // Sanitize input
    $registration_number = trim($_POST['registration_number']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $block = trim($_POST['block']);
    $room_number = trim($_POST['room_number']);
    $room_type = trim($_POST['room_type']);

    // Default password for new student (you can later hash this)
    $default_password = 'student123';

    // Check if registration number already exists
    $checkQuery = "SELECT * FROM students WHERE registration_number = ?";
    $checkStmt = $conn->prepare($checkQuery);
    if (!$checkStmt) {
        throw new Exception("Database prepare error: " . $conn->error);
    }
    $checkStmt->bind_param("s", $registration_number);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        throw new Exception("Registration number already exists!");
    }

    // Insert new student into the students table
    $insertQuery = "INSERT INTO students 
        (registration_number, full_name, email, phone, password, room_number, block, room_type) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $insertStmt = $conn->prepare($insertQuery);
    if (!$insertStmt) {
        throw new Exception("Database prepare error: " . $conn->error);
    }

    $insertStmt->bind_param(
        "ssssssss",
        $registration_number,
        $full_name,
        $email,
        $phone,
        $default_password,
        $room_number,
        $block,
        $room_type
    );

    $insertStmt->execute();

    $conn->commit();

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => "Student added successfully!"
    ]);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($checkStmt) && $checkStmt instanceof mysqli_stmt) $checkStmt->close();
    if (isset($insertStmt) && $insertStmt instanceof mysqli_stmt) $insertStmt->close();
    $conn->close();
}
?>
