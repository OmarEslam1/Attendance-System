<?php
require_once 'config/database.php';
require_once 'includes/session.php';
requireManager();

header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Attendance ID is required']);
    exit;
}

$id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT id, check_in, check_out, status FROM attendance WHERE id = ?");
    $stmt->execute([$id]);
    $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($attendance) {
        // Format datetime values for HTML datetime-local input
        $attendance['check_in'] = date('Y-m-d\TH:i', strtotime($attendance['check_in']));
        if ($attendance['check_out']) {
            $attendance['check_out'] = date('Y-m-d\TH:i', strtotime($attendance['check_out']));
        }
        // Add success flag to the response
        $attendance['success'] = true;
        echo json_encode($attendance);
    } else {
        echo json_encode(['success' => false, 'message' => 'Attendance record not found']);
    }
} catch (PDOException $e) {
    error_log("Error fetching attendance: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} 