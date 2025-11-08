<?php
session_start();

// // 🧠 Auto-redirect logged-in users to their dashboard
// if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
//     if (isset($_SESSION['admin_id'])) {
//         header("Location: ./admin/admin-dashboard.php");
//         exit();
//     } elseif (isset($_SESSION['student_id'])) {
//         header("Location: ./student/student-dashboard.php");
//         exit();
//     }
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Management System | MAHE</title>

    <!-- Tailwind & Icons -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Local CSS & JS -->
    <link rel="stylesheet" href="/hostel_management/assets/css/style.css">
    <script src="/hostel_management/assets/js/script.js" defer></script>

    <!-- Background Styling -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                        url('/hostel_management/assets/images/index-bg.png')
                        no-repeat center center fixed;
            background-size: cover;
        }

        .login-box {
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(8px);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center">
    <div class="login-box w-full max-w-md p-9 space-y-6 rounded-2xl">
        <!-- Header -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">MAHE Hostel Management</h1>
            <p class="mt-2 text-gray-600 text-sm">Select your login type to continue:</p>
        </div>

        <!-- Login Buttons -->
        <div class="space-y-4">
            <a href="./admin/admin-login.php"
               class="block w-full px-4 py-3 text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition duration-300 ease-in-out shadow">
                <i class="fas fa-user-shield mr-2"></i> Login as Admin
            </a>

            <a href="./student/student-login.php"
               class="block w-full px-4 py-3 text-center text-white bg-teal-600 rounded-lg hover:bg-teal-700 transition duration-300 ease-in-out shadow">
                <i class="fas fa-user-graduate mr-2"></i> Login as Student
            </a>
        </div>

        <!-- Footer -->
        <div class="text-center text-sm text-gray-500 pt-2">
            <p>Need help? <a href="mailto:support@mahehostel.com" class="text-blue-600 hover:underline">support@mahehostel.com</a></p>
        </div>
    </div>
</body>
</html>
