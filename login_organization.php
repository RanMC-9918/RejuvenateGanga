<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Get user from database
    $stmt = $conn->prepare("SELECT id, password FROM organization WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $stored_password);
        $stmt->fetch();
        // Verify password
        if (password_verify($password, $stored_password)) {
            // Password is correct, start a new session
            $_SESSION['org_id'] = $id;
            $_SESSION['org_name'] = $row['org_name'];
            $_SESSION['org_email'] = $row['email'];
            
            // Redirect to organization dashboard
            header("Location: organization_dashboard.php");
            exit();
        } else {
            // Password is incorrect
            header("Location: signin_organization.php?error=invalid");
            exit();
        }
    } else {
        // Email not found
        header("Location: signin_organization.php?error=invalid");
        exit();
    }
}

$conn->close();
?> 