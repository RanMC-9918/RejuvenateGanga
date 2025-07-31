<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Organization - Rejuvenate Ganga</title>
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
    <div class="container2">
        <section class="column">
            <h2>Register Your Organization</h2>
            <form action="register_organization.php" method="post">
                <label for="org_name">Organization Name *</label>
                <input type="text" id="org_name" name="org_name" placeholder="Full legal name" required>

                <label for="org_type">Type of Organization *</label>
                <select id="org_type" name="org_type" required>
                    <option value="">Select Organization Type</option>
                    <option value="NGO">NGO</option>
                    <option value="CSR">CSR Partner</option>
                    <option value="Government">Government</option>
                    <option value="School">School Club</option>
                    <option value="Other">Other</option>
                </select>

                <label for="reg_number">Registration Number *</label>
                <input type="text" id="reg_number" name="reg_number" placeholder="Government-issued or internal ID" required>

                <label for="est_date">Date of Establishment</label>
                <input type="date" id="est_date" name="est_date">

                <label for="website">Website URL</label>
                <input type="url" id="website" name="website" placeholder="https://your-organization.com">

                <label for="email">Organization Email *</label>
                <input type="email" id="email" name="email" placeholder="Your Organization Email" required>

                <label for="password">Password *</label>
                <input type="password" id="password" name="password" placeholder="Create a secure password" required>

                <label for="confirm_password">Confirm Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>

                <button type="submit" class="signin">Register Organization</button>
                <br><br>
                <p>By registering, you agree to our <a class="conditions2" href="htmlfolder/termsandconditions.html">Conditions of Use</a>.</p>
                <br>
                <p>Already have an account? <a href="signin_organization.php">Sign in here</a></p>
            </form>
        </section>
    </div>
    <script src="../javascriptfolder/index.js"></script>
    <footer>
        <p>&copy; 2024 Rejuvenate Ganga. All Rights Reserved.</p>
    </footer>
</body>
</html> 