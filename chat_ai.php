<?php
// ai_chat.php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$userMessage = $data['message'] ?? '';

// Here you would call your AI backend / API and get $reply
// For now, just echo a simple rule-based reply:
if (stripos($userMessage, 'sale') !== false) {
    $reply = "To record a sale, go to the Sales page, select products, enter quantities, then click 'Complete Sale'.";
} else {
    $reply = "I received: " . $userMessage . ". I will guide you about using the Agrovet system.";
}

echo json_encode(['reply' => $reply]);
