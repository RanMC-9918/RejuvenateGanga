<?php
session_start();
require_once 'db_connect.php';

// Handle timezone selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['timezone'])) {
    $_SESSION['timezone'] = $_POST['timezone'];
}
date_default_timezone_set($_SESSION['timezone'] ?? 'UTC');

if (!isset($_SESSION['org_id'])) {
    header("Location: signin_organization.php");
    exit();
}

$org_id = $_SESSION['org_id'];
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : (isset($_POST['event_id']) ? intval($_POST['event_id']) : 0);

// Debug information
if ($event_id == 0) {
    echo "Error: No event ID provided. GET: " . ($_GET['event_id'] ?? 'not set') . ", POST: " . ($_POST['event_id'] ?? 'not set');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update event
    $event_name = $_POST['event_name'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? '';
    $duration = isset($_POST['duration']) ? floatval($_POST['duration']) : 1;
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $is_verified = isset($_POST['is_verified']) ? 1 : 0;

    // Only allow editing if event is not completed
    $stmt = $conn->prepare("SELECT event_date, event_time, duration FROM events WHERE id = ? AND organization_id = ?");
    $stmt->bind_param("ii", $event_id, $org_id);
    $stmt->execute();
    $stmt->bind_result($db_event_date, $db_event_time, $db_duration);
    if ($stmt->fetch()) {
        $start = strtotime($db_event_date . ' ' . $db_event_time);
        $end = strtotime("+{$db_duration} hours", $start);
        if (time() < $end) {
            $stmt->close();
            $stmt2 = $conn->prepare("UPDATE events SET event_name=?, event_date=?, event_time=?, duration=?, location=?, description=?, is_verified=? WHERE id=? AND organization_id=?");
            $stmt2->bind_param("ssssssiii", $event_name, $event_date, $event_time, $duration, $location, $description, $is_verified, $event_id, $org_id);
            $stmt2->execute();
            $stmt2->close();
        } else {
            $stmt->close();
        }
    } else {
        $stmt->close();
    }
    header("Location: organization_dashboard.php");
    exit();
}

// Fetch event details
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ? AND organization_id = ?");
$stmt->bind_param("ii", $event_id, $org_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
    echo "Event not found or you do not have permission to edit this event.<br>";
    echo "Event ID: $event_id<br>";
    echo "Org ID: $org_id<br>";
    echo "Current timezone: " . date_default_timezone_get() . "<br>";
    exit();
}

// Prevent editing if event is completed
$start = strtotime($event['event_date'] . ' ' . $event['event_time']);
$duration = (float)$event['duration'];
$end = strtotime("+{$duration} hours", $start);
$current_time = time();

if ($current_time > $end) {
    echo "This event is already completed and cannot be edited.<br>";
    echo "Event start: " . date('Y-m-d H:i:s', $start) . "<br>";
    echo "Event end: " . date('Y-m-d H:i:s', $end) . "<br>";
    echo "Current time: " . date('Y-m-d H:i:s', $current_time) . "<br>";
    echo "Timezone: " . date_default_timezone_get() . "<br>";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Event</title>
    <link rel="stylesheet" href="../cssfolder/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: white;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }

        .container2 {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px 20px;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
            min-height: 150vh;
            display: flex;
            flex-direction: column;
        }

        .container2::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        h2 {
            color: #2d3748;
            font-size: clamp(1.8em, 4vw, 2.2em);
            font-weight: 700;
            margin: 0 0 30px 0;
            text-align: center;
            position: relative;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }

        form {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .form-content {
            flex: 1;
            width: 150%;
            margin-left: -25%;
            position: relative;
        }

        .form-group {
            margin-bottom: clamp(20px, 3vh, 25px);
        }

        label {
            display: block;
            color: #4a5568;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: clamp(0.85em, 2vw, 0.95em);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input[type="text"], input[type="date"], input[type="time"], input[type="number"], textarea {
            width: 100%;
            padding: clamp(12px, 2vh, 15px);
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: clamp(14px, 2.5vw, 16px);
            transition: all 0.3s ease;
            box-sizing: border-box;
            background: #f7fafc;
            min-width: 100%;
        }

        input[type="text"]:focus, input[type="date"]:focus, input[type="time"]:focus, input[type="number"]:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        textarea {
            min-height: clamp(100px, 15vh, 120px);
            resize: vertical;
            font-family: inherit;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin: clamp(20px, 3vh, 25px) 0;
            padding: clamp(12px, 2vh, 15px);
            background: #f7fafc;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
        }

        .checkbox-group input[type="checkbox"] {
            margin-right: 12px;
            transform: scale(1.2);
            accent-color: #667eea;
        }

        .checkbox-group label {
            margin: 0;
            text-transform: none;
            letter-spacing: normal;
            color: #4a5568;
        }

        .button-group {
            display: flex;
            gap: clamp(10px, 2vw, 15px);
            margin-top: auto;
            padding-top: clamp(20px, 4vh, 30px);
            justify-content: center;
            background: white;
        }

        .btn {
            padding: clamp(12px, 2.5vh, 15px) clamp(20px, 4vw, 30px);
            border: none;
            border-radius: 12px;
            font-size: clamp(14px, 2.5vw, 16px);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            min-width: clamp(100px, 20vw, 120px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
            border: 2px solid #cbd5e0;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .container2 {
                margin: 0;
                padding: clamp(20px, 4vw, 30px);
                min-height: 95vh;
            }
            
            .button-group {
                flex-direction: column;
                gap: 10px;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container2">
        <h2>Edit Event</h2>
        <form method="post" action="edit_event.php">
            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
            
            <div class="form-content">
                <div class="form-group">
                    <label for="event_name">Event Name *</label>
                    <input type="text" id="event_name" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="event_date">Event Date *</label>
                    <input type="date" id="event_date" name="event_date" value="<?php echo $event['event_date']; ?>" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label for="event_time">Event Time *</label>
                    <input type="time" id="event_time" name="event_time" value="<?php echo $event['event_time']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="duration">Duration (hours) *</label>
                    <input type="number" id="duration" name="duration" step="0.1" min="0.1" value="<?php echo htmlspecialchars($event['duration']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="location">Location *</label>
                    <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($event['description']); ?></textarea>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="is_verified" name="is_verified" <?php if ($event['is_verified']) echo 'checked'; ?>>
                    <label for="is_verified">Verified Event</label>
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-primary">Update Event</button>
                <a href="organization_dashboard.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

<script>
// Prevent picking a past date/time in the event edit form
const today = new Date();
const yyyy = today.getFullYear();
const mm = String(today.getMonth() + 1).padStart(2, '0');
const dd = String(today.getDate()).padStart(2, '0');
const minDate = `${yyyy}-${mm}-${dd}`;
document.getElementById('event_date').setAttribute('min', minDate);
document.getElementById('event_time').addEventListener('input', function() {
    const dateInput = document.getElementById('event_date');
    const timeInput = document.getElementById('event_time');
    const selectedDate = new Date(dateInput.value + 'T' + timeInput.value);
    if (selectedDate < new Date()) {
        alert('You cannot pick a time in the past.');
        timeInput.value = '';
    }
});
</script>
</body>
</html> 