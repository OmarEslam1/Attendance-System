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
error_log("Update employee request received: " . print_r($data, true));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

$id = (int)$data['id'];
$name = trim($data['name']);
$username = trim($data['username']);

if (empty($name) || empty($username)) {
    echo json_encode(['success' => false, 'message' => 'Name and username are required']);
    exit;
}

try {
    // First verify the employee exists
    $check = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'employee'");
    $check->execute([$id]);
    if (!$check->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Employee not found']);
        exit;
    }

    // Check if username is already taken by another employee
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ? AND role = 'employee'");
    $stmt->execute([$username, $id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Username is already taken']);
        exit;
    }

    // Update employee information
    if (!empty($data['password'])) {
        // Update with new password
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET name = ?, username = ?, password = ? WHERE id = ? AND role = 'employee'");
        $result = $stmt->execute([$name, $username, $password, $id]);
        error_log("Updating employee with password: ID=$id, Name=$name, Username=$username");
    } else {
        // Update without changing password
        $stmt = $pdo->prepare("UPDATE users SET name = ?, username = ? WHERE id = ? AND role = 'employee'");
        $result = $stmt->execute([$name, $username, $id]);
        error_log("Updating employee without password: ID=$id, Name=$name, Username=$username");
    }

    if ($result) {
        error_log("Employee update successful");
        echo json_encode(['success' => true, 'message' => 'Employee updated successfully']);
    } else {
        error_log("Employee update failed - no rows affected");
        echo json_encode(['success' => false, 'message' => 'Failed to update employee']);
    }
} catch (PDOException $e) {
    error_log("Database error in update_employee.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} 