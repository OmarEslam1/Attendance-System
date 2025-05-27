<?php
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Get messages between two users
        $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
        $other_user_id = isset($_GET['other_user_id']) ? $_GET['other_user_id'] : null;
        
        if (!$user_id || !$other_user_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Both user IDs are required']);
            exit();
        }

        $stmt = $conn->prepare("
            SELECT m.*, 
                   s.name as sender_name,
                   r.name as receiver_name
            FROM chat_messages m
            JOIN users s ON m.sender_id = s.id
            JOIN users r ON m.receiver_id = r.id
            WHERE (m.sender_id = ? AND m.receiver_id = ?)
               OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.sent_at ASC
        ");
        
        $stmt->bind_param("iiii", $user_id, $other_user_id, $other_user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
        
        echo json_encode(['messages' => $messages]);
        break;

    case 'POST':
        // Send new message
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['sender_id']) || !isset($data['receiver_id']) || !isset($data['message'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Sender ID, receiver ID, and message are required']);
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $data['sender_id'], $data['receiver_id'], $data['message']);
        
        if ($stmt->execute()) {
            echo json_encode([
                'message' => 'Message sent successfully',
                'id' => $conn->insert_id
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to send message']);
        }
        break;

    case 'DELETE':
        // Delete message
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Message ID is required']);
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM chat_messages WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['message' => 'Message deleted successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Message not found']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete message']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
} 