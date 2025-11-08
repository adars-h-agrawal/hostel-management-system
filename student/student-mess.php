<?php
session_start();
include('../db_connection.php'); // adjust if needed

// Redirect if not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student-login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_message'])) {
    $feedback_type = mysqli_real_escape_string($conn, $_POST['feedback_type']);
    $feedback_message = mysqli_real_escape_string($conn, $_POST['feedback_message']);
    $rating_quality = intval($_POST['rating_quality'] ?? 0);
    $rating_hygiene = intval($_POST['rating_hygiene'] ?? 0);
    $rating_service = intval($_POST['rating_service'] ?? 0);

    $insert = "INSERT INTO mess_feedback (student_id, rating_quality, rating_hygiene, rating_service, message)
               VALUES ('$student_id', '$rating_quality', '$rating_hygiene', '$rating_service', '$feedback_message')";
    mysqli_query($conn, $insert);
}

// Fetch latest feedback
$latest_feedback = mysqli_query($conn, "SELECT * FROM mess_feedback WHERE student_id = '$student_id' ORDER BY created_at DESC LIMIT 1");
$feedback = mysqli_num_rows($latest_feedback) > 0 ? mysqli_fetch_assoc($latest_feedback) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mess | Hostel Student</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }
        .active-tab { border-bottom: 3px solid #2563eb; color: #2563eb; }
        .form-input { border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.75rem 1rem; width: 100%; }
        .form-input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(59,130,246,0.2); }
        .btn-primary { background-color: #2563eb; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; transition: 0.2s; }
        .btn-primary:hover { background-color: #1d4ed8; }
    </style>
</head>
<body class="flex h-screen">
    <?php include('sidebar.php'); ?>

    <div class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow p-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-800">Mess Management</h1>
            <div class="flex items-center space-x-4">
                <i class="fas fa-bell text-gray-600"></i>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="hidden md:inline">Student</span>
                </div>
            </div>
        </header>

        <main class="p-6">
            <!-- Tabs -->
            <div class="flex border-b border-gray-200 mb-6">
                <button class="tab-btn px-4 py-2 font-medium active-tab" data-tab="menu">🍽️ Weekly Menu</button>
                <button class="tab-btn px-4 py-2 font-medium text-gray-600" data-tab="schedule">⏰ Mess Schedule</button>
                <button class="tab-btn px-4 py-2 font-medium text-gray-600" data-tab="preferences">❤️ Preferences</button>
                <button class="tab-btn px-4 py-2 font-medium text-gray-600" data-tab="feedback">💬 Feedback</button>
            </div>

            <!-- Weekly Menu -->
            <div class="tab-content" id="menu-tab">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">This Week's Menu</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <?php
                        $menu = [
                            'Monday' => ['Breakfast'=>'Poha, Tea','Lunch'=>'Rice, Dal, Veg Curry','Dinner'=>'Chapati, Paneer Masala'],
                            'Tuesday' => ['Breakfast'=>'Idli, Sambar','Lunch'=>'Rajma, Rice','Dinner'=>'Paratha, Pickle'],
                            'Wednesday' => ['Breakfast'=>'Upma, Tea','Lunch'=>'Biryani, Raita','Dinner'=>'Dal Fry, Chapati'],
                            'Thursday' => ['Breakfast'=>'Dosa, Coffee','Lunch'=>'Veg Curry, Rice','Dinner'=>'Kadhi, Khichdi'],
                            'Friday' => ['Breakfast'=>'Aloo Paratha, Curd','Lunch'=>'Dal Tadka, Rice','Dinner'=>'Paneer Butter Masala'],
                            'Saturday' => ['Breakfast'=>'Sandwich, Juice','Lunch'=>'Fried Rice, Manchurian','Dinner'=>'Dal Makhani, Naan'],
                            'Sunday' => ['Breakfast'=>'Puri, Chole','Lunch'=>'Special Thali','Dinner'=>'Pulao, Raita'],
                        ];
                        foreach ($menu as $day => $meals): ?>
                            <div class="p-4 border rounded-lg shadow-sm hover:shadow-md">
                                <h3 class="font-semibold text-gray-800 mb-3"><?= $day ?></h3>
                                <?php foreach ($meals as $meal => $item): ?>
                                    <p><span class="font-medium"><?= $meal ?>:</span> <?= $item ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="tab-content hidden" id="schedule-tab">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Mess Schedule</h2>
                    <ul class="text-gray-700 space-y-2">
                        <li><b>Breakfast:</b> 7:30 AM - 9:00 AM</li>
                        <li><b>Lunch:</b> 12:30 PM - 2:30 PM</li>
                        <li><b>Snacks:</b> 4:30 PM - 5:30 PM</li>
                        <li><b>Dinner:</b> 8:00 PM - 10:00 PM</li>
                    </ul>
                </div>
            </div>

            <!-- Preferences -->
            <div class="tab-content hidden" id="preferences-tab">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Dietary Preferences</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-semibold mb-2">Diet Type</h3>
                            <ul class="space-y-2">
                                <li><input type="radio" name="diet" checked> Vegetarian</li>
                                <li><input type="radio" name="diet"> Non-Vegetarian</li>
                                <li><input type="radio" name="diet"> Vegan</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2">Allergies</h3>
                            <ul class="space-y-2">
                                <li><input type="checkbox"> Peanuts</li>
                                <li><input type="checkbox"> Dairy</li>
                                <li><input type="checkbox"> Gluten</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feedback -->
            <div class="tab-content hidden" id="feedback-tab">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Mess Feedback</h2>
                    <form method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block mb-2">Quality</label>
                                <select name="rating_quality" class="form-input" required>
                                    <option value="">Select</option>
                                    <?php for ($i=1;$i<=5;$i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?> ★</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block mb-2">Hygiene</label>
                                <select name="rating_hygiene" class="form-input" required>
                                    <option value="">Select</option>
                                    <?php for ($i=1;$i<=5;$i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?> ★</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block mb-2">Service</label>
                                <select name="rating_service" class="form-input" required>
                                    <option value="">Select</option>
                                    <?php for ($i=1;$i<=5;$i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?> ★</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block mb-2">Feedback Type</label>
                            <select name="feedback_type" class="form-input">
                                <option>Compliment</option>
                                <option>Suggestion</option>
                                <option>Complaint</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block mb-2">Your Message</label>
                            <textarea name="feedback_message" rows="4" class="form-input" placeholder="Your feedback helps us improve..." required></textarea>
                        </div>

                        <button type="submit" class="btn-primary"><i class="fas fa-paper-plane mr-2"></i>Submit Feedback</button>
                    </form>

                    <?php if ($feedback): ?>
                        <div class="mt-6 bg-gray-50 p-4 rounded border">
                            <h3 class="font-semibold text-gray-800 mb-2">Your Latest Feedback</h3>
                            <p><b>Quality:</b> <?= $feedback['rating_quality'] ?> ★</p>
                            <p><b>Hygiene:</b> <?= $feedback['rating_hygiene'] ?> ★</p>
                            <p><b>Service:</b> <?= $feedback['rating_service'] ?> ★</p>
                            <p class="mt-2"><b>Message:</b> <?= htmlspecialchars($feedback['message']) ?></p>
                            <p class="text-xs text-gray-500 mt-1">Submitted on <?= date('d M Y', strtotime($feedback['created_at'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Tabs
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active-tab'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
                btn.classList.add('active-tab');
                document.getElementById(btn.dataset.tab + '-tab').classList.remove('hidden');
            });
        });
    </script>
</body>
</html>
