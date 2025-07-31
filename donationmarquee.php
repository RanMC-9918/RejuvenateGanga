<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establish connection
$conn = mysqli_connect("localhost:3306", "zts91xzcemos", "AaravAayansh@1", "rejuvenateganga");
if (!$conn) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Ensure all required fields exist
$firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : null;
$lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : null;
$email = isset($_POST['email']) ? trim($_POST['email']) : null;
$donation_amount = isset($_POST['donation_amount']) ? trim($_POST['donation_amount']) : null;
$date_of_donation = isset($_POST['date_of_donation']) ? trim($_POST['date_of_donation']) : null;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : null;
echo("$firstname");

$sql3 = "SELECT firstname, lastname FROM donation ORDER BY donation_amount DESC LIMIT 5";
$stmt3 = $conn->prepare($sql3);
$stmt3->execute();
mysqli_stmt_execute($stmt3);
$result2 = mysqli_stmt_get_result($stmt3);
if ($result2 && mysqli_num_rows($result2) > 0) {
    while ($row = $result2 -> fetch_row()) {
      printf ("%s (%s)\n", $row[0], $row[1]);
    }
    $result2 -> free_result();
  }

?>