<?php
session_start();
require_once("../db_connection.php");

if (!isset($_SESSION['loggedin'])) {
    header("Location: student-login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$fees = []; // ✅ Initialize before usage

// Fetch all fee details for the logged-in student
$query = "SELECT fee_id, semester, amount, status, payment_date 
          FROM fees 
          WHERE student_id = ? 
          ORDER BY semester ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$total_due = 0;
$total_paid = 0;

while ($row = $result->fetch_assoc()) {
    if ($row['status'] === 'Paid') {
        $total_paid += $row['amount'];
    } else {
        $total_due += $row['amount'];
    }
    $fees[] = $row;
}
$balance = $total_due;
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fee Payments | Hostel Student</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }
    .sidebar { width: 300px; background: linear-gradient(135deg, #1e343b 0%, #023c58 100%); height: 100vh; transition: all 0.3s; }
    .sidebar a { transition: all 0.3s; margin: 0 8px; border-radius: 8px; padding: 10px 12px; display: flex; align-items: center; }
    .sidebar a:hover { transform: translateX(5px); background-color: #334155; }
    .btn-primary { background-color: #3b82f6; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; transition: all 0.2s; font-weight: 500; }
    .btn-primary:hover { background-color: #2563eb; transform: translateY(-1px); }
    .highlight-card { background: #faf5ff; border-left: 4px solid #7c3aed; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .accent-text { color: #1a044c; }
  </style>
</head>

<body class="flex h-screen">
  <!-- Sidebar -->
  <div class="sidebar text-white p-6 hidden md:block">
    <div class="flex items-center justify-center p-3 mb-10"><i class="fas fa-user-graduate text-4xl"></i></div>
    <nav>
      <ul class="space-y-2">
        <li><a href="student-dashboard.php"><i class="fas fa-arrow-left w-6"></i><span class="ml-2">Back to Dashboard</span></a></li>
        <li><a href="student-profile.php"><i class="fas fa-user w-6"></i><span class="ml-2">Profile</span></a></li>
        <li><a href="student-fees.php" class="bg-teal-900"><i class="fas fa-money-bill-wave w-6"></i><span class="ml-2">Fee Payments</span></a></li>
        <li><a href="student-attendance.php"><i class="fas fa-clipboard-check w-6"></i><span class="ml-2">Attendance</span></a></li>
        <li><a href="student-maintenance.php"><i class="fas fa-tools w-6"></i><span class="ml-2">Maintenance</span></a></li>
        <li><a href="student-notices.php"><i class="fas fa-bell w-6"></i><span class="ml-2">Notices</span></a></li>
        <li><a href="student-complaints.php"><i class="fas fa-exclamation-triangle w-6"></i><span class="ml-2">Complaints</span></a></li>
        <li><a href="../index.php"><i class="fas fa-sign-out-alt w-6"></i><span class="ml-2">Logout</span></a></li>
      </ul>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="flex-1 overflow-auto">
    <header class="bg-white shadow p-4 flex justify-between items-center">
      <h1 class="text-xl font-bold text-gray-800">Fee Payments</h1>
      <div class="flex items-center space-x-4">
        <i class="fas fa-bell text-gray-600"></i>
        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white"><i class="fas fa-user"></i></div>
        <span class="hidden md:inline"><?php echo htmlspecialchars($_SESSION['student_name']); ?></span>
      </div>
    </header>

    <!-- Main Section -->
    <main class="p-6">
      <!-- Summary -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="p-6 bg-white shadow rounded-md"><h3 class="text-sm text-gray-500">Total Due</h3><p class="text-2xl font-bold text-blue-600">$<?php echo number_format($total_due, 2); ?></p></div>
        <div class="p-6 bg-white shadow rounded-md"><h3 class="text-sm text-gray-500">Paid</h3><p class="text-2xl font-bold text-green-600">$<?php echo number_format($total_paid, 2); ?></p></div>
        <div class="p-6 bg-white shadow rounded-md"><h3 class="text-sm text-gray-500">Balance</h3><p class="text-2xl font-bold text-red-600">$<?php echo number_format($balance, 2); ?></p></div>
      </div>

      <!-- Payment Form -->
      <div class="highlight-card p-6 rounded-md mb-6">
        <h2 class="text-lg font-semibold mb-4 accent-text">Make a Payment</h2>
        <form id="paymentForm">
          <div class="mb-4">
            <label class="block text-gray-700 mb-2 font-medium">Select Semester</label>
            <select name="semester" class="form-input" required>
              <?php if (!empty($fees)): ?>
                <?php foreach ($fees as $f): ?>
                  <?php if ($f['status'] === 'Unpaid'): ?>
                    <option value="<?php echo htmlspecialchars($f['semester']); ?>">
                      <?php echo htmlspecialchars($f['semester']); ?> — $<?php echo $f['amount']; ?>
                    </option>
                  <?php endif; ?>
                <?php endforeach; ?>
              <?php else: ?>
                <option disabled>No unpaid semesters available</option>
              <?php endif; ?>
            </select>
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 mb-2 font-medium">Payment Method</label>
            <select name="payment_method" class="form-input" required>
              <option value="">Select Method</option>
              <option>Credit/Debit Card</option>
              <option>Bank Transfer</option>
              <option>UPI</option>
            </select>
          </div>
          <button type="submit" class="btn-primary w-full"><i class="fas fa-credit-card mr-2"></i> Proceed to Pay</button>
        </form>
      </div>

      <!-- Payment History -->
      <div class="bg-white rounded-md shadow overflow-hidden">
        <div class="p-4 border-b bg-blue-50"><h2 class="text-lg font-semibold text-gray-700">Fee History</h2></div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-600">Semester</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-600">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-600">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-600">Payment Date</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <?php if (!empty($fees)): ?>
                <?php foreach ($fees as $f): ?>
                  <tr>
                    <td class="px-6 py-3 text-sm text-gray-700"><?php echo htmlspecialchars($f['semester']); ?></td>
                    <td class="px-6 py-3 text-sm text-gray-700">$<?php echo number_format($f['amount'], 2); ?></td>
                    <td class="px-6 py-3">
                      <span class="px-2 py-1 text-xs rounded-full <?php echo ($f['status']=='Paid')?'bg-green-100 text-green-800':'bg-yellow-100 text-yellow-800'; ?>">
                        <?php echo htmlspecialchars($f['status']); ?>
                      </span>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-700">
                      <?php echo $f['payment_date'] ? htmlspecialchars($f['payment_date']) : '-'; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="4" class="text-center text-gray-500 py-4">No fee records available.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <script>
    document.getElementById('paymentForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const response = await fetch('backend/student_fees.php', { method: 'POST', body: formData });
      const data = await response.json();
      alert(data.success ? data.message : 'Error: ' + data.error);
      if (data.success) location.reload();
    });
  </script>
</body>
</html>
