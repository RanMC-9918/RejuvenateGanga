<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Create Account - Rejuvenate Ganga</title>
   <link rel="stylesheet" href="../cssfolder/styles.css">

   <style>
     .conditions
     {
         color: #0000EE;
     }
      body {
         margin: 0;
         padding: 0;
         font-family: Arial, sans-serif;
      }

      .column2 {
         display: flex;
         justify-content: left;
         align-items: left;
         text-align: center;
         flex-direction: column;
         padding: 20px;
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
      @media (max-width: 768px) {
  .column2 {
    align-items: center;
    justify-content: center;
    width:100%;
    right: 130%; 
  }

  form {
    margin: 0 auto;
    width: 100%;
  }
  .btn {
         width: auto; 
        margin-left: -80px;
        padding-left: 0;

      }
}

   </style>
</head>

<body>
<nav class="navbar">
   <ul>
      <li><a href="htmlfolder/contact.html">Contact Us</a></li>
      <li><a href="htmlfolder/aboutme.html">About Us</a></li>
      <li><a href="htmlfolder/supportus.html">Support Us</a></li>
      <li><a href="htmlfolder/relatedlinks.html">Related Links</a></li>
      <li><a href="htmlfolder/ourmission.html">Our Mission</a></li>
      <li><a href="htmlfolder/aboutus.html">About Ganges</a></li>
      <li><a href="index.html">Home</a></li>
   </ul>
</nav>

<section class="column2">
   <h2>Create Account</h2>
   <form action="submit_form.php" method="post">
      <label for="firstname">First Name *</label>
      <input type="text" id="firstname" name="firstname" placeholder="First Name" required>

      <label for="lastname">Last Name *</label>
      <input type="text" id="lastname" name="lastname" placeholder="Last Name" required>

      <label for="recoverytext">Security Question *</label>
      <input type="text" id="recoverytext" name="recoverytext" placeholder="Enter High School Name" required>

      <label for="email">Email *</label>
      <input type="email" id="email" name="email" placeholder="Your Email" required>

      <label for="city">City *</label>
      <input type="text" id="city" name="city" placeholder="Enter City" required>

      <label for="state">State *</label>
      <input type="text" id="state" name="state" placeholder="Enter State" required>

      <label for="country">Country *</label>
      <input type="text" id="country" name="country" placeholder="Enter Country" required>

      <label for="password">Password *</label>
      <input type="password" id="password" name="password" placeholder="Enter Password" required>

      <label for="re-password">Re-enter Password *</label>
      <input type="password" id="re-password" name="re-password" placeholder="Re-enter Password" required>

      
      <p class="help">By continuing, you agree to our <a href="htmlfolder/termsandconditions.html">Conditions of Use</a> and Privacy Notice.</p>
    <button class="btn" type="submit">Continue</button>
      <p class="help">Need help?</p>
      <ul class="other">
         <li><a href="forget_password.php">Forgot your password?</a></li>
         <li><a href="changepassword.php">Change your password?</a></li>
         <li><a href="htmlfolder/contact.html">Other issues with Sign-In</a></li>
      </ul>

      <p class="other">Already have an account? <a href="signin.php" class="sign-in-link">Sign in</a></p>
   </form>
</section>

<footer>
   <p>&copy; 2024 Rejuvenate Ganga. All Rights Reserved.</p>
</footer>

<script src="../javascriptfolder/form_validation.js"></script>
<script src="../javascriptfolder/index.js"></script>

</body>
</html>
