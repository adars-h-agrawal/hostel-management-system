<?php
session_start();
require_once('../db_connection.php');
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Students | Hostel Admin</title>

  <!-- Tailwind & Fonts -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('/hostel_management/assets/images/admin-dashboard.png') no-repeat center center fixed;
      background-size: cover;
      overflow-x: hidden;
    }

    .sidebar {
      width: 270px;
      background: rgba(5, 10, 20, 0.45);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      position: fixed;
      top: 0;
      left: 0;
      min-height: 100vh;
      color: white;
      box-shadow: 5px 0 25px rgba(0,0,0,0.4);
      border-right: 1px solid rgba(255,255,255,0.08);
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

    .content-wrapper {
      margin-left: 270px;
      min-height: 100vh;
      background: rgba(255,255,255,0.9);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      padding: 2rem;
    }
  </style>
</head>

<body class="antialiased">

  <!-- Sidebar -->
  <aside class="sidebar hidden md:block p-6">
    <div class="text-center mb-6">
      <i class="fas fa-user-shield text-5xl text-blue-400 mb-3"></i>
      <h2 class="text-lg font-semibold">Admin Panel</h2>
      <p class="text-xs text-gray-300"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
    </div>

    <nav>
      <ul class="space-y-2">
        <li><a href="admin-dashboard.php"><i class="fas fa-home w-5 text-center"></i><span>Dashboard</span></a></li>
        <li><a href="admin-students.php" class="active"><i class="fas fa-users w-5 text-center"></i><span>Manage Students</span></a></li>
        <li><a href="admin-attendance.php"><i class="fas fa-calendar-check w-5 text-center"></i><span>Attendance</span></a></li>
        <li><a href="admin-mess.php"><i class="fas fa-utensils w-5 text-center"></i><span>Mess</span></a></li>
        <li><a href="admin-payments.php"><i class="fas fa-wallet w-5 text-center"></i><span>Payments</span></a></li>
        <li><a href="admin-maintenance.php"><i class="fas fa-tools w-5 text-center"></i><span>Maintenance</span></a></li>
        <li><a href="admin-complaints.php"><i class="fas fa-exclamation-circle w-5 text-center"></i><span>Complaints</span></a></li>
        <li><a href="../index.php" class="flex items-center gap-3 text-red-300 hover:text-white"><i class="fas fa-sign-out-alt w-5 text-center"></i><span>Logout</span></a></li>
      </ul>
    </nav>
  </aside>

  <!-- Content -->
  <div class="content-wrapper">
    <header class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Manage Students</h1>
      <button id="addStudentBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i> Add Student
      </button>
    </header>

    <!-- Add Student Modal -->
    <div id="addStudentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-sm shadow-lg">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Add New Student</h3>
          <button id="closeModal" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <form id="studentForm" action="add_student.php" method="POST" class="space-y-3">
          <div><label class="block text-gray-700 mb-1">Registration No.</label><input type="text" name="registration_number" class="w-full px-3 py-2 border rounded" required></div>
          <div><label class="block text-gray-700 mb-1">Full Name</label><input type="text" name="full_name" class="w-full px-3 py-2 border rounded" required></div>
          <div><label class="block text-gray-700 mb-1">Email</label><input type="email" name="email" class="w-full px-3 py-2 border rounded" required></div>
          <div><label class="block text-gray-700 mb-1">Phone</label><input type="tel" name="phone" class="w-full px-3 py-2 border rounded" required></div>
          <div><label class="block text-gray-700 mb-1">Block</label><input type="text" name="block" class="w-full px-3 py-2 border rounded" required></div>
          <div><label class="block text-gray-700 mb-1">Room Number</label><input type="text" name="room_number" class="w-full px-3 py-2 border rounded" required></div>
          <div>
            <label class="block text-gray-700 mb-1">Room Type</label>
            <select name="room_type" class="w-full px-3 py-2 border rounded" required>
              <option value="">Select Room Type</option>
              <option value="Single AC">Single AC</option>
              <option value="Double AC">Double AC</option>
              <option value="Single NONAC">Single NONAC</option>
              <option value="Double NONAC">Double NONAC</option>
            </select>
          </div>
          <div class="flex justify-end space-x-2 pt-4">
            <button type="button" id="cancelAdd" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white bg-opacity-95 rounded-lg shadow">
      <div class="p-4 border-b flex justify-between items-center">
        <h2 class="text-lg font-semibold">All Students</h2>
        <button id="refreshBtn" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
          <i class="fas fa-sync mr-1"></i> Refresh
        </button>
      </div>

      <div class="overflow-x-auto" style="max-height: 60vh; overflow-y: auto;">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50 sticky top-0">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-bold text-gray-500 uppercase">ID</th>
              <th class="px-6 py-3 text-left text-sm font-bold text-gray-500 uppercase">Reg No.</th>
              <th class="px-6 py-3 text-left text-sm font-bold text-gray-500 uppercase">Name</th>
              <th class="px-6 py-3 text-left text-sm font-bold text-gray-500 uppercase">Email</th>
              <th class="px-6 py-3 text-left text-sm font-bold text-gray-500 uppercase">Phone</th>
              <th class="px-6 py-3 text-left text-sm font-bold text-gray-500 uppercase">Block</th>
              <th class="px-6 py-3 text-left text-sm font-bold text-gray-500 uppercase">Room No.</th>
              <th class="px-6 py-3 text-left text-sm font-bold text-gray-500 uppercase">Room Type</th>
              <th class="px-6 py-3 text-left text-sm font-bold text-gray-500 uppercase">Joined</th>
            </tr>
          </thead>
          <tbody id="studentsTableBody" class="bg-white divide-y divide-gray-200">
            <tr><td colspan="9" class="text-center py-4 text-gray-500">Loading...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      function loadStudents() {
        $.get('includes/get_students.php', function(data) {
          $('#studentsTableBody').html(data);
        });
      }

      loadStudents();
      $('#refreshBtn').click(loadStudents);

      $('#addStudentBtn').click(() => $('#addStudentModal').removeClass('hidden'));
      $('#closeModal, #cancelAdd').click(() => {
        $('#addStudentModal').addClass('hidden');
        $('#studentForm')[0].reset();
      });
    });
  </script>
</body>
</html>
