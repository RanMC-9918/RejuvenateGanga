<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establish connection
$conn = mysqli_connect("localhost:3306", "zts91xzcemos", "AaravAayansh@1", "rejuvenateganga");
if (!$conn) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Initialize variables
$firstname = $lastname = $email = $date_of_donation = $comment = $donation_amount_usd = $donation_amount_inr = null;
$submission_success = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $currency = isset($_POST['currency']) ? trim($_POST['currency']) : null;
    $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : null;
    $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $date_of_donation = isset($_POST['date_of_donation']) ? trim($_POST['date_of_donation']) : null;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    $donation_amount = isset($_POST['donation_amount']) ? trim($_POST['donation_amount']) : null;

    // Handle currency conversion
    if ($currency === "dollar") {
        $donation_amount_usd = $donation_amount;
        $donation_amount_inr = null;
    } elseif ($currency === "rupee") {
        $donation_amount_inr = $donation_amount;
        $donation_amount_usd = null;
    }

    // Validate required fields
    $errors = [];
    if (!$firstname) $errors[] = "First name is required";
    if (!$lastname) $errors[] = "Last name is required";
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (!$date_of_donation) $errors[] = "Date of donation is required";
    if (!$donation_amount || !is_numeric($donation_amount)) $errors[] = "Valid donation amount is required";

    if (empty($errors)) {
        // Prepare and execute insert statement
        $sql = "INSERT INTO donation (firstname, lastname, email, donation_amount_usd, donation_amount_inr, date_of_donation, comment) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssss", $firstname, $lastname, $email, $donation_amount_usd, $donation_amount_inr, $date_of_donation, $comment);

        if (mysqli_stmt_execute($stmt)) {
            $submission_success = true;
        } else {
            $errors[] = "Database error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Retrieve last donation for the current email
$transactions = [];
if (isset($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $sql2 = "SELECT * FROM donation WHERE email = ? ORDER BY date_of_donation DESC LIMIT 1";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "s", $email);
    mysqli_stmt_execute($stmt2);
    $result = mysqli_stmt_get_result($stmt2);
    if ($result && mysqli_num_rows($result) > 0) {
        $transactions = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

// Get top donors
$sql3 = "SELECT firstname, lastname FROM donation 
         ORDER BY COALESCE(donation_amount_usd, 0) + COALESCE(donation_amount_inr, 0) DESC LIMIT 10";
$result2 = mysqli_query($conn, $sql3);
$name1 = "";
if ($result2 && mysqli_num_rows($result2) > 0) {
    while ($row = mysqli_fetch_row($result2)) {
        $name1 .= htmlspecialchars($row[0] . " " . $row[1]) . " | ";
    }
    mysqli_free_result($result2);
}

// Get user information from session
$user_name = "Your";
if (isset($_SESSION['email'])) {
    $conn_user = new mysqli("localhost:3306", "zts91xzcemos", "AaravAayansh@1", "rejuvenateganga");
    if (!$conn_user->connect_error) {
        $stmt = $conn_user->prepare("SELECT firstname FROM user WHERE email = ?");
        $stmt->bind_param("s", $_SESSION['email']);
        $stmt->execute();
        $stmt->bind_result($firstname);
        if ($stmt->fetch()) {
            $user_name = htmlspecialchars($firstname);
        }
        $stmt->close();
    }
    $conn_user->close();
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Keep head section unchanged -->
</head>
<body class="support">
    <?php
    // Corrected marquee section
    $conn_marquee = new mysqli("localhost:3306", "zts91xzcemos", "AaravAayansh@1", "rejuvenateganga");
    if ($conn_marquee->connect_error) {
        die("Connection failed: " . $conn_marquee->connect_error);
    }
    
    // Fixed SQL query with correct column names
    $sql = "SELECT firstname, donation_amount_usd, donation_amount_inr FROM donation 
            ORDER BY GREATEST(COALESCE(donation_amount_usd, 0), COALESCE(donation_amount_inr, 0)) DESC 
            LIMIT 5";
    
    $result = $conn_marquee->query($sql);
    $marqueeText = "";
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $amount = "";
            if (!is_null($row['donation_amount_usd'])) {
                $amount = "$" . htmlspecialchars($row['donation_amount_usd']);
            } else {
                $amount = "₹" . htmlspecialchars($row['donation_amount_inr']);
            }
            $marqueeText .= htmlspecialchars($row['firstname']) . " donated " . $amount . " | ";
        }
    }
    $conn_marquee->close();
    ?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donations - Rejuvenate Ganga</title>
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    integrity="sha512-Fo3rlrZj/k7ujTnH/4O1pGl7J0ty7sPs3nV+J8Z6n0CphYfC92VfA23P4yEmKZk9d2R4ZMGULZ9XbKnqzB0Zlw=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />
    <!-- Import Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Economica:wght@400;700&display=swap" rel="stylesheet">
    <!-- Import Roboto font for marquee -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- External CSS -->
    <link rel="stylesheet" href="../cssfolder/styles.css">
    
    <style>
/* Add viewport meta tag in HTML head */
body {
    font-family: 'Economica', sans-serif;
    color: #333;
    margin: 0;
    padding: 0;
}

h1 {
    color: #000;
    font-weight: bold;
}

h2, h3, h4, label {
    color: #333;
    font-weight: bold;
}

.section-header {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    margin: 20px 0 10px 0;
}

.container_donation {
    max-width: 600px;
    width: 600px;
    margin: auto;
    padding: 20px;
    background: #f5f5f5;
    border-radius: 8px;
    text-align: center;
    margin-left: 550px;
}

input, textarea, select {
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #555;
    background-color: #fff;
    color: #333;
    border-radius: 5px;
}

.section-container {
    border-bottom: 1px solid #ccc;
    padding-bottom: 20px;
    margin-bottom: 20px;
}

.section-container:last-child {
    border-bottom: none;
}

.left-align-fields {
    text-align: left;
}

.left-align-fields input, .left-align-fields select {
    display: block;
    width: 200px;
}

.donate-btn, .history-btn {
    background-color: #008CBA;
    color: white;
    padding: 10px 15px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    border-radius: 5px;
    width: 100%;
    margin-top: 10px;
}

.donate-btn:hover, .history-btn:hover {
    background-color: #005f73;
}

.access-history {
    margin-top: 40px;
    padding: 20px;
    background: #e0e0e0;
    border-radius: 8px;
    text-align: center;
}

.confirm-payment-container {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 15px;
    font-size: 14px;
    color: #333;
}

#confirm_payment {
    width: 18px;
    height: 18px;
    accent-color: #008CBA;
    margin-right: 8px;
    cursor: pointer;
}

#submit_button:disabled {
    background-color: #555;
    cursor: not-allowed;
    opacity: 0.6;
}

