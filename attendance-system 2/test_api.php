<?php
// Configuration
$base_url = 'http://localhost/attendance-system%202/api';
$username = 'admin';
$password = 'admin';

// Helper function to make API requests
function makeRequest($endpoint, $method = 'GET', $data = null, $token = null) {
    global $base_url;
    
    $ch = curl_init($base_url . '/' . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "\nTesting $method $endpoint\n";
    echo "Status Code: $http_code\n";
    echo "Response: " . $response . "\n";
    echo "----------------------------------------\n";
    
    return json_decode($response, true);
}

// Test 1: Login
echo "Test 1: Login\n";
$login_response = makeRequest('auth/login', 'POST', [
    'username' => $username,
    'password' => $password
]);

if (!isset($login_response['token'])) {
    die("Login failed. Cannot proceed with other tests.\n");
}

$token = $login_response['token'];
$user_id = $login_response['user']['id'];

// Test 2: Get All Users
echo "\nTest 2: Get All Users\n";
makeRequest('users', 'GET', null, $token);

// Test 3: Create New User
echo "\nTest 3: Create New User\n";
$new_user = makeRequest('users', 'POST', [
    'name' => 'Test Employee',
    'username' => 'test_employee',
    'password' => 'test123',
    'role' => 'employee'
], $token);

$new_user_id = $new_user['id'] ?? null;

// Test 4: Get Specific User
if ($new_user_id) {
    echo "\nTest 4: Get Specific User\n";
    makeRequest('users?id=' . $new_user_id, 'GET', null, $token);
}

// Test 5: Create Attendance Record
echo "\nTest 5: Create Attendance Record\n";
$attendance = makeRequest('attendance', 'POST', [
    'user_id' => $new_user_id
], $token);

$attendance_id = $attendance['id'] ?? null;

// Test 6: Get Attendance Records
echo "\nTest 6: Get Attendance Records\n";
makeRequest('attendance', 'GET', null, $token);

// Test 7: Update Attendance (Check-out)
if ($attendance_id) {
    echo "\nTest 7: Update Attendance (Check-out)\n";
    makeRequest('attendance', 'PUT', [
        'id' => $attendance_id
    ], $token);
}

// Test 8: Send Message
echo "\nTest 8: Send Message\n";
$message = makeRequest('messages', 'POST', [
    'sender_id' => $user_id,
    'receiver_id' => $new_user_id,
    'message' => 'Hello from the test script!'
], $token);

$message_id = $message['id'] ?? null;

// Test 9: Get Messages
echo "\nTest 9: Get Messages\n";
makeRequest('messages?user_id=' . $user_id . '&other_user_id=' . $new_user_id, 'GET', null, $token);

// Test 10: Delete Message
if ($message_id) {
    echo "\nTest 10: Delete Message\n";
    makeRequest('messages?id=' . $message_id, 'DELETE', null, $token);
}

// Test 11: Delete Attendance Record
if ($attendance_id) {
    echo "\nTest 11: Delete Attendance Record\n";
    makeRequest('attendance?id=' . $attendance_id, 'DELETE', null, $token);
}

// Test 12: Delete Test User
if ($new_user_id) {
    echo "\nTest 12: Delete Test User\n";
    makeRequest('users?id=' . $new_user_id, 'DELETE', null, $token);
}

echo "\nAll tests completed!\n"; 