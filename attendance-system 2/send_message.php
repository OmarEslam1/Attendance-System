<?php
require_once 'config/database.php';
require_once 'includes/session.php';
requireLogin();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];
$receiver_id = $data['receiver_id'] ?? null;
$message = trim($data['message'] ?? '');

if (!$receiver_id || !$message) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO chat_messages (sender_id, receiver_id, message) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$user_id, $receiver_id, $message]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?> 