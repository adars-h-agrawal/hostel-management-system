<?php
session_start();
include('../db_connection.php'); // use your actual connection file path

// Ensure student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student-login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Handle new request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_type = mysqli_real_escape_string($conn, $_POST['request_type']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $image_path = null;

    // Create uploads/maintenance folder if not exists
    $uploadDir = dirname(__DIR__) . "/uploads/maintenance/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Handle optional image upload
    if (!empty($_FILES['image']['name'])) {
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            // Save relative path for display
            $image_path = "uploads/maintenance/" . $fileName;
        }
    }

    $query = "INSERT INTO maintenance (student_id, request_type, description, image_path)
              VALUES ('$student_id', '$request_type', '$description', '$image_path')";
    mysqli_query($conn, $query);
}

// Fetch student’s requests
$result = mysqli_query($conn, "SELECT * FROM maintenance WHERE student_id = '$student_id' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maintenance Requests | Hostel Student</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="flex h-screen">
    <?php include('sidebar.php'); ?>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow p-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-800">Maintenance Requests</h1>
            <div class="flex items-center space-x-4">
                <i class="fas fa-bell text-gray-600"></i>
                <span class="text-gray-700">Student</span>
            </div>
        </header>

        <main class="p-6">
            <!-- Submit Request Form -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Submit New Request</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Request Type</label>
                        <select name="request_type" class="w-full border px-3 py-2 rounded-md" required>
                            <option value="">Select Type</option>
                            <option>Plumbing</option>
                            <option>Electrical</option>
                            <option>Furniture</option>
                            <option>Cleaning</option>
                            <option>Other</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="4" class="w-full border px-3 py-2 rounded-md" placeholder="Describe the issue..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Upload Image (Optional)</label>
                        <input type="file" name="image" accept="image/*" class="w-full border px-3 py-2 rounded-md">
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-paper-plane mr-2"></i> Submit Request
                    </button>
                </form>
            </div>

            <!-- View Requests -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold">My Requests</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <div class="p-4 hover:bg-gray-50 flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($row['request_type']); ?></h3>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($row['description']); ?></p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Submitted: <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                                    </p>
                                    <?php if (!empty($row['image_path'])): ?>
                                        <a href="../<?php echo htmlspecialchars($row['image_path']); ?>" target="_blank" class="text-blue-500 text-xs mt-1 inline-block">View Image</a>
                                    <?php endif; ?>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    <?php echo $row['status'] == 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                                           ($row['status'] == 'In Progress' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'); ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="p-4 text-sm text-gray-500">No maintenance requests yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
