<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['org_id'])) {
    header("Location: signin_organization.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = intval($_POST['event_id']);
    $org_id = $_SESSION['org_id'];

    // Only allow deleting events belonging to this organization and not completed
    $stmt = $conn->prepare("SELECT event_date, event_time, duration FROM events WHERE id = ? AND organization_id = ?");
    $stmt->bind_param("ii", $event_id, $org_id);
    $stmt->execute();
    $stmt->bind_result($event_date, $event_time, $duration);
    if ($stmt->fetch()) {
        $start = strtotime($event_date . ' ' . $event_time);
        $end = strtotime("+{$duration} hours", $start);
        if (time() < $end) {
            $stmt->close();
            $stmt2 = $conn->prepare("DELETE FROM events WHERE id = ? AND organization_id = ?");
            $stmt2->bind_param("ii", $event_id, $org_id);
            $stmt2->execute();
            $stmt2->close();
        } else {
            $stmt->close();
        }
    } else {
        $stmt->close();
    }
}

header("Location: organization_dashboard.php");
exit(); 