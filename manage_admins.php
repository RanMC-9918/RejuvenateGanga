<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establish connection
$conn = mysqli_connect("localhost:3306", "zts91xzcemos", "AaravAayansh@1", "rejuvenateganga");
if (!$conn) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $username = trim($_POST['username']);
                $email = trim($_POST['email']);
                $password = $_POST['password'];
                
                // Validate input
                $errors = [];
                
                if (empty($username)) {
                    $errors[] = "Username is required";
                }
                
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Valid email is required";
                }
                
                if (strlen($password) < 8) {
                    $errors[] = "Password must be at least 8 characters long";
                }
                
                // Check if username or email already exists
                $check_sql = "SELECT * FROM admin WHERE username = ? OR email = ?";
                $check_stmt = mysqli_prepare($conn, $check_sql);
                mysqli_stmt_bind_param($check_stmt, "ss", $username, $email);
                mysqli_stmt_execute($check_stmt);
                $result = mysqli_stmt_get_result($check_stmt);
                
                if (mysqli_num_rows($result) > 0) {
                    $errors[] = "Username or email already exists";
                }
                
                if (empty($errors)) {
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insert new admin
                    $sql = "INSERT INTO admin (username, email, password) VALUES (?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed_password);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $message = "New admin account created successfully";
                    } else {
                        $message = "Error creating admin account: " . mysqli_error($conn);
                    }
                } else {
                    $message = "Errors: " . implode(", ", $errors);
                }
                break;

            case 'delete':
                if (isset($_POST['admin_id'])) {
                    $admin_id = (int)$_POST['admin_id'];
                    
                    // Prevent deleting self
                    if ($admin_id == $_SESSION['admin_id']) {
                        $message = "You cannot delete your own account";
                    } else {
                        $sql = "DELETE FROM admin WHERE id = ? AND is_super_admin = 0";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "i", $admin_id);
                        
                        if (mysqli_stmt_execute($stmt)) {
                            $message = "Admin account deleted successfully";
                        } else {
                            $message = "Error deleting admin account";
                        }
                    }
                }
                break;
        }
    }
}

// Get list of all admins
$admins = [];
$sql = "SELECT id, username, email, is_super_admin, created_at FROM admin ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $admins[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Admins</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .container {
            display: flex;
            gap: 40px;
        }
        .admin-form {
            flex: 1;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .admin-list {
            flex: 2;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            background-color: #d4edda;
            color: #155724;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
        }
        .nav-links {
            margin-bottom: 20px;
        }
        .nav-links a {
            margin-right: 15px;
            color: #007bff;
            text-decoration: none;
        }
        .nav-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="nav-links">
        <a href="admin_dashboard.php">Admin Dashboard</a>
        <a href="verify_donation.php">Verify Donations</a>
        <a href="index.html">Logout</a>
    </div>

    <h1>Manage Admins</h1>
    
    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="container">
        <div class="admin-form">
            <h2>Create New Admin</h2>
            <form method="post">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Create Admin Account</button>
            </form>
        </div>

        <div class="admin-list">
            <h2>Existing Admins</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?= htmlspecialchars($admin['username']) ?></td>
                            <td><?= htmlspecialchars($admin['email']) ?></td>
                            <td><?= $admin['is_super_admin'] ? 'Super Admin' : 'Admin' ?></td>
                            <td><?= htmlspecialchars($admin['created_at']) ?></td>
                            <td>
                                <?php if (!$admin['is_super_admin'] && $admin['id'] != $_SESSION['admin_id']): ?>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="admin_id" value="<?= $admin['id'] ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this admin?');">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>