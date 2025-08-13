<?php
header('Content-Type: application/json');

$file = __DIR__ . '/progress.json';
$lastMessageFile = __DIR__ . '/last_message.txt';

// Read current file or create defaults
if (file_exists($file)) {
    $data = json_decode(file_get_contents($file), true);
} else {
    $data = [
        'done' => 0,
        'total' => 172
    ];
}

// Increment if requested
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
$funnyMessages = [
    "💪 Look at you, working out… your fingers!",
    "🚖 Keep going, the taxi won't pay for itself!",
    "🍩 No more ketchup crisps until you hit the next milestone!",
    "🏃 You're not running… but your jobs are adding up!",
    "📈 Look at that progress climb!",
    "🛠 Building that taxi empire, one job at a time!"
];

// Load last message index if exists
$lastIndex = file_exists($lastMessageFile) ? (int)file_get_contents($lastMessageFile) : -1;

// Pick message
if ($data['done'] >= $data['total']) {
    $message = "🎉 Congratulations! You can now go buy Tindy a meal 🍔";
} elseif ($data['done'] < 10) {
    $message = "🚀 Starting strong, Alan!";
} else {
    do {
        $newIndex = array_rand($funnyMessages);
    } while ($newIndex === $lastIndex && count($funnyMessages) > 1);

    $message = $funnyMessages[$newIndex];
    file_put_contents($lastMessageFile, $newIndex); // Save new index
}

$data['message'] = $message;

// Save progress
file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

// Send response
echo json_encode($data);
?>