#submit_button {
    /* Removed transition */
}

.marquee-container {
    width: 100%;
    background-color: #ffffff;
    color: #010101 !important;
    font-size: 32px;
    padding: 25px 0;
    width: 120%;
    margin-left: -100px;
    margin-top: -30px;
    border: 3px solid #000000;
    text-align: center;
    position: relative;
}

.marque_text {
    color: #00674F;
    margin-top: -30px;
    font-size: 32px;
}

.donate-btn:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
    padding: 10px 20px;
}

.navbar .logo {
    font-weight: bold;
    font-size: 20px;
    text-decoration: none;
    color: white;
}

.logout-form button {
    background-color: #00796b;
    color: red !important;
    border: none;
    padding: 8px 12px;
    cursor: pointer;
    border-radius: 4px;
    font-size: 14px;
}

.logout-form button:hover {
    background-color: #004d40;
}

.payment-extra {
    font-size: 1.2rem;
    display: inline-block;
    margin: 0 15px;
}

/* Desktop Payment Layout - Fixed Positions */
@media (min-width: 768px) {
    .payment-methods {
        display: flex;
        justify-content: center;
        position: relative;
        margin: 2rem 0;
    }

    .payment-option {
        position: relative;
        margin: 0 20px;
    }

    .payment-option:nth-child(1) { margin-left: -40px; }
    .payment-option:nth-child(2) { margin-right: 15px; }
    .payment-option:nth-child(3) { margin-right: 60px; }
    .payment-option:nth-child(4) { margin-left: -25px; }
}

