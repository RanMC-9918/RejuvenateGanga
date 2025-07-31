<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Sign in - Rejuvenate Ganga</title>
    <link rel="stylesheet" href="../cssfolder/styles.css">
</head>
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

<body>
    <div class = "container2">
    <section class="column">
        <h2>Organization Sign In</h2>
        <?php
        if (isset($_GET['error']) && $_GET['error'] == 'invalid') {
            echo '<div class="error-message">Invalid email or password. Please try again.</div>';
        }
        if (isset($_GET['registration']) && $_GET['registration'] == 'success') {
            echo '<div class="success-message">Registration successful! Please sign in.</div>';
        }
        ?>
        <form action="login_organization.php" method="post">
            
            <label for="email">Organization Email *</label>
            <input type="email" id="email" name="email" placeholder="Your Organization Email" required>
            
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
            <p>New Organization? <a href="createaccount_organization.php" class="create-account-link">Register your Organization</a></p>
            <br><br>
        </form>
    </section>
    </div>
    <script src="../javascriptfolder/index.js"></script>
    <footer>
        <p>&copy; 2024 Rejuvenate Ganga. All Rights Reserved.</p>
     </footer>
</body>
</html> 