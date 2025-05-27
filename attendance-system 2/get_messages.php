<?php
require_once 'config/database.php';
require_once 'includes/session.php';
requireLogin();

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];
$other_id = isset($_GET['employee_id']) ? $_GET['employee_id'] : (isset($_GET['manager_id']) ? $_GET['manager_id'] : null);

if (!$other_id) {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            m.*,
            s.name as sender_name,
            r.name as receiver_name
        FROM chat_messages m
        JOIN users s ON m.sender_id = s.id
        JOIN users r ON m.receiver_id = r.id
        WHERE (m.sender_id = ? AND m.receiver_id = ?)
           OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.sent_at ASC
    ");
    $stmt->execute([$user_id, $other_id, $other_id, $user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($messages);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error']);
}
?> 