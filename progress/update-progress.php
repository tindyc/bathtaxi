<?php
header('Content-Type: application/json');

// File where progress is stored
$file = 'progress.json';

// Read the JSON file
$data = json_decode(file_get_contents($file), true);

// Handle action
if ($_POST['action'] === 'increment' && $data['done'] < $data['total']) {
    $data['done']++;
    $data['remaining'] = $data['total'] - $data['done'];
    $data['percent'] = round(($data['done'] / $data['total']) * 100, 2);

    // Encouraging messages
    $messages = [
        "Great job! Keep going 🚖",
        "Another step closer! 💪",
        "You're smashing it!",
        "On the road to success!",
        "Nice work — let's keep rolling!"
    ];
    $data['message'] = $messages[array_rand($messages)];

    // Save the new data
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

// Return updated data
echo json_encode($data);
?>
