<?php
require_once 'config/database.php';
require_once 'includes/session.php';
requireManager();

header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Employee ID is required']);
    exit;
}

$id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT id, name, username FROM users WHERE id = ? AND role = 'employee'");
    $stmt->execute([$id]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($employee) {
        // Add success flag to the response
        $employee['success'] = true;
        echo json_encode($employee);
    } else {
        echo json_encode(['success' => false, 'message' => 'Employee not found']);
    }
} catch (PDOException $e) {
    error_log("Error fetching employee: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} 