/* Mobile Adjustments */
@media (max-width: 767px) {
    .container_donation {
        width: 95%;
        margin: 1rem auto;
        padding: 1rem;
        margin-left: auto;
    }

    .marquee-container {
        margin-left: -50px;
        margin-top: -10px;
        width: 115%;
        padding: 15px 0;
    }

    .marque_text {
        margin-top: -27px;
        font-size: 1.8rem;
    }

    .payment-methods {
        flex-direction: column;
        gap: 1rem;
    }

    .payment-option {
        width: 100%;
        padding: 0.8rem;
    }

    input, textarea, select {
        width: 100% !important;
    }

    .left-align-fields input, 
    .left-align-fields select {
        width: 100% !important;
    }

    .donate-btn, .history-btn {
        padding: 0.8rem;
        font-size: 0.9rem;
    }
}
    </style>
</head>
<body class="support">
<?php
    $conn_marquee = new mysqli("localhost:3306", "zts91xzcemos", "AaravAayansh@1", "rejuvenateganga");
    if ($conn_marquee->connect_error) {
        die("Connection failed: " . $conn_marquee->connect_error);
    }
    
    // Corrected SQL query using actual column names
    $sql = "SELECT firstname, donation_amount_usd, donation_amount_inr 
            FROM donation 
            ORDER BY GREATEST(COALESCE(donation_amount_usd, 0), COALESCE(donation_amount_inr, 0)) DESC 
            LIMIT 51";
    
    $result = $conn_marquee->query($sql);
    $marqueeText = "";
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $amount = "";
            if (!is_null($row['donation_amount_usd'])) {
                $amount = "$" . htmlspecialchars($row['donation_amount_usd']);
            } else {
                $amount = "₹" . htmlspecialchars($row['donation_amount_inr']);
            }
            $marqueeText .= htmlspecialchars($row['firstname']) . " donated " . $amount . " | ";
        }
    }
    $conn_marquee->close();
?>
    <marquee class="marquee-container" behavior="scroll" direction="left">
        <div class="marque_text">
            <?php echo $marqueeText; ?>
        </div>
    </marquee>
    
    <!-- Navigation Bar -->
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
    <a href="signin.php" class="logout-button">Logout</a>
