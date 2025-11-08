<?php
session_start();

// ✅ Redirect if not logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: student-login.php");
    exit();
}

require_once("../db_connection.php");

// ✅ Get logged-in student's ID
$student_id = $_SESSION['student_id'];

// ✅ Fetch student details from database
$query = "SELECT * FROM students WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student_data = $result->fetch_assoc();
} else {
    die("Student not found in database");
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile | Hostel Student</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f3f4f6;
    }
    .sidebar {
      width: 300px;
      background: linear-gradient(135deg, #1e343b 0%, #023c58 100%);
      height: 100vh;
    }
    .sidebar a {
      margin: 0 8px;
      border-radius: 8px;
      padding: 10px 12px;
      transition: all 0.3s;
    }
    .sidebar a:hover {
      transform: translateX(5px);
      background-color: #334155;
    }
    .btn-primary {
      background-color: #3b82f6;
      color: rgb(184, 248, 248);
      padding: 0.75rem 1.5rem;
      border-radius: 0.5rem;
      transition: all 0.2s;
      font-weight: 500;
    }
    .btn-primary:hover {
      background-color: #3f1c62;
      transform: translateY(-1px);
    }
    .form-input {
      border: 1px solid #8ba6d0;
      border-radius: 0.5rem;
      padding: 0.75rem 1rem;
      width: 100%;
      transition: all 0.2s;
    }
    .form-input:focus {
      outline: none;
      border-color: #923bf6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
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
        <li><a href="student-profile.php" class="flex items-center space-x-3 p-3 bg-teal-900"><i class="fas fa-user text-xl w-6 text-center"></i><span class="text-md">Profile</span></a></li>
        <li><a href="student-guestlog.php" class="flex items-center space-x-3 p-3"><i class="fas fa-users text-xl w-6 text-center"></i><span class="text-md">Guest Log</span></a></li>
        <li><a href="student-fees.php" class="flex items-center space-x-3 p-3"><i class="fas fa-money-bill-wave text-xl w-6 text-center"></i><span class="text-md">Fee Payments</span></a></li>
        <li><a href="student-attendance.php" class="flex items-center space-x-3 p-3"><i class="fas fa-clipboard-check text-xl w-6 text-center"></i><span class="text-md">Attendance</span></a></li>
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
        <h1 class="text-xl font-bold text-gray-800">My Profile</h1>
      </div>
      <div class="flex items-center space-x-4">
        <i class="fas fa-bell text-gray-600"></i>
        <div class="flex items-center space-x-2">
          <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white"><i class="fas fa-user"></i></div>
          <span class="hidden md:inline"><?php echo htmlspecialchars($_SESSION['student_name']); ?></span>
        </div>
      </div>
    </header>

    <main class="p-6">
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Personal Information</h2>

        <form id="updateProfileForm" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-gray-700 mb-2">Full Name</label>
              <input type="text" name="full_name" class="form-input w-full" value="<?php echo htmlspecialchars($student_data['full_name']); ?>" required>
            </div>
            <div>
              <label class="block text-gray-700 mb-2">Registration Number</label>
              <input type="text" class="form-input w-full" value="<?php echo htmlspecialchars($student_data['registration_number']); ?>" readonly>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-gray-700 mb-2">Email</label>
              <input type="email" name="email" class="form-input w-full" value="<?php echo htmlspecialchars($student_data['email']); ?>" required>
            </div>
            <div>
              <label class="block text-gray-700 mb-2">Phone</label>
              <input type="tel" name="phone" class="form-input w-full" value="<?php echo htmlspecialchars($student_data['phone']); ?>" required>
            </div>
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 mb-2">Address</label>
            <textarea name="address" class="form-input w-full"><?php echo htmlspecialchars($student_data['address']); ?></textarea>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-gray-700 mb-2">Date of Birth</label>
              <input type="date" name="dob" class="form-input w-full" value="<?php echo htmlspecialchars($student_data['dob']); ?>">
            </div>
            <div>
              <label class="block text-gray-700 mb-2">Blood Group</label>
              <input type="text" name="blood_group" class="form-input w-full" value="<?php echo htmlspecialchars($student_data['blood_group']); ?>">
            </div>
          </div>

          <h2 class="text-lg font-semibold mb-4 mt-6">Hostel Information</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
              <label class="block text-gray-700 mb-2">Block</label>
              <input type="text" class="form-input w-full" value="<?php echo htmlspecialchars($student_data['block']); ?>" readonly>
            </div>
            <div>
              <label class="block text-gray-700 mb-2">Room Number</label>
              <input type="text" class="form-input w-full" value="<?php echo htmlspecialchars($student_data['room_number']); ?>" readonly>
            </div>
            <div>
              <label class="block text-gray-700 mb-2">Room Type</label>
              <input type="text" class="form-input w-full" value="<?php echo htmlspecialchars($student_data['room_type']); ?>" readonly>
            </div>
          </div>

          <button type="submit" id="saveBtn" class="btn-primary">
            <i class="fas fa-save mr-2"></i> Update Profile
          </button>
        </form>

        <!-- Success Message -->
        <p id="successMessage" class="mt-4 text-green-600 font-medium hidden">
          ✅ Profile updated successfully!
        </p>
      </div>
    </main>
  </div>

  <script>
    const form = document.getElementById('updateProfileForm');
    const saveBtn = document.getElementById('saveBtn');
    const successMsg = document.getElementById('successMessage');

    form.addEventListener('submit', async function(e) {
      e.preventDefault();
      saveBtn.disabled = true;
      saveBtn.textContent = "Updating...";

      const formData = new FormData(form);

      const response = await fetch('update_student_profile.php', {
        method: 'POST',
        body: formData
      });

      const result = await response.text();
      saveBtn.disabled = false;
      saveBtn.textContent = "Update Profile";

      if (result.includes("success")) {
        successMsg.classList.remove('hidden');
        successMsg.textContent = "✅ Profile updated successfully!";
      } else {
        successMsg.classList.remove('hidden');
        successMsg.classList.add('text-red-600');
        successMsg.textContent = "❌ Failed to update profile.";
      }
    });
  </script>
</body>
</html>
