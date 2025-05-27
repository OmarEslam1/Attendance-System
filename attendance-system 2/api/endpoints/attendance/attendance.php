<?php
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Get attendance records
        $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

        $query = "SELECT a.*, u.name as employee_name 
                 FROM attendance a 
                 JOIN users u ON a.user_id = u.id 
                 WHERE DATE(a.check_in) = ?";
        $params = [$date];
        $types = "s";

        if ($user_id) {
            $query .= " AND a.user_id = ?";
            $params[] = $user_id;
            $types .= "i";
        }

        $query .= " ORDER BY a.check_in DESC";

        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $attendance = [];
        while ($row = $result->fetch_assoc()) {
            $attendance[] = $row;
        }

        echo json_encode(['attendance' => $attendance]);
        break;

    case 'POST':
        // Create new attendance record (check-in)
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['user_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'User ID is required']);
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO attendance (user_id, check_in, status) VALUES (?, NOW(), 'present')");
        $stmt->bind_param("i", $data['user_id']);
        
        if ($stmt->execute()) {
            echo json_encode([
                'message' => 'Check-in recorded successfully',
                'id' => $conn->insert_id
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to record check-in']);
        }
        break;

    case 'PUT':
        // Update attendance record (check-out)
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Attendance ID is required']);
            exit();
        }

        $stmt = $conn->prepare("UPDATE attendance SET check_out = NOW() WHERE id = ? AND check_out IS NULL");
        $stmt->bind_param("i", $data['id']);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['message' => 'Check-out recorded successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Attendance record not found or already checked out']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to record check-out']);
        }
        break;

    case 'DELETE':
        // Delete attendance record
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Attendance ID is required']);
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM attendance WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['message' => 'Attendance record deleted successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Attendance record not found']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete attendance record']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
} 