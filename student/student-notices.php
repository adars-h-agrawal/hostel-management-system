<?php
session_start();
include('../db_connection.php'); // adjust if needed

// Redirect if not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student-login.php");
    exit();
}

// Fetch only active notices (today between start and end date)
$query = "SELECT *, 
          CASE 
            WHEN CURDATE() BETWEEN start_date AND end_date THEN 'Active'
            WHEN CURDATE() < start_date THEN 'Upcoming'
            ELSE 'Expired'
          END AS status
          FROM notices 
          ORDER BY start_date DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Notices | Student Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
        }
        .sidebar {
            transition: all 0.3s;
            width: 300px;
            background: linear-gradient(135deg, #1e343b 0%, #023c58 100%);
            height: 100vh;
        }
        .sidebar a {
            transition: all 0.3s;
            margin: 0 8px;
            border-radius: 8px;
            padding: 10px 12px;
        }
        .sidebar a:hover {
            transform: translateX(5px);
            background-color: #334155;
        }
        .user-badge { background-color: #3b82f6; }
    </style>
</head>
<body class="flex h-screen">
    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow p-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <button class="md:hidden text-gray-600">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="text-xl font-bold text-gray-800">Hostel Notices</h1>
            </div>
            <div class="flex items-center space-x-4">
                <button class="text-gray-600">
                    <i class="fas fa-bell"></i>
                </button>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full user-badge flex items-center justify-center text-white">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="hidden md:inline">Student</span>
                </div>
            </div>
        </header>

        <!-- Notices Section -->
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6 text-gray-800">Hostel Notices</h1>
            
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="space-y-4">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="rounded-lg shadow bg-white p-6 hover:bg-gray-50 transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-gray-800 text-lg"><?= htmlspecialchars($row['title']); ?></h3>
                                    <p class="text-sm text-gray-600 mt-1"><?= nl2br(htmlspecialchars($row['description'])); ?></p>
                                    <p class="text-xs text-blue-600 mt-2 font-medium">
                                        Start: <?= date('d M Y', strtotime($row['start_date'])); ?> |
                                        End: <?= date('d M Y', strtotime($row['end_date'])); ?>
                                    </p>
                                </div>
                                <span class="text-xs bg-green-100 text-green-800 px-3 py-1 rounded-full">Active</span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center bg-white p-10 rounded-lg shadow">
                    <i class="fas fa-bullhorn text-gray-400 text-4xl mb-3"></i>
                    <p class="text-gray-600 font-medium">No active notices at the moment.</p>
                    <p class="text-sm text-gray-400">Please check back later for updates.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        document.querySelector('.md\\:hidden').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('hidden');
        });
    </script>
</body>
</html>
