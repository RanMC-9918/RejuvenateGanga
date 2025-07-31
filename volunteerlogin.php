<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once 'db_connect.php';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($email && $password) {
        $stmt = $conn->prepare("SELECT firstname, password FROM volunteer WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($firstname, $hashed_password);
                $stmt->fetch();
                // If passwords are hashed, use password_verify. If not, use direct comparison.
                if (password_verify($password, $hashed_password) || $password === $hashed_password) {
                    $_SESSION['email'] = $email;
                    $_SESSION['firstname'] = $firstname;
                    // Success: continue to dashboard (this page)
                } else {
                    $_SESSION['error'] = 'Invalid email or password.';
                    header('Location: signin_volunteer.php');
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Invalid email or password.';
                header('Location: signin_volunteer.php');
                exit();
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = 'Database error.';
            header('Location: signin_volunteer.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'Please enter both email and password.';
        header('Location: signin_volunteer.php');
        exit();
    }
}

// Only allow access if logged in
if (!isset($_SESSION['email'])) {
    header('Location: signin_volunteer.php');
    exit();
}

// Ensure session variables are set
if (!isset($_SESSION['email'])) {
    $_SESSION['email'] = "test@example.com"; // Set to your test email
}
if (!isset($_SESSION['firstname'])) {
    $_SESSION['firstname'] = "TestUser";
}

$servername = "localhost:3306";
$username = "zts91xzcemos";
$password = "AaravAayansh@1";
$dbname = "rejuvenateganga";

// Create a single database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Registered Events
$user_email = $_SESSION['email'];
$registered_events = [];

if ($user_email) {
    $sql_events = "SELECT `event_registered`, `date`, `duration` 
                   FROM event_registered 
                   WHERE email = ? 
                     AND `date` >= CURDATE() 
                     AND `date` <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
    
    $stmt_events = $conn->prepare($sql_events);
    if (!$stmt_events) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt_events->bind_param("s", $user_email);
    $stmt_events->execute();
    $result_events = $stmt_events->get_result();

    if ($result_events->num_rows === 0) {
        echo "<!-- No events found for this user. Check database entries. -->";
    }

    while ($row = $result_events->fetch_assoc()) {
        $registered_events[] = $row;
    }

    $stmt_events->close();
}

// Fetch Volunteer Name
$user_name = "Your"; // Default value
$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT firstname FROM volunteer WHERE email = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->bind_result($firstname);
    $stmt->fetch();
    $user_name = htmlspecialchars($firstname); // Prevent XSS
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <style>
        .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
    padding: 10px 20px;
}

.navbar .logo {
    font-weight: bold;
    font-size: 20px;
    text-decoration: none;
    color: white;
}

.logout-form {
    margin: 0;
}

.logout-form button {
    background-color: #00796b;
    color: red !important;
    border: none;
    padding: 8px 12px;
    cursor: pointer;
    border-radius: 4px;
    font-size: 14px;
}

.logout-form button:hover {
    background-color: #004d40;
}
    </style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Dashboard - Rejuvenate Ganga</title>
    <link rel="stylesheet" href="../cssfolder/styles.css">
</head>
<body>
    <nav class="navbar">
    <ul>
        <li><a href="htmlfolder/contact.html">Contact Us</a></li>
        <li><a href="htmlfolder/aboutme.html">About Us</a></li>
        <li><a href="htmlfolder/supportus.html">Support Us</a></li>
        <li><a href="htmlfolder/relatedlinks.html">Related Links</a></li>
        <li><a href="htmlfolder/ourmission.html">Our Mission</a></li>
        <li><a href="htmlfolder/aboutus.html">About Ganges</a></li>
        <li><a href="index.html">Home</a></li>
    </ul>
    <a href="signin_volunteer.php" class="logout-button">Logout</a>
</nav>
    <div class="container_donation2">
        <h1>Welcome <?php echo htmlspecialchars($user_name ?? 'Your'); ?> to Your Volunteer Dashboard</h1>
        
        <div class="roadmap">
            <h2>Roadmap of Events</h2>
            <img src="images/roadmap.png" alt="Roadmap of Events" style="width:100%; height:auto;">
        </div>
        
        <!-- Events Registered by User -->
        <div class="registered-events">
          <br>
          <br>
            <h2>Your Registered Events (Next 30 Days)</h2>
            <table>
                <tr>
                    <th>Event Name</th>
                    <th>Event Date</th>
                    <th>Duration</th>
                </tr>
                <?php 
                if (!empty($registered_events)) { 
                    foreach ($registered_events as $event) {
                        echo "<tr>
                                <td>" . htmlspecialchars($event['event_registered']) . "</td>
                                <td>" . htmlspecialchars($event['date']) . "</td>
                                <td>" . htmlspecialchars($event['duration']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No upcoming events registered.</td></tr>";
                } 
                ?>
            </table>
            
            <!-- Volunteer Hours Form -->
            <div class="container_donation3">
                <h2>View Your Volunteer Hours</h2>
                <form action="reportvolunteer.php" method="post">
                    <label>Email Address</label>
                    <input class="BoxSize" type="email" placeholder="Enter Email" id="email" name="email" required>
                    <button class="history-btn" type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
