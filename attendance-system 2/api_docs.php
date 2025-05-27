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

// Documentation array
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance System API Documentation</title>
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --background-color: #f8fafc;
            --text-color: #1e293b;
            --border-color: #e2e8f0;
            --code-bg: #f1f5f9;
            --success-color: #22c55e;
            --error-color: #ef4444;
            --warning-color: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--background-color);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        header {
            background-color: white;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        h1 {
            color: var(--primary-color);
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .subtitle {
            color: var(--text-color);
            font-size: 1.2rem;
            opacity: 0.8;
        }

        .section {
            background-color: white;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            color: var(--primary-color);
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border-color);
        }

        .endpoint {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
        }

        .endpoint:last-child {
            margin-bottom: 0;
        }

        .endpoint-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .method {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: bold;
            margin-right: 1rem;
            font-size: 0.9rem;
        }

        .get { background-color: #dbeafe; color: var(--primary-color); }
        .post { background-color: #dcfce7; color: var(--success-color); }
        .put { background-color: #fef3c7; color: var(--warning-color); }
        .delete { background-color: #fee2e2; color: var(--error-color); }

        .endpoint-path {
            font-family: monospace;
            font-size: 1.1rem;
            color: var(--text-color);
        }

        .description {
            margin-bottom: 1rem;
            color: var(--text-color);
        }

        .subsection {
            margin-bottom: 1rem;
        }

        .subsection-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }

        .code-block {
            background-color: var(--code-bg);
            padding: 1rem;
            border-radius: 4px;
            font-family: monospace;
            white-space: pre-wrap;
            margin: 0.5rem 0;
        }

        .status-code {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .status-200 { background-color: #dcfce7; color: var(--success-color); }
        .status-400 { background-color: #fef3c7; color: var(--warning-color); }
        .status-401 { background-color: #fee2e2; color: var(--error-color); }
        .status-404 { background-color: #fee2e2; color: var(--error-color); }

        .parameter {
            display: flex;
            margin-bottom: 0.5rem;
        }

        .parameter-name {
            font-weight: 600;
            min-width: 150px;
        }

        .parameter-description {
            color: var(--text-color);
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .section {
                padding: 1rem;
            }

            .endpoint-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .method {
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Attendance System API Documentation</h1>
            <p class="subtitle">Base URL: http://localhost/attendance-system%202/api</p>
        </div>
    </header>

    <div class="container">
        <?php foreach ($documentation as $section => $endpoints): ?>
            <div class="section">
                <h2 class="section-title"><?php echo strtoupper($section); ?></h2>
                
                <?php foreach ($endpoints as $name => $details): ?>
                    <div class="endpoint">
                        <div class="endpoint-header">
                            <span class="method <?php echo strtolower($details['method']); ?>">
                                <?php echo $details['method']; ?>
                            </span>
                            <span class="endpoint-path"><?php echo $details['endpoint']; ?></span>
                        </div>

                        <p class="description"><?php echo $details['description']; ?></p>

                        <?php if (isset($details['headers'])): ?>
                            <div class="subsection">
                                <h3 class="subsection-title">Headers</h3>
                                <?php foreach ($details['headers'] as $header => $value): ?>
                                    <div class="parameter">
                                        <span class="parameter-name"><?php echo $header; ?>:</span>
                                        <span class="parameter-description"><?php echo $value; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($details['query_parameters'])): ?>
                            <div class="subsection">
                                <h3 class="subsection-title">Query Parameters</h3>
                                <?php foreach ($details['query_parameters'] as $param => $desc): ?>
                                    <div class="parameter">
                                        <span class="parameter-name"><?php echo $param; ?></span>
                                        <span class="parameter-description"><?php echo $desc; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($details['request_body'])): ?>
                            <div class="subsection">
                                <h3 class="subsection-title">Request Body</h3>
                                <?php foreach ($details['request_body'] as $field => $desc): ?>
                                    <div class="parameter">
                                        <span class="parameter-name"><?php echo $field; ?></span>
                                        <span class="parameter-description"><?php echo $desc; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($details['example_request'])): ?>
                            <div class="subsection">
                                <h3 class="subsection-title">Example Request</h3>
                                <pre class="code-block"><?php echo json_encode($details['example_request'], JSON_PRETTY_PRINT); ?></pre>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($details['example_response'])): ?>
                            <div class="subsection">
                                <h3 class="subsection-title">Example Response</h3>
                                <pre class="code-block"><?php echo json_encode($details['example_response'], JSON_PRETTY_PRINT); ?></pre>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($details['status_codes'])): ?>
                            <div class="subsection">
                                <h3 class="subsection-title">Status Codes</h3>
                                <?php foreach ($details['status_codes'] as $code => $message): ?>
                                    <span class="status-code status-<?php echo $code; ?>">
                                        <?php echo $code; ?>: <?php echo $message; ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html> 