<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>AI Assistant | Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body { font-family: 'Poppins', sans-serif; background: #f3f4f6; }
    .chat-container { height: 550px; display: flex; flex-direction: column; }
    .chat-box { flex: 1; overflow-y: auto; border: 1px solid #e5e7eb; background: #fff; padding: 1rem; border-radius: 0.5rem; }
    .message { margin-bottom: 1rem; }
    .message.admin { text-align: right; }
    .message.ai { text-align: left; }
  </style>
</head>

<body class="p-8">
  <div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-4 text-gray-800 flex items-center gap-2">
      <i class="fas fa-robot text-blue-600"></i> Admin AI Assistant
    </h1>

    <div class="chat-container bg-white shadow rounded-lg p-4">
      <div id="chat-box" class="chat-box mb-4"></div>

      <form id="chat-form" class="flex space-x-3">
        <input type="text" id="chat-input" name="prompt"
          placeholder="Ask the AI Assistant (e.g., summarize maintenance requests)..."
          class="flex-1 border px-3 py-2 rounded-md focus:ring-2 focus:ring-blue-500" required>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
          <i class="fas fa-paper-plane"></i>
        </button>
      </form>
    </div>
  </div>

  <script>
  document.getElementById('chat-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const input = document.getElementById('chat-input');
    const chatBox = document.getElementById('chat-box');
    const message = input.value.trim();
    if (!message) return;

    // Show admin message
    chatBox.innerHTML += `<div class='message admin'><b>You:</b> ${message}</div>`;
    input.value = "";

    // Send to PHP backend
    const res = await fetch('llm_admin.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'prompt=' + encodeURIComponent(message)
    });
    const data = await res.json();

    // Show AI response
    chatBox.innerHTML += `<div class='message ai text-blue-700'><b>AI:</b> ${data.response}</div>`;
    chatBox.scrollTop = chatBox.scrollHeight;
  });
  </script>
</body>
</html>
