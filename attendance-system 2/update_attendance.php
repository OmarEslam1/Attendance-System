<?php
require_once 'config/database.php';
require_once 'includes/session.php';
requireManager();

header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get JSON input
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Log the incoming request
error_log("Update attendance request received: " . print_r($data, true));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

$id = (int)$data['id'];
$check_in = trim($data['check_in']);
$check_out = !empty($data['check_out']) ? trim($data['check_out']) : null;
$status = trim($data['status']);

if (empty($check_in) || empty($status)) {
    echo json_encode(['success' => false, 'message' => 'Check-in time and status are required']);
    exit;
}

// Validate status
$valid_statuses = ['present', 'late', 'absent'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    // Update attendance record
    $stmt = $pdo->prepare("UPDATE attendance SET check_in = ?, check_out = ?, status = ? WHERE id = ?");
    $result = $stmt->execute([$check_in, $check_out, $status, $id]);
    error_log("Updating attendance: ID=$id, Check-in=$check_in, Check-out=$check_out, Status=$status");

    if ($result) {
        error_log("Attendance update successful");
        echo json_encode(['success' => true, 'message' => 'Attendance record updated successfully']);
    } else {
        error_log("Attendance update failed - no rows affected");
        echo json_encode(['success' => false, 'message' => 'Failed to update attendance record']);
    }
} catch (PDOException $e) {
    error_log("Database error in update_attendance.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} 