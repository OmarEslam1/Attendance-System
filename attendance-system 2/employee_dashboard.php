<?php
require_once 'config/database.php';
require_once 'includes/session.php';
requireEmployee();

$user_id = $_SESSION['user_id'];

// Handle check-in/out
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['check_in'])) {
        $stmt = $pdo->prepare("INSERT INTO attendance (user_id, check_in, status) VALUES (?, NOW(), 'present')");
        $stmt->execute([$user_id]);
        $success = "Check-in recorded successfully!";
    } elseif (isset($_POST['check_out'])) {
        $stmt = $pdo->prepare("
            UPDATE attendance 
            SET check_out = NOW()
            WHERE user_id = ? AND check_out IS NULL
        ");
        $stmt->execute([$user_id]);
        $success = "Check-out recorded successfully!";
    }
}

// Get user's attendance records
$stmt = $pdo->prepare("
    SELECT * FROM attendance 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 50
");
$stmt->execute([$user_id]);
$attendance_records = $stmt->fetchAll();

// Get user's current status
$stmt = $pdo->prepare("
    SELECT status FROM attendance 
    WHERE user_id = ? AND check_out IS NULL 
    ORDER BY created_at DESC 
    LIMIT 1
");
$stmt->execute([$user_id]);
$current_status = $stmt->fetch();

// Get manager info for chat
$stmt = $pdo->query("SELECT id, name FROM users WHERE role = 'manager' LIMIT 1");
$manager = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="#">Employee Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Check In/Out Section -->
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h3 class="card-title">Attendance</h3>
                        <div class="text-center mb-4">
                            <p class="lead">Current Status: 
                                <span class="badge <?php echo $current_status && $current_status['status'] === 'present' ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo $current_status ? ucfirst($current_status['status']) : 'Not checked in'; ?>
                                </span>
                            </p>
                        </div>
                        <form method="POST" action="" class="d-grid gap-2">
                            <?php if (!$current_status): ?>
                                <button type="submit" name="check_in" class="btn btn-success">Check In</button>
                            <?php else: ?>
                                <button type="submit" name="check_out" class="btn btn-danger">Check Out</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Profile Section -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h3 class="card-title">My Profile</h3>
                        <form id="profile-form" method="POST" action="update_profile.php">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                            </div>
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Attendance History Section -->
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h3 class="card-title">Attendance History</h3>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($attendance_records as $record): ?>
                                    <tr>
                                        <td><?php echo date('Y-m-d', strtotime($record['created_at'])); ?></td>
                                        <td><?php echo date('H:i:s', strtotime($record['check_in'])); ?></td>
                                        <td><?php echo $record['check_out'] ? date('H:i:s', strtotime($record['check_out'])) : '-'; ?></td>
                                        <td>
                                            <span class="badge <?php 
                                                echo match($record['status']) {
                                                    'present' => 'bg-success',
                                                    'late' => 'bg-warning',
                                                    'absent' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                            ?>">
                                                <?php echo ucfirst($record['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Chat Section -->
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="card-title">Chat with Manager</h3>
                        <div id="chat-container" class="chat-container">
                            <!-- Chat messages will be loaded here -->
                        </div>
                        <form id="chat-form" class="mt-3">
                            <div class="input-group">
                                <input type="text" class="form-control" id="message" placeholder="Type your message...">
                                <button type="submit" class="btn btn-success">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load messages
        function loadMessages() {
            fetch('get_messages.php?manager_id=<?php echo $manager['id']; ?>')
                .then(response => response.json())
                .then(messages => {
                    const container = document.getElementById('chat-container');
                    container.innerHTML = messages.map(msg => `
                        <div class="chat-message ${msg.sender_id == <?php echo $user_id; ?> ? 'sent' : 'received'}">
                            <strong>${msg.sender_name}</strong>
                            <p>${msg.message}</p>
                            <small>${msg.sent_at}</small>
                        </div>
                    `).join('');
                    container.scrollTop = container.scrollHeight;
                });
        }

        // Send message
        document.getElementById('chat-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const message = document.getElementById('message').value;
            if (!message) return;

            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    receiver_id: <?php echo $manager['id']; ?>,
                    message: message
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    document.getElementById('message').value = '';
                    loadMessages();
                }
            });
        });

        // Poll for new messages every 5 seconds
        loadMessages();
        setInterval(loadMessages, 5000);

        // Profile form validation
        document.getElementById('profile-form').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const currentPassword = document.getElementById('current_password').value;
            
            if (newPassword && !currentPassword) {
                e.preventDefault();
                alert('Please enter your current password to set a new password');
            }
        });
    </script>
</body>
</html> 