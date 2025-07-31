<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

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

// Fetch Volunteer Organization
$user_email = $_SESSION['email'];
$volunteer_org = '';
$stmt_org = $conn->prepare("SELECT organization FROM volunteer WHERE email = ?");
if ($stmt_org) {
    $stmt_org->bind_param("s", $user_email);
    $stmt_org->execute();
    $stmt_org->bind_result($volunteer_org);
    $stmt_org->fetch();
    $stmt_org->close();
}

// Fetch Events from the events table for the volunteer's organization
$organization_events = [];
if ($user_email && $volunteer_org) {
    // First get the organization ID from the organization table
    $org_id = null;
    $stmt_org_id = $conn->prepare("SELECT id FROM organization WHERE LOWER(org_name) = LOWER(?)");
    if ($stmt_org_id) {
        $stmt_org_id->bind_param("s", $volunteer_org);
        $stmt_org_id->execute();
        $stmt_org_id->bind_result($org_id);
        $stmt_org_id->fetch();
        $stmt_org_id->close();
    }
    
    // Now fetch events for this organization
    if ($org_id) {
        $sql_events = "SELECT e.*, o.org_name 
                      FROM events e 
                      JOIN organization o ON e.organization_id = o.id 
                      WHERE e.organization_id = ? 
                      AND e.event_date >= CURDATE() 
                      AND e.event_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                      ORDER BY e.event_date ASC";
        $stmt_events = $conn->prepare($sql_events);
        if ($stmt_events) {
            $stmt_events->bind_param("i", $org_id);
            $stmt_events->execute();
            $result_events = $stmt_events->get_result();
            while ($row = $result_events->fetch_assoc()) {
                $organization_events[] = $row;
            }
            $stmt_events->close();
        }
    }
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
        
        <!-- Events from Volunteer's Organization -->
        <div class="registered-events">
          <br>
          <br>
            <h2>Events from Your Organization (Next 30 Days)</h2>
            <?php if (!empty($volunteer_org)): ?>
                <p><strong>Your Organization:</strong> <?php echo htmlspecialchars($volunteer_org); ?></p>
            <?php endif; ?>
            <table>
                <tr>
                    <th>Event Name</th>
                    <th>Event Date</th>
                    <th>Event Time</th>
                    <th>Duration</th>
                    <th>Location</th>
                    <th>Description</th>
                </tr>
                <?php 
                if (!empty($organization_events)) { 
                    foreach ($organization_events as $event) {
                        echo "<tr>"
                            . "<td>" . htmlspecialchars($event['event_name']) . "</td>"
                            . "<td>" . date('F j, Y', strtotime($event['event_date'])) . "</td>"
                            . "<td>" . date('g:i A', strtotime($event['event_time'])) . "</td>"
                            . "<td>" . rtrim(rtrim(number_format($event['duration'], 2), '0'), '.') . " hrs</td>"
                            . "<td>" . htmlspecialchars($event['location']) . "</td>"
                            . "<td>" . htmlspecialchars($event['description']) . "</td>"
                            . "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No upcoming events found for your organization in the next 30 days.</td></tr>";
                } 
                ?>
            </table>
            <script>
document.querySelectorAll('.event-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        var idx = this.getAttribute('data-index');
        var popup = document.getElementById('popup_' + idx);
        if (this.checked) {
            popup.style.display = '';
        } else {
            popup.style.display = 'none';
        }
    });
});
</script>
            
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
