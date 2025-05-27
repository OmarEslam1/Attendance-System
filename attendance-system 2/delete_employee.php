<?php
require_once 'config/database.php';
require_once 'includes/session.php';
requireManager();

header('Content-Type: application/json');

$employee_id = $_GET['id'] ?? null;

if (!$employee_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid request: No employee ID provided.']);
    error_log('Delete employee failed: No employee ID provided.');
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Delete employee's attendance records
    $stmt = $pdo->prepare("DELETE FROM attendance WHERE user_id = ?");
    $stmt->execute([$employee_id]);
    error_log("Deleted attendance for employee ID $employee_id, rows affected: " . $stmt->rowCount());

    // Delete employee's chat messages
    $stmt = $pdo->prepare("DELETE FROM chat_messages WHERE sender_id = ? OR receiver_id = ?");
    $stmt->execute([$employee_id, $employee_id]);
    error_log("Deleted chat messages for employee ID $employee_id, rows affected: " . $stmt->rowCount());

    // Delete employee
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'employee'");
    $stmt->execute([$employee_id]);
    $deletedRows = $stmt->rowCount();
    error_log("Deleted user for employee ID $employee_id, rows affected: $deletedRows");

    if ($deletedRows > 0) {
        $pdo->commit();
        echo json_encode(['success' => true]);
    } else {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'Employee not found or not an employee role.']);
        error_log("Delete employee failed: Employee not found or not an employee role for ID $employee_id");
    }
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Delete employee DB error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?> 