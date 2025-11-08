<?php
session_start();
require_once('../db_connection.php');

// Fetch all guest visits
$query = "SELECT * FROM guest_log ORDER BY visit_date DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Guest Management | Hostel Admin</title>

  <!-- Tailwind & Fonts -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('/hostel_management/assets/images/admin-dashboard.png') no-repeat center center fixed;
      background-size: cover;
    }
    .sidebar {
      width: 270px;
      background: rgba(15, 23, 42, 0.8);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      color: white;
      position: fixed;
      top: 0;
      left: 0;
      min-height: 100vh;
      box-shadow: 5px 0 15px rgba(0,0,0,0.4);
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
    .sidebar a:hover { background: rgba(255,255,255,0.08); transform: translateX(5px); }
    .sidebar a.active { background-color: #2563eb; }

    .status {
      padding: 2px 10px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 500;
      display: inline-block;
    }
    .status.Pending { background: #fef3c7; color: #92400e; }
    .status.Approved { background: #d1fae5; color: #065f46; }
    .status.Rejected { background: #fee2e2; color: #991b1b; }
  </style>
</head>

<body class="flex h-screen">
  <!-- Sidebar -->
  <aside class="sidebar hidden md:block p-6">
    <div class="text-center mb-6">
      <i class="fas fa-user-shield text-5xl text-blue-400 mb-3"></i>
      <h2 class="text-lg font-semibold">Admin Panel</h2>
      <p class="text-xs text-gray-300"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></p>
    </div>

    <nav>
      <ul class="space-y-2">
        <li><a href="admin-dashboard.php"><i class="fas fa-home w-5 text-center"></i> Dashboard</a></li>
        <li><a href="admin-students.php"><i class="fas fa-users w-5 text-center"></i> Students</a></li>
        <li><a href="admin-attendance.php"><i class="fas fa-calendar-check w-5 text-center"></i> Attendance</a></li>
        <li><a href="admin-mess.php"><i class="fas fa-utensils w-5 text-center"></i> Mess</a></li>
        <li><a href="admin-notices.php"><i class="fas fa-bullhorn w-5 text-center"></i> Notices</a></li>
        <li><a href="admin-payments.php"><i class="fas fa-money-check-alt w-5 text-center"></i> Payments</a></li>
        <li><a href="admin-guests.php" class="active"><i class="fas fa-user-friends w-5 text-center"></i> Guests</a></li>
        <li><a href="admin-maintenance.php"><i class="fas fa-tools w-5 text-center"></i> Maintenance</a></li>
        <li><a href="../index.php" class="text-red-300 hover:text-white"><i class="fas fa-sign-out-alt w-5 text-center"></i> Logout</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 ml-[270px] p-8 bg-white/90 backdrop-blur overflow-auto">
    <h1 class="text-2xl font-bold mb-6">Guest Management</h1>

    <!-- Update Guest Status -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <h2 class="text-lg font-semibold mb-4">Update Guest Visit Status</h2>
      <form action="admin-guest-update.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-gray-700 mb-2">Student ID</label>
          <input type="text" name="student_id" class="border rounded px-3 py-2 w-full" required>
        </div>
        <div>
          <label class="block text-gray-700 mb-2">Visit ID</label>
          <input type="text" name="guest_id" class="border rounded px-3 py-2 w-full" required>
        </div>
        <div>
          <label class="block text-gray-700 mb-2">Status</label>
          <select name="status" class="border rounded px-3 py-2 w-full">
            <option value="Pending">Pending</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
          </select>
        </div>
        <div class="col-span-3 flex justify-end">
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            <i class="fas fa-save mr-2"></i> Update Status
          </button>
        </div>
      </form>
    </div>

    <!-- Guest Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-4 border-b flex justify-between items-center">
        <h2 class="text-lg font-semibold">Guest Visit Requests</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Guest ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Student ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Guest Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Relationship</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Visit Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Purpose</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Status</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td class="px-6 py-3"><?php echo htmlspecialchars($row['guest_id']); ?></td>
                <td class="px-6 py-3"><?php echo htmlspecialchars($row['student_id']); ?></td>
                <td class="px-6 py-3"><?php echo htmlspecialchars($row['guest_name']); ?></td>
                <td class="px-6 py-3"><?php echo htmlspecialchars($row['relationship']); ?></td>
                <td class="px-6 py-3"><?php echo htmlspecialchars($row['visit_date']); ?></td>
                <td class="px-6 py-3"><?php echo htmlspecialchars($row['purpose']); ?></td>
                <td class="px-6 py-3"><span class="status <?php echo htmlspecialchars($row['status']); ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</body>
</html>
