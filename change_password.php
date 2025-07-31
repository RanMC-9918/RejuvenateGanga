<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Connection
$conn = mysqli_connect("localhost:3306", "zts91xzcemos", "AaravAayansh@1", "rejuvenateganga");

// Check connection
if (!$conn) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Set proper character encoding
mysqli_set_charset($conn, "utf8mb4");

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $old_password = isset($_POST['old_password']) ? trim($_POST['old_password']) : null;
    $new_password = trim($_POST['new_password']);

    $sql = "SELECT password FROM user WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("ERROR: Database query preparation failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 1) {
        mysqli_stmt_bind_result($stmt, $stored_password);
        mysqli_stmt_fetch($stmt);

        // Trim and compare passwords
        $stored_password = trim($stored_password);
        if ($old_password === $stored_password) {
            $update_sql = "UPDATE user SET password = ? WHERE email = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            if (!$update_stmt) {
                die("ERROR: Update preparation failed: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($update_stmt, "ss", $new_password, $email);
            if (mysqli_stmt_execute($update_stmt)) {
                echo '<script>
                    alert("Password changed successfully!");
                    setTimeout(() => window.location.href = "signin.php", 3000);
                </script>';
            } else {
                echo '<script>
                    alert("Update error: ' . mysqli_error($conn) . '");
                    setTimeout(() => window.location.href = "forget_password.php", 3000);
                </script>';
            }
            mysqli_stmt_close($update_stmt);
        } else {
            echo '<script>
                alert("Password mismatch! Stored: ' . htmlspecialchars($stored_password) . ' | Entered: ' . htmlspecialchars($old_password) . '");
                setTimeout(() => window.location.href = "changepassword.php", 3000);
            </script>';
        }
    } else {
        echo '<script>
            alert("User not found!");
            setTimeout(() => window.location.href = "changepassword.php", 3000);
        </script>';
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>