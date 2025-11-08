<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("../db_connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reg_no = trim($_POST['registrationNumber']);
    $answer = trim($_POST['securityQ']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirmPassword']);

    if (empty($reg_no) || empty($password) || empty($confirm_password) || empty($answer)) {
        $error = "All fields are required!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $stmt = $conn->prepare("SELECT answer FROM student_login WHERE student_id = ?");
        $stmt->bind_param("s", $reg_no);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $error = "No login found for the provided student ID!";
        } else {
            $row = $result->fetch_assoc();
            if (strcasecmp($row['answer'], $answer) !== 0) {
                $error = "Security answer does not match!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $update = $conn->prepare("UPDATE student_login SET password_hash = ? WHERE student_id = ?");
                $update->bind_param("ss", $hashed_password, $reg_no);

                if ($update->execute()) {
                    $success = "Password reset successful! You can now log in.";
                } else {
                    $error = "Error updating password: " . $conn->error;
                }
                $update->close();
            }
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Hostel Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                        url('/hostel_management/assets/images/student-bg.png') no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white bg-opacity-95 rounded-xl shadow-xl p-6 space-y-4">
        <div class="text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-blue-100 flex items-center justify-center mb-3">
                <i class="fas fa-unlock-alt text-blue-600 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Reset Password</h1>
            <p class="text-sm text-gray-600">Recover your account securely</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded text-center"><?= htmlspecialchars($error) ?></div>
        <?php elseif (isset($success)): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded text-center"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
                <input type="text" name="registrationNumber" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Security Answer</label>
                <input type="text" name="securityQ" class="w-full border rounded px-3 py-2" placeholder="Answer your security question" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="confirmPassword" class="w-full border rounded px-3 py-2" required>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded transition">
                Reset Password
            </button>

            <p class="text-center text-sm text-gray-500 mt-2">
                Remembered your password?
                <a href="student-login.php" class="text-blue-600 hover:underline">Login here</a>
            </p>
        </form>
    </div>
</body>
</html>
