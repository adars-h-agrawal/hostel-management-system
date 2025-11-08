<?php
session_start();
require_once('../db_connection.php');

// Fetch all complaints (except resolved)
$query = "SELECT * FROM complaints ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Complaint Management | Hostel Admin</title>

  <!-- Tailwind & Icons -->
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
      background: rgba(15, 23, 42, 0.85);
      backdrop-filter: blur(10px);
      color: white;
      position: fixed;
      top: 0;
      left: 0;
      min-height: 100vh;
      box-shadow: 5px 0 15px rgba(0, 0, 0, 0.4);
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
      background: rgba(255, 255, 255, 0.08);
      transform: translateX(5px);
    }
    .sidebar a.active {
      background-color: #7c3aed;
    }
    .status {
      padding: 3px 10px;
      border-radius: 10px;
      font-size: 12px;
      font-weight: 600;
      text-transform: capitalize;
    }
    .status.Pending { background: #fef3c7; color: #92400e; }
    .status['In Progress'] { background: #fef9c3; color: #854d0e; }
    .status.Resolved { background: #d1fae5; color: #065f46; }
  </style>
</head>

<body class="flex h-screen">
  <!-- Sidebar -->
  <aside class="sidebar p-6 hidden md:block">
    <div class="text-center mb-6">
      <i class="fas fa-user-shield text-5xl text-purple-400 mb-3"></i>
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
        <li><a href="admin-guests.php"><i class="fas fa-user-friends w-5 text-center"></i> Guests</a></li>
        <li><a href="admin-maintenance.php"><i class="fas fa-tools w-5 text-center"></i> Maintenance</a></li>
        <li><a href="admin-complaints.php" class="active"><i class="fas fa-exclamation-circle w-5 text-center"></i> Complaints</a></li>
        <li><a href="../index.php" class="text-red-300 hover:text-white"><i class="fas fa-sign-out-alt w-5 text-center"></i> Logout</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 ml-[270px] p-8 bg-white/80 backdrop-blur overflow-auto">
    <h1 class="text-2xl font-bold mb-6">Complaint Management</h1>

    <!-- Complaints List -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <h2 class="text-lg font-semibold mb-4">All Complaints</h2>
      <div class="divide-y divide-gray-200 max-h-[65vh] overflow-y-auto">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="p-4 hover:bg-gray-50 transition">
          <div class="flex justify-between">
            <div>
              <h3 class="font-semibold text-gray-800">#<?php echo $row['complaint_id']; ?> — <?php echo htmlspecialchars($row['complaint_type']); ?></h3>
              <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($row['description']); ?></p>
              <p class="text-xs text-gray-500 mt-2">Student ID: <?php echo htmlspecialchars($row['student_id']); ?> | Filed on: <?php echo date('d M Y', strtotime($row['created_at'])); ?></p>
            </div>
            <span class="status <?php echo htmlspecialchars($row['status']); ?>"><?php echo htmlspecialchars($row['status']); ?></span>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    </div>

    <!-- Update Complaint Status -->
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-lg font-semibold mb-4">Update Complaint Status</h2>
      <form method="POST" action="admin-complaints-update.php" class="space-y-4">
        <div>
          <label class="block text-gray-700 mb-2">Complaint ID</label>
          <input type="text" name="complaint_id" class="border rounded px-3 py-2 w-full" placeholder="Enter complaint ID" required>
        </div>
        <div>
          <label class="block text-gray-700 mb-2">Resolution Notes</label>
          <textarea name="resolution" class="border rounded px-3 py-2 w-full h-28" placeholder="Enter resolution details" required></textarea>
        </div>
        <div>
          <label class="block text-gray-700 mb-2">Status</label>
          <select name="status" class="border rounded px-3 py-2 w-full" required>
            <option value="">Select Status</option>
            <option value="Pending">Pending</option>
            <option value="In Progress">In Progress</option>
            <option value="Resolved">Resolved</option>
          </select>
        </div>
        <div class="flex justify-end">
          <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
            <i class="fas fa-check-circle mr-2"></i> Update
          </button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
