<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$servername = "localhost";
$port = 3306;
$username = "zts91xzcemos";
$password = "AaravAayansh@1";
$dbname = "rejuvenateganga";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}

// Check if the email is set in POST request
if (isset($_POST['email'])) {
   $email = strtolower(trim($_POST['email']));
   
   $stmt = $conn->prepare("SELECT firstname, lastname, email, donation_amount_usd, donation_amount_inr, date_of_donation, comment FROM donation WHERE LOWER(email) = ?");
   if (!$stmt) {
      die("Prepare failed: " . $conn->error);
   }
   
   $stmt->bind_param("s", $email);
   $stmt->execute();
   $result = $stmt->get_result();

   if ($result->num_rows > 0) {
       echo "<h2>Donation History for: " . htmlspecialchars($email) . "</h2>";
       echo "<table border='1'>
               <tr>
                   <th>First Name</th>
                   <th>Last Name</th>
                   <th>Email</th>
                   <th>Donation Amount (USD)</th>
                   <th>Donation Amount (INR)</th>
                   <th>Date of Donation</th>
                   <th>Comment</th>
               </tr>";
       while ($row = $result->fetch_assoc()) {
           $usd = $row["donation_amount_usd"] ?? 0;
           $inr = $row["donation_amount_inr"] ?? 0;

           $usd = $usd !== null ? $usd : 0;
           $inr = $inr !== null ? $inr : 0;

           echo "<tr>
                   <td>" . htmlspecialchars($row["firstname"] ?? '') . "</td>
                   <td>" . htmlspecialchars($row["lastname"] ?? '') . "</td>
                   <td>" . htmlspecialchars($row["email"] ?? '') . "</td>
                   <td>$" . htmlspecialchars($usd) . "</td>
                   <td>₹" . htmlspecialchars($inr) . "</td>
                   <td>" . htmlspecialchars($row["date_of_donation"] ?? '') . "</td>
                   <td>" . htmlspecialchars($row["comment"] ?? '') . "</td>
                 </tr>";
       }
       echo "</table>";
   } else {
       echo "<p>No records found for email: " . htmlspecialchars($email) . "</p>";
   }
   $stmt->close();
   
   echo "<br><br><button style='background-color: #00674F; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;' onclick=\"window.location.href='donation.php'\">Back to Donation Dashboard</button>";
} else {
   echo "<p>Please enter the registered email address.</p>";
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        td, th { 
            border: 1px solid black; 
            padding: 10px; 
            text-align: center;
        }
    </style>
</head>
<body>
</body>
</html>
