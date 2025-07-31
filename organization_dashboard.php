<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'db_connect.php';

// Check if organization is logged in
if (!isset($_SESSION['org_id'])) {
    header("Location: signin_organization.php");
    exit();
}

// Get organization details
$org_id = $_SESSION['org_id'];

// Get organization name from the database
$org_name = '';
$stmt = $conn->prepare("SELECT org_name FROM organization WHERE id = ?");
$stmt->bind_param("i", $org_id);
$stmt->execute();
$stmt->bind_result($org_name);
$stmt->fetch();
$stmt->close();

// Get total volunteers recruited
$volunteer_query = "SELECT COUNT(*) as total_volunteers 
                   FROM volunteer 
                   WHERE organization = ?";
$stmt = $conn->prepare($volunteer_query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $org_name);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$volunteer_result = $stmt->get_result();
$total_volunteers = $volunteer_result->fetch_assoc()['total_volunteers'] ?? 0;

// Get total hours logged
$hours_query = "SELECT SUM(hours) as total_hours 
                FROM volunteer_tracker vt 
                JOIN volunteer v ON vt.email = v.email 
                WHERE v.organization = ?";
$stmt = $conn->prepare($hours_query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $org_name);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$hours_result = $stmt->get_result();
$total_hours = $hours_result->fetch_assoc()['total_hours'] ?? 0;

// Get top volunteers by hours
$top_volunteers_query = "SELECT CONCAT(v.firstname, ' ', v.lastname) as name, SUM(vt.hours) as total_hours 
                        FROM volunteer v 
                        JOIN volunteer_tracker vt ON v.email = vt.email 
                        WHERE v.organization = ? 
                        GROUP BY v.email 
                        ORDER BY total_hours DESC 
                        LIMIT 5";
$stmt = $conn->prepare($top_volunteers_query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $org_name);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$top_volunteers = $stmt->get_result();

// Get organization's events
$events_query = "SELECT * FROM events WHERE organization_id = ? ORDER BY event_date DESC";
$stmt = $conn->prepare($events_query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $org_id);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$events = $stmt->get_result();

// Debug information
$debug_info = [
    'organization' => [
        'id' => $org_id,
        'name' => $org_name
    ],
    'volunteers' => [
        'total' => $total_volunteers,
        'hours' => $total_hours
    ],
    'database' => [
        'connection' => $conn ? 'Connected' : 'Failed',
        'error' => $conn->error ?? 'None'
    ]
];

// Only show debug info if there's an error
if ($conn->error || !$total_volunteers) {
    echo "<!-- Debug Info: " . json_encode($debug_info) . " -->";
}

