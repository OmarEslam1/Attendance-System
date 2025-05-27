<?php
/**
 * Attendance System API Documentation
 * 
 * Base URL: http://localhost/attendance-system%202/api
 * 
 * Authentication:
 * - All endpoints except login require a Bearer token in the Authorization header
 * - Token is obtained from the login endpoint
 * - Format: Authorization: Bearer <token>
 */

$documentation = [
    'authentication' => [
        'login' => [
            'endpoint' => '/auth/login',
            'method' => 'POST',
            'description' => 'Authenticate user and get access token',
            'request_body' => [
                'username' => 'string (required)',
                'password' => 'string (required)'
            ],
            'example_request' => [
                'username' => 'admin',
                'password' => 'admin'
            ],
            'example_response' => [
                'message' => 'Login successful',
                'token' => 'generated_token_here',
                'user' => [
                    'id' => 1,
                    'name' => 'Admin User',
                    'username' => 'admin',
                    'role' => 'manager'
                ]
            ],
            'status_codes' => [
                200 => 'Login successful',
                400 => 'Username and password are required',
                401 => 'Invalid credentials'
            ]
        ]
    ],

    'users' => [
        'get_all_users' => [
            'endpoint' => '/users',
            'method' => 'GET',
            'description' => 'Get list of all users',
            'headers' => [
                'Authorization' => 'Bearer <token>'
            ],
            'example_response' => [
                'users' => [
                    [
                        'id' => 1,
                        'name' => 'Admin User',
                        'username' => 'admin',
                        'role' => 'manager',
                        'created_at' => '2024-03-20 10:00:00'
                    ]
                ]
            ],
            'status_codes' => [
                200 => 'Success',
                401 => 'Unauthorized'
            ]
        ],

        'get_user' => [
            'endpoint' => '/users?id=1',
            'method' => 'GET',
            'description' => 'Get specific user by ID',
            'query_parameters' => [
                'id' => 'integer (required)'
            ],
            'headers' => [
                'Authorization' => 'Bearer <token>'
            ],
            'example_response' => [
                'user' => [
                    'id' => 1,
                    'name' => 'Admin User',
                    'username' => 'admin',
                    'role' => 'manager',
                    'created_at' => '2024-03-20 10:00:00'
                ]
            ],
            'status_codes' => [
                200 => 'Success',
                401 => 'Unauthorized',
                404 => 'User not found'
            ]
        ],

        'create_user' => [
            'endpoint' => '/users',
            'method' => 'POST',
            'description' => 'Create new user',
            'headers' => [
                'Authorization' => 'Bearer <token>',
                'Content-Type' => 'application/json'
            ],
            'request_body' => [
                'name' => 'string (required)',
                'username' => 'string (required)',
                'password' => 'string (required)',
                'role' => 'string (required) - enum(manager, employee)'
            ],
            'example_request' => [
                'name' => 'Test Employee',
                'username' => 'test_employee',
                'password' => 'test123',
                'role' => 'employee'
            ],
            'example_response' => [
                'message' => 'User created successfully',
                'id' => 2
            ],
            'status_codes' => [
                200 => 'User created successfully',
                400 => 'Required fields missing',
                401 => 'Unauthorized'
            ]
        ],

        'update_user' => [
            'endpoint' => '/users',
            'method' => 'PUT',
            'description' => 'Update user details',
            'headers' => [
                'Authorization' => 'Bearer <token>',
                'Content-Type' => 'application/json'
            ],
            'request_body' => [
                'id' => 'integer (required)',
                'name' => 'string (optional)',
                'password' => 'string (optional)',
                'role' => 'string (optional) - enum(manager, employee)'
            ],
            'example_request' => [
                'id' => 2,
                'name' => 'Updated Name'
            ],
            'example_response' => [
                'message' => 'User updated successfully'
            ],
            'status_codes' => [
                200 => 'User updated successfully',
                400 => 'Invalid request',
                401 => 'Unauthorized',
                404 => 'User not found'
            ]
        ],

        'delete_user' => [
            'endpoint' => '/users?id=2',
            'method' => 'DELETE',
            'description' => 'Delete user',
            'query_parameters' => [
                'id' => 'integer (required)'
            ],
            'headers' => [
                'Authorization' => 'Bearer <token>'
            ],
            'example_response' => [
                'message' => 'User deleted successfully'
            ],
            'status_codes' => [
                200 => 'User deleted successfully',
                400 => 'User ID required',
                401 => 'Unauthorized',
                404 => 'User not found'
            ]
        ]
    ],

    'attendance' => [
        'get_attendance' => [
            'endpoint' => '/attendance',
            'method' => 'GET',
            'description' => 'Get attendance records',
            'query_parameters' => [
                'user_id' => 'integer (optional)',
                'date' => 'string (optional) - format: YYYY-MM-DD'
            ],
            'headers' => [
                'Authorization' => 'Bearer <token>'
            ],
            'example_response' => [
                'attendance' => [
                    [
                        'id' => 1,
                        'user_id' => 2,
                        'check_in' => '2024-03-20 09:00:00',
                        'check_out' => '2024-03-20 17:00:00',
                        'status' => 'present',
                        'employee_name' => 'Test Employee'
                    ]
                ]
            ],
            'status_codes' => [
                200 => 'Success',
                401 => 'Unauthorized'
            ]
        ],

        'create_attendance' => [
            'endpoint' => '/attendance',
            'method' => 'POST',
            'description' => 'Create new attendance record (check-in)',
            'headers' => [
                'Authorization' => 'Bearer <token>',
                'Content-Type' => 'application/json'
            ],
            'request_body' => [
                'user_id' => 'integer (required)'
            ],
            'example_request' => [
                'user_id' => 2
            ],
            'example_response' => [
                'message' => 'Check-in recorded successfully',
                'id' => 1
            ],
            'status_codes' => [
                200 => 'Check-in recorded successfully',
                400 => 'User ID required',
                401 => 'Unauthorized'
            ]
        ],

        'update_attendance' => [
            'endpoint' => '/attendance',
            'method' => 'PUT',
            'description' => 'Update attendance record (check-out)',
            'headers' => [
                'Authorization' => 'Bearer <token>',
                'Content-Type' => 'application/json'
            ],
            'request_body' => [
                'id' => 'integer (required)'
            ],
            'example_request' => [
                'id' => 1
            ],
            'example_response' => [
                'message' => 'Check-out recorded successfully'
            ],
            'status_codes' => [
                200 => 'Check-out recorded successfully',
                400 => 'Attendance ID required',
                401 => 'Unauthorized',
                404 => 'Attendance record not found'
            ]
        ],

        'delete_attendance' => [
            'endpoint' => '/attendance?id=1',
            'method' => 'DELETE',
            'description' => 'Delete attendance record',
            'query_parameters' => [
                'id' => 'integer (required)'
            ],
            'headers' => [
                'Authorization' => 'Bearer <token>'
            ],
            'example_response' => [
                'message' => 'Attendance record deleted successfully'
            ],
            'status_codes' => [
                200 => 'Attendance record deleted successfully',
                400 => 'Attendance ID required',
                401 => 'Unauthorized',
                404 => 'Attendance record not found'
            ]
        ]
    ],

    'messages' => [
        'get_messages' => [
            'endpoint' => '/messages',
            'method' => 'GET',
            'description' => 'Get messages between two users',
            'query_parameters' => [
                'user_id' => 'integer (required)',
                'other_user_id' => 'integer (required)'
            ],
            'headers' => [
                'Authorization' => 'Bearer <token>'
            ],
            'example_response' => [
                'messages' => [
                    [
                        'id' => 1,
                        'sender_id' => 1,
                        'receiver_id' => 2,
                        'message' => 'Hello!',
                        'sent_at' => '2024-03-20 10:00:00',
                        'sender_name' => 'Admin User',
                        'receiver_name' => 'Test Employee'
                    ]
                ]
            ],
            'status_codes' => [
                200 => 'Success',
                400 => 'User IDs required',
                401 => 'Unauthorized'
            ]
        ],

        'send_message' => [
            'endpoint' => '/messages',
            'method' => 'POST',
            'description' => 'Send new message',
            'headers' => [
                'Authorization' => 'Bearer <token>',
                'Content-Type' => 'application/json'
            ],
            'request_body' => [
                'sender_id' => 'integer (required)',
                'receiver_id' => 'integer (required)',
                'message' => 'string (required)'
            ],
            'example_request' => [
                'sender_id' => 1,
                'receiver_id' => 2,
                'message' => 'Hello!'
            ],
            'example_response' => [
                'message' => 'Message sent successfully',
                'id' => 1
            ],
            'status_codes' => [
                200 => 'Message sent successfully',
                400 => 'Required fields missing',
                401 => 'Unauthorized'
            ]
        ],

        'delete_message' => [
            'endpoint' => '/messages?id=1',
            'method' => 'DELETE',
            'description' => 'Delete message',
            'query_parameters' => [
                'id' => 'integer (required)'
            ],
            'headers' => [
                'Authorization' => 'Bearer <token>'
            ],
            'example_response' => [
                'message' => 'Message deleted successfully'
            ],
            'status_codes' => [
                200 => 'Message deleted successfully',
                400 => 'Message ID required',
                401 => 'Unauthorized',
                404 => 'Message not found'
            ]
        ]
    ]
];

