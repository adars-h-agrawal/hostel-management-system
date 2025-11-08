<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include("../db_connection.php"); // ✅ one level up

// Redirect if already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: admin-dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        echo "<script>alert('All fields are required'); window.location.href='admin-login.php';</script>";
        exit();
    }

    // Prepare and execute SQL
    $stmt = $conn->prepare("SELECT admin_id, username, password, name FROM admins WHERE username = ?");
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        // ✅ You can later replace with password_verify()
        if ($password === $row['password']) {
            // Create session
            $_SESSION['loggedin'] = true;
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['username'] = $row['name'] ?? $row['username'];

            // Remember me
            if (!empty($_POST['remember-me'])) {
                setcookie("admin_username", $username, time() + (86400 * 30), "/", "", false, true);
            } else {
                setcookie("admin_username", "", time() - 3600, "/");
            }

            header("Location: admin-dashboard.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password.'); window.location.href='admin-login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Admin not found.'); window.location.href='admin-login.php';</script>";
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
    <title>Admin Login | Hostel Management</title>

    <!-- TailwindCSS & Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Local CSS & JS -->
    <link rel="stylesheet" href="/hostel_management/assets/css/style.css">
    <script src="/hostel_management/assets/js/script.js" defer></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.55)),
                        url('/hostel_management/assets/images/admin-bg.png') no-repeat center center fixed;
            background-size: cover;
        }
        .login-card {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            background-color: rgba(255, 255, 255, 0.92);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center">
    <div class="login-card w-full max-w-md p-8 space-y-6 rounded-xl">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mb-4">
                <i class="fas fa-user-shield text-blue-600 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Admin Login</h1>
            <p class="mt-1 text-gray-600 text-sm">Access your dashboard securely</p>
        </div>

        <!-- Login Form -->
        <form class="space-y-5" action="" method="POST" autocomplete="off">
            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-user"></i>
                    </span>
                    <input 
                        type="text"
                        id="username"
                        name="username"
                        value="<?php echo isset($_COOKIE['admin_username']) ? htmlspecialchars($_COOKIE['admin_username']) : ''; ?>"
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter your username"
                        required
                    >
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input 
                        type="password"
                        id="password"
                        name="password"
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter your password"
                        required
                    >
                </div>
            </div>

            <!-- Remember & Forgot -->
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="remember-me" 
                        id="remember-me"
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded"
                        <?php echo isset($_COOKIE['admin_username']) ? 'checked' : ''; ?>
                    >
                    <span class="ml-2 text-gray-700">Remember me</span>
                </label>
                <a href="#" class="text-blue-600 hover:text-blue-700">Forgot password?</a>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit"
                class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
            >
                <i class="fas fa-sign-in-alt mr-1"></i> Login
            </button>
        </form>

        <!-- Footer -->
        <div class="text-center text-sm text-gray-500 mt-4">
            <p>Not an admin? <a href="../index.php" class="text-blue-600 hover:underline">Go back</a></p>
        </div>
    </div>
</body>
</html>
