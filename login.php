<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$servername = "localhost:3306";
$username = "zts91xzcemos";
$password = "AaravAayansh@1";
$dbname = "rejuvenateganga";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    if (empty($email) || empty($password)) {
        echo "Please fill in both fields.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            // Retrieve the stored password value.
            $stmt->bind_result($stored_password);
            $stmt->fetch();
            
            $valid = false;
            // Check if the stored password appears hashed (e.g. starts with "$2y$")
            if (strpos($stored_password, '$2y$') === 0) {
                // Use password_verify if it's a hash.
                $valid = password_verify($password, $stored_password);
            } else {
                // For plain-text stored passwords, do a direct comparison.
                if ($password === $stored_password) {
                    $valid = true;
                }
            }
            
            if ($valid) {
                $_SESSION['email'] = $email;
                echo "Login successful.";
                header('Location: donation.php');
                exit();
            } else {
                echo '<script>
            alert("Error: Invaild Password");
            setTimeout(function() {
                window.location.href = "signin.php";
            }, 100);
        </script>';
            }
        } else {
            echo '<script>
            alert("Error: No account found with given Email");
            setTimeout(function() {
                window.location.href = "signin.php";
            }, 100);
        </script>';
        }
        
        $stmt->close();
    }
}

$conn->close();
?>
