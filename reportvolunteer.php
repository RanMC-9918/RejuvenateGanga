<?php
// Database connection parameters
$servername = "localhost:3306";
$username = "zts91xzcemos";
$password = "AaravAayansh@1";
$dbname = "rejuvenateganga";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}

// Check if the email is set in POST request
if (isset($_POST['email'])) {
   // Convert email to lowercase and trim spaces for a consistent match
   $email = strtolower(trim($_POST['email']));
   
   // Prepare and execute the SQL statement using LOWER() on the email column
   $stmt = $conn->prepare("SELECT firstname, lastname, email, event_enrolled, work_performed, hours FROM volunteer_tracker WHERE LOWER(email) = ?");
   if (!$stmt) {
      die("Prepare failed: " . $conn->error);
   }
   
   $stmt->bind_param("s", $email);
   $stmt->execute();
   $result = $stmt->get_result();

   // Check if any records were found
   if ($result->num_rows > 0) {
       echo "<h2>Donation History for: " . htmlspecialchars($email) . "</h2>";
       echo "<table border='1'>
               <tr>
                   <th>First Name</th>
                   <th>Last Name</th>
                   <th>Email</th>
                   <th>Event Enrolled</th>
                   <th>Work Performed</th>
                   <th>Hours</th>
               </tr>";
       while ($row = $result->fetch_assoc()) {
           echo "<tr>
                   <td>" . htmlspecialchars($row["firstname"]) . "</td>
                   <td>" . htmlspecialchars($row["lastname"]) . "</td>
                   <td>" . htmlspecialchars($row["email"]) . "</td>
                   <td>" . htmlspecialchars($row["event_enrolled"]) . "</td>
                   <td>" . htmlspecialchars($row["work_performed"]) . "</td>
                   <td>" . htmlspecialchars($row["hours"]) . "</td>
                 </tr>";
       }
       echo "</table>";
   } else {
       echo "<p>No records found for email: " . htmlspecialchars($email) . "</p>";
   }
   $stmt->close();
   
   // Add a styled button underneath the table or message
   echo "<br><br><button style='background-color: #00674F; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;' onclick=\"window.location.href='volunteer_hours.php'\">Back to Volunteer Dashboard</button>";
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
