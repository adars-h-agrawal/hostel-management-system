<?php
session_start();

// ✅ Ensure student is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: student-login.php");
    exit();
}

require_once("../db_connection.php");

// ✅ Get logged-in student details
$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'] ?? 'Student';

// ✅ Handle month filter
$selected_month = $_GET['month'] ?? date('Y-m');
$month_start = $selected_month . "-01";
$month_end = date("Y-m-t", strtotime($month_start));

// ✅ Attendance summary
$summary_sql = "
    SELECT 
        COUNT(*) AS total_days,
        SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present_days,
        SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS absent_days
    FROM attendance
    WHERE student_id = ? AND date BETWEEN ? AND ?
";
$stmt = $conn->prepare($summary_sql);
$stmt->bind_param("iss", $student_id, $month_start, $month_end);
$stmt->execute();
$summary = $stmt->get_result()->fetch_assoc();
$stmt->close();

// ✅ Attendance %
$total = $summary['total_days'] ?? 0;
$present = $summary['present_days'] ?? 0;
$attendance_percent = $total > 0 ? round(($present / $total) * 100, 2) : 0;

// ✅ Fetch records
$query = "SELECT date, status FROM attendance WHERE student_id = ? AND date BETWEEN ? AND ? ORDER BY date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $student_id, $month_start, $month_end);
$stmt->execute();
$attendance_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Attendance Log | Hostel Student</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    body { font-family: "Poppins", sans-serif; background-color: #f3f4f6; }
    .sidebar { width: 300px; background: linear-gradient(135deg, #1e343b 0%, #023c58 100%); height: 100vh; transition: all 0.3s; }
    .sidebar a { transition: all 0.3s; margin: 0 8px; border-radius: 8px; padding: 10px 12px; }
    .sidebar a:hover { transform: translateX(5px); background-color: #334155; }
    .btn-primary { background-color: #3b82f6; color: white; padding: 0.5rem 1.25rem; border-radius: 0.5rem; transition: all 0.2s; font-weight: 500; font-size: 0.875rem; }
    .btn-primary:hover { background-color: #2563eb; transform: translateY(-1px); }
    .attendance-present { background-color: #d1fae5; color: #065f46; }
    .attendance-absent { background-color: #fee2e2; color: #b91c1c; }
    .progress-bar { height: 10px; border-radius: 6px; background-color: #e5e7eb; overflow: hidden; }
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
        <li><a href="student-dashboard.php" class="flex items-center space-x-3 p-3"><i class="fas fa-arrow-left text-xl w-6 text-center"></i><span class="text-md">Back to Dashboard</span></a></li>
        <li><a href="student-profile.php" class="flex items-center space-x-3 p-3"><i class="fas fa-user text-xl w-6 text-center"></i><span class="text-md">Profile</span></a></li>
        <li><a href="student-guestlog.php" class="flex items-center space-x-3 p-3"><i class="fas fa-users text-xl w-6 text-center"></i><span class="text-md">Guest Log</span></a></li>
        <li><a href="student-fees.php" class="flex items-center space-x-3 p-3"><i class="fas fa-money-bill-wave text-xl w-6 text-center"></i><span class="text-md">Fee Payments</span></a></li>
        <li><a href="student-attendance.php" class="flex items-center space-x-3 p-3 bg-teal-900"><i class="fas fa-clipboard-check text-xl w-6 text-center"></i><span class="text-md">Attendance Log</span></a></li>
        <li><a href="student-mess.php" class="flex items-center space-x-3 p-3"><i class="fas fa-utensils text-xl w-6 text-center"></i><span class="text-md">Mess</span></a></li>
        <li><a href="student-maintenance.php" class="flex items-center space-x-3 p-3"><i class="fas fa-tools text-xl w-6 text-center"></i><span class="text-md">Maintenance</span></a></li>
        <li><a href="student-notices.php" class="flex items-center space-x-3 p-3"><i class="fas fa-bell text-xl w-6 text-center"></i><span class="text-md">Notices</span></a></li>
        <li><a href="student-complaints.php" class="flex items-center space-x-3 p-3"><i class="fas fa-exclamation-triangle text-xl w-6 text-center"></i><span class="text-md">Complaints</span></a></li>
        <li><a href="../index.php" class="flex items-center space-x-3 p-3 mt-8"><i class="fas fa-sign-out-alt text-xl w-6 text-center"></i><span class="text-md">Logout</span></a></li>
      </ul>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="flex-1 overflow-auto">
    <header class="bg-white shadow p-4 flex justify-between items-center">
      <div class="flex items-center space-x-4">
        <button class="md:hidden text-gray-600"><i class="fas fa-bars"></i></button>
        <h1 class="text-xl font-bold text-gray-800">Attendance Log</h1>
      </div>
      <div class="flex items-center space-x-4">
        <i class="fas fa-bell text-gray-600"></i>
        <div class="flex items-center space-x-2">
          <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
            <i class="fas fa-user"></i>
          </div>
          <span class="hidden md:inline"><?php echo htmlspecialchars($student_name); ?></span>
        </div>
      </div>
    </header>

    <main class="p-6">
      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="p-6 rounded-md bg-white shadow">
          <p class="text-sm text-gray-500">Total Days</p>
          <p class="text-2xl font-bold text-blue-500"><?php echo $summary['total_days'] ?? 0; ?></p>
        </div>
        <div class="p-6 rounded-md bg-white shadow">
          <p class="text-sm text-gray-500">Present</p>
          <p class="text-2xl font-bold text-green-500"><?php echo $summary['present_days'] ?? 0; ?></p>
        </div>
        <div class="p-6 rounded-md bg-white shadow">
          <p class="text-sm text-gray-500">Absent</p>
          <p class="text-2xl font-bold text-red-500"><?php echo $summary['absent_days'] ?? 0; ?></p>
        </div>

        <!-- Attendance % -->
        <div class="p-6 rounded-md bg-white shadow">
          <p class="text-sm text-gray-500">Overall Attendance</p>
          <p class="text-2xl font-bold <?php echo ($attendance_percent >= 80) ? 'text-green-500' : (($attendance_percent >= 60) ? 'text-yellow-500' : 'text-red-500'); ?>">
            <?php echo $attendance_percent; ?>%
          </p>
          <div class="progress-bar mt-2">
            <div class="h-full <?php echo ($attendance_percent >= 80) ? 'bg-green-500' : (($attendance_percent >= 60) ? 'bg-yellow-400' : 'bg-red-500'); ?>" style="width: <?php echo $attendance_percent; ?>%;"></div>
          </div>
        </div>
      </div>

      <!-- Monthly Attendance Report -->
      <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-4 border-b bg-blue-50 flex flex-col md:flex-row justify-between md:items-center gap-3">
          <h2 class="text-lg font-semibold text-gray-800">Monthly Attendance Report</h2>
          <form method="GET" class="flex space-x-3">
            <input type="month" name="month" value="<?php echo $selected_month; ?>" class="border rounded px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" />
            <button type="submit" class="btn-primary flex items-center space-x-2">
              <i class="fas fa-filter"></i><span>Filter</span>
            </button>
            <button type="button" onclick="downloadCSV()" class="btn-primary flex items-center space-x-2 bg-green-600 hover:bg-green-700">
              <i class="fas fa-download"></i><span>Download</span>
            </button>
          </form>
        </div>

        <div class="overflow-x-auto">
          <table id="attendanceTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-blue-100">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <?php while ($row = $attendance_result->fetch_assoc()): ?>
              <tr>
                <td class="px-6 py-4 text-sm text-gray-800"><?php echo htmlspecialchars($row['date']); ?></td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 text-xs rounded-full font-semibold <?php echo $row['status'] === 'Present' ? 'attendance-present' : 'attendance-absent'; ?>">
                    <?php echo htmlspecialchars($row['status']); ?>
                  </span>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Policy -->
      <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 rounded-lg p-5">
        <h2 class="text-md font-semibold text-gray-800 mb-2">Attendance Policy</h2>
        <ul class="list-disc pl-5 text-gray-600 space-y-1">
          <li>Minimum 80% attendance is required to maintain hostel residency.</li>
          <li>Check-in after 11:00 PM will be marked as late entry.</li>
          <li>3 consecutive absent days without notice may lead to disciplinary action.</li>
          <li>Medical leaves must be supported with proper documentation.</li>
        </ul>
      </div>
    </main>
  </div>

  <script>
    document.querySelector(".md\\:hidden").addEventListener("click", () => {
      document.querySelector(".sidebar").classList.toggle("hidden");
    });

    // ✅ Download as CSV with name + ID + attendance %
    function downloadCSV() {
      const table = document.getElementById("attendanceTable");
      const rows = table.querySelectorAll("tr");
      const studentName = "<?php echo $student_name; ?>";
      const studentID = "<?php echo $student_id; ?>";
      const month = "<?php echo $selected_month; ?>";
      const percent = "<?php echo $attendance_percent; ?>";

      let csv = [];
      csv.push(`Attendance Report for ${studentName} (ID: ${studentID})`);
      csv.push(`Month: ${month}`);
      csv.push(`Attendance Percentage: ${percent}%`);
      csv.push("");
      csv.push("Date,Status");

      rows.forEach(row => {
        const cols = row.querySelectorAll("td");
        if (cols.length > 0) {
          csv.push(`${cols[0].innerText},${cols[1].innerText}`);
        }
      });

      const csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
      const link = document.createElement("a");
      link.setAttribute("href", encodeURI(csvContent));
      link.setAttribute("download", `${studentName.replace(/\s+/g, '_')}_Attendance_${month}.csv`);
      document.body.appendChild(link);
      link.click();
    }
  </script>
</body>
</html>
