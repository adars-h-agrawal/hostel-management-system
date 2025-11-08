<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("../db_connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reg_no = trim($_POST['registrationNumber']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirmPassword']);
    $answer = trim($_POST['securityQ']);

    if (empty($reg_no) || empty($password) || empty($confirm_password) || empty($answer)) {
        $error = "All fields are required!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $stmt = $conn->prepare("SELECT student_id FROM student WHERE student_id = ?");
        $stmt->bind_param("s", $reg_no);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $error = "Student not found. Please enter a valid registration number.";
        } else {
            $stmt = $conn->prepare("SELECT student_id FROM student_login WHERE student_id = ?");
            $stmt->bind_param("s", $reg_no);
            $stmt->execute();
            $login_result = $stmt->get_result();

            if ($login_result->num_rows > 0) {
                $error = "This student already has login credentials. Please log in.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("INSERT INTO student_login (student_id, password_hash, answer) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $reg_no, $hashed_password, $answer);

                if ($stmt->execute()) {
                    $success = "Registration successful! You can now log in.";
                } else {
                    $error = "Error during registration: " . $stmt->error;
                }
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
    <title>Student Registration | Hostel Management</title>
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
                <i class="fas fa-user-plus text-blue-600 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Student Registration</h1>
            <p class="text-sm text-gray-600">Create your hostel account</p>
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Security Question</label>
                <input type="text" name="securityQ" class="w-full border rounded px-3 py-2" placeholder="What was the name of your first pet?" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="confirmPassword" class="w-full border rounded px-3 py-2" required>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded transition">
                Register Account
            </button>

            <p class="text-center text-sm text-gray-500 mt-2">
                Already have an account?
                <a href="student-login.php" class="text-blue-600 hover:underline">Login here</a>
            </p>
        </form>
    </div>
</body>
</html>
