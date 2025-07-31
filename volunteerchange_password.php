<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Connection
$conn = mysqli_connect("localhost:3306", "zts91xzcemos", "AaravAayansh@1", "rejuvenateganga");

// Check connection
if (!$conn) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']); // Email to identify the user
    $old_password = isset($_POST['old_password']) ? trim($_POST['old_password']) : null;
    $new_password = trim($_POST['new_password']); // New password to set

    // Retrieve the stored plain text password from the database
    $sql = "SELECT password FROM volunteer WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("ERROR: Database query preparation failed.");
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 1) {
        mysqli_stmt_bind_result($stmt, $stored_password);
        mysqli_stmt_fetch($stmt);

        // Verify the old password (plain text comparison)
        if ($old_password === $stored_password) {
            // Update the password in the database without hashing
            $update_sql = "UPDATE volunteer SET password = ? WHERE email = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            if (!$update_stmt) {
                die("ERROR: Database update query preparation failed.");
            }
            mysqli_stmt_bind_param($update_stmt, "ss", $new_password, $email);

            // Execute the update statement
            if (mysqli_stmt_execute($update_stmt)) {
                echo '<script>
                    alert("Password changed successfully! You will be redirected in 3 seconds...");
                    setTimeout(function() {
                        window.location.href = "signin_volunteer.php";
                    }, 3000);
                </script>';
                exit();
            } else {
                echo '<script>
                    alert("ERROR: Could not update password. You will be redirected in 3 seconds...");
                    setTimeout(function() {
                        window.location.href = "volunteerforget_password.php";
                    }, 3000);
                </script>';
                exit();
            }

            mysqli_stmt_close($update_stmt);
        } else {
            echo '<script>
                alert("ERROR: The old password is incorrect. You will be redirected in 3 seconds...");
                setTimeout(function() {
                    window.location.href = "volunteerchangepassword.php";
                }, 3000);
            </script>';
            exit();
        }
    } else {
        echo '<script>
            alert("ERROR: No user found with this email.");
            setTimeout(function() {
                window.location.href = "volunteerchangepassword.php";
            }, 3000);
        </script>';
        exit();
    }

    mysqli_stmt_close($stmt);
}

// Close connection
mysqli_close($conn);
?>
