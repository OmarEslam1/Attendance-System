<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isManager() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'manager';
}

function isEmployee() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'employee';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /index.php');
        exit();
    }
}

function requireManager() {
    requireLogin();
    if (!isManager()) {
        header('Location: /employee_dashboard.php');
        exit();
    }
}

function requireEmployee() {
    requireLogin();
    if (!isEmployee()) {
        header('Location: /manager_dashboard.php');
        exit();
    }
}
?> 