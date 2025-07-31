<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

$organization_id = $_POST['organization_id'] ?? null;

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = $_POST['event_name'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $duration = isset($_POST['duration']) ? floatval($_POST['duration']) : null;
    $is_verified = isset($_POST['is_verified']) ? 1 : 0;

    if ($organization_id && $event_name && $event_date && $event_time && $location && $description && $duration) {
        $stmt = $conn->prepare("INSERT INTO events (organization_id, event_name, event_date, event_time, location, description, duration, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("isssssdi", $organization_id, $event_name, $event_date, $event_time, $location, $description, $duration, $is_verified);
            if ($stmt->execute()) {
                $success = true;
            } else {
                $error = 'Failed to add event: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = 'Database error: ' . $conn->error;
        }
    } else {
        $error = 'Please fill in all required fields.';
    }
    echo json_encode(['success' => $success, 'error' => $error]);
    exit;
} 