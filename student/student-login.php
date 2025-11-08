<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../db_connection.php"); // one level up from /student/

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_id = trim($_POST['student_id'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validate input
    if (empty($student_id) || empty($password)) {
        echo "<script>alert('All fields are required'); window.location.href='student-login.php';</script>";
        exit();
    }

    // ✅ Correct query: student_login JOIN students
    $stmt = $conn->prepare("
        SELECT sl.student_id, sl.password_hash, s.full_name
        FROM student_login sl
        JOIN students s ON s.student_id = sl.student_id
        WHERE s.registration_number = ?
    ");

    if (!$stmt) {
        die('Database error: ' . $conn->error);
    }

    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        // For now, plain text comparison (later switch to password_verify)
        if ($password === $row['password_hash']) {
            $_SESSION['loggedin'] = true;
            $_SESSION['student_id'] = $row['student_id'];
            $_SESSION['student_name'] = $row['full_name'];

            if (!empty($_POST['remember-me'])) {
                setcookie("student_id", $student_id, time() + (86400 * 30), "/");
            }

            header("Location: student-dashboard.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password'); window.location.href='student-login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Student not found'); window.location.href='student-login.php';</script>";
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login | Hostel Management</title>

    <!-- Tailwind & Icons -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Inline Styles -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                        url('/hostel_management/assets/images/student-bg.png') no-repeat center center fixed;
            background-size: cover;
        }
        .login-card {
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            background-color: rgba(255, 255, 255, 0.9);
        }
        .btn-primary {
            background-color: #3b82f6;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
            font-weight: 500;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
        }
        .btn-secondary {
            background-color: #10b981;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
            font-weight: 500;
            width: 100%;
        }
        .btn-secondary:hover {
            background-color: #059669;
            transform: translateY(-1px);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center">
    <div class="login-card w-full max-w-md p-8 space-y-6 bg-white rounded-xl">
        <div class="text-center">
            <div class="mx-auto w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mb-4">
                <i class="fas fa-user-graduate text-green-600 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Student Login</h1>
            <p class="mt-2 text-gray-600">Enter your credentials to access the dashboard</p>
        </div>

        <!-- ✅ Login Form -->
        <form class="space-y-4" action="" method="POST" autocomplete="off">
            <div>
                <label for="student-id" class="block text-sm font-medium text-gray-700 mb-1">Student ID</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-id-card text-gray-400"></i>
                    </div>
                    <input 
                        type="text" 
                        id="student-id" 
                        name="student_id"
                        value="<?php echo isset($_COOKIE['student_id']) ? htmlspecialchars($_COOKIE['student_id']) : ''; ?>"
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" 
                        placeholder="Enter your student ID" 
                        required
                    >
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" 
                        placeholder="Enter your password" 
                        required
                    >
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                        <?php echo isset($_COOKIE['student_id']) ? 'checked' : ''; ?>>
                    <label for="remember-me" class="ml-2 block text-sm text-gray-700">Remember me</label>
                </div>
                <div class="text-sm">
                    <a href="student-forgot-password.php" class="font-medium text-green-600 hover:text-green-500">Forgot password?</a>
                </div>
            </div>

            <div class="space-y-3">
                <button type="submit" class="btn-primary flex justify-center items-center">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </button>
                <a href="student-register.php" class="btn-secondary flex justify-center items-center">
                    <i class="fas fa-user-plus mr-2"></i> Register
                </a>
            </div>  
        </form>

        <div class="text-center text-sm text-gray-500">
            <p>Not a student? <a href="../index.php" class="text-green-600 hover:text-green-500">Go back</a></p>
        </div>
    </div>
</body>
</html>
