<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$servername = "localhost";
$username = "zts91xzcemos";
$password = "AaravAayansh@1";
$dbname = "rejuvenateganga";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if email and recoverytext are set in POST request
if (isset($_POST['email']) && isset($_POST['recoverytext'])) {
    // Convert email to lowercase and trim spaces for consistency
    $email = trim(strtolower($_POST['email']));
    $recoverytext = trim($_POST['recoverytext']);

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT firstname, lastname, recoverytext, email, city, state, country, password FROM volunteer WHERE email = ? AND recoverytext = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $email, $recoverytext);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any records were found
    if ($result->num_rows > 0) {
        echo "<h2>Private Information for: " . htmlspecialchars($email) . "</h2>";
        echo "<table border='1'>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Recover Text</th>
                    <th>Email</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Country</th>
                    <th>Password</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row["firstname"]) . "</td>
                    <td>" . htmlspecialchars($row["lastname"]) . "</td>
                    <td>" . htmlspecialchars($row["recoverytext"]) . "</td>
                    <td>" . htmlspecialchars($row["email"]) . "</td>
                    <td>" . htmlspecialchars($row["city"]) . "</td>
                    <td>" . htmlspecialchars($row["state"]) . "</td>
                    <td>" . htmlspecialchars($row["country"]) . "</td>
                    <td>" . htmlspecialchars($row["password"]) . "</td>
                  </tr>";
        }
        echo "</table>";

        echo '<script>
            alert("User information retrieved successfully! You will be redirected in 5 seconds...");
            setTimeout(function() {
                window.location.href = "signin_volunteer.php"; // Redirect to dashboard or another page
            }, 5000); // 5-second delay before redirection
        </script>';
    } else {
        echo '<script>
            alert("ERROR: Incorrect email or recovery text. You will be redirected in 3 seconds...");
            setTimeout(function() {
                window.location.href = "forget_password.php"; // Redirect to recovery page
            }, 3000);
        </script>';
    }

    $stmt->close();
} else {
    echo '<script>
        alert("Please enter an email address and recovery text. You will be redirected in 3 seconds...");
        setTimeout(function() {
            window.location.href = "forget_password.php"; // Redirect to recovery page
        }, 3000);
    </script>';
}

$conn->close();
?>

<DOCTYPE html>
<head>
    <style>
        td, th { 
            border: 1px solid black; 
            padding: 10px; 
            text-align: center;
        }
    </style>
</head>
</html>
