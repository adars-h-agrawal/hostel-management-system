<?php
session_start();
include('../db_connection.php'); // adjust if needed

// Ensure logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student-login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Create uploads folder if missing
$uploadDir = dirname(__DIR__) . "/uploads/guest_ids/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Handle new guest request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guest_name = mysqli_real_escape_string($conn, $_POST['guest_name']);
    $relationship = mysqli_real_escape_string($conn, $_POST['relationship']);
    $visit_date = mysqli_real_escape_string($conn, $_POST['visit_date']);
    $purpose = mysqli_real_escape_string($conn, $_POST['purpose']);
    $id_proof = null;

    // Handle ID proof upload
    if (!empty($_FILES['id_proof']['name'])) {
        $fileName = time() . "_" . basename($_FILES["id_proof"]["name"]);
        $targetFile = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES["id_proof"]["tmp_name"], $targetFile)) {
            $id_proof = "uploads/guest_ids/" . $fileName;
        }
    }

    // Insert into DB
    $query = "INSERT INTO guest_log (student_id, guest_name, relationship, visit_date, purpose, status)
              VALUES ('$student_id', '$guest_name', '$relationship', '$visit_date', '$purpose', 'Pending')";
    mysqli_query($conn, $query);
}

// Fetch guest history
$guests = mysqli_query($conn, "SELECT * FROM guest_log WHERE student_id = '$student_id' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Log | Hostel Student</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }
        .sidebar { transition: all 0.3s; width: 300px; background: linear-gradient(135deg, #1e343b 0%, #023c58 100%); height: 100vh; }
        .sidebar a { transition: all 0.3s; margin: 0 8px; border-radius: 8px; padding: 10px 12px; }
        .sidebar a:hover { transform: translateX(5px); background-color: #334155; }
        .btn-primary { background-color: #2563eb; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 500; transition: 0.2s; }
        .btn-primary:hover { background-color: #1d4ed8; }
    </style>
</head>
<body class="flex h-screen">
    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow p-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <button class="md:hidden text-gray-600"><i class="fas fa-bars"></i></button>
                <h1 class="text-xl font-bold text-gray-800">Guest Log</h1>
            </div>
            <div class="flex items-center space-x-4">
                <i class="fas fa-bell text-gray-600"></i>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white"><i class="fas fa-user"></i></div>
                    <span class="hidden md:inline">Student</span>
                </div>
            </div>
        </header>

        <!-- Guest Log Content -->
        <main class="p-6">
            <!-- Form Section -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Register New Guest</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 mb-2">Guest Name</label>
                            <input type="text" name="guest_name" class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Relationship</label>
                            <input type="text" name="relationship" class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 mb-2">Visit Date</label>
                            <input type="date" name="visit_date" class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">ID Proof (Optional)</label>
                            <input type="file" name="id_proof" accept="image/*,application/pdf" class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Purpose</label>
                        <textarea name="purpose" rows="3" class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Mention purpose of visit..." required></textarea>
                    </div>
                    <button type="submit" class="btn-primary w-full">
                        <i class="fas fa-user-plus mr-2"></i> Submit Guest Request
                    </button>
                </form>
            </div>

            <!-- Guest History -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold">Guest Visit History</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Guest Name</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Relationship</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Purpose</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">ID Proof</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (mysqli_num_rows($guests) > 0): ?>
                                <?php while ($g = mysqli_fetch_assoc($guests)): ?>
                                    <tr>
                                        <td class="px-6 py-3 whitespace-nowrap"><?= date('d M Y', strtotime($g['visit_date'])) ?></td>
                                        <td class="px-6 py-3"><?= htmlspecialchars($g['guest_name']) ?></td>
                                        <td class="px-6 py-3"><?= htmlspecialchars($g['relationship']) ?></td>
                                        <td class="px-6 py-3"><?= htmlspecialchars($g['purpose']) ?></td>
                                        <td class="px-6 py-3">
                                            <span class="px-2 py-1 rounded-full text-xs 
                                                <?php
                                                    echo $g['status'] == 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                                                         ($g['status'] == 'Approved' ? 'bg-green-100 text-green-800' :
                                                         'bg-red-100 text-red-800');
                                                ?>">
                                                <?= $g['status'] ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-3">
                                            <?php if (!empty($g['id_proof'])): ?>
                                                <a href="../<?= htmlspecialchars($g['id_proof']) ?>" target="_blank" class="text-blue-600 hover:underline text-xs">View</a>
                                            <?php else: ?>
                                                <span class="text-gray-400 text-xs">None</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No guest records yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.querySelector('.md\\:hidden').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('hidden');
        });
    </script>
</body>
</html>