// Handle timezone selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['timezone'])) {
    $_SESSION['timezone'] = $_POST['timezone'];
}
date_default_timezone_set($_SESSION['timezone'] ?? 'UTC');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Dashboard - Rejuvenate Ganga</title>
    <link rel="stylesheet" href="../cssfolder/styles.css">
    <style>
        /* Base styles */
        .dashboard-container {
            padding: clamp(15px, 3vw, 30px);
            width: min(1600px, 95%);
            margin: clamp(30px, 5vh, 50px) auto 0;
        }

        /* Stats container with responsive grid */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(400px, 100%), 1fr));
            gap: clamp(20px, 3vw, 30px);
            margin-bottom: clamp(20px, 4vh, 30px);
        }

        /* Card styles with responsive padding */
        .stat-card {
            background: #fff;
            padding: clamp(20px, 4vw, 35px);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-card h3 {
            margin: 0 0 clamp(10px, 2vh, 15px) 0;
            color: #333;
            font-size: clamp(1.1em, 2vw, 1.3em);
        }

        .stat-card .number {
            font-size: clamp(2em, 4vw, 3em);
            color: #2c5282;
            font-weight: bold;
        }

        /* Top volunteers section */
        .top-volunteers {
            background: #fff;
            padding: clamp(20px, 4vw, 35px);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: clamp(20px, 4vh, 30px);
            overflow-x: auto; /* For table on small screens */
        }

        .top-volunteers table {
            width: 100%;
            min-width: 300px; /* Minimum width for table */
        }

        /* Events section */
        .events-section {
            background: #fff;
            padding: clamp(20px, 4vw, 35px);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .event-card {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s ease;
        }

        .event-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .event-card h4 {
            margin: 0 0 clamp(10px, 2vh, 15px) 0;
            color: #2c5282;
            font-size: clamp(1.1em, 2vw, 1.3em);
        }

        .event-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }

        .event-details div {
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .event-details strong {
            color: #2c5282;
            display: block;
            margin-bottom: 5px;
        }

        /* Button styles */
        .add-event-btn {
            background: #2c5282;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 20px;
            transition: background-color 0.2s;
        }

        .add-event-btn:hover {
            background: #2a4365;
        }

        /* Welcome message */
        .welcome-message {
            margin-bottom: clamp(20px, 4vh, 35px);
            color: #2c5282;
            font-size: clamp(1.5em, 3vw, 2em);
        }

        /* Media queries for specific screen sizes */
        @media screen and (max-width: 768px) {
            .dashboard-container {
                padding: 15px;
            }

            .stats-container {
                grid-template-columns: 1fr; /* Single column on mobile */
            }

            .event-details {
                grid-template-columns: 1fr; /* Single column on mobile */
            }
        }

        @media screen and (max-width: 480px) {
            .stat-card {
                padding: 15px;
            }

            .top-volunteers, .events-section {
                padding: 15px;
            }

            .add-event-btn {
                width: 100%; /* Full width button on mobile */
            }
        }

        /* Print styles */
        @media print {
            .dashboard-container {
                max-width: 100%;
                margin: 0;
                padding: 0;
            }

            .stat-card, .top-volunteers, .events-section {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            .add-event-btn {
                display: none;
            }
        }

        .volunteer-list {
            grid-column: 1 / -1;  /* Make it span full width */
            max-height: 400px;    /* Limit height */
            overflow: hidden;     /* Hide overflow */
        }
        
        .volunteer-scroll {
            max-height: 320px;    /* Leave room for header */
            overflow-y: auto;     /* Enable vertical scrolling */
            margin-top: 10px;
        }
        
        .volunteer-scroll table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .volunteer-scroll th {
            position: sticky;
            top: 0;
            background: white;
            z-index: 1;
            border-bottom: 2px solid #ddd;
        }
        
        .volunteer-scroll tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .volunteer-scroll tr:hover {
            background-color: #f0f0f0;
        }
        
        @media screen and (max-width: 768px) {
            .volunteer-list {
                max-height: 300px;
            }
            
            .volunteer-scroll {
                max-height: 220px;
            }
        }
        .tz-dropdown {
            float: right;
            margin-top: 8px;
            margin-right: 24px;
        }
        .tz-dropdown label {
            color: #2c5282;
            font-weight: bold;
            margin-right: 4px;
        }
        .tz-dropdown select {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.95em;
        }
        @media (max-width: 600px) {
            .tz-dropdown {
                float: none;
                margin: 8px 0 0 0;
                display: block;
                text-align: right;
            }
        }

        /* Event hover functionality */
        .event-card.event-upcoming:hover .event-actions {
            display: block !important;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <ul style="margin:0; padding:0;">
            <li><a href="htmlfolder/contact.html">Contact Us</a></li>
            <li><a href="htmlfolder/aboutus.html">About Us</a></li>
            <li><a href="htmlfolder/supportus.html">Support Us</a></li>
            <li><a href="htmlfolder/relatedlinks.html">Related Links</a></li>
            <li><a href="htmlfolder/ourmission.html">Our Mission</a></li>
            <li><a href="htmlfolder/aboutus.html">About Ganges</a></li>
            <li><a href="index.html">Home</a></li>
        </ul>
        <div class="tz-dropdown">
            <form method="post" action="" style="display:inline;">
                <label for="timezone"></label>
                <select name="timezone" id="timezone" onchange="this.form.submit()">
                    <?php
                    $timezones = [
                        'America/Chicago' => 'US Central',
                        'Asia/Kolkata' => 'India Standard',
                    ];
                    $user_tz = $_SESSION['timezone'] ?? date_default_timezone_get();
                    foreach ($timezones as $tz => $label) {
                        echo '<option value="' . $tz . '"' . ($tz == $user_tz ? ' selected' : '') . '>' . $label . '</option>';
                    }
                    ?>
                </select>
            </form>
        </div>
    </nav>
    <div class="dashboard-container">
        <h1 class="welcome-message" style="margin-top: 64px;">Welcome, <?php echo htmlspecialchars($org_name ?? ''); ?></h1>
        
        <div class="stats-container">
            <div class="stat-card volunteer-list">
                <h3>Volunteers</h3>
                <?php
                // Get all volunteers with their hours
                $all_volunteers_query = "SELECT CONCAT(v.firstname, ' ', v.lastname) as name, 
                                       COALESCE(SUM(vt.hours), 0) as total_hours
                                       FROM volunteer v 
                                       LEFT JOIN volunteer_tracker vt ON v.email = vt.email 
                                       WHERE v.organization = ? 
                                       GROUP BY v.email 
                                       ORDER BY total_hours DESC";
                $stmt = $conn->prepare($all_volunteers_query);
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("s", $org_name);
                if (!$stmt->execute()) {
                    die("Execute failed: " . $stmt->error);
                }
                $all_volunteers = $stmt->get_result();
                ?>
                <div class="volunteer-scroll">
                    <?php if ($all_volunteers->num_rows > 0): ?>
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align: left; padding: 8px;">Name</th>
                                    <th style="text-align: right; padding: 8px;">Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($volunteer = $all_volunteers->fetch_assoc()): ?>
                                <tr>
                                    <td style="padding: 8px;"><?php echo htmlspecialchars($volunteer['name']); ?></td>
                                    <td style="text-align: right; padding: 8px;"><?php echo number_format($volunteer['total_hours'], 1); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No volunteers found.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="stat-card">
                <h3>Total Hours Logged</h3>
                <div class="number"><?php echo number_format($total_hours, 1); ?></div>
            </div>
        </div>

        <div class="top-volunteers">
            <h2>Top 10 Volunteers</h2>
            <div class="top-volunteers-tiles" style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
            <?php
            // Get top 10 volunteers by hours
            $top10_query = "SELECT CONCAT(v.firstname, ' ', v.lastname) as name, SUM(vt.hours) as total_hours 
                            FROM volunteer v 
                            JOIN volunteer_tracker vt ON v.email = vt.email 
                            WHERE v.organization = ? 
                            GROUP BY v.email 
                            ORDER BY total_hours DESC 
                            LIMIT 10";
            $stmt = $conn->prepare($top10_query);
            $stmt->bind_param("s", $org_name);
            $stmt->execute();
            $top10 = $stmt->get_result();
            $medals = [
                0 => '<span style="font-size:1.5em;">🥇</span>',
                1 => '<span style="font-size:1.5em;">🥈</span>',
                2 => '<span style="font-size:1.5em;">🥉</span>'
            ];
            $i = 0;
            while($vol = $top10->fetch_assoc()): ?>
                <div style="background: linear-gradient(135deg, #f8fafc 60%, #e2e8f0 100%); box-shadow: 0 2px 8px rgba(44,82,130,0.08); border-radius: 12px; padding: 24px 32px; min-width: 220px; max-width: 260px; text-align: center; position: relative;">
                    <div style="font-size: 1.2em; font-weight: bold; margin-bottom: 8px; color: #2c5282;">
                        <?php echo ($i < 3 ? $medals[$i] . ' ' : ''); ?><?php echo htmlspecialchars($vol['name']); ?>
                    </div>
                    <div style="font-size: 1.7em; color: #48BB78; font-weight: bold; margin-bottom: 4px;">
                        <?php echo number_format($vol['total_hours'], 1); ?> hrs
                    </div>
                    <div style="font-size: 0.95em; color: #718096;">Volunteer</div>
                </div>
            <?php $i++; endwhile; ?>
            <?php if ($i == 0): ?>
                <div>No volunteers found.</div>
            <?php endif; ?>
            </div>
        </div>

        <div class="events-section">
            <h2>Events</h2>
            <button class="add-event-btn" onclick="showAddEventForm()">Add New Event</button>
            
            <div id="events-list">
                <?php 
                $upcoming_events = [];
                $completed_events = [];
                while($event = $events->fetch_assoc()): 
                    $start = strtotime($event['event_date'] . ' ' . $event['event_time']);
                    $duration = (float)$event['duration'];
                    $end = strtotime("+{$duration} hours", $start);
                    if (time() < $end) {
                        $upcoming_events[] = $event;
                    } else {
                        $completed_events[] = $event;
                    }
                endwhile;
                ?>
                <?php foreach($upcoming_events as $event): ?>
                <div class="event-card event-upcoming" style="position:relative;">
                    <h4><?php echo htmlspecialchars($event['event_name']); ?></h4>
                    <div class="event-details">
                        <div><strong>Date:</strong> <?php echo date('F j, Y', strtotime($event['event_date'])); ?></div>
                        <div><strong>Time:</strong> <?php echo date('g:i A', strtotime($event['event_time'])); ?></div>
                        <div><strong>Duration:</strong> <?php echo rtrim(rtrim(number_format($event['duration'], 2), '0'), '.') . ' hrs'; ?></div>
                        <div><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></div>
                    </div>
                    <p><?php echo htmlspecialchars($event['description']); ?></p>
                    <div class="event-actions" style="display:none; position:absolute; top:10px; right:10px;">
                        <form class="delete-event-form" method="post" action="delete_event.php" style="display:inline;">
                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this event?');" style="background:#e53e3e; color:white; border:none; padding:6px 12px; border-radius:4px; cursor:pointer; margin-right:5px;">Delete</button>
                        </form>
                        <form class="edit-event-form" method="get" action="edit_event.php" style="display:inline;">
                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                            <button type="submit" style="background:#718096; color:white; border:none; padding:6px 12px; border-radius:4px; cursor:pointer;">Edit</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($upcoming_events)): ?>
                    <p>No upcoming events found.</p>
                <?php endif; ?>

                <?php if (!empty($completed_events)): ?>
                    <h3 style="margin-top:40px; color:#888;">Completed Events</h3>
                    <?php foreach($completed_events as $event): ?>
                    <div class="event-card event-completed" style="opacity:0.7; position:relative;">
                        <span style="position:absolute; top:10px; right:10px; background:#38a169; color:white; padding:4px 10px; border-radius:12px; font-size:0.95em;">Completed</span>
                        <h4><?php echo htmlspecialchars($event['event_name']); ?></h4>
                        <div class="event-details">
                            <div><strong>Date:</strong> <?php echo date('F j, Y', strtotime($event['event_date'])); ?></div>
                            <div><strong>Time:</strong> <?php echo date('g:i A', strtotime($event['event_time'])); ?></div>
                            <div><strong>Duration:</strong> <?php echo rtrim(rtrim(number_format($event['duration'], 2), '0'), '.') . ' hrs'; ?></div>
                            <div><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></div>
                        </div>
                        <p><?php echo htmlspecialchars($event['description']); ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Event Modal -->
    <div id="addEventModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
        <div style="background: white; width: 90%; max-width: 500px; margin: 140px auto 50px auto; padding: 20px; border-radius: 8px;">
            <h2>Add New Event</h2>
            <form id="addEventForm" action="add_event.php" method="post">
                <input type="hidden" name="organization_id" value="<?php echo $org_id; ?>">
                
                <div style="margin-bottom: 15px;">
                    <label for="event_name">Event Name *</label>
                    <input type="text" id="event_name" name="event_name" required style="width: 100%; padding: 8px;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="event_date">Date *</label>
                    <input type="date" id="event_date" name="event_date" required style="width: 100%; padding: 8px;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="event_time">Time *</label>
                    <input type="time" id="event_time" name="event_time" required style="width: 100%; padding: 8px;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="duration">Duration (hours) *</label>
                    <input type="number" id="duration" name="duration" step="0.1" min="0.1" required style="width: 100%; padding: 8px;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="location">Location *</label>
                    <input type="text" id="location" name="location" required style="width: 100%; padding: 8px;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" required style="width: 100%; padding: 8px; height: 100px;"></textarea>
                </div>

                <div style="text-align: right;">
                    <button type="button" onclick="hideAddEventForm()" style="padding: 8px 15px; margin-right: 10px;">Cancel</button>
                    <button type="submit" style="padding: 8px 15px; background: #2c5282; color: white; border: none; border-radius: 4px;">Add Event</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddEventForm() {
            document.getElementById('addEventModal').style.display = 'block';
        }

        function hideAddEventForm() {
            document.getElementById('addEventModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            var modal = document.getElementById('addEventModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        document.getElementById('addEventForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var form = e.target;
            var formData = new FormData(form);
            fetch('add_event.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Event added successfully!');
                    window.location.href = 'organization_dashboard.php';
                } else {
                    alert(data.error || 'Failed to add event.');
                }
            })
            .catch(error => {
                alert('An error occurred. Please try again.');
            });
        });
    </script>

    <footer>
        <p>&copy; 2024 Rejuvenate Ganga. All Rights Reserved.</p>
    </footer>
</body>
</html> 