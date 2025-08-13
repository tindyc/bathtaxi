<?php
header('Content-Type: application/json');

$progressFile = 'progress.json';

// Load or initialise progress data
if (!file_exists($progressFile)) {
    $data = ['done' => 0, 'total' => 172, 'remaining' => 172];
    file_put_contents($progressFile, json_encode($data));
}
$data = json_decode(file_get_contents($progressFile), true);

// Increment jobs if action is 'increment'
if ($_POST['action'] === 'increment') {
    if ($data['done'] < $data['total']) {
        $data['done']++;
    }
}

// Calculate remaining and percent
$data['remaining'] = $data['total'] - $data['done'];
$data['percent'] = round(($data['done'] / $data['total']) * 100, 2);

// Funny messages based on progress
if ($data['done'] >= $data['total']) {
    // Fixed celebration message
    $data['message'] = "🎉 Congratulations Alan! You've hit {$data['total']} jobs! Now go buy Tindy a meal 🍔🍟";
} elseif ($data['done'] >= 150) {
    $messages = [
        "🔥 Almost there, Alan! {$data['remaining']} jobs left — push like you're running to the buffet!",
        "💥 150+ jobs done — if only your gym attendance was this good.",
        "⏳ Nearly done! Imagine the calories you’d burn if you walked this instead of drove it."
    ];
    $data['message'] = $messages[array_rand($messages)];
} elseif ($data['done'] >= 100) {
    $messages = [
        "💪 Over halfway, Alan! Now imagine if this was a treadmill.",
        "🏆 100 jobs done! Keep going — you can rest when you’re thin.",
        "😅 You’ve done {$data['done']} jobs… but how many sit-ups?"
    ];
    $data['message'] = $messages[array_rand($messages)];
} elseif ($data['done'] >= 50) {
    $messages = [
        "👏 50 down, Alan! That’s like walking halfway to Greggs.",
        "🚖 {$data['done']} jobs done — that’s a lot of sitting still.",
        "😂 50 jobs… and not one gym visit."
    ];
    $data['message'] = $messages[array_rand($messages)];
} else {
    $messages = [
        "🏃‍♂️ Keep going, Alan! Work those finger muscles.",
        "😂 {$data['done']} down, {$data['remaining']} to go — think of it as training for the Olympics… in sitting.",
        "🍩 Keep pushing, Alan! You’re working harder than you do at the gym (which isn’t hard)."
    ];
    $data['message'] = $messages[array_rand($messages)];
}

// Save updated progress
file_put_contents($progressFile, json_encode($data));

// Send updated data
echo json_encode($data);
