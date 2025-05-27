<?php
require_once 'includes/session.php';

if (isLoggedIn()) {
    if (isManager()) {
        header('Location: manager_dashboard.php');
    } else {
        header('Location: employee_dashboard.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h1 class="mb-4">Welcome to Attendance System</h1>
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Choose Login Type</h2>
                        <div class="d-grid gap-3">
                            <a href="manager_login.php" class="btn btn-primary btn-lg">Manager Login</a>
                            <a href="employee_login.php" class="btn btn-success btn-lg">Employee Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 