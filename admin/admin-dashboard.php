<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: admin-login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Admin Dashboard | Hostel Management</title>

    <!-- Tailwind & Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Local Assets -->
    <link rel="stylesheet" href="/hostel_management/assets/css/style.css">
    <script src="/hostel_management/assets/js/script.js" defer></script>

    <style>
        :root {
            --sidebar-width: 270px;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc; /* fallback color */
            overflow-x: hidden;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            position: fixed;
            top: 0;
            left: 0;
            min-height: 100vh;
            box-shadow: 5px 0 20px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(12px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }


        .content-wrapper {
            margin-left: var(--sidebar-width);
            background: url('/hostel_management/assets/images/admin-dashboard.png') no-repeat center center fixed;
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }


        .sidebar a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.9rem;
            border-radius: 0.5rem;
            color: #fff;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .sidebar a:hover {
            background: rgba(255,255,255,0.08);
            transform: translateX(5px);
        }

        .sidebar a.active {
            background-color: #2563eb;
        }


        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .content-wrapper {
                margin-left: 0;
            }
        }

        /* Header */
        header.app-header {
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 8px rgba(15,23,42,0.1);
            z-index: 10;
        }

        /* Main content */
        main.main-area {
            flex: 1;
            padding: 2rem;
            background: transparent; /* no overlay */
        }

        /* Dashboard cards */
        .card {
            border-radius: 0.75rem;
            padding: 1.5rem;
            color: white;
            display: flex;
            align-items: start;
            gap: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }

        /* Responsive grid */
        .dashboard-grid {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        }
    </style>
</head>
<body class="antialiased">
    <!-- Sidebar -->
    <aside class="sidebar text-white hidden md:block">
        <div class="text-center mb-6">
            <i class="fas fa-user-shield text-5xl text-blue-400 mb-3"></i>
            <h2 class="text-lg font-semibold">Admin Panel</h2>
            <p class="text-xs text-gray-300"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
        </div>
        <nav>
            <ul class="space-y-2">
                <li><a href="admin-dashboard.php" class="active"><i class="fas fa-home w-5 text-center"></i><span>Dashboard Home</span></a></li>
                <li><a href="admin-students.php"><i class="fas fa-users w-5 text-center"></i><span>Manage Students</span></a></li>
                <li><a href="admin-attendance.php"><i class="fas fa-calendar-check w-5 text-center"></i><span>Upload Attendance</span></a></li>
                <li><a href="admin-mess.php"><i class="fas fa-utensils w-5 text-center"></i><span>Mess Details</span></a></li>
                <li><a href="admin-notices.php"><i class="fas fa-bullhorn w-5 text-center"></i><span>Post Notices</span></a></li>
                <li><a href="admin-payments.php"><i class="fas fa-money-check-alt w-5 text-center"></i><span>Fee Payments</span></a></li>
                <li><a href="admin-guests.php"><i class="fas fa-user-friends w-5 text-center"></i><span>Guest Logs</span></a></li>
                <li><a href="admin-maintenance.php"><i class="fas fa-tools w-5 text-center"></i><span>Maintenance</span></a></li>
                <li><a href="admin-complaints.php"><i class="fas fa-exclamation-circle w-5 text-center"></i><span>Complaints</span></a></li>
                <li><a href="admin-llm.php"><i class="fas fa-robot w-5 text-center"></i><span>AI Chat Bot</span></a></li>
                <li class="mt-6">
                    <a href="logout.php" class="flex items-center space-x-3 p-3 hover:bg-gray-700 mt-8">
                        <i class="fas fa-sign-out-alt text-md w-6 text-center"></i>
                        <span class="text-sm">Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main content -->
    <div class="content-wrapper">
        <header class="app-header p-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button id="mobileToggle" class="md:hidden p-2 rounded bg-gray-200 text-gray-700">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="text-2xl font-bold text-gray-800" style="font-family: 'Montserrat', sans-serif;">MAHE Hostel Management System</h1>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gray-400 flex items-center justify-center text-white">
                    <i class="fas fa-user"></i>
                </div>
                <span class="hidden sm:inline text-sm text-gray-700"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
        </header>

        <main class="main-area">
            <h2 class="text-3xl font-bold mb-6 text-gray-900">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> </h2>

            <div class="dashboard-grid">
                <a href="admin-students.php" class="card bg-blue-600 hover:bg-blue-700"><i class="fas fa-users text-3xl"></i><div><h3 class="text-lg font-semibold">Students</h3><p class="text-sm text-blue-100">Manage student data</p></div></a>
                <a href="admin-attendance.php" class="card bg-green-600 hover:bg-green-700"><i class="fas fa-calendar-check text-3xl"></i><div><h3 class="text-lg font-semibold">Attendance</h3><p class="text-sm text-green-100">Upload and manage attendance</p></div></a>
                <a href="admin-payments.php" class="card bg-yellow-500 hover:bg-yellow-600"><i class="fas fa-wallet text-3xl"></i><div><h3 class="text-lg font-semibold">Payments</h3><p class="text-sm text-yellow-100">View fee records</p></div></a>
                <a href="admin-mess.php" class="card bg-orange-500 hover:bg-orange-600"><i class="fas fa-utensils text-3xl"></i><div><h3 class="text-lg font-semibold">Mess Details</h3><p class="text-sm text-orange-100">Manage mess menu & billing</p></div></a>
                <a href="admin-maintenance.php" class="card bg-purple-600 hover:bg-purple-700"><i class="fas fa-tools text-3xl"></i><div><h3 class="text-lg font-semibold">Maintenance</h3><p class="text-sm text-purple-100">Handle maintenance requests</p></div></a>
                <a href="admin-complaints.php" class="card bg-red-600 hover:bg-red-700"><i class="fas fa-exclamation-circle text-3xl"></i><div><h3 class="text-lg font-semibold">Complaints</h3><p class="text-sm text-red-100">View and resolve issues</p></div></a>
                <a href="admin-notices.php" class="card bg-indigo-600 hover:bg-indigo-700"><i class="fas fa-bullhorn text-3xl"></i><div><h3 class="text-lg font-semibold">Notices</h3><p class="text-sm text-indigo-100">Post hostel notices</p></div></a>
            </div>
        </main>
    </div>

    <script>
        const mobileToggle = document.getElementById('mobileToggle');
        const sidebar = document.querySelector('.sidebar');
        mobileToggle && mobileToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
    </script>
</body>
</html>
