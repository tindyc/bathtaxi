<?php
header('Content-Type: application/json');

$progressFile = 'progress.json';
$data = json_decode(file_get_contents($progressFile), true);

if ($_POST['action'] === 'increment') {
    if ($data['done'] < $data['total']) {
        $data['done']++;

        // Save to file
        file_put_contents($progressFile, json_encode($data, JSON_PRETTY_PRINT));
    }
}

// Calculate stats
$remaining = $data['total'] - $data['done'];
$percent = ($data['done'] / $data['total']) * 100;

// Encouraging messages
$messages = [
    "Great job! Keep it going 🚀",
    "Another one down! 💪",
    "You're getting closer to your goal 🎯",
    "Keep pushing, you're doing amazing! 🙌"
];
$message = $messages[array_rand($messages)];

// Send response
echo json_encode([
    'done' => $data['done'],
    'total' => $data['total'],
    'remaining' => $remaining,
    'percent' => $percent,
    'message' => $message
]);
