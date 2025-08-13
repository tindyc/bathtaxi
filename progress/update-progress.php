<?php
header('Content-Type: application/json');

$file = __DIR__ . '/progress.json';

// Read current file or create defaults
if (file_exists($file)) {
    $data = json_decode(file_get_contents($file), true);
} else {
    $data = [
        'done' => 0,
        'total' => 172
    ];
}

if ($_POST['action'] === 'increment') {
    $data['done']++;
}

// Prevent exceeding total
if ($data['done'] > $data['total']) {
    $data['done'] = $data['total'];
}

// Calculate remaining and percent
$data['remaining'] = $data['total'] - $data['done'];
$data['percent'] = round(($data['done'] / $data['total']) * 100, 2);

// Funny motivational messages
if ($data['done'] >= $data['total']) {
    $data['message'] = "🎉 Congratulations! You can now go buy Tindy a meal 🍔";
} elseif ($data['done'] < 10) {
    $data['message'] = "🚀 Starting strong, Alan!";
} else {
    $data['message'] = "💪 Keep going Alan, work for it like you're in the gym!";
}

// Save progress
file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

// Send response
echo json_encode($data);
