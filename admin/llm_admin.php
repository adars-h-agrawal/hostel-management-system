<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prompt = $_POST['prompt'] ?? '';

    if (!$prompt) {
        echo json_encode(['response' => '⚠️ Please enter a question.']);
        exit;
    }

    // Send prompt to Python backend (Flask running at localhost:5000)
    $url = "http://127.0.0.1:5000/chat";
    $data = json_encode(['prompt' => $prompt]);

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => $data,
            'timeout' => 30
        ]
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        echo json_encode(['response' => '❌ Could not connect to AI server.']);
    } else {
        echo $result; // Already JSON from Python
    }
}
?>
