<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in - Rejuvenate Ganga</title>
    <link rel="stylesheet" href="../cssfolder/styles.css">
</head>
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

<body>
    <div class = "container2">
    <section class="column">
        <h2>Sign In</h2>
        <form action="login.php" method="post">
            
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" placeholder="Your Email" required>
            
            <label for="password">Password *</label>
            <input type="password" name="password" id="password" placeholder="Password" required>


            <button type="submit" class = "signin">Continue</button>
            <br><br>
            <p>By continuing, you agree to our <a class="conditions2" href="htmlfolder/termsandconditions.html">Conditions of Use</a>.</p>
            <br>
            <p class="help">Need help?</p>
            <ul>
                <li><a href="forget_password.php">Forgot your password?</a></li>
                <li><a href="changepassword.php">Change Password?</a></li>
                <li><a href="htmlfolder/contact.html">Other Issues with Login?</a></li>
            </ul>
            <br>
            <p>New to Our Site? <a href="../createaccount.php" class="create-account-link"></a></p>
            <p><a href="../createaccount.php" class="create-account-link">Register as Donator</a></p>
            <br><br>
        </form>
        <button class="donate" onclick="window.location.href='donation_withoutsignin.php';">Donate Without Signin</button>
    </section>
    </div>
    <script src="../javascriptfolder/index.js"></script>
    <footer>
        <p>&copy; 2024 Rejuvenate Ganga. All Rights Reserved.</p>
     </footer>
</body>
</html>

