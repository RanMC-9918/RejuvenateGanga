<!DOCTYPE html>
<html>

<head>
    <title>Insert Page</title>
</head>

<body>
    <center>
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        // Connect to the database
        $conn = mysqli_connect("localhost:3306", "zts91xzcemos", "AaravAayansh@1", "rejuvenateganga");
        
        // Check connection
        if($conn === false){
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }
        
        // Retrieve values from the form
        $firstname = $_REQUEST['firstname'];
        $lastname = $_REQUEST['lastname'];
        $recoverytext = $_REQUEST['recoverytext'];
        $email = $_REQUEST['email'];
        $city = $_REQUEST['city'];
        $state = $_REQUEST['state'];
        $country = $_REQUEST['country'];
        $password = $_REQUEST['password'];
        
        // Check for duplicate email before inserting
        $duplicateQuery = "SELECT email FROM user WHERE email = '$email'";
        $duplicateResult = mysqli_query($conn, $duplicateQuery);
        if(mysqli_num_rows($duplicateResult) > 0){
            echo '<script>
                    alert("Email already exists. Please use a different email or login.");
                    setTimeout(function() {
                        window.location.href = "createaccount.php";
                    }, 100);
                  </script>';
            exit;
        }
        
        // Insert the new user into the database
        $sql = "INSERT INTO user (firstname, lastname, recoverytext, email, city, state, country, password) 
                VALUES ('$firstname', '$lastname', '$recoverytext', '$email', '$city', '$state', '$country', '$password')";
        
        if(mysqli_query($conn, $sql)){
            echo '<script>
                    alert("Account Creation Successful!");
                    setTimeout(function() {
                        window.location.href = "signin.php";
                    }, 100);
                  </script>';
        } else {
            echo '<script>
                    alert("Error: Account Could not be created");
                    setTimeout(function() {
                        window.location.href = "createaccount.php";
                    }, 100);
                  </script>';
        }
        
        // Close connection
        mysqli_close($conn);
        ?>
    </center>
</body>

</html>
