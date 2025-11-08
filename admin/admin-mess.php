<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: admin-login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mess Management | Hostel Admin</title>

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

    /* Sidebar */
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

    .menu-box {
      background: rgba(255, 255, 255, 0.85);
      border-radius: 12px;
      box-shadow: 0 6px 12px rgba(0,0,0,0.1);
      padding: 1.5rem;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .menu-box:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 18px rgba(0,0,0,0.15);
    }
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
        <li><a href="admin-mess.php" class="active"><i class="fas fa-utensils w-5 text-center"></i> Mess Details</a></li>
        <li><a href="admin-payments.php"><i class="fas fa-wallet w-5 text-center"></i> Payments</a></li>
        <li><a href="admin-maintenance.php"><i class="fas fa-tools w-5 text-center"></i> Maintenance</a></li>
        <li><a href="admin-complaints.php"><i class="fas fa-exclamation-circle w-5 text-center"></i> Complaints</a></li>
        <li><a href="../index.php" class="text-red-300 hover:text-white"><i class="fas fa-sign-out-alt w-5 text-center"></i> Logout</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 ml-[270px] p-8 bg-white/80 backdrop-blur overflow-auto">
    <header class="mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Mess Management</h1>
      <p class="text-gray-600">Weekly Menu Overview</p>
    </header>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div class="menu-box">
        <h3 class="font-bold text-xl mb-3 text-blue-600">Monday</h3>
        <p><b>Breakfast:</b> Poha, Bread, Butter, Tea</p>
        <p><b>Lunch:</b> Rice, Dal, Mix Veg, Chapati</p>
        <p><b>Dinner:</b> Roti, Chana Masala, Rice</p>
      </div>

      <div class="menu-box">
        <h3 class="font-bold text-xl mb-3 text-blue-600">Tuesday</h3>
        <p><b>Breakfast:</b> Idli, Sambar, Chutney</p>
        <p><b>Lunch:</b> Jeera Rice, Rajma, Chapati</p>
        <p><b>Dinner:</b> Paratha, Dal Fry</p>
      </div>

      <div class="menu-box">
        <h3 class="font-bold text-xl mb-3 text-blue-600">Wednesday</h3>
        <p><b>Breakfast:</b> Upma, Bread Jam</p>
        <p><b>Lunch:</b> Veg Biryani, Raita</p>
        <p><b>Dinner:</b> Roti, Palak Paneer</p>
      </div>

      <div class="menu-box">
        <h3 class="font-bold text-xl mb-3 text-blue-600">Thursday</h3>
        <p><b>Breakfast:</b> Dosa, Chutney</p>
        <p><b>Lunch:</b> Rice, Sambar, Chapati</p>
        <p><b>Dinner:</b> Khichdi, Kadhi</p>
      </div>

      <div class="menu-box">
        <h3 class="font-bold text-xl mb-3 text-blue-600">Friday</h3>
        <p><b>Breakfast:</b> Aloo Paratha, Curd</p>
        <p><b>Lunch:</b> Rice, Dal Tadka</p>
        <p><b>Dinner:</b> Roti, Matar Paneer</p>
      </div>

      <div class="menu-box">
        <h3 class="font-bold text-xl mb-3 text-blue-600">Saturday</h3>
        <p><b>Breakfast:</b> Sandwich, Juice</p>
        <p><b>Lunch:</b> Fried Rice, Manchurian</p>
        <p><b>Dinner:</b> Roti, Dal Makhani</p>
      </div>

      <div class="menu-box">
        <h3 class="font-bold text-xl mb-3 text-blue-600">Sunday</h3>
        <p><b>Breakfast:</b> Puri, Chole</p>
        <p><b>Lunch:</b> Special Thali</p>
        <p><b>Dinner:</b> Pulao, Raita</p>
      </div>
    </div>
  </main>
</body>
</html>
