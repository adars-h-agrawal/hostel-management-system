<?php
session_start();
require_once('../db_connection.php');

// Handle new notice submission (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    header('Content-Type: application/json');

    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if (empty($title) || empty($content) || empty($start_date) || empty($end_date)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO notices (title, description, start_date, end_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $content, $start_date, $end_date);
    $success = $stmt->execute();

    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Notice published successfully!' : 'Database error: ' . $stmt->error
    ]);
    exit;
}

// Fetch all existing notices
$notices = mysqli_query($conn, "SELECT * FROM notices ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notices Management | Hostel Admin</title>

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
    .notice-card {
      background: rgba(255,255,255,0.9);
      border-radius: 12px;
      padding: 1.25rem;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      margin-bottom: 1rem;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .notice-card:hover { transform: translateY(-3px); box-shadow: 0 8px 16px rgba(0,0,0,0.15); }
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
        <li><a href="admin-attendance.php"><i class="fas fa-calendar-check w-5 text-center"></i> Attendance</a></li>
        <li><a href="admin-mess.php"><i class="fas fa-utensils w-5 text-center"></i> Mess Details</a></li>
        <li><a href="admin-notices.php" class="active"><i class="fas fa-bullhorn w-5 text-center"></i> Notices</a></li>
        <li><a href="admin-payments.php"><i class="fas fa-wallet w-5 text-center"></i> Payments</a></li>
        <li><a href="admin-maintenance.php"><i class="fas fa-tools w-5 text-center"></i> Maintenance</a></li>
        <li><a href="../index.php" class="text-red-300 hover:text-white"><i class="fas fa-sign-out-alt w-5 text-center"></i> Logout</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 ml-[270px] p-8 bg-white/80 backdrop-blur overflow-auto">
    <h1 class="text-2xl font-bold mb-6">Notices Management</h1>

    <!-- New Notice Form -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
      <h2 class="text-lg font-semibold mb-4">Create New Notice</h2>
      <form id="noticeForm" class="space-y-4">
        <div>
          <label class="block text-gray-700 mb-2">Title</label>
          <input type="text" name="title" class="w-full px-3 py-2 border rounded" placeholder="Notice title" required>
        </div>
        <div>
          <label class="block text-gray-700 mb-2">Content</label>
          <textarea name="content" class="w-full px-3 py-2 border rounded h-28" placeholder="Write the notice content..." required></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-gray-700 mb-2">Start Date</label>
            <input type="date" name="start_date" class="w-full px-3 py-2 border rounded" required>
          </div>
          <div>
            <label class="block text-gray-700 mb-2">End Date</label>
            <input type="date" name="end_date" class="w-full px-3 py-2 border rounded" required>
          </div>
        </div>
        <div class="flex justify-end">
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            <i class="fas fa-paper-plane mr-2"></i> Publish Notice
          </button>
        </div>
      </form>
    </div>

    <!-- Notice List -->
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-lg font-semibold mb-4">Published Notices</h2>

      <?php if (mysqli_num_rows($notices) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($notices)): ?>
          <div class="notice-card">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($row['title']); ?></h3>
                <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($row['description']); ?></p>
              </div>
              <div class="text-gray-500 text-sm text-right">
                <?php echo date('M d', strtotime($row['start_date'])) . ' - ' . date('M d', strtotime($row['end_date'])); ?>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-gray-600">No notices published yet.</p>
      <?php endif; ?>
    </div>
  </main>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $('#noticeForm').on('submit', function(e) {
      e.preventDefault();
      $.post('admin-notices.php', $(this).serialize(), function(response) {
        if (response.success) {
          alert('✅ ' + response.message);
          location.reload();
        } else {
          alert('❌ ' + response.error);
        }
      }, 'json');
    });
  </script>
</body>
</html>