</nav>

    <!-- Donation Form Container -->
    <div class="container_donation">
        <h1>Welcome <?php echo htmlspecialchars($user_name); ?> to Your Donation Dashboard</h1>
        <form action="donation_submit.php" method="post">
            <!-- Top section: Currency, Amount and Date -->
            <div class="left-align-fields section-container">
                <label for="currency">Choose a Currency:</label>
		            <select id="currency" name="currency">
			        <option value="" disabled selected>Select a Currency</option>
			        <option value="dollar">Dollar</option>
			        <option value="rupee">Rupee</option>
		        </select>
                
                <label class="section-header">Amount</label>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <input type="number" placeholder="Enter amount" id="donation_amount" name="donation_amount" min="10" required>
                    <!-- Dollar equivalent field -->
                    <input type="text" id="dollar_equivalent" placeholder="USD Equivalent" readonly style="display: none;">
                </div>
                
                <label class="section-header">Date of Donation</label>
                <input type="date" id="date_of_donation" name="date_of_donation" required>
            </div>
            
            <!-- Additional form fields -->
            <h4 class="section-header">Your Information</h4>
            <input type="text" placeholder="Enter Firstname" id="firstname" name="firstname" required>
            <input type="text" placeholder="Enter Lastname" id="lastname" name="lastname" required>
            <input type="email" placeholder="Enter Email" id="email" name="email" required>
            <textarea id="comment" name="comment" rows="4" placeholder="Leave a message..."></textarea>
            <input type="checkbox" id="payment_info_confirm" onchange="handlePaymentConfirmation()" required>
                <label for="payment_info_confirm" style="font-size: 16px; color: #333; font-weight: bold;">
                    Check only if you are done with your information details and good to make payment
                </label>
            <h4 class="section-header">Payment Details</h4>
            <!-- Replacing the select dropdown with fancy radio buttons -->
            <div class="payment-methods">
                <label class="payment-option">
                    <input type="radio" name="donation-method" value="paypal" onclick="handlePaymentSelection(this)" required>
                    <i class="fab fa-paypal"></i> PayPal
                </label>
                <label class="payment-option">
                    <input type="radio" name="donation-method" value="zelle" onclick="handlePaymentSelection(this)">
                    <i class="fas fa-university"></i> Zelle
                    <span class="payment-extra" id="zelleExtra" style="display:none;">Email: aarav.singh042009@gmail.com <br>Number: +1 (262)-307-9264</span>
                </label>
                <label class="payment-option">
                    <input type="radio" name="donation-method" value="gofundme" onclick="handlePaymentSelection(this)">
                    <i class="fas fa-university"></i> GoFundMe
                </label>
                <label class="payment-option">
                    <input type="radio" name="donation-method" value="upi" onclick="handlePaymentSelection(this)">
                    <i class="fas fa-mobile-alt"></i> UPI
                    <span class="payment-extra" id="upiExtra" style="display:none;">
                        UPI ID: Abhijhim@icici <img src="path_to_upi_image.png" alt="UPI" style="width:20px;height:20px;">
                    </span>
                </label>
            </div>
            <label for="transaction_id" class="section-header"></label>
            <input type="text" id="transaction_id" name="transaction_id" placeholder="Enter your payment transaction ID" required>
            <br>
            <p style="font-size: 16px; color: #333; font-weight:bold;">Check this box after completing the payment.</p>
            <input type="checkbox" id="confirm_payment" onchange="toggleSubmitButton()"> I have completed my payment.

            <button class="donate-btn" type="submit" id="submit_button" disabled>Donate</button>
        </form>
    </div>
    
    <!-- Access Donation History -->
    <div class="container_donation">
        <h2>Access Donation History</h2>
        <form action="report_account.php" method="post">
            <label>Email Address</label>
            <input type="email" placeholder="Enter Email" id="email" name="email" required>
            <button class="history-btn" type="submit">Submit</button>
        </form>
    </div>

    <script>
     document.getElementById('currency').addEventListener('change', updateDollarEquivalent);
    document.getElementById('donation_amount').addEventListener('input', updateDollarEquivalent);

function handlePaymentConfirmation() {
    const checkbox = document.getElementById('payment_info_confirm');
    if (checkbox.checked) {
        alert("Depending on what option you have selected for payment, it might take you to a new screen! Example 'Paypal'\n\nKindly ensure to come back to this payment form and enter your actual payment transaction ID and mark it complete by clicking on 'Submit' button.");
    }
}
// Updated payment selection function to freeze the form after a selection is made
function handlePaymentSelection(radio) {
    const paymentURLs = {
        paypal: "https://paypal.me/AaravS167?country.x=US&locale.x=en_US",
        gofundme: "https://www.gofundme.com/f/rejuvenate-ganga-make-the-ganga-flourish-again?attribution_id=sl:b3f86e8a-f496-4fe7-81cf-0d03061d6fd9&lang=en_US&utm_campaign=man_ss_icons&utm_medium=customer&utm_source=copy_link"
    };

    // Show additional information for certain payment methods
    if (radio.value === "zelle") {
        document.getElementById("zelleExtra").style.display = "inline";
    } 
    else if (radio.value === "upi") {
        document.getElementById("upiExtra").style.display = "inline";
    }
    else if (radio.value === "paypal") {
        document.getElementById("upiExtra").style.display = "inline";
    }
    else if (radio.value === "gofundme") {
        document.getElementById("upiExtra").style.display = "inline";
    }

    // Open external payment URL if applicable
    let url = paymentURLs[radio.value];
    if (url) {
        window.open(url, "_blank");
    }

    // Freeze the donation form fields after a payment option is selected
    freezeFormFields();
}

function toggleSubmitButton() {
    let checkbox = document.getElementById("confirm_payment");
    let submitButton = document.getElementById("submit_button");
    submitButton.disabled = !checkbox.checked;
}