// Output documentation in a readable format
echo "Attendance System API Documentation\n";
echo "=================================\n\n";

foreach ($documentation as $section => $endpoints) {
    echo strtoupper($section) . "\n";
    echo str_repeat('-', strlen($section)) . "\n\n";
    
    foreach ($endpoints as $name => $details) {
        echo "Endpoint: {$details['endpoint']}\n";
        echo "Method: {$details['method']}\n";
        echo "Description: {$details['description']}\n\n";
        
        if (isset($details['query_parameters'])) {
            echo "Query Parameters:\n";
            foreach ($details['query_parameters'] as $param => $desc) {
                echo "- $param: $desc\n";
            }
            echo "\n";
        }
        
        if (isset($details['request_body'])) {
            echo "Request Body:\n";
            foreach ($details['request_body'] as $field => $desc) {
                echo "- $field: $desc\n";
            }
            echo "\n";
        }
        
        if (isset($details['example_request'])) {
            echo "Example Request:\n";
            echo json_encode($details['example_request'], JSON_PRETTY_PRINT) . "\n\n";
        }
        
        if (isset($details['example_response'])) {
            echo "Example Response:\n";
            echo json_encode($details['example_response'], JSON_PRETTY_PRINT) . "\n\n";
        }
        
        if (isset($details['status_codes'])) {
            echo "Status Codes:\n";
            foreach ($details['status_codes'] as $code => $message) {
                echo "- $code: $message\n";
            }
        }
        
        echo "\n" . str_repeat('=', 50) . "\n\n";
    }
} 