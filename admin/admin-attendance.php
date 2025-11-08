<?php
session_start();
require_once('../db_connection.php');

// --- HANDLE FORM SUBMISSION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    header('Content-Type: application/json');

    $student_id = trim($_POST['student_id'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $status = trim($_POST['status'] ?? 'Absent');

    if (empty($student_id) || empty($date)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit;
    }

    if (!in_array($status, ['Present', 'Absent'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid status']);
        exit;
    }

    // Check if student exists
    $check = $conn->prepare("SELECT student_id FROM students WHERE student_id = ?");
    $check->bind_param("i", $student_id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Student not found']);
        exit;
    }
    $check->close();

    // Check if record already exists
    $exists = $conn->prepare("SELECT attendance_id FROM attendance WHERE student_id = ? AND date = ?");
    $exists->bind_param("is", $student_id, $date);
    $exists->execute();
    $exists->store_result();
    if ($exists->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Attendance already recorded for this student']);
        exit;
    }
    $exists->close();

    // Insert new attendance record
    $stmt = $conn->prepare("INSERT INTO attendance (student_id, date, status) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $student_id, $date, $status);
    $success = $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => $success, 'message' => 'Attendance recorded successfully']);
    exit;
}

// --- FETCH ATTENDANCE DATA ---
$query = "SELECT a.attendance_id, a.student_id, s.full_name, a.date, a.status 
          FROM attendance a 
          JOIN students s ON a.student_id = s.student_id 
          ORDER BY a.date DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance Management | Hostel Admin</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('/hostel_management/assets/images/admin-dashboard.png') no-repeat center center fixed;
      background-size: cover;
    }
    .sidebar {
      width: 270px;
      background: rgba(5,10,20,0.45);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      position: fixed;
      top: 0;
      left: 0;
      min-height: 100vh;
      color: white;
      box-shadow: 5px 0 25px rgba(0,0,0,0.4);
      border-right: 1px solid rgba(255,255,255,0.08);
      transition: transform 0.3s ease;
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
    .sidebar a:hover { background: rgba(255,255,255,0.1); transform: translateX(5px); }
    .sidebar a.active { background-color: #2563eb; }
    .present-status { background-color: #bbf7d0; color: #166534; padding: 3px 10px; border-radius: 12px; font-size: 12px; }
    .absent-status { background-color: #fecaca; color: #b91c1c; padding: 3px 10px; border-radius: 12px; font-size: 12px; }
    tr.fade-out { opacity: 0; transition: opacity 0.4s ease; }
  </style>
</head>

<body class="flex h-screen">
  <!-- Sidebar -->
  <aside class="sidebar hidden md:block p-6">
    <div class="text-center mb-6">
      <i class="fas fa-user-shield text-5xl text-blue-400 mb-3"></i>
      <h2 class="text-lg font-semibold">Admin Panel</h2>
      <p class="text-xs text-gray-300"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
    </div>

    <nav>
      <ul class="space-y-2">
        <li><a href="admin-dashboard.php"><i class="fas fa-home w-5 text-center"></i> Dashboard</a></li>
        <li><a href="admin-students.php"><i class="fas fa-users w-5 text-center"></i> Students</a></li>
        <li><a href="admin-attendance.php" class="active"><i class="fas fa-calendar-check w-5 text-center"></i> Attendance</a></li>
        <li><a href="admin-payments.php"><i class="fas fa-wallet w-5 text-center"></i> Payments</a></li>
        <li><a href="admin-maintenance.php"><i class="fas fa-tools w-5 text-center"></i> Maintenance</a></li>
        <li><a href="../index.php" class="text-red-300 hover:text-white"><i class="fas fa-sign-out-alt w-5 text-center"></i> Logout</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="flex-1 ml-[270px] p-6 bg-white/80 backdrop-blur">
    <h1 class="text-2xl font-bold mb-6">Attendance Management</h1>

    <!-- Add Attendance Form -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <h2 class="text-lg font-semibold mb-4">Mark Attendance</h2>
      <form id="attendanceForm" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-gray-700 mb-2">Student ID</label>
            <input type="number" name="student_id" class="w-full px-3 py-2 border rounded" required>
          </div>
          <div>
            <label class="block text-gray-700 mb-2">Date</label>
            <input type="date" name="date" class="w-full px-3 py-2 border rounded" required>
          </div>
          <div>
            <label class="block text-gray-700 mb-2">Status</label>
            <select name="status" class="w-full px-3 py-2 border rounded">
              <option value="Present">Present</option>
              <option value="Absent">Absent</option>
            </select>
          </div>
        </div>
        <div class="flex justify-end">
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            <i class="fas fa-save mr-2"></i> Save Attendance
          </button>
        </div>
      </form>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-4 border-b flex justify-between items-center">
        <h2 class="text-lg font-semibold">Attendance Records</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 uppercase">Student ID</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 uppercase">Name</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 uppercase">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr data-id="<?php echo $row['attendance_id']; ?>">
              <td class="px-6 py-4 text-sm text-gray-700"><?php echo $row['date']; ?></td>
              <td class="px-6 py-4 text-sm text-gray-700"><?php echo $row['student_id']; ?></td>
              <td class="px-6 py-4 text-sm text-gray-700"><?php echo htmlspecialchars($row['full_name']); ?></td>
              <td class="px-6 py-4 text-sm">
                <span class="<?php echo $row['status'] === 'Present' ? 'present-status' : 'absent-status'; ?>">
                  <?php echo $row['status']; ?>
                </span>
              </td>
              <td class="px-6 py-4 text-sm">
                <button class="delete-btn text-red-500 hover:text-red-700">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    // Add Attendance
    $('#attendanceForm').on('submit', function(e) {
      e.preventDefault();
      $.post('admin-attendance.php', $(this).serialize(), function(response) {
        if (response.success) {
          alert('✅ ' + response.message);
          location.reload();
        } else {
          alert('❌ ' + response.error);
        }
      }, 'json');
    });

    // Delete Attendance
    $(document).on('click', '.delete-btn', function() {
      const row = $(this).closest('tr');
      const id = row.data('id');
      if (confirm('Are you sure you want to delete this attendance record?')) {
        $.post('delete_attendance.php', { id }, function(response) {
          if (response.success) {
            row.addClass('fade-out');
            setTimeout(() => row.remove(), 400);
          } else {
            alert('❌ ' + response.error);
          }
        }, 'json');
      }
    });
  </script>
</body>
</html>