document.getElementById('currency').addEventListener('change', updateDollarEquivalent);
document.getElementById('donation_amount').addEventListener('input', updateDollarEquivalent);
    // Modified freeze function to freeze donation email but keep history email editable
    function freezeFormFields() {
        // Target only the donation form container
        const donationForm = document.querySelector('.container_donation');
        
        donationForm.querySelectorAll("input, textarea, select").forEach(field => {
            // Skip ONLY the submit button
            if (field.id === "submit_button") {
                return;
            }

            // Special handling for currency dropdown
            if (field.id === "currency") {
                field.disabled = true;
                return;
            }

            // Freeze all other fields including donation email
            if (field.tagName === "SELECT") {
                field.disabled = true;
            } else {
                field.readOnly = true;
            }
        });
    }

    // Rest of the functions remain the same
    function handlePaymentSelection(radio) {
        const paymentURLs = {
            paypal: "https://paypal.me/AaravS167?country.x=US&locale.x=en_US",
            gofundme: "https://gofund.me/d4b27b9c",
        };

        
        if (radio.value === "zelle") {
            document.getElementById("zelleExtra").style.display = "inline";
        } else if (radio.value === "upi") {
            document.getElementById("upiExtra").style.display = "inline";
        }

        let url = paymentURLs[radio.value];
        if (url) {
            window.open(url, "_blank");
        }
    }

    // Keep the toggle function unchanged
    function toggleSubmitButton() {
        let checkbox = document.getElementById("confirm_payment");
        let submitButton = document.getElementById("submit_button");
        submitButton.disabled = !checkbox.checked;
    }

    // Keep the currency conversion logic unchanged
    function updateDollarEquivalent() {
        const currencySelect = document.getElementById('currency');
        const amountInput = document.getElementById('donation_amount');
        const dollarEquivalentInput = document.getElementById('dollar_equivalent');

        if (currencySelect.value === 'rupee' && amountInput.value) {
            dollarEquivalentInput.style.display = 'block';
            fetch(`?convert=1&amount=${encodeURIComponent(amountInput.value)}`)
                .then(response => response.json())
                .then(data => {
                    dollarEquivalentInput.value = data.converted ? 
                        `${parseFloat(data.converted).toFixed(2)} USD` : 
                        'Error: Unable to fetch conversion rate.';
                })
                .catch(error => {
                    dollarEquivalentInput.value = 'Error: Unable to fetch conversion rate.';
                    console.error('Error:', error);
                });
        } else {
            dollarEquivalentInput.style.display = 'none';
            dollarEquivalentInput.value = '';
        }
    }

    document.getElementById('currency').addEventListener('change', updateDollarEquivalent);
    document.getElementById('donation_amount').addEventListener('input', updateDollarEquivalent);
    
    function handlePaymentSelection(radio) {
    const paymentURLs = {
        paypal: "https://paypal.me/AaravS167?country.x=US&locale.x=en_US",
        gofundme: "https://www.gofundme.com/f/rejuvenate-ganga-make-the-ganga-flourish-again"
    };

    // Show additional information first
    if (radio.value === "zelle") {
        document.getElementById("zelleExtra").style.display = "inline";
    } 
    else if (radio.value === "upi") {
        document.getElementById("upiExtra").style.display = "inline";
    }

    // Open external URLs first
    let url = paymentURLs[radio.value];
    if (url) {
        window.open(url, "_blank");
    }

    // Show alert after a small delay to allow UI updates
    setTimeout(() => {
        alert("Kindly make the payment using the opened window or displayed details and come back to the site to complete the payment.");
    }, 100);
}

function toggleSubmitButton() {
    let checkbox = document.getElementById("confirm_payment");
    let submitButton = document.getElementById("submit_button");
    submitButton.disabled = !checkbox.checked;
    
    if (checkbox.checked) {
        setTimeout(() => {
            alert("Thank you for confirming your payment! Please submit once your done!");
        }, 100);
    }
}
</script>

    <!-- External JavaScript -->
    <script src="../javascriptfolder/index.js"></script>
</body>
</html>
