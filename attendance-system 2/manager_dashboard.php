<?php
require_once 'config/database.php';
require_once 'includes/session.php';
requireManager();

// Handle employee addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_employee'])) {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, 'employee')");
        $stmt->execute([$name, $username, $password]);
        $success = "Employee added successfully!";
    } catch (PDOException $e) {
        $error = "Error adding employee: " . $e->getMessage();
    }
}

// Get all employees
$stmt = $pdo->query("SELECT id, name, username, created_at FROM users WHERE role = 'employee' ORDER BY name");
$employees = $stmt->fetchAll();

// Get attendance records
$stmt = $pdo->query("
    SELECT a.*, u.name as employee_name 
    FROM attendance a 
    JOIN users u ON a.user_id = u.id 
    ORDER BY a.created_at DESC 
    LIMIT 50
");
$attendance_records = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Manager Dashboard</a>
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
        <!-- Add a fixed notification div at the top -->
        <div id="notification" class="alert alert-success alert-dismissible fade" style="display: none;" role="alert">
            <span id="notification-message"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Add Employee Section -->
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h3 class="card-title">Add New Employee</h3>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" name="add_employee" class="btn btn-primary">Add Employee</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Employee List Section -->
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h3 class="card-title">Employee List</h3>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Joined Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($employees as $employee): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($employee['id']); ?></td>
                                        <td><?php echo htmlspecialchars($employee['name']); ?></td>
                                        <td><?php echo htmlspecialchars($employee['username']); ?></td>
                                        <td><?php echo htmlspecialchars($employee['created_at']); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="editEmployee(<?php echo $employee['id']; ?>)">Edit</button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteEmployee(<?php echo $employee['id']; ?>)">Delete</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Records Section -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <h3 class="card-title">Recent Attendance Records</h3>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendance_records as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['employee_name']); ?></td>
                                <td><?php echo htmlspecialchars($record['check_in']); ?></td>
                                <td><?php echo htmlspecialchars($record['check_out'] ?? 'Not checked out'); ?></td>
                                <td><?php echo htmlspecialchars($record['status']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editAttendance(<?php echo $record['id']; ?>)">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteAttendance(<?php echo $record['id']; ?>)">Delete</button>
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
                <h3 class="card-title">Chat with Employees</h3>
                <div class="row">
                    <div class="col-md-4">
                        <div class="list-group">
                            <?php foreach ($employees as $employee): ?>
                            <button class="list-group-item list-group-item-action" onclick="selectEmployee(<?php echo $employee['id']; ?>, '<?php echo htmlspecialchars($employee['name']); ?>')">
                                <?php echo htmlspecialchars($employee['name']); ?>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div id="chat-container" class="chat-container">
                            <!-- Chat messages will be loaded here -->
                        </div>
                        <form id="chat-form" class="mt-3" style="display: none;">
                            <div class="input-group">
                                <input type="text" class="form-control" id="message" placeholder="Type your message...">
                                <button type="submit" class="btn btn-primary">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedEmployeeId = null;

        function selectEmployee(id, name) {
            selectedEmployeeId = id;
            document.getElementById('chat-form').style.display = 'block';
            loadMessages(id);
        }

        function loadMessages(employeeId) {
            fetch(`get_messages.php?employee_id=${employeeId}`)
                .then(response => response.json())
                .then(messages => {
                    const container = document.getElementById('chat-container');
                    container.innerHTML = messages.map(msg => `
                        <div class="chat-message ${msg.sender_id == <?php echo $_SESSION['user_id']; ?> ? 'sent' : 'received'}">
                            <strong>${msg.sender_name}</strong>
                            <p>${msg.message}</p>
                            <small>${msg.sent_at}</small>
                        </div>
                    `).join('');
                    container.scrollTop = container.scrollHeight;
                });
        }

        document.getElementById('chat-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const message = document.getElementById('message').value;
            if (!message || !selectedEmployeeId) return;

            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    receiver_id: selectedEmployeeId,
                    message: message
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    document.getElementById('message').value = '';
                    loadMessages(selectedEmployeeId);
                }
            });
        });

        // Poll for new messages every 5 seconds
        setInterval(() => {
            if (selectedEmployeeId) {
                loadMessages(selectedEmployeeId);
            }
        }, 5000);

        // Edit Employee Function
        function editEmployee(id) {
            console.log('Editing employee:', id);
            fetch(`get_employee.php?id=${id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(employee => {
                    console.log('Received employee data:', employee);
                    if (!employee.success) {
                        throw new Error(employee.message || 'Failed to fetch employee data');
                    }
                    // Populate the edit form
                    document.getElementById('edit_employee_id').value = employee.id;
                    document.getElementById('edit_name').value = employee.name;
                    document.getElementById('edit_username').value = employee.username;
                    
                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('editEmployeeModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error fetching employee:', error);
                    alert('Error loading employee data: ' + error.message);
                });
        }

        // Edit Attendance Function
        function editAttendance(id) {
            console.log('Editing attendance:', id);
            fetch(`get_attendance.php?id=${id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(attendance => {
                    console.log('Received attendance data:', attendance);
                    if (!attendance.success) {
                        throw new Error(attendance.message || 'Failed to fetch attendance data');
                    }
                    // Populate the edit form
                    document.getElementById('edit_attendance_id').value = attendance.id;
                    document.getElementById('edit_check_in').value = attendance.check_in;
                    document.getElementById('edit_check_out').value = attendance.check_out || '';
                    document.getElementById('edit_status').value = attendance.status;
                    
                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('editAttendanceModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error fetching attendance:', error);
                    alert('Error loading attendance data: ' + error.message);
                });
        }

        // Handle employee edit form submission
        function saveEmployeeChanges() {
            const submitButton = document.querySelector('#editEmployeeContent button');
            submitButton.disabled = true;
            submitButton.innerHTML = 'Saving...';
            
            const formData = {
                id: document.getElementById('edit_employee_id').value,
                name: document.getElementById('edit_name').value,
                username: document.getElementById('edit_username').value,
                password: document.getElementById('edit_password').value
            };
            
            console.log('Sending employee data:', formData);
            
            fetch('update_employee.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(result => {
                console.log('Update result:', result);
                if (result.success) {
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editEmployeeModal'));
                    if (modal) {
                        modal.hide();
                    }
                    // Show success message
                    const notification = document.getElementById('notification');
                    const message = document.getElementById('notification-message');
                    message.textContent = result.message || 'Employee updated successfully!';
                    notification.style.display = 'block';
                    notification.classList.add('show');
                    // Reload the page after a short delay
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    alert('Error updating employee: ' + (result.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error updating employee:', error);
                alert('Error updating employee. Please check console for details.');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Save Changes';
            });
        }

        // Handle attendance edit form submission
        function saveAttendanceChanges() {
            const submitButton = document.querySelector('#editAttendanceContent button');
            submitButton.disabled = true;
            submitButton.innerHTML = 'Saving...';
            
            const formData = {
                id: document.getElementById('edit_attendance_id').value,
                check_in: document.getElementById('edit_check_in').value,
                check_out: document.getElementById('edit_check_out').value,
                status: document.getElementById('edit_status').value
            };
            
            console.log('Sending attendance data:', formData);
            
            fetch('update_attendance.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(result => {
                console.log('Update result:', result);
                if (result.success) {
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editAttendanceModal'));
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Show success message
                    const notification = document.getElementById('notification');
                    const message = document.getElementById('notification-message');
                    message.textContent = result.message || 'Attendance record updated successfully!';
                    notification.style.display = 'block';
                    notification.classList.add('show');
                    
                    // Reload the page after a short delay
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    alert('Error updating attendance: ' + (result.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error updating attendance:', error);
                alert('Error updating attendance. Please check console for details.');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Save Changes';
            });
        }

        function deleteEmployee(id) {
            if (confirm('Are you sure you want to delete this employee?')) {
                console.log('Deleting employee:', id);
                fetch(`delete_employee.php?id=${id}`)
                    .then(response => response.json())
                    .then(result => {
                        console.log('Delete result:', result);
                        if (result.success) {
                            location.reload();
                        } else {
                            alert('Error deleting employee: ' + (result.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting employee:', error);
                        alert('Error deleting employee. Please check console for details.');
                    });
            }
        }

        function deleteAttendance(id) {
            if (confirm('Are you sure you want to delete this attendance record?')) {
                console.log('Deleting attendance:', id);
                fetch(`delete_attendance.php?id=${id}`)
                    .then(response => response.json())
                    .then(result => {
                        console.log('Delete result:', result);
                        if (result.success) {
                            location.reload();
                        } else {
                            alert('Error deleting attendance: ' + (result.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting attendance:', error);
                        alert('Error deleting attendance. Please check console for details.');
                    });
            }
        }
    </script>

    <!-- Edit Employee Modal -->
    <div class="modal fade" id="editEmployeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="editEmployeeContent">
                        <input type="hidden" id="edit_employee_id">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="edit_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="edit_username" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">New Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" id="edit_password">
                        </div>
                        <button type="button" class="btn btn-primary" onclick="saveEmployeeChanges()">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Attendance Modal -->
    <div class="modal fade" id="editAttendanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Attendance Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="editAttendanceContent">
                        <input type="hidden" id="edit_attendance_id">
                        <div class="mb-3">
                            <label for="edit_check_in" class="form-label">Check In</label>
                            <input type="datetime-local" class="form-control" id="edit_check_in" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_check_out" class="form-label">Check Out</label>
                            <input type="datetime-local" class="form-control" id="edit_check_out">
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-control" id="edit_status" required>
                                <option value="present">Present</option>
                                <option value="late">Late</option>
                                <option value="absent">Absent</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="saveAttendanceChanges()">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 