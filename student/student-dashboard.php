<?php
session_start();
require_once('../db_connection.php');

// 🔒 Security check — only logged-in students can access
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: student-login.php");
    exit();
}
$studentName = $_SESSION['student_name'] ?? 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | Hostel Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #1e3a8a 0%, #172554 100%);
            color: #e0f2fe;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .sidebar img {
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .sidebar p {
            font-size: 0.875rem;
            color: #bae6fd;
        }

        .dashboard-card {
            background: #f1f5f9;
            border-radius: 0.75rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 1.25rem;
            transition: all 0.3s ease;
            height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            background-color: #f8fafc;
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        }

        .dashboard-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 3rem;
            width: 10rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            z-index: 20;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }
    </style>
</head>
<body class="antialiased flex bg-gray-50">

    <!-- Sidebar -->
    <aside class="sidebar hidden md:flex flex-col">
        <img src="/hostel_management/assets/images/student-dashboard.png" alt="Student Hostel">
        <p class="text-center">Welcome, <strong><?php echo htmlspecialchars($studentName); ?></strong> 👋</p>
    </aside>

    <!-- Main Section -->
    <main class="flex-1 md:ml-[260px] overflow-auto">
        <!-- Header -->
        <header class="bg-blue-100 shadow-md p-4 flex justify-between items-center sticky top-0 z-10">
            <h1 class="text-lg md:text-2xl font-bold text-gray-800">MAHE Hostel Management System — Student Dashboard</h1>

            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <div class="relative">
                    <button id="notificationButton" class="text-gray-600 text-xl">
                        <i class="fas fa-bell"></i>
                        <span id="notificationBadge" class="absolute -top-1 -right-2 bg-red-500 text-white text-xs w-4 h-4 flex items-center justify-center rounded-full hidden">0</span>
                    </button>
                    <div id="notificationDropdown" class="absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-72 hidden">
                        <div class="p-3 border-b">
                            <h3 class="font-semibold text-gray-700 text-sm">Notifications</h3>
                        </div>
                        <div id="notificationList" class="max-h-64 overflow-y-auto"></div>
                        <div class="border-t text-center p-2">
                            <button id="markAllRead" class="text-xs text-blue-600 hover:underline">Mark all as read</button>
                        </div>
                    </div>
                </div>

                <!-- Profile Dropdown -->
                <div class="relative dropdown">
                    <button class="flex items-center space-x-2 text-gray-700">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="hidden md:inline text-sm font-medium"><?php echo htmlspecialchars($studentName); ?></span>
                    </button>
                    <div class="dropdown-menu">
                        <a href="student-profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-user mr-2"></i>Profile</a>
                        <a href="student-logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Cards -->
        <section class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Profile -->
                <div class="dashboard-card">
                    <div class="flex items-center space-x-3">
                        <div class="dashboard-icon bg-blue-100 text-blue-600"><i class="fas fa-user"></i></div>
                        <h2 class="font-semibold text-gray-800 text-lg">Profile</h2>
                    </div>
                    <p class="text-gray-600 text-sm">View and edit your profile details.</p>
                    <a href="student-profile.php" class="text-blue-600 font-medium text-sm">View Profile →</a>
                </div>

                <!-- Guest Log -->
                <div class="dashboard-card">
                    <div class="flex items-center space-x-3">
                        <div class="dashboard-icon bg-purple-100 text-purple-600"><i class="fas fa-users"></i></div>
                        <h2 class="font-semibold text-gray-800 text-lg">Guest Log</h2>
                    </div>
                    <p class="text-gray-600 text-sm">Register and view guest visits.</p>
                    <a href="student-guestlog.php" class="text-purple-600 font-medium text-sm">Manage Guests →</a>
                </div>

                <!-- Fees -->
                <div class="dashboard-card">
                    <div class="flex items-center space-x-3">
                        <div class="dashboard-icon bg-yellow-100 text-yellow-600"><i class="fas fa-money-bill-wave"></i></div>
                        <h2 class="font-semibold text-gray-800 text-lg">Fee Payments</h2>
                    </div>
                    <p class="text-gray-600 text-sm">View and make hostel payments.</p>
                    <a href="student-fees.php" class="text-yellow-600 font-medium text-sm">Pay Fees →</a>
                </div>

                <!-- Attendance -->
                <div class="dashboard-card">
                    <div class="flex items-center space-x-3">
                        <div class="dashboard-icon bg-green-100 text-green-600"><i class="fas fa-clipboard-check"></i></div>
                        <h2 class="font-semibold text-gray-800 text-lg">Attendance</h2>
                    </div>
                    <p class="text-gray-600 text-sm">Track your hostel attendance.</p>
                    <a href="student-attendance.php" class="text-green-600 font-medium text-sm">View Log →</a>
                </div>

                <!-- Maintenance -->
                <div class="dashboard-card">
                    <div class="flex items-center space-x-3">
                        <div class="dashboard-icon bg-indigo-100 text-indigo-600"><i class="fas fa-tools"></i></div>
                        <h2 class="font-semibold text-gray-800 text-lg">Maintenance</h2>
                    </div>
                    <p class="text-gray-600 text-sm">Submit maintenance requests.</p>
                    <a href="student-maintenance.php" class="text-indigo-600 font-medium text-sm">Request Service →</a>
                </div>

                <!-- Notices -->
                <div class="dashboard-card">
                    <div class="flex items-center space-x-3">
                        <div class="dashboard-icon bg-pink-100 text-pink-600"><i class="fas fa-bullhorn"></i></div>
                        <h2 class="font-semibold text-gray-800 text-lg">Notices</h2>
                    </div>
                    <p class="text-gray-600 text-sm">Stay updated on important announcements.</p>
                    <a href="student-notices.php" class="text-pink-600 font-medium text-sm">View Notices →</a>
                </div>

                <!-- Complaints -->
                <div class="dashboard-card">
                    <div class="flex items-center space-x-3">
                        <div class="dashboard-icon bg-orange-100 text-orange-600"><i class="fas fa-exclamation-triangle"></i></div>
                        <h2 class="font-semibold text-gray-800 text-lg">Complaints</h2>
                    </div>
                    <p class="text-gray-600 text-sm">Submit and track your complaints.</p>
                    <a href="student-complaints.php" class="text-orange-600 font-medium text-sm">Submit Complaint →</a>
                </div>

                <!-- Mess -->
                <div class="dashboard-card">
                    <div class="flex items-center space-x-3">
                        <div class="dashboard-icon bg-teal-100 text-teal-600"><i class="fas fa-utensils"></i></div>
                        <h2 class="font-semibold text-gray-800 text-lg">Mess</h2>
                    </div>
                    <p class="text-gray-600 text-sm">View weekly mess menu.</p>
                    <a href="student-mess.php" class="text-teal-600 font-medium text-sm">View Menu →</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Scripts -->
    <script>
        // Notifications
        const notifBtn = document.getElementById('notificationButton');
        const notifDropdown = document.getElementById('notificationDropdown');
        const notifBadge = document.getElementById('notificationBadge');
        const notifList = document.getElementById('notificationList');
        const markAll = document.getElementById('markAllRead');

        function updateBadge() {
            const unread = notifList.querySelectorAll('.unread').length;
            notifBadge.textContent = unread;
            notifBadge.classList.toggle('hidden', unread === 0);
        }

        notifBtn.addEventListener('click', e => {
            e.stopPropagation();
            notifDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', e => {
            if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
                notifDropdown.classList.add('hidden');
            }
        });

        // Simulate notifications
        function simulateNotification() {
            const now = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            const item = document.createElement('div');
            item.className = 'p-3 border-b unread';
            item.innerHTML = `<p class='font-medium text-sm'>Hostel notice updated</p><p class='text-xs text-gray-500'>${now}</p>`;
            notifList.prepend(item);
            updateBadge();
        }

        markAll.addEventListener('click', () => {
            notifList.querySelectorAll('.unread').forEach(el => el.classList.remove('unread'));
            updateBadge();
        });

        setInterval(simulateNotification, 30000);
    </script>
</body>
</html>
