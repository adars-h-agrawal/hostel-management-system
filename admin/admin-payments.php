<?php
session_start();
require_once('../db_connection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fee Management | Hostel Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f3f4f6;
      overflow-x: hidden;
    }

    .sidebar {
      width: 300px;
      position: fixed;
      top: 0; left: 0;
      height: 100vh;
      background: linear-gradient(135deg, rgba(30,41,59,0.9), rgba(15,23,42,0.88));
      box-shadow: 4px 0 15px rgba(0,0,0,0.25);
      z-index: 40;
      transition: left 0.3s ease-in-out;
    }
    .sidebar a { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; transition: all 0.3s; margin: 0 8px; }
    .sidebar a:hover { transform: translateX(5px); background-color: #334155; }
    .main-content { margin-left: 300px; padding: 1.5rem; width: calc(100% - 300px); }
    @media (max-width: 768px) { .sidebar { left: -300px; } .sidebar.active { left: 0; } .main-content { margin-left: 0; width: 100%; } }
  </style>
</head>

<body class="bg-gray-50 h-screen">
  <div class="flex h-full">

    <!-- Sidebar -->
    <div class="sidebar text-white p-6 hidden md:block" id="sidebar">
      <div class="flex items-center justify-center p-3 mb-6"><i class="fas fa-user-shield text-4xl"></i></div>
      <nav>
        <ul class="space-y-2">
          <li><a href="admin-dashboard.php"><i class="fas fa-arrow-left"></i>Back</a></li>
          <li><a href="admin-students.php"><i class="fas fa-users"></i>Students</a></li>
          <li><a href="admin-notices.php"><i class="fas fa-bullhorn"></i>Notices</a></li>
          <li><a href="admin-payments.php" class="bg-gray-700"><i class="fas fa-money-check-alt"></i>Fee Payments</a></li>
          <li><a href="admin-maintenance.php"><i class="fas fa-tools"></i>Maintenance</a></li>
          <li><a href="admin-complaints.php"><i class="fas fa-exclamation-circle"></i>Complaints</a></li>
          <li><a href="../index.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
        </ul>
      </nav>
    </div>

    <!-- Main -->
    <div class="main-content">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Fee Management</h1>
        <button id="toggleSidebar" class="md:hidden text-gray-700"><i class="fas fa-bars text-2xl"></i></button>
      </div>

      <div class="flex flex-wrap gap-3 mb-6">
        <button id="generateInvoicesBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          <i class="fas fa-file-invoice mr-2"></i> Generate Invoices
        </button>
        <button id="sendRemindersBtn" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
          <i class="fas fa-envelope mr-2"></i> Send Reminders
        </button>
        <button id="refreshPaymentsBtn" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
          <i class="fas fa-sync mr-2"></i> Refresh
        </button>
      </div>

      <!-- Payments Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-4 border-b">
          <h2 class="text-lg font-semibold">Fee Payment Records</h2>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Student Name</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Reg No</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Semester</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Payment Date</th>
              </tr>
            </thead>
            <tbody id="paymentTableBody">
              <tr><td colspan="6" class="text-center py-4">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Set Semester Fee -->
      <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h2 class="text-lg font-semibold mb-4">Set Semester Fee</h2>
        <form id="setFeeForm">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-700 mb-2">Semester</label>
              <input type="text" name="semester" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div>
              <label class="block text-gray-700 mb-2">Amount</label>
              <input type="number" name="amount" class="w-full px-3 py-2 border rounded" required>
            </div>
          </div>
          <div class="mt-4">
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
              <i class="fas fa-save mr-2"></i> Update Fee
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
  $(document).ready(function() {
    $('#toggleSidebar').click(() => $('#sidebar').toggleClass('active'));

    function loadPayments() {
      $('#paymentTableBody').html('<tr><td colspan="6" class="text-center py-4">Loading...</td></tr>');
      $.get('includes/get_fee_payment.php', function(data) {
        $('#paymentTableBody').html(data);
      });
    }

    $('#generateInvoicesBtn').click(function() {
      if (confirm('Generate invoices for all unpaid students?')) {
        $.post('includes/generate_invoices.php', resp => { alert(resp); loadPayments(); });
      }
    });

    $('#sendRemindersBtn').click(function() {
      if (confirm('Send reminders to all students with unpaid fees?')) {
        $.post('includes/send_reminders.php', resp => alert(resp));
      }
    });

    $('#refreshPaymentsBtn').click(() => loadPayments());

    $('#setFeeForm').submit(function(e) {
      e.preventDefault();
      $.post('includes/set_semester_fee.php', $(this).serialize(), resp => alert(resp));
    });

    loadPayments();
  });
  </script>
</body>
</html>
