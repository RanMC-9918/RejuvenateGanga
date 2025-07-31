<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Get form data
        $org_name = $conn->real_escape_string($_POST['org_name']);
        $org_type = $conn->real_escape_string($_POST['org_type']);
        $reg_number = $conn->real_escape_string($_POST['reg_number']);
        $est_date = !empty($_POST['est_date']) ? $conn->real_escape_string($_POST['est_date']) : null;
        $website = !empty($_POST['website']) ? $conn->real_escape_string($_POST['website']) : null;
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

        // Validate password match
        if ($_POST['password'] !== $_POST['confirm_password']) {
            throw new Exception("Passwords do not match!");
        }

        // Check if email already exists
        $check_email = "SELECT * FROM organization WHERE email = ?";
        $stmt = $conn->prepare($check_email);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception("Email already registered!");
        }

        // Insert into database using prepared statement
        $sql = "INSERT INTO organization (org_name, org_type, reg_number, est_date, website, email, password) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $org_name, $org_type, $reg_number, $est_date, $website, $email, $password);

        if ($stmt->execute()) {
            // Redirect to sign in page
            header("Location: signin_organization.php?registration=success");
            exit();
        } else {
            throw new Exception("Error: " . $stmt->error);
        }
    } catch (Exception $e) {
        // Log the error and show user-friendly message
        error_log("Registration error: " . $e->getMessage());
        header("Location: createaccount_organization.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}

$conn->close();
?> 