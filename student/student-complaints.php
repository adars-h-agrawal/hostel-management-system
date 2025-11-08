<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: student-login.php");
    exit();
}

require_once("../db_connection.php");
$student_id = $_SESSION['student_id'];

// Fetch student complaints
$query = "SELECT * FROM complaints WHERE student_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Complaints | Hostel Student</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
body { font-family: 'Poppins', sans-serif; background-color: #e6f7fb; }
.sidebar { width: 300px; background: linear-gradient(135deg, #1e343b 0%, #023c58 100%); height: 100vh; transition: all 0.3s; }
.sidebar a { transition: all 0.3s; margin: 0 8px; border-radius: 8px; padding: 10px 12px; }
.sidebar a:hover { transform: translateX(5px); background-color: #334155; }
.btn-primary { background-color: #3b82f6; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; transition: all 0.2s; font-weight: 500; }
.btn-primary:hover { background-color: #2563eb; transform: translateY(-1px); }
.badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
.badge.Pending { background-color: #fef3c7; color: #92400e; }
.badge['In Progress'] { background-color: #e0f2fe; color: #0369a1; }
.badge.Resolved { background-color: #dcfce7; color: #166534; }
</style>
</head>

<body class="flex h-screen">
  <!-- Sidebar -->
  <div class="sidebar text-white p-6 hidden md:block">
    <div class="flex items-center justify-center p-3 mb-10">
      <i class="fas fa-user-graduate text-4xl"></i>
    </div>
    <nav>
      <ul class="space-y-2">
        <li><a href="student-dashboard.php" class="flex items-center space-x-3 p-3"><i class="fas fa-arrow-left text-xl w-6"></i><span>Back to Dashboard</span></a></li>
        <li><a href="student-profile.php" class="flex items-center space-x-3 p-3"><i class="fas fa-user text-xl w-6"></i><span>Profile</span></a></li>
        <li><a href="student-guestlog.php" class="flex items-center space-x-3 p-3"><i class="fas fa-users text-xl w-6"></i><span>Guest Log</span></a></li>
        <li><a href="student-fees.php" class="flex items-center space-x-3 p-3"><i class="fas fa-money-bill-wave text-xl w-6"></i><span>Fee Payments</span></a></li>
        <li><a href="student-attendance.php" class="flex items-center space-x-3 p-3"><i class="fas fa-clipboard-check text-xl w-6"></i><span>Attendance</span></a></li>
        <li><a href="student-maintenance.php" class="flex items-center space-x-3 p-3"><i class="fas fa-tools text-xl w-6"></i><span>Maintenance</span></a></li>
        <li><a href="student-notices.php" class="flex items-center space-x-3 p-3"><i class="fas fa-bell text-xl w-6"></i><span>Notices</span></a></li>
        <li><a href="student-complaints.php" class="flex items-center space-x-3 p-3 bg-teal-900"><i class="fas fa-exclamation-triangle text-xl w-6"></i><span>Complaints</span></a></li>
        <li><a href="../index.php" class="flex items-center space-x-3 p-3 mt-8"><i class="fas fa-sign-out-alt text-xl w-6"></i><span>Logout</span></a></li>
      </ul>
    </nav>
  </div>

  <!-- Main Section -->
  <div class="flex-1 overflow-auto">
    <header class="bg-white shadow p-4 flex justify-between items-center">
      <h1 class="text-xl font-bold text-gray-800">Raise Complaints</h1>
      <div class="flex items-center space-x-4">
        <i class="fas fa-bell text-gray-600"></i>
        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
          <i class="fas fa-user"></i>
        </div>
        <span class="hidden md:inline"><?php echo htmlspecialchars($_SESSION['student_name']); ?></span>
      </div>
    </header>

    <main class="p-6">
      <!-- New Complaint Form -->
      <div class="bg-white rounded-md shadow p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4 text-gray-700">Submit a New Complaint</h2>
        <form action="backend/complain_stud.php" method="POST">
          <div class="mb-4">
            <label class="block text-gray-700 mb-2 font-medium">Complaint Type</label>
            <select name="complaint_type" class="form-input w-full border rounded p-2" required>
              <option value="">Select type</option>
              <option value="Noise">Noise</option>
              <option value="Cleanliness">Cleanliness</option>
              <option value="Facilities">Facilities</option>
              <option value="Security">Security</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 mb-2 font-medium">Description</label>
            <textarea name="description" class="form-input w-full border rounded p-2 h-32" placeholder="Describe your issue..." required></textarea>
          </div>
          <button type="submit" class="btn-primary w-full"><i class="fas fa-paper-plane mr-2"></i>Submit Complaint</button>
        </form>
      </div>

      <!-- Complaint History -->
      <div class="bg-white rounded-md shadow">
        <div class="p-4 border-b bg-blue-50">
          <h2 class="text-lg font-semibold text-gray-700">My Complaints</h2>
        </div>
        <div class="divide-y divide-gray-200">
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <div class="p-4 hover:bg-gray-50">
                <div class="flex justify-between">
                  <div>
                    <h3 class="text-md font-semibold text-gray-800"><?php echo htmlspecialchars($row['complaint_type']); ?></h3>
                    <p class="text-sm text-gray-600 mt-1"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                    <p class="text-xs text-gray-500 mt-2"><i class="fas fa-calendar-alt mr-1"></i><?php echo date("d M Y, h:i A", strtotime($row['created_at'])); ?></p>
                  </div>
                  <span class="badge <?php echo htmlspecialchars($row['status']); ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                </div>
                <p class="mt-2 text-sm text-green-700"><i class="fas fa-clipboard-check mr-1"></i>Admin Note: <?php echo htmlspecialchars($row['resolution']); ?></p>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="p-4 text-gray-500 text-center">No complaints submitted yet.</div>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
