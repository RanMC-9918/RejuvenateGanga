<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "AaravAayansh@1";
$dbname = "rejuvenateganga";

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Use the session email if set
$email = isset($_SESSION['email']) ? $_SESSION['email'] : null;
$message = "";

// Handle Change Password form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (!$email) {
        $message = "Session expired. Please log in again.";
    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match!";
    } else {
        // Fetch the current hashed password from the database
        $stmt = $conn->prepare("SELECT password FROM volunteer WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashed_password);
            $stmt->fetch();

            // Verify the current password
            if (password_verify($current_password, $hashed_password)) {
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password in the database
                $update_stmt = $conn->prepare("UPDATE volunteer SET password = ? WHERE email = ?");
                $update_stmt->bind_param("ss", $new_hashed_password, $email);
                
                if ($update_stmt->execute()) {
                    $message = "Password successfully changed!";
                } else {
                    $message = "Error updating password. Please try again.";
                }

                $update_stmt->close();
            } else {
                $message = "Current password is incorrect!";
            }
        } else {
            $message = "User not found!";
        }
        
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password - Rejuvenate Ganga</title>
  <!-- Import Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Economica:wght@400;700&display=swap" rel="stylesheet">
  <!-- External CSS -->
  <link rel="stylesheet" href="../cssfolder/styles.css">
  <style>
  body {
    font-family: 'Economica', sans-serif;
    background-color: #EAEAEA;
    color: #333;
    text-align: center;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  .main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
  }

  .container_donation, 
  .password-container {
    width: 100%;
    max-width: 600px;
    margin: 80px auto 20px auto;
    padding: 20px;
    background: #F8F8F8;
    border-radius: 8px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .container_donation h2, 
  .password-container h2 {
    margin-top: 0px;
    color: #444;
    font-size: 1.8rem;
  }

  input, textarea, select {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #555;
    background-color: #FFF;
    color: #333;
    border-radius: 5px;
    font-size: 1rem;
  }

  .donate-btn, 
  .history-btn, 
  .password-btn {
    background-color: #008CBA;
    color: white;
    padding: 10px 15px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    border-radius: 5px;
    width: 100%;
    margin-top: 10px;
    transition: 0.3s;
  }

  .donate-btn:hover, 
  .history-btn:hover, 
  .password-btn:hover {
    background-color: #005f73;
  }

  .password-message {
    margin-top: 10px;
    color: yellow;
    font-size: 14px;
  }

  footer {
    background: #fff;
    padding: 15px 0;
    box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
    width: 100%;
  }

  footer p {
    margin: 0;
    color: #333;
    font-size: 14px;
  }

  .access-history {
    margin-top: 20px;
  }

  label.toggle-password {
    font-size: 14px;
    display: inline-block;
    margin-top: 10px;
    cursor: pointer;
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .container_donation, 
    .password-container {
      margin: 40px 10px 20px 10px;
      padding: 15px;
    }

    .container_donation h2, 
    .password-container h2 {
      font-size: 1.5rem;
    }

    input, textarea, select {
      font-size: 0.9rem;
    }

    .donate-btn, 
    .history-btn, 
    .password-btn {
      font-size: 15px;
    }
  }

  @media (max-width: 480px) {
    .container_donation, 
    .password-container {
      margin: 30px 5px 15px 5px;
      padding: 10px;
    }

    .container_donation h2, 
    .password-container h2 {
      font-size: 1.3rem;
    }
  }
</style>

</head>
<body>
  <!-- Navigation Bar (unchanged) -->
  <nav class="navbar">
    <ul>
      <li><a href="htmlfolder/contact.html">Contact Us</a></li>
      <li><a href="htmlfolder/aboutus.html">About Us</a></li>
      <li><a href="htmlfolder/supportus.html">Support Us</a></li>
      <li><a href="htmlfolder/relatedlinks.html">Related Links</a></li>
      <li><a href="htmlfolder/ourmission.html">Our Mission</a></li>
      <li><a href="htmlfolder/aboutus.html">About Ganges</a></li>
      <li><a href="index.html">Home</a></li>
    </ul>
  </nav>
<br>
<br>
<br>
<br>
  <!-- Password Recovery Form -->
  <div class="container_donation access-history">
    <h2>Password Recovery</h2>
    <form action="volunteer_password.php" method="post">
      <label>Email Address</label>
      <input type="email" placeholder="Enter Email" id="email-history" name="email" required>
      <label>Security Question</label>
      <input type="text" placeholder="Enter your High school name" id="recoverytext" name="recoverytext" required>
      <button class="history-btn" type="submit">Submit</button>
    </form>
  </div>
  <script>
    function togglePasswordVisibility() {
      var oldPassword = document.getElementById("old_password");
      var newPassword = document.getElementById("new_password");
      var confirmPassword = document.getElementById("confirm_password");
      if(document.getElementById("togglePassword").checked) {
        oldPassword.type = "text";
        newPassword.type = "text";
        confirmPassword.type = "text";
      } else {
        oldPassword.type = "password";
        newPassword.type = "password";
        confirmPassword.type = "password";
      }
    }
  </script>
</body>
</html>
