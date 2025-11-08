<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("../../db_connection.php");

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['student_id'])) throw new Exception("User not logged in.");

    $student_id = $_SESSION['student_id'];
    $semester = $_POST['semester'] ?? '';
    $method = $_POST['payment_method'] ?? '';

    if (empty($semester) || empty($method)) throw new Exception("Semester and payment method required.");

    $stmt = $conn->prepare("SELECT fee_id, amount, status FROM fees WHERE student_id = ? AND semester = ?");
    $stmt->bind_param("is", $student_id, $semester);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) throw new Exception("No unpaid fee found for this semester.");

    $fee = $res->fetch_assoc();
    if ($fee['status'] === 'Paid') throw new Exception("This semester's fee is already paid.");

    // Mark as paid
    $update = $conn->prepare("UPDATE fees SET status='Paid', payment_date=CURDATE() WHERE fee_id=?");
    $update->bind_param("i", $fee['fee_id']);
    $update->execute();

    echo json_encode(['success'=>true, 'message'=>"Payment successful for {$semester}!"]);

} catch (Exception $e) {
    echo json_encode(['success'=>false, 'error'=>$e->getMessage()]);
} finally {
    $conn->close();
}
?>
