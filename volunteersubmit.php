<!DOCTYPE html>
<html>

<head>
    <title>Insert Page page</title>
</head>

<body>
    <center>
        <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
        // servername => localhost
        // username => root
        // password => empty
        // database name => staff
        $conn = mysqli_connect("localhost:3306", "zts91xzcemos", "AaravAayansh@1", "rejuvenateganga");
        
        // Check connection
        if($conn === false){
            die("ERROR: Could not connect. " 
                . mysqli_connect_error());
        }
        
        // Taking all 5 values from the form data(input)
        $firstname = $_REQUEST['firstname'];
        $lastname = $_REQUEST['lastname'];
        $email = $_REQUEST['email'];
        $password = $_REQUEST['password'];
        $recoverytext = $_REQUEST['recoverytext'];
        $city = $_REQUEST ['city'];
        $state = $_REQUEST['state'];
        $country = $_REQUEST['country'];
        $dob = $_REQUEST['dob'];
        $organization = $_REQUEST['organization'];
 
        
        // Performing insert query execution
        // here our table name is college
        $sql = "INSERT INTO volunteer VALUES ('$firstname', 
            '$lastname','$email','$password', '$recoverytext','$city','$state','$country','$dob', '$organization')";
        
        if(mysqli_query($conn, $sql)){
            echo '<script>
            alert("Account Creation Successful!");
            setTimeout(function() {
                window.location.href = "signin_volunteer.php";
            }, 100);
        </script>';

        } else{
            echo '<script>
            alert("Error: Account Could not be created");
            setTimeout(function() {
                window.location.href = "volunteeraccount.php";
            }, 100);
        </script>';
        }
        
        // Close connection
        mysqli_close($conn);
        ?>
    </center>
</body>

</html>