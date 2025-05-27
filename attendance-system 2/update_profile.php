<?php
require_once 'config/database.php';
require_once 'includes/session.php';
requireEmployee();

$user_id = $_SESSION['user_id'];
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    if (empty($name)) {
        $error = 'Name is required';
    } else {
        try {
            // Start transaction
            $pdo->beginTransaction();

            // Update name
            $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
            $stmt->execute([$name, $user_id]);
            $_SESSION['name'] = $name;

            // Update password if provided
            if (!empty($new_password)) {
                if (empty($current_password)) {
                    throw new Exception('Current password is required to set a new password');
                }

                // Verify current password
                $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();

                if (!password_verify($current_password, $user['password'])) {
                    throw new Exception('Current password is incorrect');
                }

                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $user_id]);
            }

            $pdo->commit();
            $success = 'Profile updated successfully';
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    }
}

// Redirect back to dashboard with message
if ($success) {
    $_SESSION['success'] = $success;
} elseif ($error) {
    $_SESSION['error'] = $error;
}
header('Location: employee_dashboard.php');
exit;
?> 