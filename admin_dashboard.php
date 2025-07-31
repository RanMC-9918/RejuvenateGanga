<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in as admin
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Establish connection
$conn = mysqli_connect("localhost:3306", "zts91xzcemos", "AaravAayansh@1", "rejuvenateganga");
if (!$conn) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Get statistics
$stats = [];

// Total donations
$sql = "SELECT 
    COUNT(*) as total_donations,
    SUM(CASE WHEN donation_amount_usd > 0 THEN donation_amount_usd ELSE 0 END) as total_usd,
    SUM(CASE WHEN donation_amount_inr > 0 THEN donation_amount_inr ELSE 0 END) as total_inr
    FROM donation";
$result = mysqli_query($conn, $sql);
$stats['donations'] = mysqli_fetch_assoc($result);

// Pending donations
$sql = "SELECT COUNT(*) as pending_count FROM pending_donations";
$result = mysqli_query($conn, $sql);
$stats['pending'] = mysqli_fetch_assoc($result);

// Recent donations (last 5)
$recent_donations = [];
$sql = "SELECT * FROM donation ORDER BY date_of_donation DESC LIMIT 5";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $recent_donations[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Rejuvenate Ganga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding: 20px;
        }

        .sidebar-header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #4f5962;
            margin-bottom: 20px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
        }

        .sidebar-menu li {
            margin-bottom: 10px;
        }

        .sidebar-menu a {
            color: #c2c7d0;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 4px;
            transition: 0.3s;
        }

        .sidebar-menu a:hover {
            background-color: #4f5962;
            color: white;
        }

        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
        }

        .main-content {
            flex: 1;
            padding: 20px;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
            text-transform: uppercase;
        }

        .stat-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #343a40;
            margin: 10px 0;
        }

        .recent-donations {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-super {
            background-color: #ffd700;
            color: #000;
        }

        .badge-admin {
            background-color: #90EE90;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <div class="user-info">
                    <div>
                        <div><?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
                        <span class="badge <?php echo $_SESSION['is_super_admin'] ? 'badge-super' : 'badge-admin'; ?>">
                            <?php echo $_SESSION['is_super_admin'] ? 'Super Admin' : 'Admin'; ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <ul class="sidebar-menu">
                <!--<li>-->
                <!--    <a href="admin_dashboard.php">-->
                <!--        <i class="fas fa-tachometer-alt"></i> Dashboard-->
                <!--    </a>-->
                <!--</li>-->
                <li>
                    <a href="verify_donation.php">
                        <i class="fas fa-check-circle"></i> Verify Donations
                    </a>
                </li>
                <?php if($_SESSION['is_super_admin']): ?>
                    <li>
                        <a href="manage_admins.php">
                            <i class="fas fa-users-cog"></i> Manage Admins
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="index.html">
                        <i class="fas fa-home"></i> Main Site
                    </a>
                </li>
                <li>
                    <a href="index.html">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

        <div class="main-content">
            <h1>Dashboard</h1>

            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Donations</h3>
                    <div class="value"><?php echo number_format($stats['donations']['total_donations']); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total USD Donations</h3>
                    <div class="value">$<?php echo number_format($stats['donations']['total_usd'], 2); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total INR Donations</h3>
                    <div class="value">₹<?php echo number_format($stats['donations']['total_inr'], 2); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Pending Verifications</h3>
                    <div class="value"><?php echo number_format($stats['pending']['pending_count']); ?></div>
                </div>
            </div>

            <div class="recent-donations">
                <h2>Recent Donations</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_donations as $donation): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($donation['firstname'] . ' ' . $donation['lastname']); ?></td>
                                <td><?php echo htmlspecialchars($donation['email']); ?></td>
                                <td>
                                    <?php
                                    if ($donation['donation_amount_usd'] > 0) {
                                        echo '$' . number_format($donation['donation_amount_usd'], 2);
                                    } else {
                                        echo '₹' . number_format($donation['donation_amount_inr'], 2);
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($donation['date_of_donation']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>