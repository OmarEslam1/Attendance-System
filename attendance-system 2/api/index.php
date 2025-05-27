<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/database.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get the request method and path
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace('/api/', '', $path);

// Authentication middleware
function authenticateRequest() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No authorization token provided']);
        exit();
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);
    // TODO: Implement proper JWT token validation
    // For now, we'll just check if the token exists
    if (empty($token)) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
        exit();
    }
}

// Route the request
switch ($path) {
    case 'auth/login':
        require_once 'endpoints/auth/login.php';
        break;
    case 'attendance':
        authenticateRequest();
        require_once 'endpoints/attendance/attendance.php';
        break;
    case 'users':
        authenticateRequest();
        require_once 'endpoints/users/users.php';
        break;
    case 'messages':
        authenticateRequest();
        require_once 'endpoints/messages/messages.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
} 