<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in- Rejuvenate Ganga</title>
    <link rel="stylesheet" href="../cssfolder/styles.css">
</head>
<style>
    
</style>
<nav class="navbar">
    <ul>
        <li><a href="htmlfolder/contactus.html">Contact Us</a></li>
        <li><a href="htmlfolder/aboutme.html">About Us</a></li>
        <li><a href="htmlfolder/supportus.html">Support Us</a></li>
        <li><a href="htmlfolder/relatedlinks.html">Related Links</a></li>
        <li><a href="htmlfolder/ourmission.html">Our Mission</a></li>
        <li><a href="htmlfolder/aboutus.html">About Ganges</a></li>
        <li><a href="index.html">Home</a></li>
     </ul>
</nav>
<style>
    .Volunteer{
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-left: 525px;
    text-decoration: none;
    margin-top: 150px;
    position: relative;
    height: auto;
    width: 600px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
form {
    display: flex;
    flex-direction: column;
    background-color: #f9f9f9;
    padding: 20px;
    margin-bottom: 70px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    min-height: 100vh; /* Always at least full screen */
    height: auto; /* Grow with content */
    
}



      label {
         margin-bottom: 5px;
         text-align: left;
      }

      input {
         padding: 8px;
         margin-bottom: 15px;
         width: 50%;
         box-sizing: border-box;
      }

      .btn {
         padding: 10px;
         background-color: #4CAF50;
         color: white;
         text-align: center !important;
         border: none;
         cursor: pointer;
         margin-top: 10px;

      }

      .help, .other {
         text-align: left;
         top: 200px;
         postion: relative;
         text-decoration: none;
      }

      .other li {
         margin-bottom: 6px;
         text-decoration: none;
      }
</style>
<body class="volunteerbody">
<div class="Volunteer" id="Volunteer">
    <h2 class = Volunteeraccount>Register as a Volunteer</h2>
    <form action="volunteersubmit.php" method="post">
    <label for="firstname">First Name *</label>
    <input type="text" id="firstname" name="firstname" placeholder="First Name" required>

    <label for="lastname">Last Name *</label>
    <input type="text" id="lastname" name="lastname" placeholder="Last Name" required>

    <label for="email">Email *</label>
    <input type="email" id="email" name="email" placeholder="Your Email" required>

    <label for="password">Password *</label>
    <input type="password" id="password" name="password" placeholder="Enter password" required>

    <label for="re-password">Re-enter password *</label>
    <input type="password" id="re-password" name="re-password" placeholder="Re-enter password" required>

    <label for="recoverytext">Security Question *</label>
    <input type="text" id="recoverytext" name="recoverytext" placeholder="Enter Highschool Name" required>

    <label for="dob">Date of Birth *</label>
    <input type="date" id="dob" name="dob" placeholder="Enter Date of Birth" required>

    <label for="city">City *</label>
    <input type="city" id="city" name="city" placeholder="Enter city" required>

    <label for="state">State *</label>
    <input type="state" id="state" name="state" placeholder="Enter state" required>

    <label for="country">Country *</label>
    <input type="country" id="country" name="country" placeholder="Enter country" required>

    <label for="organization">Organization *</label>
    <input type="organization" id="organization" name="organization" placeholder="Enter Organization">
<ul class="other">
    <li>Need Help?</li>
    <li><a href="volunteerforget_password.php">Forgot your password?</a></li>
    <li><a href="volunteerchangepassword.php">Change your password?</a></li>
    <li><a href="htmlfolder/contact.html">Other issues with Sign-In</a></li>
    </ul>
    <button class="volunteerbtn">Continue</button>
    </form>
   
    <script src="../javascriptfolder/form_validation.js"></script>
</div>
</body>
<script src="../javascriptfolder/index.js"></script>
<footer>
    <p>&copy; 2024 Rejuvenate Ganga. All Rights Reserved.</p>
 </footer>
</html>